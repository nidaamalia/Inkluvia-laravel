<?php

namespace App\Services;

use App\Models\Material;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MaterialConversionService
{
    protected PdfToJsonService $pdfToJsonService;
    protected GeminiPdfProcessorService $geminiService;

    public function __construct()
    {
        $this->pdfToJsonService = new PdfToJsonService();
        $this->geminiService = new GeminiPdfProcessorService();
    }

    /**
     * Convert a material's PDF into JSON content and persist results.
     */
    public function convertMaterial(Material $material, array $options = [], array $config = []): array
    {
        $pdfPath = Storage::disk('private')->path($material->file_path);

        $options = $this->buildOptionsFromMaterial($material, $options);

        $jsonData = $this->runConversionPipeline($pdfPath, $options);

        if (!$jsonData) {
            throw new \RuntimeException('PDF conversion failed');
        }

        $jsonPath = 'materials/json/' . $material->id . '.json';
        Storage::disk('private')->put(
            $jsonPath,
            json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        $deleteOriginal = $config['delete_original'] ?? true;
        if ($deleteOriginal && Storage::disk('private')->exists($material->file_path)) {
            Storage::disk('private')->delete($material->file_path);
        }

        $targetStatus = $config['target_status'] ?? 'review';

        $updates = [
            'file_path' => $jsonPath,
            'total_halaman' => count($jsonData['pages'] ?? []),
            'status' => $targetStatus,
        ];

        if ($targetStatus === 'published') {
            $updates['published_at'] = now();
        }

        $material->update($updates);

        Log::info('PDF conversion completed for material', [
            'material_id' => $material->id,
            'status' => $targetStatus,
            'pages' => count($jsonData['pages'] ?? []),
        ]);

        return $jsonData;
    }

    /**
     * Generate JSON preview data for an uploaded PDF without persisting it.
     */
    public function previewUploadedFile(UploadedFile $file, array $options = []): array
    {
        $tempPath = $file->store('temp/material-previews', 'private');
        $absolutePdfPath = Storage::disk('private')->path($tempPath);

        try {
            $options = $this->buildPreviewOptions($options);
            $jsonData = $this->runConversionPipeline($absolutePdfPath, $options);

            if (!$jsonData) {
                throw new \RuntimeException('Failed to convert PDF to JSON');
            }

            return $jsonData;
        } finally {
            Storage::disk('private')->delete($tempPath);
        }
    }

    /**
     * Execute Gemini + fallback conversion for a given PDF path.
     */
    protected function runConversionPipeline(string $pdfPath, array $options): ?array
    {
        Log::info('Starting Gemini conversion pipeline', ['pdf_path' => $pdfPath]);

        $jsonData = $this->geminiService->processPdfWithGemini($pdfPath, $options);

        if (!$jsonData) {
            Log::warning('Gemini processing failed, using standard PDF conversion');
            $fallbackOptions = $options;
            $fallbackOptions['auto_caption'] = $fallbackOptions['auto_caption'] ?? true;
            $jsonData = $this->pdfToJsonService->convertPdfToJson($pdfPath, $fallbackOptions);
        }

        return $jsonData;
    }

    protected function buildOptionsFromMaterial(Material $material, array $overrides = []): array
    {
        return $this->mergeOptions([
            'judul' => $material->judul,
            'penerbit' => $material->penerbit,
            'tahun' => $material->tahun_terbit,
            'edisi' => $material->edisi,
            'caption_images' => true,
            'ocr_images' => true,
            'sanitize_content' => true,
            'convert_math' => true,
            'gemini_api_key' => config('services.gemini.api_key'),
        ], $overrides);
    }

    protected function buildPreviewOptions(array $overrides = []): array
    {
        return $this->mergeOptions([
            'caption_images' => true,
            'ocr_images' => true,
            'sanitize_content' => true,
            'convert_math' => true,
            'gemini_api_key' => config('services.gemini.api_key'),
            'auto_caption' => true,
        ], $overrides);
    }

    protected function mergeOptions(array $defaults, array $overrides): array
    {
        foreach ($overrides as $key => $value) {
            $defaults[$key] = $value;
        }

        return $defaults;
    }
}
