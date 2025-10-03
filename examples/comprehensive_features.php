<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dintero\DinteroClient;

// Initialize the Dintero client
$dintero = new DinteroClient([
    'environment' => 'sandbox',
    'api_key' => 'your_api_key_here',
]);

echo "=== Comprehensive Dintero PHP Wrapper Examples ===\n\n";

// 1. Payment Sessions (existing functionality)
echo "1. Creating Payment Session...\n";
try {
    $session = $dintero->paymentSessions->create([
        'order' => [
            'amount' => 10000,
            'currency' => 'NOK',
            'items' => [
                [
                    'name' => 'Example Product',
                    'amount' => 10000,
                    'quantity' => 1,
                ]
            ]
        ],
        'url' => [
            'return_url' => 'https://example.com/return',
            'callback_url' => 'https://example.com/callback',
        ]
    ]);
    echo "✅ Payment session created: {$session['id']}\n";
} catch (Exception $e) {
    echo "❌ Failed to create payment session: {$e->getMessage()}\n";
}

// 2. Split Payouts for Marketplaces
echo "\n2. Creating Split Payout for Marketplace...\n";
try {
    $splits = [
        [
            'recipient_id' => 'seller_123',
            'amount' => 7000,
            'description' => 'Seller revenue',
        ],
        [
            'recipient_id' => 'platform',
            'amount' => 3000,
            'description' => 'Platform fee',
        ]
    ];
    
    $payout = $dintero->payouts->createSplit('transaction_123', $splits);
    echo "✅ Split payout created: {$payout['id']}\n";
} catch (Exception $e) {
    echo "❌ Failed to create split payout: {$e->getMessage()}\n";
}

// 3. Payment Links and QR Codes
echo "\n3. Creating Payment Link with QR Code...\n";
try {
    $paymentLink = $dintero->paymentLinks->createQuick(15000, 'NOK', [
        'description' => 'Quick payment for invoice #INV-001',
        'expires_at' => date('Y-m-d H:i:s', strtotime('+7 days')),
    ]);
    
    echo "✅ Payment link created: {$paymentLink['url']}\n";
    
    // Generate QR code
    $qrCode = $dintero->paymentLinks->getQrCode($paymentLink['id'], [
        'size' => '300x300',
        'format' => 'png'
    ]);
    echo "✅ QR code generated: {$qrCode['url']}\n";
} catch (Exception $e) {
    echo "❌ Failed to create payment link: {$e->getMessage()}\n";
}

// 4. Subscription Management
echo "\n4. Creating Subscription...\n";
try {
    $subscription = $dintero->billing->createSubscription([
        'customer_id' => 'customer_123',
        'plan_id' => 'plan_premium',
        'trial_period_days' => 14,
        'metadata' => ['source' => 'website_signup']
    ]);
    echo "✅ Subscription created: {$subscription['id']}\n";
} catch (Exception $e) {
    echo "❌ Failed to create subscription: {$e->getMessage()}\n";
}

// 5. Virtual Cards and Gift Cards
echo "\n5. Creating Gift Card...\n";
try {
    $giftCard = $dintero->cards->createGiftCard(50000, [
        'currency' => 'NOK',
        'recipient_email' => 'recipient@example.com',
        'recipient_name' => 'John Doe',
        'message' => 'Happy Birthday!',
        'expires_at' => date('Y-m-d H:i:s', strtotime('+1 year')),
    ]);
    echo "✅ Gift card created: {$giftCard['card_code']}\n";
} catch (Exception $e) {
    echo "❌ Failed to create gift card: {$e->getMessage()}\n";
}

// 6. Loyalty and Discount Management
echo "\n6. Creating Discount Code...\n";
try {
    $discount = $dintero->loyalty->createDiscount([
        'code' => 'SAVE20',
        'type' => 'percentage',
        'value' => 20,
        'minimum_amount' => 10000,
        'usage_limit' => 100,
        'expires_at' => date('Y-m-d H:i:s', strtotime('+30 days')),
    ]);
    echo "✅ Discount code created: {$discount['code']}\n";
} catch (Exception $e) {
    echo "❌ Failed to create discount: {$e->getMessage()}\n";
}

// 7. Loyalty Points Management
echo "\n7. Awarding Loyalty Points...\n";
try {
    $pointsAwarded = $dintero->loyalty->awardPoints('customer_123', 100, [
        'reason' => 'Purchase reward',
        'transaction_id' => 'trans_456',
    ]);
    echo "✅ Loyalty points awarded: {$pointsAwarded['points']} points\n";
} catch (Exception $e) {
    echo "❌ Failed to award points: {$e->getMessage()}\n";
}

// 8. Transaction Management
echo "\n8. Capturing Transaction...\n";
try {
    $capture = $dintero->transactions->capture('transaction_123', [
        'amount' => 8000, // Partial capture
    ]);
    echo "✅ Transaction captured: {$capture['amount']} of {$capture['authorized_amount']}\n";
} catch (Exception $e) {
    echo "❌ Failed to capture transaction: {$e->getMessage()}\n";
}

// 9. Reporting and Analytics
echo "\n9. Generating Revenue Report...\n";
try {
    $revenueReport = $dintero->reports->getRevenueReports([
        'start_date' => date('Y-m-d', strtotime('-30 days')),
        'end_date' => date('Y-m-d'),
        'group_by' => 'day',
    ]);
    echo "✅ Revenue report generated with {$revenueReport['total_transactions']} transactions\n";
} catch (Exception $e) {
    echo "❌ Failed to generate report: {$e->getMessage()}\n";
}

// 10. Profile and Configuration Management
echo "\n10. Updating Checkout Configuration...\n";
try {
    $checkoutConfig = $dintero->profiles->updateCheckoutConfig([
        'theme' => 'dark',
        'primary_color' => '#007bff',
        'logo_url' => 'https://example.com/logo.png',
        'terms_url' => 'https://example.com/terms',
        'privacy_url' => 'https://example.com/privacy',
    ]);
    echo "✅ Checkout configuration updated\n";
} catch (Exception $e) {
    echo "❌ Failed to update configuration: {$e->getMessage()}\n";
}

// 11. Card Management (Virtual Cards)
echo "\n11. Creating Virtual Card with Balance...\n";
try {
    $virtualCard = $dintero->cards->create([
        'type' => 'virtual',
        'currency' => 'NOK',
        'customer_id' => 'customer_123',
        'initial_balance' => 25000,
    ]);
    
    echo "✅ Virtual card created: {$virtualCard['card_number']}\n";
    
    // Check balance
    $balance = $dintero->cards->getBalance($virtualCard['id']);
    echo "✅ Card balance: {$balance['available_amount']} {$balance['currency']}\n";
} catch (Exception $e) {
    echo "❌ Failed to create virtual card: {$e->getMessage()}\n";
}

// 12. Advanced Billing Features
echo "\n12. Creating Billing Plan...\n";
try {
    $plan = $dintero->billing->createPlan([
        'name' => 'Premium Plan',
        'amount' => 29900, // 299 NOK
        'currency' => 'NOK',
        'interval' => 'monthly',
        'interval_count' => 1,
        'trial_period_days' => 7,
        'features' => [
            'unlimited_transactions',
            'priority_support',
            'advanced_analytics'
        ]
    ]);
    echo "✅ Billing plan created: {$plan['name']}\n";
} catch (Exception $e) {
    echo "❌ Failed to create billing plan: {$e->getMessage()}\n";
}

// 13. Webhook Management
echo "\n13. Creating Webhook Endpoint...\n";
try {
    $webhook = $dintero->webhooks->createEndpoint('https://example.com/webhooks/dintero', [
        'transaction.completed',
        'transaction.failed',
        'subscription.cancelled',
        'invoice.paid',
    ]);
    echo "✅ Webhook endpoint created: {$webhook['url']}\n";
} catch (Exception $e) {
    echo "❌ Failed to create webhook: {$e->getMessage()}\n";
}

// 14. Advanced Reporting
echo "\n14. Getting Dashboard Analytics...\n";
try {
    $analytics = $dintero->reports->getDashboardAnalytics([
        'period' => 'last_30_days',
        'metrics' => ['revenue', 'transactions', 'customers', 'conversion_rate'],
    ]);
    echo "✅ Dashboard analytics retrieved:\n";
    echo "   - Revenue: {$analytics['revenue']['total']} {$analytics['revenue']['currency']}\n";
    echo "   - Transactions: {$analytics['transactions']['count']}\n";
    echo "   - New Customers: {$analytics['customers']['new']}\n";
} catch (Exception $e) {
    echo "❌ Failed to get analytics: {$e->getMessage()}\n";
}

// 15. Fraud and Risk Management
echo "\n15. Getting Risk Analysis...\n";
try {
    $riskAnalysis = $dintero->reports->getRiskAnalysis([
        'start_date' => date('Y-m-d', strtotime('-7 days')),
        'end_date' => date('Y-m-d'),
    ]);
    echo "✅ Risk analysis retrieved: {$riskAnalysis['risk_score']}% risk score\n";
} catch (Exception $e) {
    echo "❌ Failed to get risk analysis: {$e->getMessage()}\n";
}

echo "\n=== All Examples Completed ===\n";
echo "This demonstrates the comprehensive feature set of the Dintero PHP Wrapper!\n";
echo "Features covered:\n";
echo "✓ Payment Sessions & Checkout\n";
echo "✓ Split Payouts for Marketplaces\n";
echo "✓ Payment Links & QR Codes\n";
echo "✓ Subscription & Billing Management\n";
echo "✓ Virtual Cards & Gift Cards\n";
echo "✓ Loyalty Programs & Discounts\n";
echo "✓ Transaction Management\n";
echo "✓ Comprehensive Reporting\n";
echo "✓ Profile & Configuration Management\n";
echo "✓ Webhook Management\n";
echo "✓ Analytics & Risk Assessment\n";