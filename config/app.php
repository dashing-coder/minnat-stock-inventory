<?php

return [
    'name' => env('APP_NAME', 'M/S Afrin Traders'),
    'env' => env('APP_ENV', 'production'),
    // Never expose exception details when APP_ENV=production, even if .env was misconfigured.
    'debug' => strtolower((string) env('APP_ENV', 'production')) !== 'production' && (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => array_filter(explode(',', env('APP_PREVIOUS_KEYS', ''))),
];
