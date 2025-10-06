<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class GeminiPdfProcessorService
{
    protected string $pythonScriptPath;
    protected string $tempDir;

    public function __construct()
    {
        $this->pythonScriptPath = base_path('app/Services/pdf_to_json.py');
        $this->tempDir = storage_path('app/temp/gemini_processing');
        
        if (!file_exists($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }
    }

    /**
     * Process PDF with Gemini AI for enhanced text extraction and image captioning
     * 
     * @param string $pdfPath Full path to PDF file
     * @param array $options Processing options
     * @return array|null Processed JSON data
     */
    public function processPdfWithGemini(string $pdfPath, array $options = []): ?array
    {
        try {
            if (!file_exists($this->pythonScriptPath)) {
                Log::error('Gemini Python script not found: ' . $this->pythonScriptPath);
                return null;
            }

            if (!file_exists($pdfPath) || !is_readable($pdfPath)) {
                Log::error('PDF file not found or not readable: ' . $pdfPath);
                return null;
            }

            // Generate output filename
            $fileHash = md5_file($pdfPath);
            $outputFile = $this->tempDir . '/gemini_processed_' . $fileHash . '.json';
            
            // Check cache (1 hour)
            if (file_exists($outputFile) && (time() - filemtime($outputFile)) < 3600) {
                Log::info('Using cached Gemini-processed JSON for: ' . basename($pdfPath));
                $cachedData = json_decode(file_get_contents($outputFile), true);
                if ($cachedData) {
                    return $cachedData;
                }
            }

            $pythonCmd = $this->resolvePythonCommand();
            $pythonScript = escapeshellarg($this->pythonScriptPath);
            $pdfFile = escapeshellarg($pdfPath);
            $outputFileEscaped = escapeshellarg($outputFile);
            
            // Build command with options
            $params = [];
            if (isset($options['judul']) && $options['judul']) {
                $params[] = '--judul ' . escapeshellarg($options['judul']);
            }
            if (isset($options['penerbit']) && $options['penerbit']) {
                $params[] = '--penerbit ' . escapeshellarg($options['penerbit']);
            }
            if (isset($options['tahun']) && $options['tahun']) {
                $params[] = '--tahun ' . escapeshellarg($options['tahun']);
            }
            if (isset($options['edisi']) && $options['edisi']) {
                $params[] = '--edisi ' . escapeshellarg($options['edisi']);
            }
            
            // Always enable auto-captioning when using Gemini integration
            if (!isset($options['auto_caption']) || $options['auto_caption']) {
                $params[] = '--auto-caption';
            }

            $captionMaxWords = $options['caption_max_words']
                ?? config('services.gemini.caption_max_words', 20);
            if (!empty($captionMaxWords)) {
                $params[] = '--caption-max-words ' . escapeshellarg((int) $captionMaxWords);
            }

            $maxImagesPerPage = $options['max_images_per_page']
                ?? config('services.gemini.max_images_per_page', 1);
            if ($maxImagesPerPage !== null) {
                $params[] = '--max-images-per-page ' . escapeshellarg((int) $maxImagesPerPage);
            }

            $minImageAreaRatio = $options['min_image_area_ratio']
                ?? config('services.gemini.min_image_area_ratio', 0.01);
            if ($minImageAreaRatio !== null) {
                $params[] = '--min-image-area-ratio ' . escapeshellarg($minImageAreaRatio);
            }

            $geminiApiKey = $options['gemini_api_key']
                ?? config('services.gemini.api_key');
            if (!empty($geminiApiKey)) {
                $params[] = '--gemini-api-key ' . escapeshellarg($geminiApiKey);
            }
            
            $paramString = implode(' ', $params);
            $cmd = trim("{$pythonCmd} {$pythonScript} {$pdfFile} -o {$outputFileEscaped} {$paramString}");
            
            Log::info('Executing Gemini PDF processing: ' . $cmd);
            
            $output = [];
            $returnCode = 0;
            exec($cmd . ' 2>&1', $output, $returnCode);
            
            if ($returnCode !== 0) {
                Log::error('Gemini PDF processing failed', [
                    'command' => $cmd,
                    'return_code' => $returnCode,
                    'output' => implode("\n", $output)
                ]);
                return null;
            }
            
            if (!file_exists($outputFile)) {
                Log::error('Gemini output file not created: ' . $outputFile);
                return null;
            }

            $jsonContent = file_get_contents($outputFile);
            $data = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid JSON generated by Gemini: ' . json_last_error_msg());
                return null;
            }

            if (!isset($data['pages']) || !is_array($data['pages']) || empty($data['pages'])) {
                Log::warning('Gemini processing resulted in empty or invalid pages data');
                return null;
            }

            if ($data && isset($data['pages'])) {
                $data = $this->postProcessJsonData($data);
            }
            
            Log::info('Gemini PDF processing successful', [
                'pages_count' => count($data['pages'] ?? []),
                'has_images' => $data['images_captioned'] ?? ($data['images_count'] ?? 0),
                'total_lines' => array_sum(array_map(function($page) {
                    return count($page['lines'] ?? []);
                }, $data['pages'] ?? []))
            ]);

            return $data;

        } catch (\Exception $e) {
            Log::error('Gemini PDF processing error', [
                'pdf_path' => $pdfPath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Clean up and structure JSON data
     */
    private function postProcessJsonData(array $jsonData): array
    {
        foreach ($jsonData['pages'] as &$page) {
            // Remove duplicate lines
            $seen = [];
            $cleanLines = [];
            
            foreach ($page['lines'] ?? [] as $line) {
                $text = trim($line['text']);
                
                // Skip if empty or already seen
                if (empty($text) || isset($seen[$text])) {
                    continue;
                }
                
                $seen[$text] = true;
                $cleanLines[] = $line;
            }
            
            $page['lines'] = $cleanLines;
        }
        
        return $jsonData;
    }

    /**
     * Determine which Python command to execute
     */
    protected function resolvePythonCommand(): string
    {
        $configured = config('services.python.binary') ?? env('PYTHON_BINARY');

        if ($configured) {
            return trim($configured);
        }

        if (PHP_OS_FAMILY === 'Windows') {
            return 'py -3';
        }

        return 'python3';
    }

    /**
     * Clean up temporary files
     */
    public function cleanupTempFiles(int $olderThanHours = 24): void
    {
        $files = glob($this->tempDir . '/gemini_processed_*.json');
        $threshold = time() - ($olderThanHours * 3600);
        
        foreach ($files as $file) {
            if (filemtime($file) < $threshold) {
                unlink($file);
            }
        }
    }
    
}