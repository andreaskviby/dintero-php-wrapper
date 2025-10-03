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
        $response = $this->httpClient->post('/sessions', $this->prepareData($data));
        return $response->json();
    }

    /**
     * Retrieve a payment session
     */
    public function get(string $sessionId): array
    {
        $response = $this->httpClient->get("/sessions/{$sessionId}");
        return $response->json();
    }

    /**
     * Update a payment session
     */
    public function update(string $sessionId, array $data): array
    {
        $response = $this->httpClient->put("/sessions/{$sessionId}", $this->prepareData($data));
        return $response->json();
    }

    /**
     * Cancel a payment session
     */
    public function cancel(string $sessionId): array
    {
        $response = $this->httpClient->post("/sessions/{$sessionId}/cancel");
        return $response->json();
    }

    /**
     * Capture a payment session
     */
    public function capture(string $sessionId, array $data = []): array
    {
        $response = $this->httpClient->post("/sessions/{$sessionId}/capture", $this->prepareData($data));
        return $response->json();
    }

    /**
     * Get payment session events
     */
    public function getEvents(string $sessionId): array
    {
        $response = $this->httpClient->get("/sessions/{$sessionId}/events");
        return $response->json();
    }

    /**
     * List payment sessions
     */
    public function list(array $params = []): array
    {
        $response = $this->httpClient->get('/sessions', $params);
        return $response->json();
    }

    /**
     * Get all payment sessions (paginated)
     */
    public function all(array $params = []): \Generator
    {
        return $this->paginate('/sessions', $params);
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
}