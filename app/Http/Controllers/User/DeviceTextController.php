<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Device;
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
            'text' => 'required|string|max:255',
            'device_ids' => 'sometimes|array',
            'device_ids.*' => 'exists:devices,id'
        ]);

        // Debug: Log the incoming request
        \Log::info('SendText Request:', [
            'text' => $request->text,
            'device_ids' => $request->device_ids ?? 'not provided'
        ]);

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
                'text' => $request->text,
                'timestamp' => now()->toISOString(),
                'device_id' => $device->id
            ];

            // Log the attempt
            $logMessage = 'MQTT Publish Attempt - Topic: abatago/00/control, Message: 77, QoS: 0';
            $logs[] = $logMessage;
            
            // Publish and get result
            $success = $this->mqttService->publish('abatago/00/control', '0010');
            
            // Log the result
            $resultMessage = $success 
                ? 'MQTT Publish Success - Topic: abatago/00/control, Message Length: 2'
                : 'MQTT Publish Failed - Topic: abatago/00/control';
            $logs[] = $resultMessage;
            
            $results[] = [
                'device_id' => $device->id,
                'device_name' => $device->nama_device,
                'serial_number' => $device->serial_number,
                'success' => $success,
                'topic' => 'abatago/00/control'
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
