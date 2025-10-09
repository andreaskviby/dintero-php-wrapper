<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dintero\DinteroClient;
use Dintero\Support\Builders\PaymentSessionBuilder;

// Initialize the Dintero client
$dintero = new DinteroClient([
    'environment' => 'sandbox',
    'api_key' => 'your_api_key_here',
    'account_id' => 'your_account_id', // Optional: Account ID (T- prefix added automatically for sandbox)
]);

// Test connection
try {
    if ($dintero->ping()) {
        echo "✅ Successfully connected to Dintero API\n";
    }
} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Example 1: Create a payment session using array data
echo "\n=== Example 1: Create Payment Session ===\n";

try {
    $session = $dintero->paymentSessions->create([
        'order' => [
            'amount' => 10000, // 100.00 NOK
            'currency' => 'NOK',
            'items' => [
                [
                    'name' => 'Example Product',
                    'amount' => 10000,
                    'quantity' => 1,
                    'description' => 'An example product for testing',
                ]
            ]
        ],
        'url' => [
            'return_url' => 'https://example.com/return',
            'callback_url' => 'https://example.com/callback',
        ],
        'customer' => [
            'email' => 'customer@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]
    ]);

    echo "Payment session created with ID: " . $session['id'] . "\n";
    echo "Hosted payment page: " . $session['url']['hosted_payment_page'] . "\n";
} catch (Exception $e) {
    echo "Failed to create payment session: " . $e->getMessage() . "\n";
}

// Example 2: Using the fluent builder
echo "\n=== Example 2: Using Fluent Builder ===\n";

try {
    $sessionData = (new PaymentSessionBuilder())
        ->withReturnUrl('https://example.com/return')
        ->withCallbackUrl('https://example.com/callback')
        ->withOrderData([
            'amount' => 15000,
            'currency' => 'NOK',
            'items' => [
                [
                    'name' => 'Premium Product',
                    'amount' => 15000,
                    'quantity' => 1,
                ]
            ]
        ])
        ->withCustomerEmail('premium@example.com')
        ->withCustomerName('Jane', 'Smith')
        ->withMetadata(['order_id' => 'ORD-12345'])
        ->build();

    $session = $dintero->paymentSessions->create($sessionData);
    echo "Premium payment session created with ID: " . $session['id'] . "\n";
} catch (Exception $e) {
    echo "Failed to create premium payment session: " . $e->getMessage() . "\n";
}

// Example 3: Customer management
echo "\n=== Example 3: Customer Management ===\n";

try {
    // Create a customer
    $customer = $dintero->customers->create([
        'email' => 'newcustomer@example.com',
        'first_name' => 'New',
        'last_name' => 'Customer',
        'phone_number' => '+47 12345678',
    ]);

    echo "Customer created with ID: " . $customer['id'] . "\n";

    // List customers
    $customers = $dintero->customers->list(['limit' => 5]);
    echo "Found " . count($customers['data']) . " customers\n";
} catch (Exception $e) {
    echo "Customer management failed: " . $e->getMessage() . "\n";
}

// Example 4: Webhook signature verification
echo "\n=== Example 4: Webhook Verification ===\n";

$webhookPayload = '{"event": "transaction.completed", "data": {"id": "test-123"}}';
$webhookSignature = hash_hmac('sha256', $webhookPayload, 'your_webhook_secret');
$webhookSecret = 'your_webhook_secret';

$isValid = $dintero->webhooks->verifySignature($webhookPayload, $webhookSignature, $webhookSecret);
echo "Webhook signature is " . ($isValid ? "valid" : "invalid") . "\n";

echo "\n=== Examples completed ===\n";