<?php

namespace App\Services;

use App\Models\Material;
use App\Models\BrailleContent;
use App\Models\MaterialBrailleContent;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class PdfConversionService
{
    private $pythonScriptPath;
    private $tempDir;
    private $brailleConverter;

    public function __construct()
    {
        $this->pythonScriptPath = app_path('Services/pdf_converter.py');
        $this->tempDir = storage_path('app/temp');
        $this->brailleConverter = new BrailleConverter();
        
        // Ensure temp directory exists
        if (!file_exists($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }
    }

    /**
     * Convert PDF to Braille content
     */
    public function convertPdfToBraille(Material $material)
    {
        try {
            Log::info("Starting PDF to Braille conversion for material ID: {$material->id}");

            // Step 1: Read existing JSON data (created by PdfToJsonService)
            $jsonData = $this->readJsonData($material->file_path);
            
            if (!$jsonData) {
                throw new Exception('Failed to read JSON data from file: ' . $material->file_path);
            }

            // Step 2: Convert text to Braille using BrailleConverter
            $brailleContent = $this->convertTextToBraille($jsonData);

            // Step 3: Save Braille content
            $brailleDataPath = $this->saveBrailleContent($material, $brailleContent);

            // Step 4: Update material record
            $pageCount = isset($brailleContent['pages']) ? count($brailleContent['pages']) : count($brailleContent);
            $material->update([
                'braille_data_path' => $brailleDataPath,
                'total_halaman' => $pageCount,
                'status' => 'published'
            ]);

            Log::info("PDF to Braille conversion completed for material ID: {$material->id}");
            
            return true;

        } catch (Exception $e) {
            Log::error("PDF to Braille conversion failed for material ID: {$material->id}. Error: " . $e->getMessage());
            
            // Update material status to indicate failure
            $material->update(['status' => 'draft']);
            
            return false;
        }
    }

    /**
     * Read existing JSON data from file
     */
    private function readJsonData($filePath)
    {
        if (!Storage::disk('private')->exists($filePath)) {
            throw new Exception('JSON file not found: ' . $filePath);
        }

        $jsonContent = Storage::disk('private')->get($filePath);
        $jsonData = json_decode($jsonContent, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON file: ' . json_last_error_msg());
        }

        return $jsonData;
    }

    /**
     * Convert extracted text to Braille using BrailleConverter
     */
    private function convertTextToBraille($jsonData)
    {
        if (isset($jsonData['pages']) && is_array($jsonData['pages'])) {
            $brailleJson = [
                'judul' => $this->brailleConverter->toBraille($jsonData['judul'] ?? ''),
                'penerbit' => $this->brailleConverter->toBraille($jsonData['penerbit'] ?? ''),
                'tahun' => $this->brailleConverter->toBraille($jsonData['tahun'] ?? ''),
                'edisi' => $this->brailleConverter->toBraille($jsonData['edisi'] ?? ''),
                'pages' => []
            ];
            
            foreach ($jsonData['pages'] as $pageData) {
                $braillePage = $this->convertPageToBraille($pageData);
                $brailleJson['pages'][] = $braillePage;
            }
            
            return $brailleJson;
        }
        
        return ['pages' => []];
    }

    /**
     * Convert a single page to Braille using BrailleConverter
     */
    private function convertPageToBraille($pageData)
    {
        $braillePage = [
            'page' => $pageData['page'] ?? 1,
            'lines' => []
        ];
        
        if (isset($pageData['lines']) && is_array($pageData['lines'])) {
            foreach ($pageData['lines'] as $index => $line) {
                if (isset($line['text']) && !empty(trim($line['text']))) {
                    $originalText = trim($line['text']);
                    
                    // Convert to Braille using BrailleConverter
                    $brailleText = $this->brailleConverter->toBraille($originalText);
                    
                    // Get decimal values for each character
                    $decimalValues = $this->convertBrailleToDecimalValues($brailleText);
                    
                    $braillePage['lines'][] = [
                        'line' => $index + 1,
                        'text' => $brailleText,
                        'original_text' => $originalText,
                        'decimal_values' => $decimalValues,
                        'decimal' => implode('', $decimalValues)
                    ];
                }
            }
        }
        
        return $braillePage;
    }

    /**
     * Convert Braille Unicode text to decimal values
     */
    private function convertBrailleToDecimalValues(string $brailleText): array
    {
        if (empty($brailleText)) {
            return [];
        }

        $decimalValues = [];
        $length = mb_strlen($brailleText, 'UTF-8');

        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($brailleText, $i, 1, 'UTF-8');
            $decimal = $this->brailleCharToDecimal($char);
            $decimalValues[] = str_pad((string)$decimal, 2, '0', STR_PAD_LEFT);
        }

        return $decimalValues;
    }

    /**
     * Convert a single Braille character to decimal value
     */
    private function brailleCharToDecimal(string $brailleChar): int
    {
        $codePoint = $this->getCodePoint($brailleChar);

        if ($codePoint === null || $codePoint < 0x2800 || $codePoint > 0x28FF) {
            return 0; // Return 0 for space or invalid characters
        }

        // Braille pattern dots are stored in bits 0-5 of the offset from 0x2800
        $mask = $codePoint - 0x2800;
        
        return $mask & 0b00111111; // Extract 6 bits
    }

    /**
     * Get Unicode code point of a character
     */
    private function getCodePoint(string $char): ?int
    {
        if ($char === '' || $char === ' ') {
            return 0x2800; // Braille space
        }

        $encoded = mb_convert_encoding($char, 'UCS-4BE', 'UTF-8');
        if ($encoded === false) {
            return null;
        }

        $codePoint = unpack('N', $encoded);

        return $codePoint[1] ?? null;
    }

    /**
     * Save Braille content to database and file
     */
    private function saveBrailleContent(Material $material, $brailleContent)
    {
        // Clear existing Braille content
        MaterialBrailleContent::where('material_id', $material->id)->delete();

        if (isset($brailleContent['pages']) && is_array($brailleContent['pages'])) {
            foreach ($brailleContent['pages'] as $pageData) {
                $brailleText = '';
                $originalText = '';
                $lineCount = 0;
                $characterCount = 0;
                
                if (isset($pageData['lines']) && is_array($pageData['lines'])) {
                    foreach ($pageData['lines'] as $line) {
                        $brailleText .= $line['text'] . "\n";
                        $originalText .= ($line['original_text'] ?? '') . "\n";
                        $characterCount += mb_strlen($line['text'], 'UTF-8');
                    }
                    $lineCount = count($pageData['lines']);
                }
                
                MaterialBrailleContent::create([
                    'material_id' => $material->id,
                    'page_number' => $pageData['page'] ?? 1,
                    'braille_text' => trim($brailleText),
                    'original_text' => trim($originalText),
                    'metadata' => [
                        'judul' => $brailleContent['judul'] ?? '',
                        'penerbit' => $brailleContent['penerbit'] ?? '',
                        'tahun' => $brailleContent['tahun'] ?? '',
                        'edisi' => $brailleContent['edisi'] ?? ''
                    ],
                    'line_count' => $lineCount,
                    'character_count' => $characterCount
                ]);
            }
        }

        // Save to file
        $fileName = 'braille_' . $material->id . '_' . time() . '.json';
        $filePath = 'materials/braille/' . $fileName;
        
        Storage::disk('private')->put(
            $filePath, 
            json_encode($brailleContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        return $filePath;
    }

    /**
     * Get conversion status
     */
    public function getConversionStatus(Material $material)
    {
        $brailleContents = MaterialBrailleContent::where('material_id', $material->id)->get();
        
        return [
            'total_pages' => $brailleContents->count(),
            'conversion_complete' => $material->braille_data_path !== null,
            'status' => $material->status,
            'last_updated' => $material->updated_at
        ];
    }

    /**
     * Reconvert material
     */
    public function reconvert(Material $material)
    {
        // Delete existing Braille content
        MaterialBrailleContent::where('material_id', $material->id)->delete();
        
        if ($material->braille_data_path) {
            Storage::disk('private')->delete($material->braille_data_path);
        }

        // Reset material status
        $material->update([
            'braille_data_path' => null,
            'total_halaman' => 0,
            'status' => 'processing'
        ]);

        // Reconvert
        return $this->convertPdfToBraille($material);
    }
}