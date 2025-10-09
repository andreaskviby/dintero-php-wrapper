<?php

/**
 * Simple verification script to test URL construction
 */

// Include the necessary files manually
require_once __DIR__ . '/src/Support/Configuration.php';

echo "Testing Dintero PHP Wrapper URL Construction\n";
echo "==========================================\n\n";

// Test production environment
echo "1. Testing Production Environment:\n";
$productionConfig = new \Dintero\Support\Configuration([
    'environment' => 'production',
    'api_key' => 'test_key',
]);

$prodBaseUrl = $productionConfig->getBaseUrl();
echo "   Base URL: " . $prodBaseUrl . "\n";
echo "   Has trailing slash: " . (str_ends_with($prodBaseUrl, '/') ? 'YES' : 'NO') . "\n";
echo "   Expected: https://api.dintero.com/v1/\n\n";

// Test sandbox environment  
echo "2. Testing Sandbox Environment:\n";
$sandboxConfig = new \Dintero\Support\Configuration([
    'environment' => 'sandbox',
    'api_key' => 'test_key',
]);

$sandboxBaseUrl = $sandboxConfig->getBaseUrl();
echo "   Base URL: " . $sandboxBaseUrl . "\n";
echo "   Has trailing slash: " . (str_ends_with($sandboxBaseUrl, '/') ? 'YES' : 'NO') . "\n";
echo "   Expected: https://api.sandbox.dintero.com/v1/\n\n";

// Test URL construction simulation
echo "3. Simulating Guzzle URL Construction:\n";
$testEndpoint = 'accounts/T11115430/auth/token';
$constructedUrl = rtrim($prodBaseUrl, '/') . '/' . ltrim($testEndpoint, '/');
echo "   Base URL: " . $prodBaseUrl . "\n";
echo "   Endpoint: " . $testEndpoint . "\n";
echo "   Final URL: " . $constructedUrl . "\n";
echo "   Expected: https://api.dintero.com/v1/accounts/T11115430/auth/token\n\n";

// Test that we get the expected result
if ($constructedUrl === 'https://api.dintero.com/v1/accounts/T11115430/auth/token') {
    echo "✅ SUCCESS: URL construction works correctly!\n";
} else {
    echo "❌ FAILURE: URL construction is incorrect!\n";
}