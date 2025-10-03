<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class DeviceButtonChannel
{
    public const BUTTON_PREV_PAGE = '1';
    public const BUTTON_NEXT_PAGE = '4';
    public const BUTTON_PREV_LINE = '2';
    public const BUTTON_NEXT_LINE = '5';
    public const BUTTON_PREV_CHUNK = '3';
    public const BUTTON_NEXT_CHUNK = '6';

    /**
     * Resolve MQTT topic to subscribe for button events.
     */
    public static function topicForSerial(string $serialNumber): string
    {
        $baseTopic = config('mqtt.topics.device_button', 'abatago');
        $baseTopic = rtrim($baseTopic, '/');

        // Device button topic structure: {baseTopic}/{serial}/button
        // return $baseTopic . '/' . trim($serialNumber) . '/button';
        return 'abatago/00/button';
    }

    /**
     * Map payload to navigation action identifiers used by the front-end.
     */
    public static function mapPayloadToAction(?string $payload): ?string
    {
        if ($payload === null || $payload === '') {
            return null;
        }

        $normalized = trim($payload);

        return match ($normalized) {
            self::BUTTON_PREV_PAGE => 'page-prev',
            self::BUTTON_NEXT_PAGE => 'page-next',
            self::BUTTON_PREV_LINE => 'line-prev',
            self::BUTTON_NEXT_LINE => 'line-next',
            self::BUTTON_PREV_CHUNK => 'chunk-prev',
            self::BUTTON_NEXT_CHUNK => 'chunk-next',
            default => null,
        };
    }

    /**
     * Log an ignored payload for debug purposes
     */
    public static function logIgnoredPayload(string $payload): void
    {
        Log::debug('Device button payload ignored', ['payload' => $payload]);
    }
}
