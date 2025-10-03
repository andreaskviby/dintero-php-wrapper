<?php

declare(strict_types=1);

namespace Dintero\Resources;

use Dintero\Exceptions\DinteroException;

/**
 * Webhooks resource
 */
class Webhooks extends BaseResource
{
    /**
     * Verify webhook signature
     */
    public function verifySignature(string $payload, string $signature, string $secret): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Parse webhook payload
     */
    public function parsePayload(string $payload): array
    {
        $data = json_decode($payload, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new DinteroException('Invalid webhook payload: ' . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Handle webhook event
     */
    public function handleEvent(string $payload, string $signature, string $secret, ?callable $handler = null): array
    {
        if (!$this->verifySignature($payload, $signature, $secret)) {
            throw new DinteroException('Invalid webhook signature');
        }

        $event = $this->parsePayload($payload);

        if ($handler && is_callable($handler)) {
            $handler($event);
        }

        return $event;
    }

    /**
     * Create webhook endpoint
     */
    public function create(array $data): array
    {
        $response = $this->httpClient->post('/webhooks', $this->prepareData($data));
        return $response->json();
    }

    /**
     * Retrieve a webhook
     */
    public function get(string $webhookId): array
    {
        $response = $this->httpClient->get("/webhooks/{$webhookId}");
        return $response->json();
    }

    /**
     * Update a webhook
     */
    public function update(string $webhookId, array $data): array
    {
        $response = $this->httpClient->put("/webhooks/{$webhookId}", $this->prepareData($data));
        return $response->json();
    }

    /**
     * Delete a webhook
     */
    public function delete(string $webhookId): void
    {
        $this->httpClient->delete("/webhooks/{$webhookId}");
    }

    /**
     * List webhooks
     */
    public function list(array $params = []): array
    {
        $response = $this->httpClient->get('/webhooks', $params);
        return $response->json();
    }

    /**
     * Test webhook endpoint
     */
    public function test(string $webhookId): array
    {
        $response = $this->httpClient->post("/webhooks/{$webhookId}/test");
        return $response->json();
    }

    /**
     * Get webhook events
     */
    public function getEvents(string $webhookId, array $params = []): array
    {
        $response = $this->httpClient->get("/webhooks/{$webhookId}/events", $params);
        return $response->json();
    }

    /**
     * Resend webhook event
     */
    public function resendEvent(string $webhookId, string $eventId): array
    {
        $response = $this->httpClient->post("/webhooks/{$webhookId}/events/{$eventId}/resend");
        return $response->json();
    }

    /**
     * Get supported webhook events
     */
    public function getSupportedEvents(): array
    {
        return [
            'transaction.created',
            'transaction.updated',
            'transaction.completed',
            'transaction.failed',
            'transaction.cancelled',
            'refund.created',
            'refund.completed',
            'refund.failed',
            'session.created',
            'session.updated',
            'session.expired',
            'customer.created',
            'customer.updated',
            'order.created',
            'order.updated',
            'order.cancelled',
        ];
    }

    /**
     * Create webhook with common configuration
     */
    public function createEndpoint(string $url, array $events = [], array $options = []): array
    {
        $data = [
            'url' => $url,
            'events' => !empty($events) ? $events : $this->getSupportedEvents(),
            'active' => $options['active'] ?? true,
            'secret' => $options['secret'] ?? null,
            'metadata' => $options['metadata'] ?? [],
        ];

        return $this->create($data);
    }
}