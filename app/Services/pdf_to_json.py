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

def extract_text_from_pdf(
    pdf_path,
    auto_caption=False,
    max_caption_words=12,
    gemini_api_key=None,
    stats=None,
    max_images_per_page=1,
    min_image_area_ratio=0.01
):
    doc = fitz.open(pdf_path)
    pages = []

    gemini_client = None
    total_captions = 0

    if auto_caption:
        if GeminiPdfProcessor is None:
            raise RuntimeError(
                "GeminiPdfProcessor is not available. Install the Gemini dependencies first."
            )
        resolved_api_key = gemini_api_key or GEMINI_API_KEY or os.getenv("GEMINI_API_KEY")
        if not resolved_api_key:
            raise RuntimeError(
                "GEMINI_API_KEY is not set. Provide it via environment variable or CLI argument."
            )
        gemini_client = GeminiPdfProcessor(api_key=resolved_api_key)

    for page_num in range(doc.page_count):
        page = doc.load_page(page_num)
        lines = []
        blocks = page.get_text("blocks")
        line_number = 1

        for block in blocks:
            text = block[4].strip()
            if text:
                for line in text.splitlines():
                    if line.strip():
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

            for img in images:
                try:
                    xref = img[0]
                    if xref in processed_xrefs:
                        continue
                    width = img[2] or 0
                    height = img[3] or 0
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
                    caption_text = gemini_client.caption_image(
                        candidate["bytes"],
                        max_words=max_caption_words
                    )
                    lines.append({
                        "line": line_number,
                        "text": f"[Gambar: {caption_text}]"
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

    if stats is not None:
        stats["images_captioned"] = total_captions

    return pages

def extract_metadata(pdf_path):
    doc = fitz.open(pdf_path)
    meta = doc.metadata
    # Use common PDF metadata fields
    return {
        "judul": meta.get("title"),
        "penerbit": meta.get("publisher") or meta.get("author"),
        "tahun": meta.get("creationDate", "")[2:6] if meta.get("creationDate") else None,
        "edisi": None  # Usually not in PDF metadata
    }

def main():
    ap = argparse.ArgumentParser(description="PDF -> JSON (pages + lines) using PyMuPDF")
    ap.add_argument("pdf", help="Path to input PDF file")
    ap.add_argument("-o", "--output", help="Path to output JSON (default: input.json beside PDF)")
    ap.add_argument("--judul", help="Override judul (title)")
    ap.add_argument("--penerbit", help="Override penerbit (publisher)")
    ap.add_argument("--tahun", type=int, help="Override tahun (year)")
    ap.add_argument("--edisi", help="Override edisi (edition)")
    ap.add_argument(
        "--auto-caption",
        action="store_true",
        help="Automatically caption images using Google Gemini (requires GEMINI_API_KEY)"
    )
    ap.add_argument(
        "--caption-max-words",
        type=int,
        default=12,
        help="Maximum number of words per generated image caption (default: 12)"
    )
    ap.add_argument(
        "--gemini-api-key",
        help="Override the GEMINI_API_KEY environment variable for Gemini integration"
    )
    ap.add_argument(
        "--max-images-per-page",
        type=int,
        default=1,
        help="Maximum number of images to caption per page (default: 1)"
    )
    ap.add_argument(
        "--min-image-area-ratio",
        type=float,
        default=0.01,
        help="Minimum area ratio (image area / page area) to consider for captioning (default: 0.01)"
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
        "pages": pages
    }

    if args.auto_caption:
        payload["images_captioned"] = stats.get("images_captioned", 0)

    with open(out_path, "w", encoding="utf-8") as f:
        json.dump(payload, f, ensure_ascii=False, indent=2)

    print(f"OK: wrote {out_path}")

if __name__ == "__main__":
    main()