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
use Dintero\Resources\Transactions;
use Dintero\Resources\Payouts;
use Dintero\Resources\PaymentLinks;
use Dintero\Resources\Billing;
use Dintero\Resources\Cards;
use Dintero\Resources\Loyalty;
use Dintero\Resources\Reports;
use Dintero\Resources\Profiles;
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
    public readonly Transactions $transactions;
    public readonly Payouts $payouts;
    public readonly PaymentLinks $paymentLinks;
    public readonly Billing $billing;
    public readonly Cards $cards;
    public readonly Loyalty $loyalty;
    public readonly Reports $reports;
    public readonly Profiles $profiles;

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
        $this->transactions = new Transactions($this->httpClient);
        $this->payouts = new Payouts($this->httpClient);
        $this->paymentLinks = new PaymentLinks($this->httpClient);
        $this->billing = new Billing($this->httpClient);
        $this->cards = new Cards($this->httpClient);
        $this->loyalty = new Loyalty($this->httpClient);
        $this->reports = new Reports($this->httpClient);
        $this->profiles = new Profiles($this->httpClient);
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