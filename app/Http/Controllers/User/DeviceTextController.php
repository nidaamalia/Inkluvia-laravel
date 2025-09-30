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
        $validated = $request->validate([
            'text' => 'nullable|string|max:255',
            'chunk_text' => 'nullable|string|max:20',
            'decimal_values' => 'sometimes|array',
            'decimal_values.*' => 'string',
            'device_ids' => 'sometimes|array',
            'device_ids.*' => 'exists:devices,id',
            'device_serials' => 'sometimes|array',
            'device_serials.*' => 'string'
        ]);

        // Debug: Log the incoming request
        \Log::info('SendText Request:', [
            'text' => $request->text,
            'chunk_text' => $request->chunk_text,
            'decimal_values' => $request->decimal_values ?? [],
            'device_ids' => $request->device_ids ?? 'not provided',
            'device_serials' => $request->device_serials ?? 'not provided'
        ]);

        $chunkText = $request->input('chunk_text', $request->input('text', ''));
        $decimalValues = $request->input('decimal_values', []);

        if (!is_array($decimalValues)) {
            $decimalValues = [];
        }

        $decimalValues = array_values(array_filter($decimalValues, function ($value) {
            return $value !== null && $value !== '';
        }));

        if (empty($decimalValues)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data desimal yang dapat dikirim.',
            ], 422);
        }

        $decimalPayload = implode('', $decimalValues);

        // Get devices with more detailed logging
        if (!empty($validated['device_ids'])) {
            $devices = Device::whereIn('id', $validated['device_ids'])->get();
            \Log::info('Using specific devices (by ID):', $devices->toArray());
        } elseif (!empty($validated['device_serials'])) {
            $devices = Device::whereIn('serial_number', $validated['device_serials'])->get();
            \Log::info('Using specific devices (by serial):', $devices->toArray());
        } else {
            $devices = Device::where('status', 'aktif')->get();
            \Log::info('Using all active devices:', $devices->toArray());
        }

        if ($devices->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada perangkat yang cocok dengan pilihan Anda.'
            ], 404);
        }

        $results = [];
        $logs = [
            'request' => $request->all(),
            'device_count' => $devices->count(),
            'devices' => $devices->toArray()
        ];
        $successCount = 0;

        foreach ($devices as $device) {
            $topic = 'edubraille/' . $device->id . '/control';
            $payload = [
                'command' => 'display_text',
                'device_id' => $device->id,
                'device_serial' => $device->serial_number,
                'text' => $chunkText,
                'chunk_text' => $chunkText,
                'decimal_values' => $decimalValues,
                'decimal_string' => $decimalPayload,
                'timestamp' => now()->toISOString()
            ];

            $logMessage = sprintf(
                'MQTT Publish Attempt - Topic: %s, Payload: %s',
                $topic,
                json_encode($payload)
            );
            $logs[] = $logMessage;
            
            $success = $this->mqttService->publish($topic, json_encode($payload));
            
            $resultMessage = $success 
                ? sprintf('MQTT Publish Success - Topic: %s, Device ID: %d', $topic, $device->id)
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
