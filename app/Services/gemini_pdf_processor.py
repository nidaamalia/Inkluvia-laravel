#!/usr/bin/env python3
"""
Enhanced PDF to JSON Converter with Google Gemini AI
- Extracts text from PDFs (including image-based text via OCR)
- Captions images using Gemini Vision
- Handles complex layouts
"""

import argparse
import json
import os
import sys
import io
from pathlib import Path
from typing import Dict, List, Any, Optional

import fitz  # PyMuPDF
from PIL import Image
from google import genai
from google.genai import types

# Load API key from environment or use provided key
GEMINI_API_KEY = os.getenv('GEMINI_API_KEY', 'AIzaSyBW_Uc0GgDfhDImCdOWQr-sz-YIZUr_MFw')

class GeminiPdfProcessor:
    def __init__(self, api_key: str):
        """Initialize Gemini client"""
        self.client = genai.Client(api_key=api_key)
        self.model = "gemini-2.0-flash-exp"  # Use latest model
        
    def extract_text_and_images(self, pdf_path: str) -> Dict[str, Any]:
        """Extract text and images from PDF"""
        doc = fitz.open(pdf_path)
        pages_data = []
        total_images = 0
        
        for page_num in range(doc.page_count):
            page = doc.load_page(page_num)
            page_data = {
                'page': page_num + 1,
                'lines': [],
                'images': []
            }
            
            # Extract text
            blocks = page.get_text("blocks")
            line_number = 1
            
            for block in blocks:
                # block[4] is the text content
                text = block[4].strip()
                if text:
                    for line in text.splitlines():
                        if line.strip():
                            page_data['lines'].append({
                                "line": line_number,
                                "text": line.strip()
                            })
                            line_number += 1
            
            # Extract images
            images = page.get_images()
            for img_index, img in enumerate(images):
                try:
                    xref = img[0]
                    base_image = doc.extract_image(xref)
                    image_bytes = base_image["image"]
                    image_ext = base_image["ext"]
                    
                    # Store image data for Gemini processing
                    page_data['images'].append({
                        'index': img_index,
                        'bytes': image_bytes,
                        'extension': image_ext,
                        'xref': xref
                    })
                    total_images += 1
                except Exception as e:
                    print(f"Warning: Could not extract image {img_index} on page {page_num + 1}: {e}", file=sys.stderr)
            
            pages_data.append(page_data)
        
        doc.close()
        
        return {
            'pages': pages_data,
            'total_images': total_images
        }
    
    def caption_image(self, image_bytes: bytes, max_words: int = 20) -> str:
        """Generate image caption using Gemini Vision"""
        try:
            prompt = (
                "Deskripsikan gambar ini dalam bahasa Indonesia dengan maksimal "
                f"{max_words} kata. Fokus pada subjek utama dan detail penting."
                " Gunakan kalimat singkat dan jelas."
            )
            
            response = self.client.models.generate_content(
                model=self.model,
                contents=[
                    types.Part.from_bytes(
                        data=image_bytes,
                        mime_type='image/jpeg'
                    ),
                    prompt
                ]
            )
            
            caption = response.text.strip()
            caption = " ".join(caption.split())
            
            # Ensure it doesn't exceed max words
            words = caption.split()
            if len(words) > max_words:
                caption = ' '.join(words[:max_words]) + '...'
            
            return caption
            
        except Exception as e:
            print(f"Warning: Failed to caption image: {e}", file=sys.stderr)
            return "[Gambar tidak dapat dideskripsikan]"
    
    def ocr_image_text(self, image_bytes: bytes) -> Optional[str]:
        """Extract text from image using Gemini OCR"""
        try:
            prompt = "Extract all visible text from this image. Return only the text content, maintaining the original layout and formatting as much as possible. If there is no text, return 'NO_TEXT'."
            
            response = self.client.models.generate_content(
                model=self.model,
                contents=[
                    types.Part.from_bytes(
                        data=image_bytes,
                        mime_type='image/jpeg'
                    ),
                    prompt
                ]
            )
            
            text = response.text.strip()
            
            if text and text != 'NO_TEXT' and len(text) > 5:
                return text
            
            return None
            
        except Exception as e:
            print(f"Warning: Failed to OCR image: {e}", file=sys.stderr)
            return None
    
    def enhance_pdf_content(self, pdf_path: str) -> str:
        """Use Gemini to analyze and extract content from entire PDF"""
        try:
            filepath = Path(pdf_path)
            
            prompt = """Analyze this PDF document and extract all text content with high accuracy.
            
Important instructions:
1. Extract ALL text, including text that might be embedded in images
2. Maintain the original structure and layout
3. For any images you find, provide a brief description in [Image: description] format (max 10 words)
4. Preserve formatting like bullet points, numbers, and headings
5. Return the content in a structured format with clear page breaks

Format your response as JSON with this structure:
{
    "pages": [
        {
            "page": 1,
            "content": "Full text content of page 1...",
            "images": ["Image 1 description", "Image 2 description"]
        }
    ]
}"""
            
            response = self.client.models.generate_content(
                model=self.model,
                contents=[
                    types.Part.from_bytes(
                        data=filepath.read_bytes(),
                        mime_type='application/pdf'
                    ),
                    prompt
                ]
            )
            
            return response.text
            
        except Exception as e:
            print(f"Warning: Full PDF analysis failed: {e}", file=sys.stderr)
            return None
    
    def process_pdf(
        self,
        pdf_path: str,
        caption_images: bool = True,
        ocr_images: bool = True,
        use_full_pdf_analysis: bool = False
    ) -> Dict[str, Any]:
        """Main processing function"""
        
        print(f"Processing PDF with Gemini AI: {pdf_path}", file=sys.stderr)
        
        # Method 1: Use full PDF analysis (more accurate but slower)
        if use_full_pdf_analysis:
            print("Using full PDF analysis mode...", file=sys.stderr)
            gemini_result = self.enhance_pdf_content(pdf_path)
            
            if gemini_result:
                try:
                    # Try to parse Gemini's JSON response
                    # Remove markdown code blocks if present
                    gemini_result = gemini_result.replace('```json', '').replace('```', '').strip()
                    parsed_data = json.loads(gemini_result)
                    
                    # Convert to standard format
                    pages = []
                    for page_data in parsed_data.get('pages', []):
                        lines = []
                        content = page_data.get('content', '')
                        
                        line_num = 1
                        for line in content.split('\n'):
                            if line.strip():
                                lines.append({
                                    'line': line_num,
                                    'text': line.strip()
                                })
                                line_num += 1
                        
                        # Add image descriptions as separate lines
                        for img_desc in page_data.get('images', []):
                            lines.append({
                                'line': line_num,
                                'text': f"[Gambar: {img_desc}]"
                            })
                            line_num += 1
                        
                        pages.append({
                            'page': page_data.get('page', len(pages) + 1),
                            'lines': lines
                        })
                    
                    return {
                        'pages': pages,
                        'method': 'gemini_full_pdf_analysis'
                    }
                    
                except json.JSONDecodeError:
                    print("Warning: Could not parse Gemini JSON response, falling back to standard method", file=sys.stderr)
        
        # Method 2: Standard extraction with enhancements
        print("Using standard extraction with Gemini enhancements...", file=sys.stderr)
        data = self.extract_text_and_images(pdf_path)
        
        processed_pages = []
        image_count = 0
        
        for page_data in data['pages']:
            processed_lines = list(page_data['lines'])  # Copy existing lines
            
            # Process images on this page
            for img_data in page_data.get('images', []):
                image_bytes = img_data['bytes']
                image_count += 1
                
                # Try OCR first if enabled
                ocr_text = None
                if ocr_images:
                    print(f"  OCR processing image {image_count}...", file=sys.stderr)
                    ocr_text = self.ocr_image_text(image_bytes)
                
                # If OCR found text, add it to lines
                if ocr_text:
                    for line in ocr_text.split('\n'):
                        if line.strip():
                            processed_lines.append({
                                'line': len(processed_lines) + 1,
                                'text': line.strip(),
                                'source': 'ocr'
                            })
                
                # Generate caption
                if caption_images:
                    print(f"  Captioning image {image_count}...", file=sys.stderr)
                    caption = self.caption_image(image_bytes)
                    processed_lines.append({
                        'line': len(processed_lines) + 1,
                        'text': f"[Gambar: {caption}]",
                        'source': 'image_caption'
                    })
            
            processed_pages.append({
                'page': page_data['page'],
                'lines': processed_lines
            })
        
        print(f"Processed {len(processed_pages)} pages with {image_count} images", file=sys.stderr)
        
        return {
            'pages': processed_pages,
            'images_count': image_count,
            'method': 'standard_with_gemini_enhancements'
        }

def main():
    """Main CLI function"""
    parser = argparse.ArgumentParser(
        description='Process PDF with Google Gemini AI for enhanced text extraction and image captioning'
    )
    parser.add_argument('pdf', help='Path to input PDF file')
    parser.add_argument('-o', '--output', help='Path to output JSON file')
    parser.add_argument('--judul', help='Material title')
    parser.add_argument('--penerbit', help='Publisher')
    parser.add_argument('--tahun', type=int, help='Publication year')
    parser.add_argument('--edisi', help='Edition')
    parser.add_argument('--caption-images', action='store_true', default=True, help='Caption images (default: True)')
    parser.add_argument('--no-caption-images', action='store_false', dest='caption_images', help='Disable image captioning')
    parser.add_argument('--ocr-images', action='store_true', default=True, help='OCR text from images (default: True)')
    parser.add_argument('--no-ocr-images', action='store_false', dest='ocr_images', help='Disable OCR')
    parser.add_argument('--full-analysis', action='store_true', help='Use full PDF analysis (slower but more accurate)')
    
    args = parser.parse_args()
    
    # Validate PDF file
    if not os.path.isfile(args.pdf) or not args.pdf.lower().endswith('.pdf'):
        print("Error: Input must be a .pdf file", file=sys.stderr)
        sys.exit(2)
    
    # Determine output path
    out_path = args.output or (os.path.splitext(args.pdf)[0] + '_gemini.json')
    
    try:
        # Initialize processor
        processor = GeminiPdfProcessor(api_key=GEMINI_API_KEY)
        
        # Process PDF
        result = processor.process_pdf(
            args.pdf,
            caption_images=args.caption_images,
            ocr_images=args.ocr_images,
            use_full_pdf_analysis=args.full_analysis
        )
        
        # Add metadata
        payload = {
            'judul': args.judul or 'Untitled',
            'penerbit': args.penerbit or None,
            'tahun': args.tahun or None,
            'edisi': args.edisi or None,
            'pages': result['pages'],
            'processing_method': result.get('method', 'unknown'),
            'images_processed': result.get('images_count', 0)
        }
        
        # Write output
        with open(out_path, 'w', encoding='utf-8') as f:
            json.dump(payload, f, ensure_ascii=False, indent=2)
        
        print(f"OK: Successfully processed PDF with Gemini AI", file=sys.stderr)
        print(f"OK: Wrote {out_path}", file=sys.stderr)
        
    except Exception as e:
        print(f"Error: {str(e)}", file=sys.stderr)
        sys.exit(1)

if __name__ == "__main__":
    main()