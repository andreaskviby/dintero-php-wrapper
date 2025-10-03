<?php

declare(strict_types=1);

namespace Dintero;

use Dintero\Contracts\DinteroClientInterface;
use Dintero\Exceptions\DinteroException;
use Dintero\Http\HttpClient;
use Dintero\Resources\PaymentSessions;
use Dintero\Resources\Customers;
use Dintero\Resources\Orders;
use Dintero\Resources\Refunds;
use Dintero\Resources\Webhooks;
use Dintero\Support\Configuration;

/**
 * Main Dintero client class
 */
class DinteroClient implements DinteroClientInterface
{
    private HttpClient $httpClient;
    private Configuration $config;

    // Resource instances
    public readonly PaymentSessions $paymentSessions;
    public readonly Customers $customers;
    public readonly Orders $orders;
    public readonly Refunds $refunds;
    public readonly Webhooks $webhooks;

    public function __construct(array $config = [])
    {
        $this->config = new Configuration($config);
        $this->httpClient = new HttpClient($this->config);

        // Initialize resource instances
        $this->paymentSessions = new PaymentSessions($this->httpClient);
        $this->customers = new Customers($this->httpClient);
        $this->orders = new Orders($this->httpClient);
        $this->refunds = new Refunds($this->httpClient);
        $this->webhooks = new Webhooks($this->httpClient);
    }

    public function getConfig(): Configuration
    {
        return $this->config;
    }

    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }

    /**
     * Test the connection to Dintero API
     */
    public function ping(): bool
    {
        try {
            $response = $this->httpClient->get('/accounts/profile');
            return $response->getStatusCode() === 200;
        } catch (DinteroException $e) {
            return false;
        }
    }

    /**
     * Get account information
     */
    public function getAccount(): array
    {
        $response = $this->httpClient->get('/accounts/profile');
        return $response->json();
    }
}