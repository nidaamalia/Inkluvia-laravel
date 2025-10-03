<?php

namespace App\Services;

use App\Models\Material;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MaterialContentService
{
    /**
     * Cached original pages keyed by material ID.
     *
     * @var array<int, array<int, array<int, string>>>
     */
    protected array $originalPagesCache = [];

    /**
     * Cached Braille pages keyed by material ID.
     *
     * @var array<int, array<int, array<int, array<string, mixed>>>>
     */
    protected array $braillePagesCache = [];

    /**
     * Get normalized original page lines for a material.
     */
    public function getOriginalPages(Material $material): array
    {
        $materialId = $material->id;

        if (!array_key_exists($materialId, $this->originalPagesCache)) {
            $this->originalPagesCache[$materialId] = $this->loadOriginalPages($material);
        }

        return $this->originalPagesCache[$materialId];
    }

    /**
     * Get original page lines for a specific page.
     */
    public function getOriginalPageLines(Material $material, int $pageNumber): array
    {
        $pages = $this->getOriginalPages($material);

        return $pages[$pageNumber] ?? [];
    }

    /**
     * Get total page count for original content.
     */
    public function getOriginalTotalPages(Material $material): int
    {
        $pages = $this->getOriginalPages($material);

        return max(1, count($pages));
    }

    /**
     * Get normalized Braille page data for a material.
     */
    public function getBraillePages(Material $material): array
    {
        $materialId = $material->id;

        if (!array_key_exists($materialId, $this->braillePagesCache)) {
            $this->braillePagesCache[$materialId] = $this->loadBraillePages($material);
        }

        return $this->braillePagesCache[$materialId];
    }

    /**
     * Get Braille lines for a specific page.
     */
    public function getBraillePageLines(Material $material, int $pageNumber): array
    {
        $pages = $this->getBraillePages($material);

        return $pages[$pageNumber] ?? [];
    }

    /**
     * Get Braille line data (text and decimals) for a specific line index.
     */
    public function getBrailleLineData(Material $material, int $pageNumber, int $lineIndex): array
    {
        $lines = $this->getBraillePageLines($material, $pageNumber);

        return $lines[$lineIndex] ?? [
            'text' => '',
            'decimal_values' => [],
            'raw_decimal' => ''
        ];
    }

    /**
     * Load and normalize original pages from JSON file.
     */
    protected function loadOriginalPages(Material $material): array
    {
        $filePath = $material->file_path;

        if (!$filePath) {
            return [];
        }

        try {
            if (!Storage::disk('private')->exists($filePath)) {
                Log::warning('Original material file not found', [
                    'material_id' => $material->id,
                    'file_path' => $filePath,
                ]);

                return [];
            }

            $content = Storage::disk('private')->get($filePath);
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Failed to decode original material JSON', [
                    'material_id' => $material->id,
                    'file_path' => $filePath,
                    'error' => json_last_error_msg(),
                ]);

                return [];
            }

            return $this->normalizeOriginalPages($data);
        } catch (\Throwable $e) {
            Log::error('Error loading original material content', [
                'material_id' => $material->id,
                'file_path' => $filePath,
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Load and normalize Braille pages from JSON file.
     */
    protected function loadBraillePages(Material $material): array
    {
        $filePath = $material->braille_data_path;

        if (!$filePath) {
            return [];
        }

        try {
            if (!Storage::disk('private')->exists($filePath)) {
                Log::warning('Braille material file not found', [
                    'material_id' => $material->id,
                    'file_path' => $filePath,
                ]);

                return [];
            }

            $content = Storage::disk('private')->get($filePath);
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Failed to decode Braille material JSON', [
                    'material_id' => $material->id,
                    'file_path' => $filePath,
                    'error' => json_last_error_msg(),
                ]);

                return [];
            }

            return $this->normalizeBraillePages($data);
        } catch (\Throwable $e) {
            Log::error('Error loading Braille material content', [
                'material_id' => $material->id,
                'file_path' => $filePath,
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Normalize original JSON structure into [pageNumber => lines[]].
     */
    protected function normalizeOriginalPages(mixed $data): array
    {
        $pages = [];

        if (is_array($data)) {
            if (isset($data['pages']) && is_array($data['pages'])) {
                foreach ($data['pages'] as $index => $pageData) {
                    $pageNumber = $this->resolvePageNumber($pageData, $index);
                    $pages[$pageNumber] = $this->extractOriginalLines($pageData);
                }
            } else {
                foreach ($data as $index => $pageData) {
                    if (!is_array($pageData)) {
                        continue;
                    }

                    $pageNumber = $this->resolvePageNumber($pageData, $index);
                    $pages[$pageNumber] = $this->extractOriginalLines($pageData);
                }
            }
        }

        ksort($pages);

        return $pages;
    }

    /**
     * Normalize Braille JSON structure into [pageNumber => [lines]].
     */
    protected function normalizeBraillePages(mixed $data): array
    {
        $pages = [];

        if (is_array($data)) {
            if (isset($data['pages']) && is_array($data['pages'])) {
                foreach ($data['pages'] as $index => $pageData) {
                    $pageNumber = $this->resolvePageNumber($pageData, $index);
                    $pages[$pageNumber] = $this->extractBrailleLines($pageData);
                }
            } else {
                foreach ($data as $index => $pageData) {
                    if (!is_array($pageData)) {
                        continue;
                    }

                    $pageNumber = $this->resolvePageNumber($pageData, $index);
                    $pages[$pageNumber] = $this->extractBrailleLines($pageData);
                }
            }
        }

        ksort($pages);

        return $pages;
    }

    /**
     * Resolve page number from JSON data.
     */
    protected function resolvePageNumber(array $pageData, int $index): int
    {
        return (int) ($pageData['page'] ?? $pageData['page_number'] ?? ($index + 1));
    }

    /**
     * Extract original lines from page data.
     */
    protected function extractOriginalLines(array $pageData): array
    {
        $lines = [];

        if (isset($pageData['lines']) && is_array($pageData['lines'])) {
            foreach ($pageData['lines'] as $line) {
                if (is_string($line)) {
                    $lines[] = $line;
                } elseif (is_array($line) && isset($line['text'])) {
                    $lines[] = (string) $line['text'];
                }
            }
        } elseif (isset($pageData['text'])) {
            $textValue = $pageData['text'];

            if (is_array($textValue)) {
                foreach ($textValue as $textLine) {
                    if ($textLine !== null && $textLine !== '') {
                        $lines[] = (string) $textLine;
                    }
                }
            } else {
                $splitted = preg_split("/(\r\n|\r|\n)/", (string) $textValue);
                if (is_array($splitted)) {
                    foreach ($splitted as $textLine) {
                        if ($textLine !== null && $textLine !== '') {
                            $lines[] = $textLine;
                        }
                    }
                }
            }
        }

        return $lines;
    }

    /**
     * Extract Braille lines (text + decimal values) from page data.
     */
    protected function extractBrailleLines(array $pageData): array
    {
        $lines = [];

        if (!isset($pageData['lines']) || !is_array($pageData['lines'])) {
            return $lines;
        }

        foreach ($pageData['lines'] as $line) {
            if (is_string($line)) {
                $lines[] = [
                    'text' => $line,
                    'decimal_values' => [],
                    'raw_decimal' => '',
                ];
                continue;
            }

            if (!is_array($line)) {
                continue;
            }

            $text = (string) ($line['text'] ?? '');
            $decimalValues = [];
            $rawDecimal = '';

            if (isset($line['decimal_values']) && is_array($line['decimal_values'])) {
                $decimalValues = array_values(array_map(fn($value) => str_pad((string) $value, 2, '0', STR_PAD_LEFT), $line['decimal_values']));
                $rawDecimal = implode('', $decimalValues);
            } elseif (isset($line['decimal'])) {
                $rawDecimal = (string) $line['decimal'];
                $decimalValues = $this->splitDecimalString($rawDecimal);
            }

            $lines[] = [
                'text' => $text,
                'decimal_values' => $decimalValues,
                'raw_decimal' => $rawDecimal,
            ];
        }

        return $lines;
    }

    /**
     * Split a decimal string (either concatenated or space separated) into values per character.
     */
    protected function splitDecimalString(string $decimal): array
    {
        $decimal = trim($decimal);

        if ($decimal === '') {
            return [];
        }

        if (str_contains($decimal, ' ')) {
            $parts = preg_split('/\s+/', $decimal, -1, PREG_SPLIT_NO_EMPTY);
            return array_map(fn($value) => str_pad($value, 2, '0', STR_PAD_LEFT), $parts ?: []);
        }

        $parts = [];
        $length = strlen($decimal);

        for ($i = 0; $i < $length; $i += 2) {
            $segment = substr($decimal, $i, 2);
            if ($segment === false || $segment === '') {
                continue;
            }

            $parts[] = str_pad($segment, 2, '0', STR_PAD_LEFT);
        }

        return $parts;
    }
}
