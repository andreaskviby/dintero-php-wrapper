<?php

declare(strict_types=1);

namespace Dintero\Tests\Unit\Support;

use Dintero\Support\Configuration;
use Dintero\Exceptions\ConfigurationException;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    public function test_can_create_configuration_with_api_key()
    {
        $config = new Configuration([
            'environment' => 'sandbox',
            'api_key' => 'test_key',
        ]);

        $this->assertEquals('sandbox', $config->getEnvironment());
        $this->assertEquals('test_key', $config->getApiKey());
        $this->assertTrue($config->isSandbox());
        $this->assertFalse($config->isProduction());
    }

    public function test_can_create_configuration_with_oauth_credentials()
    {
        $config = new Configuration([
            'environment' => 'production',
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret',
        ]);

        $this->assertEquals('production', $config->getEnvironment());
        $this->assertEquals('test_client_id', $config->getClientId());
        $this->assertEquals('test_client_secret', $config->getClientSecret());
        $this->assertTrue($config->isProduction());
        $this->assertFalse($config->isSandbox());
    }

    public function test_throws_exception_for_invalid_environment()
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Environment must be either "production" or "sandbox"');

        new Configuration([
            'environment' => 'invalid',
            'api_key' => 'test_key',
        ]);
    }

    public function test_throws_exception_for_missing_credentials()
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Either api_key or both client_id and client_secret must be provided');

        new Configuration([
            'environment' => 'sandbox',
        ]);
    }

    public function test_sets_correct_base_url_for_sandbox()
    {
        $config = new Configuration([
            'environment' => 'sandbox',
            'api_key' => 'test_key',
        ]);

        $this->assertStringContains('sandbox', $config->getBaseUrl());
        $this->assertStringContains('sandbox', $config->getCheckoutBaseUrl());
    }

    public function test_account_id_configuration()
    {
        $config = new Configuration([
            'environment' => 'sandbox',
            'api_key' => 'test_key',
            'account_id' => 'test_account_id',
        ]);

        $this->assertEquals('test_account_id', $config->getAccountId());
    }

    public function test_checkout_base_url_configuration()
    {
        $config = new Configuration([
            'environment' => 'production',
            'api_key' => 'test_key',
        ]);

        $this->assertEquals('https://checkout.dintero.com/v1', $config->getCheckoutBaseUrl());
    }

    public function test_checkout_sandbox_base_url_configuration()
    {
        $config = new Configuration([
            'environment' => 'sandbox',
            'api_key' => 'test_key',
        ]);

        $this->assertEquals('https://checkout.sandbox.dintero.com/v1', $config->getCheckoutBaseUrl());
    }
}