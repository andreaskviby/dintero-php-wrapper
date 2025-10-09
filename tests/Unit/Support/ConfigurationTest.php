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
    }

    public function test_default_base_urls_have_trailing_slash()
    {
        $config = new Configuration([
            'environment' => 'production',
            'api_key' => 'test_key',
        ]);

        $baseUrl = $config->getBaseUrl();
        $this->assertStringEndsWith('/', $baseUrl);
        $this->assertEquals('https://api.dintero.com/v1/', $baseUrl);
    }

    public function test_sandbox_base_url_has_trailing_slash()
    {
        $config = new Configuration([
            'environment' => 'sandbox',
            'api_key' => 'test_key',
        ]);

        $baseUrl = $config->getBaseUrl();
        $this->assertStringEndsWith('/', $baseUrl);
        $this->assertEquals('https://api.sandbox.dintero.com/v1/', $baseUrl);
    }

    public function test_custom_base_url_preserves_trailing_slash()
    {
        $config = new Configuration([
            'environment' => 'production',
            'api_key' => 'test_key',
            'base_url' => 'https://custom.api.com/v2/',
        ]);

        $baseUrl = $config->getBaseUrl();
        $this->assertStringEndsWith('/', $baseUrl);
        $this->assertEquals('https://custom.api.com/v2/', $baseUrl);
    }
}