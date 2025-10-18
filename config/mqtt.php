<?php

return [
    'server' => env('MOSQUITTO_HOST', env('MQTT_SERVER', 'mosquitto-railway-template-production-22d4.up.railway.app')),
    'port' => (int) env('MOSQUITTO_PORT', env('MQTT_PORT', 1883)),
    'username' => env('MOSQUITTO_USERNAME', env('MQTT_USERNAME', '')),
    'password' => env('MOSQUITTO_PASSWORD', env('MQTT_PASSWORD', '')),
    'topic' => env('MQTT_TOPIC', 'abatago/0000'),
    'client_prefix' => env('MQTT_CLIENT_PREFIX', 'phpClient_'),
    'ws_url' => env('MQTT_WS_URL', 'wss://caddy-production-8ef8.up.railway.app/mqtt'),
    'timeout' => (int) env('MQTT_TIMEOUT', 60),
    'keep_alive' => (int) env('MQTT_KEEP_ALIVE', 60),
    'qos' => (int) env('MQTT_QOS', 0),

    // Connection settings
    'keep_alive' => 60,
    'clean_session' => true,
    'timeout' => 10,
    
    // Topics untuk berbagai fungsi
    'topics' => [
        'device_status' => 'inkluvia/device/status',
        'device_command' => 'inkluvia/device/command',
        'material_send' => 'inkluvia/material/send',
        'device_heartbeat' => 'inkluvia/device/heartbeat',
        'device_button' => env('MQTT_DEVICE_BUTTON_TOPIC', 'abatago'),
    ]
];