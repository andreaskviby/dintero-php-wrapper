# Installation Guide

This guide will help you install and configure the Dintero PHP Wrapper in your project.

## Requirements

- PHP 8.1 or higher
- Composer
- ext-json
- ext-curl

## Installation

### Step 1: Install via Composer

```bash
composer require andreaskviby/dintero-php-wrapper
```

### Step 2: Basic PHP Usage

Create a simple PHP script:

```php
<?php

require_once 'vendor/autoload.php';

use Dintero\DinteroClient;

$dintero = new DinteroClient([
    'environment' => 'sandbox', // or 'production'
    'api_key' => 'your_api_key_here',
]);

// Test the connection
if ($dintero->ping()) {
    echo "Connected to Dintero!";
}
```

### Step 3: Laravel Installation

The package will auto-register in Laravel 5.5+.

#### Publish Configuration

```bash
php artisan vendor:publish --provider="Dintero\Laravel\DinteroServiceProvider" --tag="dintero-config"
```

#### Environment Configuration

Add to your `.env` file:

```env
DINTERO_ENVIRONMENT=sandbox
DINTERO_API_KEY=your_api_key_here
DINTERO_ACCOUNT_ID=your_account_id  # Optional: Account ID 
DINTERO_WEBHOOK_SECRET=your_webhook_secret
```

#### Laravel Usage

```php
use Dintero\Laravel\Facades\Dintero;

// Create payment session
$session = Dintero::paymentSessions()->create([
    'order' => [
        'amount' => 10000,
        'currency' => 'NOK',
    ],
    'url' => [
        'return_url' => url('/payment/return'),
    ]
]);

redirect($session['url']['hosted_payment_page']);
```

## Configuration Options

### Environment Variables

```env
# Required
DINTERO_ENVIRONMENT=sandbox          # sandbox or production
DINTERO_API_KEY=your_api_key

# OAuth Alternative (instead of API key)
DINTERO_CLIENT_ID=your_client_id
DINTERO_CLIENT_SECRET=your_client_secret

# Optional
DINTERO_ACCOUNT_ID=your_account_id   # Account ID (T- prefix added automatically for sandbox)
DINTERO_TIMEOUT=30                   # Request timeout in seconds
DINTERO_RETRY_ATTEMPTS=3             # Number of retry attempts
DINTERO_LOG_REQUESTS=false           # Log API requests
DINTERO_WEBHOOK_SECRET=your_secret   # Webhook verification secret
DINTERO_DEFAULT_CURRENCY=NOK         # Default currency
```

### Programmatic Configuration

```php
$config = [
    'environment' => 'sandbox',
    'api_key' => 'your_api_key',
    'account_id' => 'your_account_id',
    'timeout' => 30,
    'retry_attempts' => 3,
    'log_requests' => true,
];

$dintero = new DinteroClient($config);
```

## Quick Examples

### Payment Session

```php
$session = $dintero->paymentSessions->create([
    'order' => [
        'amount' => 10000, // 100.00 NOK (amount in øre)
        'currency' => 'NOK',
        'items' => [
            [
                'name' => 'Product Name',
                'amount' => 10000,
                'quantity' => 1,
            ]
        ]
    ],
    'url' => [
        'return_url' => 'https://yoursite.com/return',
        'callback_url' => 'https://yoursite.com/callback',
    ]
]);
```

### Webhook Handling (Laravel)

The package automatically handles webhooks. Just configure the webhook URL in Dintero dashboard:

```
https://yoursite.com/dintero/webhooks
```

Listen for events in your EventServiceProvider:

```php
protected $listen = [
    'dintero.webhook.transaction.completed' => [
        PaymentCompletedListener::class,
    ],
];
```

### Customer Management

```php
// Create customer
$customer = $dintero->customers->create([
    'email' => 'customer@example.com',
    'first_name' => 'John',
    'last_name' => 'Doe',
]);

// Get customer
$customer = $dintero->customers->get($customerId);
```

### Refunds

```php
// Full refund
$refund = $dintero->refunds->full($transactionId);

// Partial refund
$refund = $dintero->refunds->partial($transactionId, 5000); // 50.00 NOK
```

## Troubleshooting

### Common Issues

1. **Authentication Error**: Check your API key and environment settings
2. **SSL Certificate Error**: Ensure your server has updated CA certificates
3. **Timeout Error**: Increase timeout value in configuration
4. **Webhook Verification Failed**: Check webhook secret configuration

### Debug Mode

Enable debug logging:

```php
$dintero = new DinteroClient([
    'api_key' => 'your_key',
    'log_requests' => true,
    'log_responses' => true,
]);
```

## Support

- 📖 [Documentation](README.md)
- 🐛 [Issues](https://github.com/andreaskviby/dintero-php-wrapper/issues)
- 💬 [Discussions](https://github.com/andreaskviby/dintero-php-wrapper/discussions)