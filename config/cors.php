<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:3501', 'http://127.0.0.1:3501'],
    'allowed_headers' => ['*'],
    'supports_credentials' => true,
];
