<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dintero Environment
    |--------------------------------------------------------------------------
    |
    | This value determines which environment to use for Dintero API calls.
    | Supported: "sandbox", "production"
    |
    */
    'environment' => env('DINTERO_ENVIRONMENT', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | API Credentials
    |--------------------------------------------------------------------------
    |
    | Your Dintero API credentials. You can use either API key or OAuth2
    | client credentials (client_id + client_secret).
    |
    */
    'api_key' => env('DINTERO_API_KEY'),
    'client_id' => env('DINTERO_CLIENT_ID'),
    'client_secret' => env('DINTERO_CLIENT_SECRET'),
    'account_id' => env('DINTERO_ACCOUNT_ID'),

    /*
    |--------------------------------------------------------------------------
    | API URLs
    |--------------------------------------------------------------------------
    |
    | The base URLs for Dintero API endpoints.
    |
    */
    'base_url' => env('DINTERO_BASE_URL', 'https://api.dintero.com/v1'),
    'sandbox_base_url' => env('DINTERO_SANDBOX_BASE_URL', 'https://api.sandbox.dintero.com/v1'),
    'checkout_base_url' => env('DINTERO_CHECKOUT_BASE_URL', 'https://checkout.dintero.com/v1'),
    'checkout_sandbox_base_url' => env('DINTERO_CHECKOUT_SANDBOX_BASE_URL', 'https://checkout.sandbox.dintero.com/v1'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for HTTP client behavior.
    |
    */
    'timeout' => env('DINTERO_TIMEOUT', 30),
    'retry_attempts' => env('DINTERO_RETRY_ATTEMPTS', 3),
    'retry_delay' => env('DINTERO_RETRY_DELAY', 1000), // milliseconds
    'user_agent' => 'Dintero PHP Wrapper/1.0 Laravel',

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Enable logging of API requests and responses for debugging.
    |
    */
    'log_requests' => env('DINTERO_LOG_REQUESTS', false),
    'log_responses' => env('DINTERO_LOG_RESPONSES', false),

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for webhook handling and verification.
    |
    */
    'verify_webhooks' => env('DINTERO_VERIFY_WEBHOOKS', true),
    'webhook_secret' => env('DINTERO_WEBHOOK_SECRET'),
    'webhook_middleware' => ['api'],
    'webhook_route_prefix' => 'dintero/webhooks',

    /*
    |--------------------------------------------------------------------------
    | Default Payment Configuration
    |--------------------------------------------------------------------------
    |
    | Default configuration for payment sessions.
    |
    */
    'default_currency' => env('DINTERO_DEFAULT_CURRENCY', 'NOK'),
    'default_profile_id' => env('DINTERO_DEFAULT_PROFILE_ID'),
    'default_return_url' => env('DINTERO_DEFAULT_RETURN_URL'),
    'default_callback_url' => env('DINTERO_DEFAULT_CALLBACK_URL'),

    /*
    |--------------------------------------------------------------------------
    | Laravel Integration
    |--------------------------------------------------------------------------
    |
    | Configuration specific to Laravel integration.
    |
    */
    'queue_webhooks' => env('DINTERO_QUEUE_WEBHOOKS', false),
    'webhook_queue' => env('DINTERO_WEBHOOK_QUEUE', 'default'),
    'cache_customers' => env('DINTERO_CACHE_CUSTOMERS', true),
    'cache_ttl' => env('DINTERO_CACHE_TTL', 3600), // seconds
];