<?php

namespace App\Services;

use App\Models\BraillePattern;
use App\Models\Device;
use App\Models\Material;

class MaterialSessionService
{
    protected MaterialContentService $materialContentService;

    public function __construct(MaterialContentService $materialContentService)
    {
        $this->materialContentService = $materialContentService;
    }

    public function resolveCharacterCapacity(array $deviceIds): int
    {
        if (empty($deviceIds)) {
            return 5;
        }

        $minCapacity = Device::whereIn('id', $deviceIds)->min('character_capacity');

        return $minCapacity && $minCapacity > 0 ? (int) $minCapacity : 5;
    }

    public function composeState(
        Material $material,
        int $pageParam,
        $lineParam,
        $chunkParam,
        int $characterCapacity
    ): array {
        $pageNumber = max(1, $pageParam);
        $originalPages = $this->materialContentService->getOriginalPages($material);
        $braillePages = $this->materialContentService->getBraillePages($material);

        if (empty($originalPages)) {
            return $this->emptyState($pageNumber, $characterCapacity);
        }

        if (!array_key_exists($pageNumber, $originalPages)) {
            $pageNumber = (int) array_key_first($originalPages);
        }

        $originalLines = $originalPages[$pageNumber] ?? [];
        $totalPages = count($originalPages);
        $totalLines = count($originalLines);

        $currentLineIndex = 0;
        if ($lineParam === 'last' && $totalLines > 0) {
            $currentLineIndex = $totalLines - 1;
        } elseif (is_numeric($lineParam)) {
            $requestedIndex = max(0, ((int) $lineParam) - 1);
            $currentLineIndex = min($requestedIndex, max($totalLines - 1, 0));
        }

        $currentLineText = $originalLines[$currentLineIndex] ?? '';
        $lineChunks = $this->chunkText($currentLineText, $characterCapacity);
        $totalChunks = count($lineChunks);

        $currentChunkIndex = 0;
        if ($chunkParam === 'last' && $totalChunks > 0) {
            $currentChunkIndex = $totalChunks - 1;
        } elseif (is_numeric($chunkParam)) {
            $requestedChunk = max(0, ((int) $chunkParam) - 1);
            $currentChunkIndex = min($requestedChunk, max($totalChunks - 1, 0));
        }

        $currentChunkText = $lineChunks[$currentChunkIndex] ?? '';
        $currentChunkDecimalValues = $this->convertTextToDecimalValues($currentChunkText);
        $currentChunkDecimal = implode(' ', $currentChunkDecimalValues);

        if (!empty($braillePages[$pageNumber] ?? [])) {
            $brailleLine = $braillePages[$pageNumber][$currentLineIndex] ?? null;
            if ($brailleLine && !empty($brailleLine['decimal_values'])) {
                $chunkLength = strlen($currentChunkText);
                $offset = $currentChunkIndex * $characterCapacity;
                $brailleSlice = array_slice($brailleLine['decimal_values'], $offset, $chunkLength);
                $brailleSlice = array_map(
                    fn($value) => str_pad((string) $value, 2, '0', STR_PAD_LEFT),
                    $brailleSlice
                );

                if (!empty($brailleSlice)) {
                    $currentChunkDecimalValues = $brailleSlice;
                    $currentChunkDecimal = implode(' ', $brailleSlice);
                }
            }
        }

        $braillePatterns = [];
        $brailleBinaryPatterns = [];
        $brailleDecimalPatterns = [];

        if ($currentLineText !== '') {
            foreach (str_split($currentLineText) as $char) {
                if (!array_key_exists($char, $braillePatterns)) {
                    if ($char === ' ') {
                        $braillePatterns[$char] = '⠀';
                        $brailleBinaryPatterns[$char] = '000000';
                        $brailleDecimalPatterns[$char] = 0;
                    } else {
                        $pattern = BraillePattern::getByCharacter($char);
                        $braillePatterns[$char] = $pattern ? $pattern->braille_unicode : '⠀';
                        $brailleBinaryPatterns[$char] = $pattern ? $pattern->dots_binary : '000000';
                        $brailleDecimalPatterns[$char] = $pattern ? $pattern->dots_decimal : 0;
                    }
                }
            }
        }

        $hasNextChunk = $currentChunkIndex < max($totalChunks - 1, 0);
        $hasNextLine = $currentLineIndex < max($totalLines - 1, 0);
        $hasPrevious = $currentChunkIndex > 0 || $currentLineIndex > 0;

        return [
            'pageNumber' => $pageNumber,
            'totalPages' => max(1, $totalPages),
            'originalLines' => $originalLines,
            'totalLines' => $totalLines,
            'currentLineIndex' => $currentLineIndex,
            'currentLineText' => $currentLineText,
            'characterCapacity' => $characterCapacity,
            'lineChunks' => $lineChunks,
            'totalChunks' => $totalChunks,
            'currentChunkIndex' => $currentChunkIndex,
            'currentChunkText' => $currentChunkText,
            'currentChunkDecimalValues' => $currentChunkDecimalValues,
            'currentChunkDecimal' => $currentChunkDecimal,
            'braillePatterns' => $braillePatterns,
            'brailleBinaryPatterns' => $brailleBinaryPatterns,
            'brailleDecimalPatterns' => $brailleDecimalPatterns,
            'hasNextChunk' => $hasNextChunk,
            'hasNextLine' => $hasNextLine,
            'hasPrevious' => $hasPrevious,
        ];
    }

    public function getInitialState(Material $material, int $characterCapacity): array
    {
        return $this->composeState($material, 1, 1, 1, $characterCapacity);
    }

    public function convertTextToDecimalValues(string $text): array
    {
        if ($text === '') {
            return [];
        }

        $values = [];

        foreach (str_split($text) as $char) {
            if ($char === ' ') {
                $values[] = '00';
                continue;
            }

            $pattern = BraillePattern::getByCharacter($char);
            $decimalValue = $pattern ? $pattern->dots_decimal : 0;
            $values[] = str_pad((string) $decimalValue, 2, '0', STR_PAD_LEFT);
        }

        return $values;
    }

    protected function emptyState(int $pageNumber, int $characterCapacity): array
    {
        return [
            'pageNumber' => $pageNumber,
            'totalPages' => 1,
            'originalLines' => [],
            'totalLines' => 0,
            'currentLineIndex' => 0,
            'currentLineText' => '',
            'characterCapacity' => $characterCapacity,
            'lineChunks' => [],
            'totalChunks' => 0,
            'currentChunkIndex' => 0,
            'currentChunkText' => '',
            'currentChunkDecimalValues' => [],
            'currentChunkDecimal' => '',
            'braillePatterns' => [],
            'brailleBinaryPatterns' => [],
            'brailleDecimalPatterns' => [],
            'hasNextChunk' => false,
            'hasNextLine' => false,
            'hasPrevious' => false,
        ];
    }

    protected function chunkText(string $text, int $characterCapacity): array
    {
        $safeCapacity = max(1, $characterCapacity);

        if ($text === '') {
            return [];
        }

        return str_split($text, $safeCapacity);
    }
}
