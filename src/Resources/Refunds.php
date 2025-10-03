<?php

declare(strict_types=1);

namespace Dintero\Resources;

/**
 * Refunds resource
 */
class Refunds extends BaseResource
{
    /**
     * Create a refund
     */
    public function create(array $data): array
    {
        $response = $this->httpClient->post('/refunds', $this->prepareData($data));
        return $response->json();
    }

    /**
     * Retrieve a refund
     */
    public function get(string $refundId): array
    {
        $response = $this->httpClient->get("/refunds/{$refundId}");
        return $response->json();
    }

    /**
     * List refunds
     */
    public function list(array $params = []): array
    {
        $response = $this->httpClient->get('/refunds', $params);
        return $response->json();
    }

    /**
     * Get all refunds (paginated)
     */
    public function all(array $params = []): \Generator
    {
        return $this->paginate('/refunds', $params);
    }

    /**
     * Refund a transaction
     */
    public function refundTransaction(string $transactionId, ?int $amount = null, ?string $reason = null): array
    {
        $data = [
            'transaction_id' => $transactionId,
        ];

        if ($amount !== null) {
            $data['amount'] = $amount;
        }

        if ($reason !== null) {
            $data['reason'] = $reason;
        }

        return $this->create($data);
    }

    /**
     * Partial refund
     */
    public function partial(string $transactionId, int $amount, array $items = [], ?string $reason = null): array
    {
        $data = [
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'reason' => $reason,
        ];

        if (!empty($items)) {
            $data['items'] = $items;
        }

        return $this->create($data);
    }

    /**
     * Full refund
     */
    public function full(string $transactionId, ?string $reason = null): array
    {
        return $this->refundTransaction($transactionId, null, $reason);
    }

    /**
     * Get refunds for a specific transaction
     */
    public function getByTransaction(string $transactionId): array
    {
        $response = $this->httpClient->get('/refunds', ['transaction_id' => $transactionId]);
        return $response->json();
    }

    /**
     * Get refund status
     */
    public function getStatus(string $refundId): string
    {
        $refund = $this->get($refundId);
        return $refund['status'] ?? 'unknown';
    }

    /**
     * Check if refund is completed
     */
    public function isCompleted(string $refundId): bool
    {
        return $this->getStatus($refundId) === 'completed';
    }

    /**
     * Check if refund is pending
     */
    public function isPending(string $refundId): bool
    {
        return $this->getStatus($refundId) === 'pending';
    }

    /**
     * Check if refund failed
     */
    public function isFailed(string $refundId): bool
    {
        return $this->getStatus($refundId) === 'failed';
    }
}