<?php

declare(strict_types=1);

namespace Dintero\Tests\Unit\Laravel;

use Dintero\DinteroClient;
use Dintero\Laravel\DinteroServiceProvider;
use PHPUnit\Framework\TestCase;

class DinteroServiceProviderTest extends TestCase
{
    public function test_service_provider_provides_correct_services()
    {
        $provider = new DinteroServiceProvider(app: null);
        
        // Test that the provider correctly returns the services it provides
        $provides = $provider->provides();
        $this->assertContains(DinteroClient::class, $provides);
        $this->assertContains('dintero', $provides);
    }

    public function test_config_includes_checkout_urls()
    {
        $configPath = __DIR__ . '/../../../config/dintero.php';
        $this->assertFileExists($configPath);
        
        $config = include $configPath;
        
        // Verify new configuration keys exist
        $this->assertArrayHasKey('account_id', $config);
        $this->assertArrayHasKey('checkout_base_url', $config);
        $this->assertArrayHasKey('checkout_sandbox_base_url', $config);
        
        // Verify default values
        $this->assertEquals('https://checkout.dintero.com/v1', $config['checkout_base_url']);
        $this->assertEquals('https://checkout.sandbox.dintero.com/v1', $config['checkout_sandbox_base_url']);
    }
}