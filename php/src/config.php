<?php

return [
    'db' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'name' => getenv('DB_NAME') ?: 'autolux_vendas',
        'user' => getenv('DB_USER') ?: 'autolux',
        'pass' => getenv('DB_PASS') ?: 'autolux123',
    ],
    'node_api_url' => rtrim(getenv('NODE_API_URL') ?: 'http://localhost:3000', '/'),
];
