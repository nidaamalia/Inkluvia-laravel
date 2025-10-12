#!/usr/bin/env python3
import argparse
import json
import os
import sys

import fitz  # PyMuPDF

CURRENT_DIR = os.path.dirname(os.path.abspath(__file__))
if CURRENT_DIR not in sys.path:
    sys.path.insert(0, CURRENT_DIR)

try:
    from gemini_pdf_processor import GeminiPdfProcessor, GEMINI_API_KEY
except ImportError:
    GeminiPdfProcessor = None
    GEMINI_API_KEY = os.getenv("GEMINI_API_KEY")

DEFAULT_CAPTION_MAX_WORDS = int(os.getenv("GEMINI_CAPTION_MAX_WORDS", 12))
DEFAULT_MAX_IMAGES_PER_PAGE = int(os.getenv("GEMINI_MAX_IMAGES_PER_PAGE", 1))
DEFAULT_MIN_IMAGE_AREA_RATIO = float(os.getenv("GEMINI_MIN_IMAGE_AREA_RATIO", 0.01))


def extract_text_from_pdf(
    pdf_path,
    auto_caption=False,
    max_caption_words=DEFAULT_CAPTION_MAX_WORDS,
    gemini_api_key=None,
    stats=None,
    max_images_per_page=DEFAULT_MAX_IMAGES_PER_PAGE,
    min_image_area_ratio=DEFAULT_MIN_IMAGE_AREA_RATIO,
):
    doc = fitz.open(pdf_path)
    pages = []

    gemini_client = None
    total_captions = 0

    if auto_caption:
        if GeminiPdfProcessor is None:
            raise RuntimeError(
                "GeminiPdfProcessor is not available. Install Gemini dependencies and ensure gemini_pdf_processor.py is accessible."
            )

        resolved_api_key = gemini_api_key or GEMINI_API_KEY or os.getenv("GEMINI_API_KEY")
        if not resolved_api_key:
            raise RuntimeError(
                "GEMINI_API_KEY is not set. Provide it via environment variable or --gemini-api-key argument."
            )

        gemini_client = GeminiPdfProcessor(api_key=resolved_api_key)

    try:
        total_pages = doc.page_count
        
        for page_num in range(total_pages):
            page = doc.load_page(page_num)
            lines = []
            blocks = page.get_text("blocks")
            line_number = 1

            for block in blocks:
                text = block[4].strip()
                if text:
                    for line in text.splitlines():
                        if line.strip():
                            # Apply sanitization if using Gemini
                            if gemini_client:
                                cleaned = gemini_client.sanitize_content(line.strip(), page_num + 1, total_pages)
                                if cleaned:
                                    processed = gemini_client.process_text_line(cleaned)
                                    if processed:
                                        lines.append({
                                            "line": line_number,
                                            "text": processed
                                        })
                                        line_number += 1
                            else:
                                lines.append({
                                    "line": line_number,
                                    "text": line.strip()
                                })
                                line_number += 1

            if auto_caption and gemini_client:
                images = page.get_images(full=True)
                processed_xrefs = set()
                page_area = page.rect.width * page.rect.height if page.rect else 0
                candidates = []

                for image_meta in images:
                    try:
                        xref = image_meta[0]
                        if xref in processed_xrefs:
                            continue

                        width = image_meta[2] or 0
                        height = image_meta[3] or 0
                        area = width * height
                        area_ratio = (area / page_area) if page_area else 0

                        if min_image_area_ratio and area_ratio < min_image_area_ratio:
                            processed_xrefs.add(xref)
                            continue

                        base_image = doc.extract_image(xref)
                        image_bytes = base_image["image"]

                        candidates.append({
                            "xref": xref,
                            "area": area,
                            "bytes": image_bytes
                        })
                        processed_xrefs.add(xref)

                    except Exception as exc:
                        print(
                            f"Warning: Failed to prepare image on page {page_num + 1}: {exc}",
                            file=sys.stderr
                        )

                if max_images_per_page is not None and max_images_per_page > 0:
                    candidates.sort(key=lambda item: item["area"], reverse=True)
                    selected_images = candidates[:max_images_per_page]
                else:
                    selected_images = candidates

                for candidate in selected_images:
                    try:
                        # Use the detailed caption method
                        caption_text = gemini_client.caption_image_detailed(candidate["bytes"])
                        lines.append({
                            "line": line_number,
                            "text": f"[Deskripsi Gambar: {caption_text}]"
                        })
                        line_number += 1
                        total_captions += 1
                    except Exception as exc:
                        print(
                            f"Warning: Failed to caption image on page {page_num + 1}: {exc}",
                            file=sys.stderr
                        )

            pages.append({
                "page": page_num + 1,
                "lines": lines
            })

    finally:
        doc.close()

    if stats is not None:
        stats["images_captioned"] = total_captions

    return pages


def extract_metadata(pdf_path):
    doc = fitz.open(pdf_path)
    meta = doc.metadata
    return {
        "judul": meta.get("title"),
        "penerbit": meta.get("publisher") or meta.get("author"),
        "tahun": meta.get("creationDate", "")[2:6] if meta.get("creationDate") else None,
        "edisi": None
    }


def main():
    ap = argparse.ArgumentParser(description="PDF -> JSON with accessibility features")
    ap.add_argument("pdf", help="Path to input PDF file")
    ap.add_argument("-o", "--output", help="Path to output JSON (default: input.json beside PDF)")
    ap.add_argument("--judul", help="Override judul (title)")
    ap.add_argument("--penerbit", help="Override penerbit (publisher)")
    ap.add_argument("--tahun", type=int, help="Override tahun (year)")
    ap.add_argument("--edisi", help="Override edisi (edition)")
    ap.add_argument(
        "--auto-caption",
        action="store_true",
        default=False,
        help="Automatically caption PDF images using Google Gemini with detailed descriptions"
    )
    ap.add_argument(
        "--caption-max-words",
        type=int,
        default=DEFAULT_CAPTION_MAX_WORDS,
        help="Maximum number of words per image caption (ignored for detailed captions)"
    )
    ap.add_argument(
        "--gemini-api-key",
        help="Override GEMINI_API_KEY environment variable"
    )
    ap.add_argument(
        "--max-images-per-page",
        type=int,
        default=DEFAULT_MAX_IMAGES_PER_PAGE,
        help="Maximum number of images to caption on each page"
    )
    ap.add_argument(
        "--min-image-area-ratio",
        type=float,
        default=DEFAULT_MIN_IMAGE_AREA_RATIO,
        help="Minimum image area ratio (image area / page area) to consider for captioning"
    )

    args = ap.parse_args()

    if not os.path.isfile(args.pdf) or not args.pdf.lower().endswith(".pdf"):
        print("Input must be a .pdf file", file=sys.stderr)
        sys.exit(2)

    out_path = args.output or (os.path.splitext(args.pdf)[0] + ".json")
    meta = extract_metadata(args.pdf)
    stats = {}
    pages = extract_text_from_pdf(
        args.pdf,
        auto_caption=args.auto_caption,
        max_caption_words=args.caption_max_words,
        gemini_api_key=args.gemini_api_key,
        stats=stats,
        max_images_per_page=args.max_images_per_page,
        min_image_area_ratio=args.min_image_area_ratio
    )

    payload = {
        "judul": args.judul or meta["judul"],
        "penerbit": args.penerbit or meta["penerbit"],
        "tahun": args.tahun or meta["tahun"],
        "edisi": args.edisi or meta["edisi"],
        "pages": pages,
        "processing_method": "pdf_to_json_with_accessibility" if args.auto_caption else "pdf_to_json"
    }

    if args.auto_caption:
        payload["images_captioned"] = stats.get("images_captioned", 0)
        payload["accessibility_features"] = [
            "detailed_image_captions",
            "content_sanitization",
            "math_chemistry_conversion"
        ]

    with open(out_path, "w", encoding="utf-8") as f:
        json.dump(payload, f, ensure_ascii=False, indent=2)

    print(f"OK: wrote {out_path}")


if __name__ == "__main__":
    main()