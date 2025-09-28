<?php

namespace App\Services;

use Bluerhinos\phpMQTT;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class MqttService
{
    private $mqtt;
    private $server;
    private $port;
    private $username;
    private $password;
    private $clientId;

    public function __construct()
    {
        $this->server = config('mqtt.server');
        $this->port = config('mqtt.port');
        $this->username = config('mqtt.username');
        $this->password = config('mqtt.password');
        $this->clientId = config('mqtt.client_prefix') . uniqid();
    }

    /**
     * Connect to MQTT broker
     */
    public function connect(): bool
    {
        // Check if MQTT is enabled
        if (!config('mqtt.enabled', true)) {
            $msg = 'MQTT is disabled in configuration';
            Log::warning($msg);
            echo '<script>console.warn("' . $msg . '");</script>';
            return false;
        }

        // If already connected, return true
        if ($this->mqtt && $this->mqtt->connect) {
            return true;
        }

        try {
            $this->mqtt = new phpMQTT(
                $this->server,
                $this->port,
                $this->clientId . '_' . uniqid()
            );

            if (!$this->mqtt) {
                throw new \Exception('Failed to initialize MQTT client');
            }
            
            $connected = $this->mqtt->connect(
                config('mqtt.clean_session', true),
                null,
                $this->username,
                $this->password
            );

            if ($connected) {
                $msg = 'MQTT connected successfully to ' . $this->server . ':' . $this->port;
                Log::info($msg);
                echo '<script>console.log("' . $msg . '");</script>';
                return true;
            }

            $error = 'Failed to connect to MQTT broker at ' . $this->server . ':' . $this->port;
            Log::error($error);
            echo '<script>console.error("' . $error . '");</script>';
            return false;

        } catch (\Exception $e) {
            $error = 'MQTT connection error: ' . $e->getMessage();
            Log::error($error);
            echo '<script>console.error("' . addslashes($error) . '");</script>';
            return false;
        }
    }

    /**
     * Publish message to topic
     */
    public function publish(string $topic, $message, int $qos = 0): bool
    {
        try {
            // Ensure we have a connection
            if (!$this->mqtt) {
                $connected = $this->connect();
                if (!$connected) {
                    $error = 'Failed to connect to MQTT broker';
                    Log::error($error);
                    echo '<script>console.error("' . $error . '");</script>';
                    return false;
                }
            }

            // Convert message to string if it's an array
            $messageString = is_array($message) ? json_encode($message) : (string)$message;
            
            // Log the attempt
            $logMessage = 'Publishing to ' . $topic . ': ' . $messageString;
            Log::info($logMessage);
            echo '<script>console.log("' . addslashes($logMessage) . '");</script>';
            try {
                // Publish the message
                $result = $this->mqtt->publish($topic, $messageString, $qos, 0);
                
                if ($result) {
                    $successMsg = 'Successfully published to ' . $topic;
                    Log::info($successMsg);
                    echo '<script>console.log("' . addslashes($successMsg) . '");</script>';
                    return true;
                } else {
                    $errorMsg = 'Failed to publish to ' . $topic . '. Error: ' . json_encode(error_get_last());
                    Log::error($errorMsg);
                    echo '<script>console.error("' . addslashes($errorMsg) . '");</script>';
                    return false;
                }
            } catch (\Exception $e) {
                echo '<script>console.error("MQTT Publish Exception - Topic: ' . $topic . ', Error: ' . $e->getMessage() . '");</script>';
                error_log('MQTT Publish Exception - Topic: ' . $topic . ', Error: ' . $e->getMessage() . ', Trace: ' . $e->getTraceAsString());
                
                if ($this->mqtt) {
                    $this->mqtt->close();
                }
                return false;
            }
        } catch (\Exception $e) {
            Log::error('MQTT Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Resolve topic from configuration and append serial number when provided
     */
    private function resolveTopic(string $configKey, ?string $serialNumber = null): string
    {
        $topics = config('mqtt.topics', []);
        $baseTopic = $topics[$configKey] ?? config('mqtt.topic', 'mqtt/default');

        $baseTopic = rtrim($baseTopic, '/');
        if ($serialNumber !== null) {
            $baseTopic .= '/' . trim($serialNumber);
        }

        return $baseTopic;
    }

    /**
     * Send command payload to a specific device
     */
    public function sendDeviceCommand(string $serialNumber, array $commandData): bool
    {
        $topic = $this->resolveTopic('device_command', $serialNumber);

        $payload = $commandData;
        $payload['serial_number'] = $serialNumber;
        $payload['timestamp'] = $payload['timestamp'] ?? now()->toISOString();

        return $this->publish($topic, $payload);
    }

    /**
     * Send material payload to a specific device
     */
    public function sendMaterial(string $serialNumber, array $materialData): bool
    {
        $topic = $this->resolveTopic('material_send', $serialNumber);

        $payload = $materialData;
        $payload['serial_number'] = $serialNumber;
        $payload['type'] = $payload['type'] ?? 'material_send';
        $payload['timestamp'] = $payload['timestamp'] ?? now()->toISOString();

        return $this->publish($topic, $payload);
    }

    /**
     * Send ping command to a device
     */
    public function pingDevice(string $serialNumber): bool
    {
        return $this->sendDeviceCommand($serialNumber, [
            'type' => 'ping'
        ]);
    }

    /**
     * Disconnect from MQTT broker
     */
    public function disconnect(): void
    {
        if ($this->mqtt) {
            Log::info('MQTT disconnected');
        }
    }

    /**
    /**
     * Get connection status
     */
    public function isConnected(): bool
    {
        return $this->mqtt !== null;
    }

    /**
     * Get MQTT configuration
     */
    public function getConfig(): array
    {
        return [
            'server' => $this->server,
            'port' => $this->port,
            'client_id' => $this->clientId,
            'ws_url' => config('mqtt.ws_url'),
            'topics' => config('mqtt.topics')
        ];
    }
}