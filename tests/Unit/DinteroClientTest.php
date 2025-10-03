<?php

declare(strict_types=1);

namespace Dintero\Tests\Unit;

use Dintero\DinteroClient;
use Dintero\Support\Configuration;
use PHPUnit\Framework\TestCase;

class DinteroClientTest extends TestCase
{
    public function test_can_create_client_with_config()
    {
        $config = [
            'environment' => 'sandbox',
            'api_key' => 'test_key',
        ];

        $client = new DinteroClient($config);

        $this->assertInstanceOf(Configuration::class, $client->getConfig());
        $this->assertEquals('sandbox', $client->getConfig()->getEnvironment());
        $this->assertEquals('test_key', $client->getConfig()->getApiKey());
    }

    public function test_has_resource_instances()
    {
        $client = new DinteroClient(['api_key' => 'test']);

        $this->assertInstanceOf(\Dintero\Resources\PaymentSessions::class, $client->paymentSessions);
        $this->assertInstanceOf(\Dintero\Resources\Customers::class, $client->customers);
        $this->assertInstanceOf(\Dintero\Resources\Orders::class, $client->orders);
        $this->assertInstanceOf(\Dintero\Resources\Refunds::class, $client->refunds);
        $this->assertInstanceOf(\Dintero\Resources\Webhooks::class, $client->webhooks);
    }
}