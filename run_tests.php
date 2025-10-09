<?php

/**
 * Simple test runner for Configuration tests
 */

require_once __DIR__ . '/src/Support/Configuration.php';
require_once __DIR__ . '/src/Exceptions/DinteroException.php';
require_once __DIR__ . '/src/Exceptions/ConfigurationException.php';

echo "Running Configuration Tests\n";
echo "===========================\n\n";

class SimpleTestRunner
{
    private int $passed = 0;
    private int $failed = 0;

    public function assertEquals($expected, $actual, $message = '')
    {
        if ($expected === $actual) {
            $this->passed++;
            echo "✅ PASS: " . ($message ?: "assertEquals") . "\n";
        } else {
            $this->failed++;
            echo "❌ FAIL: " . ($message ?: "assertEquals") . " - Expected: '$expected', Got: '$actual'\n";
        }
    }

    public function assertStringEndsWith($suffix, $string, $message = '')
    {
        if (str_ends_with($string, $suffix)) {
            $this->passed++;
            echo "✅ PASS: " . ($message ?: "assertStringEndsWith") . "\n";
        } else {
            $this->failed++;
            echo "❌ FAIL: " . ($message ?: "assertStringEndsWith") . " - String '$string' does not end with '$suffix'\n";
        }
    }

    public function assertTrue($condition, $message = '')
    {
        if ($condition) {
            $this->passed++;
            echo "✅ PASS: " . ($message ?: "assertTrue") . "\n";
        } else {
            $this->failed++;
            echo "❌ FAIL: " . ($message ?: "assertTrue") . " - Condition is false\n";
        }
    }

    public function summary()
    {
        echo "\n" . str_repeat("=", 40) . "\n";
        echo "Test Summary: {$this->passed} passed, {$this->failed} failed\n";
        return $this->failed === 0;
    }
}

$test = new SimpleTestRunner();

// Test 1: Default base URLs have trailing slash
echo "Test 1: Default base URLs have trailing slash\n";
$config = new \Dintero\Support\Configuration([
    'environment' => 'production',
    'api_key' => 'test_key',
]);

$baseUrl = $config->getBaseUrl();
$test->assertStringEndsWith('/', $baseUrl, 'Production base URL ends with /');
$test->assertEquals('https://api.dintero.com/v1/', $baseUrl, 'Production base URL matches expected');

// Test 2: Sandbox base URL has trailing slash
echo "\nTest 2: Sandbox base URL has trailing slash\n";
$config = new \Dintero\Support\Configuration([
    'environment' => 'sandbox',
    'api_key' => 'test_key',
]);

$baseUrl = $config->getBaseUrl();
$test->assertStringEndsWith('/', $baseUrl, 'Sandbox base URL ends with /');
$test->assertEquals('https://api.sandbox.dintero.com/v1/', $baseUrl, 'Sandbox base URL matches expected');

// Test 3: Custom base URL preserves trailing slash
echo "\nTest 3: Custom base URL preserves trailing slash\n";
$config = new \Dintero\Support\Configuration([
    'environment' => 'production',
    'api_key' => 'test_key',
    'base_url' => 'https://custom.api.com/v2/',
]);

$baseUrl = $config->getBaseUrl();
$test->assertStringEndsWith('/', $baseUrl, 'Custom base URL ends with /');
$test->assertEquals('https://custom.api.com/v2/', $baseUrl, 'Custom base URL matches expected');

$success = $test->summary();
exit($success ? 0 : 1);