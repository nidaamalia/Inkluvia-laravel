<?php
// app/Helpers/MqttHelper.php

namespace App\Helpers;

use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;
use Exception;

class MqttHelper
{
    private $client;
    private $brokerUrl;
    private $port;
    private $clientId;
    private $username;
    private $password;

    public function __construct()
    {
        $this->brokerUrl = config('mqtt.broker_url', 'broker.hivemq.com');
        $this->port = config('mqtt.port', 8883);
        $this->username = config('mqtt.username', '');
        $this->password = config('mqtt.password', '');
        $this->clientId = 'inkluvia-' . uniqid();
    }

    /**
     * Send Braille pattern to multiple devices
     *
     * @param array $deviceIds Array of device IDs
     * @param int $decimalValue The decimal value of Braille pattern
     * @param string $character The character being sent (for logging)
     * @return array Results of the operation
     */
    public function sendBrailleToDevices(array $deviceIds, int $decimalValue, string $character): array
    {
        $results = [];
        $formattedValue = str_pad($decimalValue, 2, '0', STR_PAD_LEFT);
        
        foreach ($deviceIds as $deviceId) {
            $topic = "edubraille/{$deviceId}/control";
            $results[$deviceId] = $this->publish($topic, $formattedValue, $character);
        }

        return $results;
    }

    /**
     * Publish a message to MQTT broker
     */
    private function publish(string $topic, string $message, string $character): array
    {
        try {
            $this->connect();

            $this->client->publish($topic, $message, 1, false);
            
            return [
                'success' => true,
                'message' => "Terkirim: {$character}",
                'topic' => $topic,
                'payload' => $message
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal mengirim ke MQTT: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        } finally {
            $this->disconnect();
        }
    }

    /**
     * Connect to MQTT broker
     */
    private function connect(): void
    {
        $this->client = new MqttClient(
            $this->brokerUrl,
            $this->port,
            $this->clientId,
            MqttClient::MQTT_3_1,
            null
        );

        $connectionSettings = (new ConnectionSettings())
            ->setUsername($this->username)
            ->setPassword($this->password)
            ->setUseTls(true);

        $this->client->connect($connectionSettings, true);
    }

    /**
     * Disconnect from MQTT broker
     */
    private function disconnect(): void
    {
        if ($this->client && $this->client->isConnected()) {
            $this->client->disconnect();
        }
    }

    /**
     * Get the MQTT client instance
     */
    public function getClient(): ?MqttClient
    {
        return $this->client ?? null;
    }
}