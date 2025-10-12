<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class HybridPdfProcessorService
{
    protected string $pythonScriptPath;
    protected string $tempDir;

    public function __construct()
    {
        $this->pythonScriptPath = base_path('app/Services/hybrid_pdf_processor.py');
        $this->tempDir = storage_path('app/temp/hybrid_processing');
        
        if (!file_exists($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }
    }

    /**
     * Process PDF with Hybrid AI (Gemini + OpenAI)
     * - Image Captioning: Gemini
     * - Content Sanitization: OpenAI
     * - Math Conversion: OpenAI
     * 
     * @param string $pdfPath Full path to PDF file
     * @param array $options Processing options
     * @return array|null Processed JSON data
     */
    public function processPdfWithHybridAI(string $pdfPath, array $options = []): ?array
    {
        try {
            if (!file_exists($this->pythonScriptPath)) {
                Log::error('Hybrid Python script not found: ' . $this->pythonScriptPath);
                return null;
            }

            if (!file_exists($pdfPath) || !is_readable($pdfPath)) {
                Log::error('PDF file not found or not readable: ' . $pdfPath);
                return null;
            }

            // Generate output filename
            $fileHash = md5_file($pdfPath);
            $outputFile = $this->tempDir . '/hybrid_' . $fileHash . '.json';
            
            // Check cache (1 hour)
            if (file_exists($outputFile) && (time() - filemtime($outputFile)) < 3600) {
                Log::info('Using cached Hybrid AI JSON for: ' . basename($pdfPath));
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
            
            // Metadata
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
            
            // Image processing with Gemini (default enabled)
            if (!isset($options['caption_images']) || $options['caption_images']) {
                $params[] = '--caption-images';
            } else {
                $params[] = '--no-caption-images';
            }

            if (!isset($options['ocr_images']) || $options['ocr_images']) {
                $params[] = '--ocr-images';
            } else {
                $params[] = '--no-ocr-images';
            }

            // Content sanitization with OpenAI (default enabled)
            if (!isset($options['sanitize_content']) || $options['sanitize_content']) {
                $params[] = '--sanitize-content';
            } else {
                $params[] = '--no-sanitize-content';
            }

            // Mathematical conversion with OpenAI (default enabled)
            if (!isset($options['convert_math']) || $options['convert_math']) {
                $params[] = '--convert-math';
            } else {
                $params[] = '--no-convert-math';
            }

            // Set API keys as environment variables
            $geminiApiKey = $options['gemini_api_key']
                ?? config('services.gemini.api_key')
                ?? env('GEMINI_API_KEY');
            
            $openaiApiKey = $options['openai_api_key']
                ?? config('services.openai.api_key')
                ?? env('OPENAI_API_KEY');
            
            if (!empty($geminiApiKey)) {
                putenv("GEMINI_API_KEY={$geminiApiKey}");
            }
            
            if (!empty($openaiApiKey)) {
                putenv("OPENAI_API_KEY={$openaiApiKey}");
            }
            
            $paramString = implode(' ', $params);
            $cmd = trim("{$pythonCmd} {$pythonScript} {$pdfFile} -o {$outputFileEscaped} {$paramString}");
            
            Log::info('Executing Hybrid AI PDF processing: ' . $cmd);
            
            $output = [];
            $returnCode = 0;
            exec($cmd . ' 2>&1', $output, $returnCode);
            
            if ($returnCode !== 0) {
                Log::error('Hybrid AI PDF processing failed', [
                    'command' => $cmd,
                    'return_code' => $returnCode,
                    'output' => implode("\n", $output)
                ]);
                return null;
            }
            
            if (!file_exists($outputFile)) {
                Log::error('Hybrid AI output file not created: ' . $outputFile);
                return null;
            }

            $jsonContent = file_get_contents($outputFile);
            $data = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid JSON generated by Hybrid AI: ' . json_last_error_msg());
                return null;
            }

            if (!isset($data['pages']) || !is_array($data['pages']) || empty($data['pages'])) {
                Log::warning('Hybrid AI processing resulted in empty or invalid pages data');
                return null;
            }

            // Post-process to ensure quality
            if ($data && isset($data['pages'])) {
                $data = $this->postProcessJsonData($data);
            }
            
            Log::info('Hybrid AI PDF processing successful', [
                'method' => $data['processing_method'] ?? 'unknown',
                'ai_services' => $data['ai_services'] ?? [],
                'pages_count' => count($data['pages'] ?? []),
                'images_processed' => $data['images_processed'] ?? 0,
                'total_lines' => array_sum(array_map(function($page) {
                    return count($page['lines'] ?? []);
                }, $data['pages'] ?? []))
            ]);

            return $data;

        } catch (\Exception $e) {
            Log::error('Hybrid AI PDF processing error', [
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
                
                // Skip if empty
                if (empty($text)) {
                    continue;
                }
                
                // Skip if duplicate (exact match)
                if (isset($seen[$text])) {
                    continue;
                }
                
                $seen[$text] = true;
                $cleanLines[] = $line;
            }
            
            $page['lines'] = $cleanLines;
            
            // Re-number lines
            foreach ($page['lines'] as $index => &$line) {
                $line['line'] = $index + 1;
            }
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
        $files = glob($this->tempDir . '/hybrid_*.json');
        $threshold = time() - ($olderThanHours * 3600);
        
        foreach ($files as $file) {
            if (filemtime($file) < $threshold) {
                @unlink($file);
            }
        }
    }
    
    /**
     * Test connection to both AI services
     */
    public function testConnection(): array
    {
        $results = [
            'gemini' => false,
            'openai' => false
        ];
        
        try {
            // Test Gemini
            $geminiKey = config('services.gemini.api_key') ?? env('GEMINI_API_KEY');
            if (!empty($geminiKey)) {
                putenv("GEMINI_API_KEY={$geminiKey}");
                
                $pythonCmd = $this->resolvePythonCommand();
                $testScript = "import os; from google import genai; client = genai.Client(api_key=os.getenv('GEMINI_API_KEY')); print('GEMINI_OK')";
                
                $cmd = "{$pythonCmd} -c " . escapeshellarg($testScript);
                
                $output = [];
                $returnCode = 0;
                exec($cmd . ' 2>&1', $output, $returnCode);
                
                if ($returnCode === 0 && in_array('GEMINI_OK', $output)) {
                    $results['gemini'] = true;
                    Log::info('Gemini connection test successful');
                }
            }
            
            // Test OpenAI
            $openaiKey = config('services.openai.api_key') ?? env('OPENAI_API_KEY');
            if (!empty($openaiKey)) {
                putenv("OPENAI_API_KEY={$openaiKey}");
                
                $pythonCmd = $this->resolvePythonCommand();
                $testScript = "import os; from openai import OpenAI; client = OpenAI(api_key=os.getenv('OPENAI_API_KEY')); print('OPENAI_OK')";
                
                $cmd = "{$pythonCmd} -c " . escapeshellarg($testScript);
                
                $output = [];
                $returnCode = 0;
                exec($cmd . ' 2>&1', $output, $returnCode);
                
                if ($returnCode === 0 && in_array('OPENAI_OK', $output)) {
                    $results['openai'] = true;
                    Log::info('OpenAI connection test successful');
                }
            }
            
            Log::info('Connection test results', $results);
            
        } catch (\Exception $e) {
            Log::error('Connection test error: ' . $e->getMessage());
        }
        
        return $results;
    }
}