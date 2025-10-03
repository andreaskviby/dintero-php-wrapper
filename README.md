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

### Core Payment Features
- **Payment Sessions** - Create and manage checkout sessions
- **Split Payouts** - Marketplace payments with multiple recipients
- **Payment Links** - Generate payment URLs and QR codes
- **Recurring Billing** - Subscription and invoice management
- **Virtual Cards** - Gift cards, vouchers, and wallet functionality
- **Loyalty Programs** - Discount codes, points, and stamp cards
- **Transaction Management** - Direct transaction control and monitoring

### Advanced Features
- **Comprehensive Reporting** - Revenue, analytics, and reconciliation reports
- **Profile Management** - Merchant settings and checkout configuration  
- **Multi-currency Support** - Handle payments in multiple currencies
- **Fraud Protection** - Risk analysis and fraud detection
- **Real-time Events** - Webhook handling for instant notifications
- **API Versioning** - Support for different API versions

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

### Split Payouts (Marketplace)

```php
// Create split payout for marketplace
$splits = [
    ['recipient_id' => 'seller_123', 'amount' => 7000],
    ['recipient_id' => 'platform', 'amount' => 3000]
];
$payout = $dintero->payouts->createSplit('transaction_123', $splits);

// Create payout with multiple recipients
$recipients = [
    ['recipient_id' => 'seller_1', 'amount' => 5000],
    ['recipient_id' => 'seller_2', 'amount' => 3000]
];
$payout = $dintero->payouts->createWithRecipients($recipients);

// Get payout status
$status = $dintero->payouts->getStatus('payout-id');

// Download payout report
$report = $dintero->payouts->downloadReport('report-id', 'csv');
```

### Payment Links & QR Codes

```php
// Create quick payment link
$link = $dintero->paymentLinks->createQuick(10000, 'NOK', [
    'description' => 'Invoice payment',
    'expires_at' => '2024-12-31 23:59:59'
]);

// Generate QR code for payment link
$qrCode = $dintero->paymentLinks->getQrCode('link-id', [
    'size' => '300x300',
    'format' => 'png'
]);

// Create recurring payment link
$recurringLink = $dintero->paymentLinks->createRecurring([
    'amount' => 29900,
    'currency' => 'NOK',
    'interval' => 'monthly'
]);

// Share payment link via email
$dintero->paymentLinks->shareViaEmail('link-id', [
    'recipient_email' => 'customer@example.com',
    'subject' => 'Payment Request'
]);
```

### Subscription & Billing

```php
// Create subscription
$subscription = $dintero->billing->createSubscription([
    'customer_id' => 'customer-123',
    'plan_id' => 'premium-plan',
    'trial_period_days' => 14
]);

// Create billing plan
$plan = $dintero->billing->createPlan([
    'name' => 'Premium Plan',
    'amount' => 29900,
    'currency' => 'NOK',
    'interval' => 'monthly'
]);

// Cancel subscription
$dintero->billing->cancelSubscription('subscription-id');

// Create and send invoice
$invoice = $dintero->billing->createInvoice($invoiceData);
$dintero->billing->sendInvoice('invoice-id');
```

### Virtual Cards & Gift Cards

```php
// Create gift card
$giftCard = $dintero->cards->createGiftCard(50000, [
    'currency' => 'NOK',
    'recipient_email' => 'recipient@example.com',
    'message' => 'Happy Birthday!'
]);

// Create virtual card
$virtualCard = $dintero->cards->create([
    'type' => 'virtual',
    'initial_balance' => 25000,
    'currency' => 'NOK'
]);

// Load balance to card
$dintero->cards->loadBalance('card-id', 10000);

// Reserve amount on card
$reservation = $dintero->cards->reserve('card-id', 5000);

// Capture reserved amount
$dintero->cards->capture('card-id', 'reservation-id');
```

### Loyalty & Discounts

```php
// Create discount code
$discount = $dintero->loyalty->createDiscount([
    'code' => 'SAVE20',
    'type' => 'percentage',
    'value' => 20,
    'minimum_amount' => 10000
]);

// Award loyalty points
$dintero->loyalty->awardPoints('customer-id', 100, [
    'reason' => 'Purchase reward'
]);

// Create stamp card
$stampCard = $dintero->loyalty->createStampCard([
    'name' => 'Coffee Loyalty Card',
    'stamps_required' => 10,
    'reward_description' => 'Free coffee'
]);

// Add stamp to card
$dintero->loyalty->addStamp('stamp-card-id');
```

### Transaction Management

```php
// Get transaction
$transaction = $dintero->transactions->get('transaction-id');

// Capture transaction
$capture = $dintero->transactions->capture('transaction-id', [
    'amount' => 8000 // Partial capture
]);

// Void transaction
$void = $dintero->transactions->void('transaction-id', 'Customer request');

// Get transaction events
$events = $dintero->transactions->getEvents('transaction-id');

// Check transaction status
$isSuccessful = $dintero->transactions->isSuccessful('transaction-id');
```

### Reporting & Analytics

```php
// Get revenue reports
$revenue = $dintero->reports->getRevenueReports([
    'start_date' => '2024-01-01',
    'end_date' => '2024-01-31',
    'group_by' => 'day'
]);

// Get dashboard analytics
$analytics = $dintero->reports->getDashboardAnalytics([
    'period' => 'last_30_days'
]);

// Generate custom report
$report = $dintero->reports->generateCustomReport([
    'metrics' => ['revenue', 'transactions'],
    'filters' => ['currency' => 'NOK']
]);

// Schedule report
$schedule = $dintero->reports->scheduleReport([
    'type' => 'revenue',
    'frequency' => 'weekly',
    'email' => 'admin@example.com'
]);
```

### Profile & Configuration

```php
// Get merchant profile
$profile = $dintero->profiles->get();

// Update checkout configuration
$config = $dintero->profiles->updateCheckoutConfig([
    'theme' => 'dark',
    'primary_color' => '#007bff',
    'logo_url' => 'https://example.com/logo.png'
]);

// Get payment methods
$methods = $dintero->profiles->getPaymentMethods();

// Enable payment method
$dintero->profiles->enablePaymentMethod('vipps');

// Update branding
$dintero->profiles->updateBranding([
    'company_name' => 'My Company',
    'primary_color' => '#ff6b35'
]);
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