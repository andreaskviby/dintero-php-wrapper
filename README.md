# Dintero PHP Wrapper

A comprehensive PHP wrapper for the Dintero payment provider with seamless Laravel integration.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/andreaskviby/dintero-php-wrapper.svg?style=flat-square)](https://packagist.org/packages/andreaskviby/dintero-php-wrapper)
[![Total Downloads](https://img.shields.io/packagist/dt/andreaskviby/dintero-php-wrapper.svg?style=flat-square)](https://packagist.org/packages/andreaskviby/dintero-php-wrapper)
[![License](https://img.shields.io/github/license/andreaskviby/dintero-php-wrapper.svg?style=flat-square)](LICENSE.md)

## Features

- 🚀 **Complete API Coverage** - All Dintero API endpoints supported
- 🎯 **Laravel Integration** - Service provider, facade, and configuration
- 🔒 **Secure Webhooks** - Built-in webhook verification and handling
- 💎 **Fluent API** - Intuitive and easy-to-use interface
- 🛡️ **Type Safety** - Full type hints and DTOs
- ⚡ **Performance** - HTTP client with retry logic and caching
- 📊 **Logging** - Comprehensive request/response logging
- 🧪 **Thoroughly Tested** - Comprehensive test suite

## Installation

Install the package via Composer:

```bash
composer require andreaskviby/dintero-php-wrapper
```

### Laravel Installation

The package will automatically register itself in Laravel 5.5+.

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Dintero\Laravel\DinteroServiceProvider" --tag="dintero-config"
```

Add your Dintero credentials to your `.env` file:

```env
DINTERO_ENVIRONMENT=sandbox
DINTERO_API_KEY=your_api_key_here
# OR use OAuth2 credentials
DINTERO_CLIENT_ID=your_client_id
DINTERO_CLIENT_SECRET=your_client_secret

# Webhook configuration
DINTERO_WEBHOOK_SECRET=your_webhook_secret
```

## Quick Start

### Basic Usage

```php
use Dintero\DinteroClient;

$dintero = new DinteroClient([
    'environment' => 'sandbox', // or 'production'
    'api_key' => 'your_api_key_here',
]);

// Test connection
if ($dintero->ping()) {
    echo "Connected to Dintero!";
}
```

### Laravel Usage

```php
use Dintero\Laravel\Facades\Dintero;

// Using the facade
$session = Dintero::paymentSessions()->create([
    'order' => [
        'amount' => 10000, // 100.00 NOK
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
        'return_url' => 'https://your-site.com/return',
        'callback_url' => 'https://your-site.com/callback',
    ]
]);
```

### Using Dependency Injection

```php
use Dintero\DinteroClient;

class PaymentController extends Controller
{
    public function createPayment(DinteroClient $dintero)
    {
        $session = $dintero->paymentSessions->create([
            // Payment data
        ]);
        
        return redirect($session['url']['hosted_payment_page']);
    }
}
```

## API Reference

### Payment Sessions

```php
// Create payment session
$session = $dintero->paymentSessions->create([
    'order' => [
        'amount' => 10000,
        'currency' => 'NOK',
    ],
    'url' => [
        'return_url' => 'https://example.com/return',
    ]
]);

// Get payment session
$session = $dintero->paymentSessions->get('session-id');

// Update payment session
$session = $dintero->paymentSessions->update('session-id', $data);

// Capture payment
$capture = $dintero->paymentSessions->capture('session-id');

// Cancel payment
$cancel = $dintero->paymentSessions->cancel('session-id');

// List all sessions
$sessions = $dintero->paymentSessions->list();

// Get all sessions (paginated)
foreach ($dintero->paymentSessions->all() as $session) {
    // Process each session
}
```

### Fluent Payment Session Builder

```php
use Dintero\Support\Builders\PaymentSessionBuilder;

$sessionData = (new PaymentSessionBuilder())
    ->withReturnUrl('https://example.com/return')
    ->withCallbackUrl('https://example.com/callback')
    ->withOrderData([
        'amount' => 10000,
        'currency' => 'NOK',
    ])
    ->withCustomerEmail('customer@example.com')
    ->withCustomerName('John', 'Doe')
    ->withMetadata(['order_id' => '12345'])
    ->build();

$session = $dintero->paymentSessions->create($sessionData);
```

### Customers

```php
// Create customer
$customer = $dintero->customers->create([
    'email' => 'customer@example.com',
    'first_name' => 'John',
    'last_name' => 'Doe',
]);

// Get customer
$customer = $dintero->customers->get('customer-id');

// Update customer
$customer = $dintero->customers->update('customer-id', $data);

// List customers
$customers = $dintero->customers->list();

// Search customers
$customers = $dintero->customers->search('john@example.com');
```

### Orders

```php
// Create order
$order = $dintero->orders->create([
    'amount' => 10000,
    'currency' => 'NOK',
    'items' => [
        [
            'name' => 'Product',
            'amount' => 10000,
            'quantity' => 1,
        ]
    ]
]);

// Get order
$order = $dintero->orders->get('order-id');

// Add item to order
$item = $dintero->orders->addItem('order-id', [
    'name' => 'Additional Product',
    'amount' => 5000,
    'quantity' => 1,
]);
```

### Refunds

```php
// Create refund
$refund = $dintero->refunds->create([
    'transaction_id' => 'transaction-id',
    'amount' => 5000, // Partial refund
    'reason' => 'Customer request',
]);

// Full refund
$refund = $dintero->refunds->full('transaction-id');

// Partial refund
$refund = $dintero->refunds->partial('transaction-id', 5000);

// Get refund
$refund = $dintero->refunds->get('refund-id');
```

### Webhooks

```php
// Verify webhook signature
$isValid = $dintero->webhooks->verifySignature($payload, $signature, $secret);

// Handle webhook
$event = $dintero->webhooks->handleEvent($payload, $signature, $secret, function($event) {
    // Process the event
    match ($event['type']) {
        'transaction.completed' => handlePaymentCompleted($event),
        'transaction.failed' => handlePaymentFailed($event),
        default => null,
    };
});

// Create webhook endpoint
$webhook = $dintero->webhooks->createEndpoint('https://example.com/webhooks', [
    'transaction.completed',
    'transaction.failed',
]);
```

## Laravel Webhooks

The package automatically sets up webhook routes when used with Laravel:

```php
// config/dintero.php
return [
    'webhook_middleware' => ['api'],
    'webhook_route_prefix' => 'dintero/webhooks',
    'verify_webhooks' => true,
    'webhook_secret' => env('DINTERO_WEBHOOK_SECRET'),
];
```

Listen for webhook events:

```php
// In your EventServiceProvider
protected $listen = [
    'dintero.webhook.transaction.completed' => [
        PaymentCompletedListener::class,
    ],
    'dintero.webhook.transaction.failed' => [
        PaymentFailedListener::class,
    ],
];
```

## Configuration

### Environment Variables

```env
# Required
DINTERO_ENVIRONMENT=sandbox  # or production
DINTERO_API_KEY=your_api_key

# OR OAuth2 (alternative to API key)
DINTERO_CLIENT_ID=your_client_id
DINTERO_CLIENT_SECRET=your_client_secret

# Optional
DINTERO_TIMEOUT=30
DINTERO_RETRY_ATTEMPTS=3
DINTERO_LOG_REQUESTS=false
DINTERO_WEBHOOK_SECRET=your_webhook_secret
DINTERO_DEFAULT_CURRENCY=NOK
```

### Configuration Array

```php
$config = [
    'environment' => 'sandbox',
    'api_key' => 'your_api_key',
    'timeout' => 30,
    'retry_attempts' => 3,
    'log_requests' => true,
];

$dintero = new DinteroClient($config);
```

## Error Handling

The package provides specific exception classes:

```php
use Dintero\Exceptions\{
    DinteroException,
    AuthenticationException,
    ValidationException,
    RateLimitException
};

try {
    $session = $dintero->paymentSessions->create($data);
} catch (ValidationException $e) {
    // Handle validation errors
    $errors = $e->getErrors();
} catch (AuthenticationException $e) {
    // Handle authentication errors
} catch (RateLimitException $e) {
    // Handle rate limiting
} catch (DinteroException $e) {
    // Handle general Dintero errors
}
```

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email security@kviby.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Credits

- [Andreas Kviby](https://github.com/andreaskviby)
- [All Contributors](../../contributors)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes.