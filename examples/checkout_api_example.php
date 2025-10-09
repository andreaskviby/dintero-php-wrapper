<?php

/**
 * Example demonstrating the updated Dintero wrapper with checkout API support
 * 
 * This example shows how the wrapper now correctly handles both:
 * - api.dintero.com/v1/ for authentication and transactions
 * - checkout.dintero.com/v1/sessions-profile for creating payment sessions
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Dintero\DinteroClient;

// Initialize client with configuration
$client = new DinteroClient([
    'environment' => 'sandbox',  // or 'production'
    'api_key' => 'your_api_key_here',
    'account_id' => 'your_account_id_here'
]);

try {
    // Create a payment session - now uses checkout.dintero.com/v1/sessions-profile
    $paymentSession = $client->paymentSessions->create([
        'url' => [
            'return_url' => 'https://your-site.com/return',
            'callback_url' => 'https://your-site.com/callback'
        ],
        'order' => [
            'amount' => 10000, // 100.00 NOK in øre
            'currency' => 'NOK',
            'items' => [
                [
                    'id' => 'item1',
                    'description' => 'Test Product',
                    'quantity' => 1,
                    'amount' => 10000
                ]
            ]
        ],
        'customer' => [
            'email' => 'customer@example.com'
        ]
    ]);

    echo "Payment session created successfully!\n";
    echo "Session ID: " . $paymentSession['id'] . "\n";
    echo "Checkout URL: " . $paymentSession['url']['checkout_url'] . "\n";

    // Retrieve payment session - also uses checkout API
    $retrievedSession = $client->paymentSessions->get($paymentSession['id']);
    echo "Retrieved session status: " . $retrievedSession['status'] . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Note: Authentication and transaction operations still use api.dintero.com/v1/
// Only payment session operations use checkout.dintero.com/v1/sessions-profile