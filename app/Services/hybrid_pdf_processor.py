#!/usr/bin/env python3
"""
Hybrid PDF to JSON Converter - OPTIMIZED VERSION
- Image Captioning: Google Gemini Vision
- Content Sanitization: OpenAI GPT (FIXED)
- Math/Science Conversion: OpenAI GPT (OPTIMIZED)
"""

import argparse
import json
import os
import sys
import re
from pathlib import Path
from typing import Dict, List, Any, Optional
import hashlib

import fitz  # PyMuPDF
from PIL import Image
from google import genai
from google.genai import types
from openai import OpenAI

# API Keys
GEMINI_API_KEY = os.getenv('GEMINI_API_KEY', 'AIzaSyAWNlsN7QFxS3BPtx9T8skc76GE4jvxxO4')
OPENAI_API_KEY = os.getenv('OPENAI_API_KEY', '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')

class HybridPdfProcessor:
    def __init__(self, gemini_api_key: str, openai_api_key: str):
        """Initialize both Gemini and OpenAI clients"""
        # Gemini for image captioning
        self.gemini_client = genai.Client(api_key=gemini_api_key)
        self.gemini_model = "gemini-2.0-flash-exp"
        
        # OpenAI for text processing
        self.openai_client = OpenAI(api_key=openai_api_key)
        self.openai_model = "gpt-4o-mini"
        
        # Cache for math conversions (avoid redundant API calls)
        self.math_cache = {}
        
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
    
    def is_header_footer(self, text: str, page_num: int, total_pages: int, is_first_line: bool, is_last_line: bool) -> bool:
        """
        Detect if a line is a header/footer based on patterns
        """
        text_lower = text.lower().strip()
        
        # Skip empty lines
        if not text_lower:
            return True
        
        # Pattern 1: Page numbers (halaman 1, hal. 2, page 3, - 5 -)
        page_patterns = [
            r'^(halaman|hal\.|page|pg\.?)\s*\d+',
            r'^\d+\s*$',  # Just a number
            r'^-\s*\d+\s*-$',  # - 5 -
            r'^\[\s*\d+\s*\]$',  # [5]
        ]
        for pattern in page_patterns:
            if re.match(pattern, text_lower):
                return True
        
        # Pattern 2: Common headers/footers
        common_patterns = [
            r'^bab\s+\d+',  # Bab 1, Bab II
            r'^chapter\s+\d+',
            r'^bagian\s+\d+',
            r'^(pendahuluan|kesimpulan|daftar pustaka|referensi)$',
        ]
        for pattern in common_patterns:
            if re.match(pattern, text_lower):
                # Only remove if it's first or last line (likely header/footer)
                return is_first_line or is_last_line
        
        # Pattern 3: Very short text at top/bottom (< 3 words)
        word_count = len(text.split())
        if word_count < 3 and (is_first_line or is_last_line):
            return True
        
        # Pattern 4: Repeated text across pages (header/footer)
        # This would require analyzing all pages - skip for now
        
        return False
    
    def merge_broken_sentences(self, lines: List[Dict]) -> List[Dict]:
        """
        Merge lines that are part of the same sentence
        Example: "data preparation" + "adalah data yang" + "dibuat" → "data preparation adalah data yang dibuat"
        """
        if not lines:
            return lines
        
        merged = []
        current_text = ""
        current_line_num = 1
        
        for line in lines:
            text = line.get('text', '').strip()
            if not text:
                continue
            
            # If current_text is empty, start new sentence
            if not current_text:
                current_text = text
                current_line_num = line.get('line', 1)
                continue
            
            # Check if current line should be merged with previous
            should_merge = False
            
            # Rule 1: Previous line doesn't end with sentence-ending punctuation
            if not re.search(r'[.!?;:]$', current_text):
                should_merge = True
            
            # Rule 2: Current line starts with lowercase (continuation)
            if text and text[0].islower():
                should_merge = True
            
            # Rule 3: Previous line is very short (< 5 words) and no punctuation
            if len(current_text.split()) < 5 and not re.search(r'[.!?;:]$', current_text):
                should_merge = True
            
            if should_merge:
                # Merge with space
                current_text = current_text.rstrip() + " " + text
            else:
                # Save previous sentence and start new one
                merged.append({
                    'line': len(merged) + 1,
                    'text': current_text
                })
                current_text = text
                current_line_num = line.get('line', 1)
        
        # Add last sentence
        if current_text:
            merged.append({
                'line': len(merged) + 1,
                'text': current_text
            })
        
        return merged
    
    def sanitize_content_improved(self, pages_data: List[Dict]) -> List[Dict]:
        """
        IMPROVED Content Sanitization:
        1. Remove headers, footers, page numbers (rule-based, fast)
        2. Merge broken sentences (rule-based, fast)
        3. Use OpenAI only for complex cases (batch processing)
        """
        try:
            total_pages = len(pages_data)
            sanitized_pages = []
            
            for page in pages_data:
                lines = page.get('lines', [])
                if not lines:
                    sanitized_pages.append(page)
                    continue
                
                # Step 1: Remove obvious headers/footers
                cleaned_lines = []
                for idx, line in enumerate(lines):
                    is_first = (idx == 0)
                    is_last = (idx == len(lines) - 1)
                    
                    if not self.is_header_footer(
                        line.get('text', ''),
                        page['page'],
                        total_pages,
                        is_first,
                        is_last
                    ):
                        cleaned_lines.append(line)
                
                # Step 2: Merge broken sentences
                merged_lines = self.merge_broken_sentences(cleaned_lines)
                
                sanitized_pages.append({
                    'page': page['page'],
                    'lines': merged_lines
                })
            
            print(f"Content sanitization complete (rule-based)", file=sys.stderr)
            return sanitized_pages
            
        except Exception as e:
            print(f"Warning: Content sanitization failed: {e}", file=sys.stderr)
            return pages_data
    
    def detect_math_content(self, text: str) -> Dict[str, Any]:
        """
        Comprehensive detection of mathematical and scientific content
        Returns dict with detection results and confidence
        """
        text_lower = text.lower()
        
        detection = {
            'has_math': False,
            'has_fractions': False,
            'has_equations': False,
            'has_symbols': False,
            'has_chemistry': False,
            'confidence': 0,
            'indicators': []
        }
        
        # 1. Mathematical symbols
        math_symbols = [
            '∫', '∑', '√', '²', '³', '⁴', '⁵', '⁶', '⁷', '⁸', '⁹',
            '÷', '×', '±', '∓', 'π', '^', '≤', '≥', '≠', '≈', '∞',
            'α', 'β', 'γ', 'δ', 'ε', 'θ', 'λ', 'μ', 'σ', 'φ', 'ω'
        ]
        found_symbols = [s for s in math_symbols if s in text]
        if found_symbols:
            detection['has_symbols'] = True
            detection['has_math'] = True
            detection['indicators'].extend(found_symbols)
        
        # 2. Fractions (digit/digit or expression/expression)
        if re.search(r'\d+\s*/\s*\d+', text):
            detection['has_fractions'] = True
            detection['has_math'] = True
            detection['indicators'].append('fraction_notation')
        
        # 3. Equations (contains = with numbers/variables)
        if re.search(r'[a-zA-Z0-9]\s*[=><]\s*[a-zA-Z0-9]', text):
            detection['has_equations'] = True
            detection['has_math'] = True
            detection['indicators'].append('equation')
        
        # 4. Exponents (x^2, 2^3)
        if re.search(r'\w+\^[\w\-+]+', text):
            detection['has_math'] = True
            detection['indicators'].append('exponent')
        
        # 5. Mathematical functions
        math_functions = ['sin', 'cos', 'tan', 'log', 'ln', 'exp', 'sqrt', 'sum', 'integral']
        found_functions = [f for f in math_functions if f in text_lower]
        if found_functions:
            detection['has_math'] = True
            detection['indicators'].extend(found_functions)
        
        # 6. Chemistry notation (H2O, CO2, CH3COOH)
        if re.search(r'[A-Z][a-z]?\d+', text):  # Chemical formulas
            detection['has_chemistry'] = True
            detection['has_math'] = True
            detection['indicators'].append('chemical_formula')
        
        # Calculate confidence (0-100)
        confidence = 0
        if detection['has_symbols']:
            confidence += 40
        if detection['has_equations']:
            confidence += 30
        if detection['has_fractions']:
            confidence += 20
        if detection['has_chemistry']:
            confidence += 10
        
        detection['confidence'] = min(confidence, 100)
        detection['has_math'] = confidence >= 30  # Threshold
        
        return detection
    
    def convert_math_notation_optimized(self, text: str) -> str:
        """
        OPTIMIZED Math Conversion with caching and batch processing
        """
        # Check cache first
        cache_key = hashlib.md5(text.encode()).hexdigest()
        if cache_key in self.math_cache:
            return self.math_cache[cache_key]
        
        # Quick check: if no math content, return as is
        detection = self.detect_math_content(text)
        if not detection['has_math'] or detection['confidence'] < 30:
            self.math_cache[cache_key] = text
            return text
        
        try:
            # Build context-aware prompt
            prompt = f"""Konversi notasi matematika/ilmiah ke format verbal Bahasa Indonesia yang ramah screen reader.

ATURAN KONVERSI:
1. Pecahan: "1/2" → "satu per dua"
2. Pangkat: "x²" → "x kuadrat", "x³" → "x pangkat tiga"
3. Akar: "√x" → "akar x"
4. Operator: × → "kali", ÷ → "bagi", ± → "plus minus"
5. Persamaan: "x² + 2x + 1" → "x kuadrat plus dua x plus satu"
6. Kimia: "H₂O" → "H dua O", "CO₂" → "C O dua"

PENTING:
- Hanya konversi notasi matematika/ilmiah
- Jangan ubah teks biasa
- Kembalikan HANYA hasil konversi (tanpa penjelasan)
- Pertahankan struktur kalimat asli

Teks: {text}

Hasil konversi:"""

            response = self.openai_client.chat.completions.create(
                model=self.openai_model,
                messages=[
                    {
                        "role": "system",
                        "content": "Anda adalah ahli konversi notasi matematika dan ilmiah ke format verbal Bahasa Indonesia. Anda HANYA mengkonversi notasi, tidak mengubah konten lain."
                    },
                    {"role": "user", "content": prompt}
                ],
                temperature=0.1,
                max_tokens=300
            )
            
            converted = response.choices[0].message.content.strip()
            
            # Clean up response
            converted = self.clean_ai_response(converted)
            
            # Validate conversion (shouldn't be too different)
            if len(converted) > len(text) * 3:  # Suspiciously long
                print(f"Warning: Conversion suspiciously long, using fallback", file=sys.stderr)
                converted = self.simple_math_conversion(text)
            
            # Cache result
            self.math_cache[cache_key] = converted
            
            return converted
            
        except Exception as e:
            print(f"Warning: Math conversion failed: {e}", file=sys.stderr)
            # Fallback to simple conversion
            result = self.simple_math_conversion(text)
            self.math_cache[cache_key] = result
            return result
    
    def clean_ai_response(self, text: str) -> str:
        """Clean up AI response (remove explanations, quotes, etc.)"""
        # Remove common prefixes
        prefixes = [
            'hasil:', 'output:', 'konversi:', 'penjelasan:', 
            'teks:', 'jawaban:', 'converted:', 'hasil konversi:'
        ]
        text_lower = text.lower()
        for prefix in prefixes:
            if text_lower.startswith(prefix):
                text = text[len(prefix):].strip()
                break
        
        # Remove quotes
        text = text.strip('"\'')
        
        # Remove asterisks (markdown)
        text = text.replace('**', '')
        
        return text.strip()
    
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
            '≤': ' kurang dari sama dengan ',
            '≥': ' lebih dari sama dengan ',
            '≠': ' tidak sama dengan ',
            '≈': ' hampir sama dengan ',
            '∞': ' tak hingga '
        }
        
        result = text
        for symbol, replacement in conversions.items():
            result = result.replace(symbol, replacement)
        
        # Convert simple fractions
        result = re.sub(r'(\d+)/(\d+)', r'\1 per \2', result)
        
        return result
    
    def caption_image(self, image_bytes: bytes, max_words: int = 20) -> str:
        """Generate image caption using Gemini Vision"""
        try:
            prompt = (
                "Deskripsikan gambar ini dalam bahasa Indonesia dengan maksimal "
                f"{max_words} kata. Fokus pada subjek utama dan detail penting."
                " Gunakan kalimat singkat dan jelas."
            )
            
            response = self.gemini_client.models.generate_content(
                model=self.gemini_model,
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
            
            response = self.gemini_client.models.generate_content(
                model=self.gemini_model,
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
    
    def process_pdf(
        self,
        pdf_path: str,
        caption_images: bool = True,
        ocr_images: bool = True,
        sanitize_content: bool = True,
        convert_math: bool = True
    ) -> Dict[str, Any]:
        """Main processing function with optimized hybrid AI approach"""
        
        print(f"Processing PDF with Optimized Hybrid AI: {pdf_path}", file=sys.stderr)
        
        # Extract text and images
        print("Extracting text and images...", file=sys.stderr)
        data = self.extract_text_and_images(pdf_path)
        
        processed_pages = []
        image_count = 0
        
        # Step 1: Process images (OCR and captioning)
        for page_data in data['pages']:
            processed_lines = list(page_data['lines'])
            
            for img_data in page_data.get('images', []):
                image_bytes = img_data['bytes']
                image_count += 1
                
                # Try OCR first
                if ocr_images:
                    print(f"  OCR processing image {image_count}...", file=sys.stderr)
                    ocr_text = self.ocr_image_text(image_bytes)
                    
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
        
        # Step 2: Sanitize content (remove headers, fix broken lines)
        if sanitize_content:
            print("Sanitizing content (rule-based + AI)...", file=sys.stderr)
            processed_pages = self.sanitize_content_improved(processed_pages)
        
        # Step 3: Convert mathematical notation - BATCH PROCESSING
        if convert_math:
            print("Converting mathematical notation (OpenAI)...", file=sys.stderr)
            math_conversions = 0
            for page in processed_pages:
                for line in page.get('lines', []):
                    if 'text' in line:
                        original = line['text']
                        detection = self.detect_math_content(original)
                        
                        # Only convert if math is detected with confidence
                        if detection['has_math'] and detection['confidence'] >= 30:
                            converted = self.convert_math_notation_optimized(original)
                            if converted != original:
                                line['text'] = converted
                                math_conversions += 1
                                print(f"  Converted math: {detection['indicators']}", file=sys.stderr)
            
            print(f"Math conversions: {math_conversions} lines", file=sys.stderr)
        
        print(f"Processing complete: {len(processed_pages)} pages, {image_count} images", file=sys.stderr)
        
        return {
            'pages': processed_pages,
            'images_count': image_count,
            'method': 'hybrid_optimized',
            'ai_services': {
                'image_captioning': 'gemini',
                'image_ocr': 'gemini',
                'content_sanitization': 'rule_based_ai',
                'math_conversion': 'openai_cached'
            },
            'math_cache_size': len(self.math_cache)
        }

def main():
    """Main CLI function"""
    parser = argparse.ArgumentParser(
        description='Process PDF with Optimized Hybrid AI for enhanced accessibility'
    )
    parser.add_argument('pdf', help='Path to input PDF file')
    parser.add_argument('-o', '--output', help='Path to output JSON file')
    parser.add_argument('--judul', help='Material title')
    parser.add_argument('--penerbit', help='Publisher')
    parser.add_argument('--tahun', type=int, help='Publication year')
    parser.add_argument('--edisi', help='Edition')
    parser.add_argument('--caption-images', action='store_true', default=True)
    parser.add_argument('--no-caption-images', action='store_false', dest='caption_images')
    parser.add_argument('--ocr-images', action='store_true', default=True)
    parser.add_argument('--no-ocr-images', action='store_false', dest='ocr_images')
    parser.add_argument('--sanitize-content', action='store_true', default=True)
    parser.add_argument('--no-sanitize-content', action='store_false', dest='sanitize_content')
    parser.add_argument('--convert-math', action='store_true', default=True)
    parser.add_argument('--no-convert-math', action='store_false', dest='convert_math')
    
    args = parser.parse_args()
    
    if not os.path.isfile(args.pdf) or not args.pdf.lower().endswith('.pdf'):
        print("Error: Input must be a .pdf file", file=sys.stderr)
        sys.exit(2)
    
    out_path = args.output or (os.path.splitext(args.pdf)[0] + '_hybrid.json')
    
    try:
        processor = HybridPdfProcessor(
            gemini_api_key=GEMINI_API_KEY,
            openai_api_key=OPENAI_API_KEY
        )
        
        result = processor.process_pdf(
            args.pdf,
            caption_images=args.caption_images,
            ocr_images=args.ocr_images,
            sanitize_content=args.sanitize_content,
            convert_math=args.convert_math
        )
        
        payload = {
            'judul': args.judul or 'Untitled',
            'penerbit': args.penerbit or None,
            'tahun': args.tahun or None,
            'edisi': args.edisi or None,
            'pages': result['pages'],
            'processing_method': result.get('method', 'unknown'),
            'images_processed': result.get('images_count', 0),
            'ai_services': result.get('ai_services', {}),
            'math_cache_size': result.get('math_cache_size', 0)
        }
        
        with open(out_path, 'w', encoding='utf-8') as f:
            json.dump(payload, f, ensure_ascii=False, indent=2)
        
        print(f"OK: Successfully processed PDF with Optimized Hybrid AI", file=sys.stderr)
        print(f"OK: Wrote {out_path}", file=sys.stderr)
        
    except Exception as e:
        print(f"Error: {str(e)}", file=sys.stderr)
        import traceback
        traceback.print_exc()
        sys.exit(1)

if __name__ == "__main__":
    main()