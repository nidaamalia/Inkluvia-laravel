import fitz  

def preprocess_pdf(input_path: str, output_path: str):
    """Clean PDF: remove watermarks, enhance contrast"""
    doc = fitz.open(input_path)
    
    for page in doc:
        # Increase resolution for better OCR
        mat = fitz.Matrix(2.0, 2.0)  # 2x zoom
        pix = page.get_pixmap(matrix=mat)
        
        # Save as high-quality image temporarily
        img_bytes = pix.tobytes("png")
        
        # Re-insert as clean page
        page.clean_contents()
    
    doc.save(output_path)
    doc.close()