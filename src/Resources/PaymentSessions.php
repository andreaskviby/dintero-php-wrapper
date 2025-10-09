<?php

declare(strict_types=1);

namespace Dintero\Resources;

/**
 * Payment Sessions resource
 */
class PaymentSessions extends BaseResource
{
    /**
     * Create a new payment session
     */
    public function create(array $data): array
    {
        $checkoutBaseUrl = $this->httpClient->getConfig()->getCheckoutBaseUrl();
        $response = $this->httpClient->requestWithBaseUrl('POST', '/sessions-profile', $checkoutBaseUrl, ['json' => $this->prepareData($data)]);
        return $response->json();
    }

    /**
     * Retrieve a payment session
     */
    public function get(string $sessionId): array
    {
        $checkoutBaseUrl = $this->httpClient->getConfig()->getCheckoutBaseUrl();
        $response = $this->httpClient->requestWithBaseUrl('GET', "/sessions-profile/{$sessionId}", $checkoutBaseUrl);
        return $response->json();
    }

    /**
     * Update a payment session
     */
    public function update(string $sessionId, array $data): array
    {
        $checkoutBaseUrl = $this->httpClient->getConfig()->getCheckoutBaseUrl();
        $response = $this->httpClient->requestWithBaseUrl('PUT', "/sessions-profile/{$sessionId}", $checkoutBaseUrl, ['json' => $this->prepareData($data)]);
        return $response->json();
    }

    /**
     * Cancel a payment session
     */
    public function cancel(string $sessionId): array
    {
        $checkoutBaseUrl = $this->httpClient->getConfig()->getCheckoutBaseUrl();
        $response = $this->httpClient->requestWithBaseUrl('POST', "/sessions-profile/{$sessionId}/cancel", $checkoutBaseUrl);
        return $response->json();
    }

    /**
     * Capture a payment session
     */
    public function capture(string $sessionId, array $data = []): array
    {
        $checkoutBaseUrl = $this->httpClient->getConfig()->getCheckoutBaseUrl();
        $response = $this->httpClient->requestWithBaseUrl('POST', "/sessions-profile/{$sessionId}/capture", $checkoutBaseUrl, ['json' => $this->prepareData($data)]);
        return $response->json();
    }

    /**
     * Get payment session events
     */
    public function getEvents(string $sessionId): array
    {
        $checkoutBaseUrl = $this->httpClient->getConfig()->getCheckoutBaseUrl();
        $response = $this->httpClient->requestWithBaseUrl('GET', "/sessions-profile/{$sessionId}/events", $checkoutBaseUrl);
        return $response->json();
    }

    /**
     * List payment sessions
     */
    public function list(array $params = []): array
    {
        $checkoutBaseUrl = $this->httpClient->getConfig()->getCheckoutBaseUrl();
        $response = $this->httpClient->requestWithBaseUrl('GET', '/sessions-profile', $checkoutBaseUrl, ['query' => $params]);
        return $response->json();
    }

    /**
     * Get all payment sessions (paginated)
     */
    public function all(array $params = []): \Generator
    {
        return $this->paginateWithCheckout('/sessions-profile', $params);
    }

    /**
     * Create a payment session with comprehensive data
     */
    public function createComprehensive(array $orderData, array $customerData = [], array $options = []): array
    {
        $sessionData = [
            'url' => [
                'return_url' => $options['return_url'] ?? null,
                'callback_url' => $options['callback_url'] ?? null,
            ],
            'order' => $orderData,
            'customer' => $customerData,
            'profile_id' => $options['profile_id'] ?? null,
            'expires_at' => $options['expires_at'] ?? null,
            'metadata' => $options['metadata'] ?? [],
        ];

        return $this->create($sessionData);
    }

    /**
     * Paginate through all results using checkout API
     */
    protected function paginateWithCheckout(string $endpoint, array $params = []): \Generator
    {
        $page = 1;
        $limit = $params['limit'] ?? 100;
        $checkoutBaseUrl = $this->httpClient->getConfig()->getCheckoutBaseUrl();

        do {
            $response = $this->httpClient->requestWithBaseUrl('GET', $endpoint, $checkoutBaseUrl, [
                'query' => array_merge($params, [
                    'page' => $page,
                    'limit' => $limit,
                ])
            ]);

            $data = $response->json();
            $items = $data['data'] ?? $data['items'] ?? [];

            foreach ($items as $item) {
                yield $item;
            }

            $hasMore = !empty($items) && count($items) === $limit;
            $page++;
        } while ($hasMore);
    }
}