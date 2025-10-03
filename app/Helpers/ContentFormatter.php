<?php

namespace App\Helpers;

use App\Models\Device;

class ContentFormatter
{
    /**
     * Format content based on the smallest character capacity of selected devices
     *
     * @param string $content
     * @param array $deviceIds
     * @param int $defaultCapacity
     * @return array
     */
    public static function formatForDevices(string $content, array $deviceIds, int $defaultCapacity = 20): array
    {
        // Get the smallest character capacity from selected devices
        $capacity = self::getMinCharacterCapacity($deviceIds, $defaultCapacity);
        
        // Split content into fixed-size chunks based on device capacity
        $lines = [];
        $words = preg_split('/\s+/', trim($content));
        $currentLine = '';
        
        foreach ($words as $word) {
            // If adding the next word would exceed capacity, start a new line
            if (strlen($currentLine . ' ' . $word) > $capacity && $currentLine !== '') {
                $lines[] = $currentLine;
                $currentLine = '';
            }
            
            // Add word to current line
            if ($currentLine === '') {
                $currentLine = $word;
            } else {
                $currentLine .= ' ' . $word;
            }
            
            // If a single word is longer than capacity, split it
            while (strlen($currentLine) > $capacity) {
                $lines[] = substr($currentLine, 0, $capacity);
                $currentLine = substr($currentLine, $capacity);
            }
        }
        
        // Add the last line if not empty
        if ($currentLine !== '') {
            $lines[] = $currentLine;
        }
        
        return $lines;
    }
    
    /**
     * Get the minimum character capacity from selected devices
     *
     * @param array $deviceIds
     * @param int $defaultCapacity
     * @return int
     */
    protected static function getMinCharacterCapacity(array $deviceIds, int $defaultCapacity): int
    {
        if (empty($deviceIds)) {
            return $defaultCapacity;
        }
        
        $minCapacity = Device::whereIn('id', $deviceIds)
            ->min('character_capacity');
            
        return $minCapacity ?: $defaultCapacity;
    }
    
    /**
     * Split content into fixed-size chunks based on character capacity
     * This is a simpler version that just splits the text into chunks of fixed size
     * without trying to preserve words
     *
     * @param string $content
     * @param int $chunkSize
     * @return array
     */
    protected static function splitIntoChunks(string $content, int $chunkSize): array
    {
        if ($chunkSize <= 0) {
            $chunkSize = 20; // Fallback to default if invalid
        }
        
        $content = trim($content);
        $length = mb_strlen($content);
        $chunks = [];
        
        for ($i = 0; $i < $length; $i += $chunkSize) {
            $chunks[] = mb_substr($content, $i, $chunkSize);
        }
        
        return $chunks;
    }
    
    /**
     * Get paginated content based on page number and lines per page
     *
     * @param array $allLines
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public static function getPaginatedContent(array $allLines, int $page, int $perPage = 5): array
    {
        $totalLines = count($allLines);
        $totalPages = ceil($totalLines / $perPage);
        $page = max(1, min($page, $totalPages)); // Ensure page is within bounds
        
        $start = ($page - 1) * $perPage;
        $paginatedLines = array_slice($allLines, $start, $perPage);
        
        return [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'lines' => $paginatedLines,
            'has_next' => $page < $totalPages,
            'has_previous' => $page > 1,
        ];
    }
}
