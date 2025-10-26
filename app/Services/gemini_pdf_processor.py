#!/usr/bin/env python3
"""
Enhanced PDF to JSON Converter with Google Gemini AI
Optimized for accessibility and screen readers
"""

import argparse
import json
import os
import sys
import re
from typing import Dict, List, Any, Optional

try:
    import fitz  # PyMuPDF
    from PIL import Image
    from google import genai
    from google.genai import types
except ImportError as e:
    print(f"Error: Missing required module - {e}", file=sys.stderr)
    print("Install using: pip install PyMuPDF Pillow google-genai", file=sys.stderr)
    sys.exit(1)

# Load API key from environment
GEMINI_API_KEY = os.getenv('GEMINI_API_KEY')

if not GEMINI_API_KEY:
    print("Error: GEMINI_API_KEY environment variable not set", file=sys.stderr)
    sys.exit(1)


class GeminiPdfProcessor:
    def __init__(self, api_key: str):
        """Initialize Gemini client with error handling"""
        try:
            self.client = genai.Client(api_key=api_key)
            self.model = "gemini-2.0-flash-exp"
            self.max_retries = 3
            self.retry_delay = 2
            self.quota_exceeded = False
        except Exception as e:
            print(f"Error: Failed to initialize Gemini client - {e}", file=sys.stderr)
            raise
        
    def _handle_quota_error(self, error: Exception) -> bool:
        message = str(error)
        lowered = message.lower()
        if 'resource_exhausted' in lowered or 'quota' in lowered or '429' in message:
            if not self.quota_exceeded:
                print("Warning: Gemini quota exhausted, disabling AI features for this run", file=sys.stderr)
            self.quota_exceeded = True
            return True
        return False

    def extract_text_and_images(self, pdf_path: str) -> Dict[str, Any]:
        """Extract text and images from PDF with error handling"""
        try:
            doc = fitz.open(pdf_path)
        except Exception as e:
            print(f"Error: Cannot open PDF file - {e}", file=sys.stderr)
            raise
        
        pages_data = []
        total_images = 0
        
        for page_num in range(doc.page_count):
            try:
                page = doc.load_page(page_num)
                page_data = {
                    'page': page_num + 1,
                    'lines': [],
                    'images': []
                }
                
                # Extract text blocks
                blocks = page.get_text("blocks")
                line_number = 1
                
                for block in blocks:
                    text = block[4].strip() if len(block) > 4 else ""
                    if text:
                        for line in text.splitlines():
                            line = line.strip()
                            if line:
                                page_data['lines'].append({
                                    "line": line_number,
                                    "text": line
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
                
            except Exception as e:
                print(f"Warning: Error processing page {page_num + 1}: {e}", file=sys.stderr)
                continue
        
        doc.close()
        
        return {
            'pages': pages_data,
            'total_images': total_images
        }
    
    def sanitize_content(self, pages_data: List[Dict]) -> List[Dict]:
        """
        Sanitize content using Gemini AI:
        - Remove headers, footers, page numbers
        - Fix broken sentences across lines
        - Clean up formatting issues
        """
        if self.quota_exceeded:
            return pages_data
        try:
            # Prepare content for analysis
            full_text = ""
            for page in pages_data:
                page_lines = [line['text'] for line in page.get('lines', [])]
                full_text += f"\n--- Halaman {page['page']} ---\n" + "\n".join(page_lines) + "\n"
            
            if not full_text.strip():
                return pages_data
            
            prompt = """Analisis teks PDF ini dan bersihkan untuk aksesibilitas. Ikuti aturan ini:

1. HAPUS: Header, footer, nomor halaman, dan elemen navigasi berulang
2. PERBAIKI BARIS TERPUTUS: Jika teks terpisah di beberapa baris, gabungkan menjadi kalimat lengkap
   Contoh: "data preparation" + "adalah data yang" + "dibuat" → "data preparation adalah data yang dibuat"
3. PERTAHANKAN: Semua konten bermakna, heading, dan struktur
4. OUTPUT: Kembalikan HANYA teks yang sudah dibersihkan, terorganisir per halaman

Format respons sebagai JSON:
{
  "pages": [
    {
      "page": 1,
      "lines": [
        {"text": "baris bersih 1"},
        {"text": "baris bersih 2"}
      ]
    }
  ]
}

Teks yang akan dibersihkan:
""" + full_text

            response = self.client.models.generate_content(
                model=self.model,
                contents=[prompt]
            )
            
            result_text = response.text.strip()
            # Remove markdown code blocks
            result_text = re.sub(r'```json\s*', '', result_text)
            result_text = re.sub(r'```\s*', '', result_text)
            result_text = result_text.strip()
            
            cleaned_data = json.loads(result_text)
            
            if 'pages' in cleaned_data and isinstance(cleaned_data['pages'], list):
                print(f"✓ Content sanitization successful", file=sys.stderr)
                return cleaned_data['pages']
            
        except json.JSONDecodeError as e:
            print(f"Warning: Failed to parse sanitization result: {e}", file=sys.stderr)
        except Exception as e:
            if self._handle_quota_error(e):
                return pages_data
            print(f"Warning: Content sanitization failed: {e}", file=sys.stderr)
        
        return pages_data
    
    def convert_math_notation(self, text: str) -> str:
        """Convert mathematical notation to screen-reader friendly format"""
        if self.quota_exceeded:
            return self.simple_math_conversion(text)
        try:
            # Check for math indicators
            math_indicators = ['∫', '∑', '√', '²', '³', '⁴', '÷', '×', '±', 'π', 
                             '/', '^', '≤', '≥', '≠', '≈', '∞', '°', 'α', 'β', 'θ']
            
            has_math = any(indicator in text for indicator in math_indicators)
            has_fraction = bool(re.search(r'\d+/\d+', text))
            has_equation = bool(re.search(r'[=+\-*/^]', text))
            
            if not (has_math or has_fraction or has_equation):
                return text
            
            prompt = f"""Konversi teks ini agar mudah dibaca oleh screen reader untuk tunanetra.

Aturan:
1. Konversi pecahan: "1/2" → "satu per dua" atau "setengah"
2. Konversi simbol matematika:
   - ∫ → "integral"
   - ∑ → "sigma" atau "jumlah"
   - √ → "akar"
   - ² → "kuadrat" atau "pangkat dua"
   - ³ → "pangkat tiga"
   - × → "kali"
   - ÷ → "bagi"
   - ± → "plus minus"
   - π → "pi"
   - ° → "derajat"
3. Konversi persamaan ke format lisan:
   - "x² + 2x + 1" → "x kuadrat plus dua x plus satu"
   - "∫(x)dx" → "integral x dx"
4. Tetap jelas dan natural dalam Bahasa Indonesia
5. PENTING: Kembalikan HANYA teks hasil konversi, tanpa penjelasan

Teks: {text}

Hasil konversi:"""

            response = self.client.models.generate_content(
                model=self.model,
                contents=[prompt]
            )
            
            converted = response.text.strip()
            
            # Clean up response
            if '\n' in converted:
                converted = converted.split('\n')[0].strip()
            
            # Remove common unwanted phrases
            unwanted = ['hasil konversi:', 'konversi:', 'output:', 'jawaban:']
            for phrase in unwanted:
                if converted.lower().startswith(phrase):
                    converted = converted[len(phrase):].strip()
            
            return converted if converted else text
            
        except Exception as e:
            if self._handle_quota_error(e):
                return self.simple_math_conversion(text)
            print(f"Warning: Math conversion failed, using fallback: {e}", file=sys.stderr)
            return self.simple_math_conversion(text)
    
    def simple_math_conversion(self, text: str) -> str:
        """Simple fallback math conversion without AI"""
        conversions = {
            '∫': ' integral ',
            '∑': ' sigma ',
            '√': ' akar ',
            '²': ' kuadrat ',
            '³': ' pangkat tiga ',
            '⁴': ' pangkat empat ',
            '×': ' kali ',
            '÷': ' bagi ',
            '±': ' plus minus ',
            'π': ' pi ',
            '°': ' derajat ',
            '≤': ' kurang dari sama dengan ',
            '≥': ' lebih dari sama dengan ',
            '≠': ' tidak sama dengan ',
            '≈': ' hampir sama dengan ',
            '∞': ' tak hingga ',
            'α': ' alpha ',
            'β': ' beta ',
            'θ': ' theta '
        }
        
        result = text
        for symbol, replacement in conversions.items():
            result = result.replace(symbol, replacement)
        
        # Convert simple fractions
        result = re.sub(r'(\d+)/(\d+)', r'\1 per \2', result)
        
        return result
    
    def caption_image(self, image_bytes: bytes, max_words: int = 25) -> str:
        """Generate detailed image caption using Gemini Vision"""
        if self.quota_exceeded:
            return "[Gambar tidak dapat dideskripsikan]"
        try:
            prompt = (
                "Deskripsikan gambar ini dalam Bahasa Indonesia dengan maksimal "
                f"{max_words} kata. Fokus pada:\n"
                "1. Subjek utama gambar\n"
                "2. Detail penting (grafik, diagram, tabel, dll)\n"
                "3. Teks yang terlihat (jika ada)\n"
                "4. Konteks atau makna gambar\n\n"
                "Gunakan kalimat singkat, jelas, dan informatif untuk tunanetra."
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
            
            # Ensure max words
            words = caption.split()
            if len(words) > max_words:
                caption = ' '.join(words[:max_words]) + '...'
            
            return caption
            
        except Exception as e:
            if self._handle_quota_error(e):
                return "[Gambar tidak dapat dideskripsikan]"
            print(f"Warning: Failed to caption image: {e}", file=sys.stderr)
            return "[Gambar tidak dapat dideskripsikan]"
    
    def ocr_image_text(self, image_bytes: bytes) -> Optional[str]:
        """Extract text from image using Gemini OCR"""
        if self.quota_exceeded:
            return None
        try:
            prompt = (
                "Ekstrak semua teks yang terlihat dari gambar ini. "
                "Kembalikan hanya konten teks, pertahankan format dan layout asli sebisa mungkin. "
                "Jika tidak ada teks, kembalikan 'NO_TEXT'."
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
            
            text = response.text.strip()
            
            if text and text != 'NO_TEXT' and len(text) > 5:
                return text
            
            return None
            
        except Exception as e:
            if self._handle_quota_error(e):
                return None
            print(f"Warning: Failed to OCR image: {e}", file=sys.stderr)
            return None
    
    def process_pdf(
        self,
        pdf_path: str,
        caption_images: bool = True,
        ocr_images: bool = True,
        sanitize_content: bool = True,
        convert_math: bool = True,
        max_caption_words: int = 25
    ) -> Dict[str, Any]:
        """Main processing function with all enhancements"""
        
        print(f"🚀 Processing PDF with Gemini AI: {pdf_path}", file=sys.stderr)
        
        # Extract text and images
        print("📄 Extracting text and images...", file=sys.stderr)
        data = self.extract_text_and_images(pdf_path)
        
        processed_pages = []
        images_captioned = 0
        images_ocr = 0
        
        # Step 1: Process images (OCR and captioning)
        for page_data in data['pages']:
            processed_lines = list(page_data['lines'])
            
            for img_data in page_data.get('images', []):
                image_bytes = img_data['bytes']
                
                # Try OCR first
                if ocr_images:
                    print(f"  🔍 OCR processing image {images_ocr + 1}...", file=sys.stderr)
                    ocr_text = self.ocr_image_text(image_bytes)
                    
                    if ocr_text:
                        for line in ocr_text.split('\n'):
                            line = line.strip()
                            if line:
                                processed_lines.append({
                                    'line': len(processed_lines) + 1,
                                    'text': line,
                                    'source': 'ocr'
                                })
                        images_ocr += 1
                
                # Generate caption
                if caption_images:
                    print(f"  🖼️  Captioning image {images_captioned + 1}...", file=sys.stderr)
                    caption = self.caption_image(image_bytes, max_caption_words)
                    processed_lines.append({
                        'line': len(processed_lines) + 1,
                        'text': f"[Gambar: {caption}]",
                        'source': 'image_caption'
                    })
                    images_captioned += 1
            
            processed_pages.append({
                'page': page_data['page'],
                'lines': processed_lines
            })
        
        # Step 2: Sanitize content
        if sanitize_content and processed_pages:
            print("🧹 Sanitizing content with AI...", file=sys.stderr)
            processed_pages = self.sanitize_content(processed_pages)
        
        # Step 3: Convert mathematical notation
        if convert_math:
            print("🔢 Converting mathematical notation...", file=sys.stderr)
            math_converted = 0
            for page in processed_pages:
                for line in page.get('lines', []):
                    if 'text' in line:
                        original = line['text']
                        converted = self.convert_math_notation(original)
                        if converted != original:
                            line['text'] = converted
                            math_converted += 1
            
            if math_converted > 0:
                print(f"  ✓ Converted {math_converted} lines with math notation", file=sys.stderr)
        
        print(f"✅ Processing complete!", file=sys.stderr)
        print(f"   Pages: {len(processed_pages)}", file=sys.stderr)
        print(f"   Images captioned: {images_captioned}", file=sys.stderr)
        print(f"   Images with OCR: {images_ocr}", file=sys.stderr)
        if self.quota_exceeded:
            print("⚠️ Gemini quota exhausted during processing; AI enhancements were partially disabled", file=sys.stderr)
        
        return {
            'pages': processed_pages,
            'images_count': data['total_images'],
            'images_captioned': images_captioned,
            'images_ocr': images_ocr,
            'processing_method': 'gemini_full_enhancement',
            'quota_exceeded': self.quota_exceeded
        }


def main():
    """Main CLI function"""
    parser = argparse.ArgumentParser(
        description='Process PDF with Google Gemini AI for enhanced accessibility',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  python gemini_pdf_processor.py input.pdf -o output.json
  python gemini_pdf_processor.py input.pdf --judul "Matematika" --penerbit "Erlangga"
  python gemini_pdf_processor.py input.pdf --no-caption-images --no-sanitize-content
        """
    )
    
    parser.add_argument('pdf', help='Path to input PDF file')
    parser.add_argument('-o', '--output', help='Path to output JSON file')
    parser.add_argument('--judul', help='Material title')
    parser.add_argument('--penerbit', help='Publisher')
    parser.add_argument('--tahun', type=int, help='Publication year')
    parser.add_argument('--edisi', help='Edition')
    
    # Processing options
    parser.add_argument('--caption-images', action='store_true', default=True,
                       help='Generate image captions (default: enabled)')
    parser.add_argument('--no-caption-images', action='store_false', dest='caption_images',
                       help='Disable image captioning')
    
    parser.add_argument('--ocr-images', action='store_true', default=True,
                       help='Extract text from images (default: enabled)')
    parser.add_argument('--no-ocr-images', action='store_false', dest='ocr_images',
                       help='Disable image OCR')
    
    parser.add_argument('--sanitize-content', action='store_true', default=True,
                       help='Clean headers/footers and fix broken lines (default: enabled)')
    parser.add_argument('--no-sanitize-content', action='store_false', dest='sanitize_content',
                       help='Disable content sanitization')
    
    parser.add_argument('--convert-math', action='store_true', default=True,
                       help='Convert math notation to readable format (default: enabled)')
    parser.add_argument('--no-convert-math', action='store_false', dest='convert_math',
                       help='Disable math conversion')
    
    parser.add_argument('--max-caption-words', type=int, default=25,
                       help='Maximum words for image captions (default: 25)')
    
    args = parser.parse_args()
    
    # Validate PDF file
    if not os.path.isfile(args.pdf):
        print(f"Error: File not found: {args.pdf}", file=sys.stderr)
        sys.exit(2)
    
    if not args.pdf.lower().endswith('.pdf'):
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
            sanitize_content=args.sanitize_content,
            convert_math=args.convert_math,
            max_caption_words=args.max_caption_words
        )
        
        # Build output payload
        payload = {
            'judul': args.judul or 'Untitled',
            'penerbit': args.penerbit or None,
            'tahun': args.tahun or None,
            'edisi': args.edisi or None,
            'pages': result['pages'],
            'processing_method': result.get('processing_method', 'gemini'),
            'images_processed': result.get('images_count', 0),
            'images_captioned': result.get('images_captioned', 0),
            'images_ocr': result.get('images_ocr', 0),
            'ai_services': ['Gemini Vision', 'Gemini AI']
        }
        
        # Write output
        with open(out_path, 'w', encoding='utf-8') as f:
            json.dump(payload, f, ensure_ascii=False, indent=2)
        
        print(f"\n✅ SUCCESS: PDF processed with Gemini AI", file=sys.stderr)
        print(f"📁 Output: {out_path}", file=sys.stderr)
        print(f"📊 Stats:", file=sys.stderr)
        print(f"   - Pages: {len(result['pages'])}", file=sys.stderr)
        print(f"   - Images: {result.get('images_count', 0)}", file=sys.stderr)
        print(f"   - Captions: {result.get('images_captioned', 0)}", file=sys.stderr)
        print(f"   - OCR: {result.get('images_ocr', 0)}", file=sys.stderr)
        
    except KeyboardInterrupt:
        print("\n⚠️  Processing interrupted by user", file=sys.stderr)
        sys.exit(130)
    except Exception as e:
        print(f"\n❌ Error: {str(e)}", file=sys.stderr)
        import traceback
        traceback.print_exc(file=sys.stderr)
        sys.exit(1)


if __name__ == "__main__":
    main()