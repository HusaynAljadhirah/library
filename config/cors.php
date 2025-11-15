<?php

return [

    // Apply CORS to API routes and the CSRF cookie endpoint for Sanctum
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // Allow standard methods
    'allowed_methods' => ['*'],

    // Explicitly allow your SPA origins (no wildcard when using credentials)
    'allowed_origins' => [
        'http://localhost:5173',
        'http://127.0.0.1:5173',
    ],

    'allowed_origins_patterns' => [],

    // Allow all headers from the SPA
    'allowed_headers' => ['*'],

    // Expose no special headers by default
    'exposed_headers' => [],

    // Cache preflight responses (0 disables caching)
    'max_age' => 0,

    // Enable credentials so cookies are sent (required for Sanctum SPA auth)
    'supports_credentials' => true,
];
