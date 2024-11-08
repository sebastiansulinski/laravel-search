<?php

return [
    'typesense' => [
        'api_key' => env('TYPESENSE_API_KEY', 'xyz'),
        'nodes' => [
            [
                'host' => env('TYPESENSE_HOST', 'localhost'),
                'port' => env('TYPESENSE_PORT', '8108'),
                'path' => env('TYPESENSE_PATH', ''),
                'protocol' => env('TYPESENSE_PROTOCOL', 'http'),
            ],
        ],
        'nearest_node' => [
            'host' => env('TYPESENSE_HOST', 'localhost'),
            'port' => env('TYPESENSE_PORT', '8108'),
            'path' => env('TYPESENSE_PATH', ''),
            'protocol' => env('TYPESENSE_PROTOCOL', 'http'),
        ],
        'connection_timeout_seconds' => env('TYPESENSE_CONNECTION_TIMEOUT_SECONDS', 2),
        'healthcheck_interval_seconds' => env('TYPESENSE_HEALTHCHECK_INTERVAL_SECONDS', 30),
        'num_retries' => env('TYPESENSE_NUM_RETRIES', 3),
        'retry_interval_seconds' => env('TYPESENSE_RETRY_INTERVAL_SECONDS', 1),
    ],
];
