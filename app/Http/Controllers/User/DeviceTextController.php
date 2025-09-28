<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\BraillePattern;
use App\Services\MqttService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeviceTextController extends Controller
{
    protected $mqttService;

    public function __construct(MqttService $mqttService)
    {
        $this->mqttService = $mqttService;
    }

    public function sendText(Request $request)
    {
        $request->validate([
            'text' => 'nullable|string|max:255',
            'chunk_text' => 'nullable|string|max:255',
            'decimal_values' => 'sometimes|array',
            'decimal_values.*' => 'string',
            'device_ids' => 'sometimes|array',
            'device_ids.*' => 'exists:devices,id'
        ]);

        // Debug: Log the incoming request
        \Log::info('SendText Request:', [
            'text' => $request->text,
            'chunk_text' => $request->chunk_text,
            'decimal_values' => $request->decimal_values ?? [],
            'device_ids' => $request->device_ids ?? 'not provided'
        ]);

        $chunkText = $request->input('chunk_text', $request->input('text', ''));
        $providedDecimals = $request->input('decimal_values', []);

        $decimalValues = [];

        if (!empty($providedDecimals)) {
            foreach ($providedDecimals as $value) {
                if ($value === null || $value === '') {
                    continue;
                }

                if ($value === '00') {
                    $decimalValues[] = '00';
                    continue;
                }

                $decimalValues[] = str_pad((string)$value, 2, '0', STR_PAD_LEFT);
            }
        }

        if (empty($decimalValues) && $chunkText !== '') {
            $characters = str_split($chunkText);
            foreach ($characters as $char) {
                if ($char === ' ') {
                    $decimalValues[] = '00';
                    continue;
                }

                $pattern = BraillePattern::getByCharacter($char);
                $decimal = $pattern ? $pattern->dots_decimal : 0;
                $decimalValues[] = str_pad((string)$decimal, 2, '0', STR_PAD_LEFT);
            }
        }

        if (empty($decimalValues)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data desimal yang dapat dikirim.',
            ], 422);
        }

        $decimalPayload = implode(' ', $decimalValues);

        // Get devices with more detailed logging
        if ($request->has('device_ids')) {
            $devices = Device::whereIn('id', $request->device_ids)->get();
            \Log::info('Using specific devices:', $devices->toArray());
        } else {
            $devices = Device::where('status', 'aktif')->get();
            \Log::info('Using all active devices:', $devices->toArray());
        }

        $results = [];
        $topicPrefix = 'edubraille/';
        $logs = [
            'request' => $request->all(),
            'device_count' => $devices->count(),
            'devices' => $devices->toArray()
        ];
        $successCount = 0;

        // Start output buffering to capture any direct echoes
        ob_start();
        
        foreach ($devices as $device) {
            $topic = $topicPrefix . $device->id . '/control';
            $payload = [
                'command' => 'display_text',
                'text' => $chunkText,
                'chunk_text' => $chunkText,
                'decimal_values' => $decimalValues,
                'decimal_string' => $decimalPayload,
                'timestamp' => now()->toISOString(),
                'device_id' => $device->id
            ];

            // Log the attempt
            $logMessage = sprintf(
                'MQTT Publish Attempt - Topic: %s, Message: %s, QoS: 0',
                $topic,
                $decimalPayload
            );
            $logs[] = $logMessage;
            
            // Publish and get result
            $success = $this->mqttService->publish($topic, $decimalPayload);
            
            // Log the result
            $resultMessage = $success 
                ? sprintf('MQTT Publish Success - Topic: %s, Message Length: %d', $topic, strlen($decimalPayload))
                : sprintf('MQTT Publish Failed - Topic: %s', $topic);
            $logs[] = $resultMessage;
            
            $results[] = [
                'device_id' => $device->id,
                'device_name' => $device->nama_device,
                'serial_number' => $device->serial_number,
                'success' => $success,
                'topic' => $topic,
                'decimal_values' => $decimalValues
            ];

            if ($success) {
                $successCount++;
            }
        }
        
        // Get any output that was echoed
        $output = ob_get_clean();
        if (!empty($output)) {
            $logs = array_merge($logs, explode("\n", trim($output)));
        }

        $allSuccess = $successCount === count($devices);
        $message = $allSuccess 
            ? 'Teks berhasil dikirim ke ' . $successCount . ' perangkat'
            : 'Teks berhasil dikirim ke ' . $successCount . ' dari ' . count($devices) . ' perangkat';

        $response = [
            'success' => $allSuccess,
            'message' => $message,
            'results' => $results,
            'logs' => $logs,
            'debug' => [
                'devices_found' => $devices->count(),
                'devices_processed' => count($results),
                'successful_sends' => $successCount
            ]
        ];

        // Log the full response for debugging
        \Log::info('SendText Response:', $response);

        return response()->json($response);
    }

    public function listDevices()
    {
        $devices = Device::where('status', 'aktif')
                        ->select('id', 'nama_device as name', 'serial_number')
                        ->get();
        
        \Log::info('List of active devices:', $devices->toArray());
        return response()->json($devices);
    }
}
