<?php

declare(strict_types=1);

namespace Dintero\Resources;

/**
 * Transactions resource for direct transaction management
 */
class Transactions extends BaseResource
{
    /**
     * Retrieve a transaction
     */
    public function get(string $transactionId): array
    {
        $response = $this->httpClient->get("/transactions/{$transactionId}");
        return $response->json();
    }

    /**
     * List transactions
     */
    public function list(array $params = []): array
    {
        $response = $this->httpClient->get('/transactions', $params);
        return $response->json();
    }

    /**
     * Get all transactions (paginated)
     */
    public function all(array $params = []): \Generator
    {
        return $this->paginate('/transactions', $params);
    }

    /**
     * Capture a transaction
     */
    public function capture(string $transactionId, array $data = []): array
    {
        $response = $this->httpClient->post("/transactions/{$transactionId}/capture", $this->prepareData($data));
        return $response->json();
    }

    /**
     * Void a transaction
     */
    public function void(string $transactionId, ?string $reason = null): array
    {
        $data = [];
        if ($reason) {
            $data['reason'] = $reason;
        }
        
        $response = $this->httpClient->post("/transactions/{$transactionId}/void", $data);
        return $response->json();
    }

    /**
     * Cancel a transaction
     */
    public function cancel(string $transactionId, ?string $reason = null): array
    {
        $data = [];
        if ($reason) {
            $data['reason'] = $reason;
        }
        
        $response = $this->httpClient->post("/transactions/{$transactionId}/cancel", $data);
        return $response->json();
    }

    /**
     * Get transaction events
     */
    public function getEvents(string $transactionId): array
    {
        $response = $this->httpClient->get("/transactions/{$transactionId}/events");
        return $response->json();
    }

    /**
     * Get transaction refunds
     */
    public function getRefunds(string $transactionId): array
    {
        $response = $this->httpClient->get("/transactions/{$transactionId}/refunds");
        return $response->json();
    }

    /**
     * Get transaction status
     */
    public function getStatus(string $transactionId): string
    {
        $transaction = $this->get($transactionId);
        return $transaction['status'] ?? 'unknown';
    }

    /**
     * Check if transaction is successful
     */
    public function isSuccessful(string $transactionId): bool
    {
        return in_array($this->getStatus($transactionId), ['completed', 'captured']);
    }

    /**
     * Check if transaction is pending
     */
    public function isPending(string $transactionId): bool
    {
        return in_array($this->getStatus($transactionId), ['pending', 'authorized']);
    }

    /**
     * Check if transaction is failed
     */
    public function isFailed(string $transactionId): bool
    {
        return in_array($this->getStatus($transactionId), ['failed', 'declined', 'error']);
    }

    /**
     * Search transactions
     */
    public function search(array $criteria): array
    {
        $response = $this->httpClient->post('/transactions/search', $criteria);
        return $response->json();
    }

    /**
     * Get transaction summary
     */
    public function getSummary(array $filters = []): array
    {
        $response = $this->httpClient->get('/transactions/summary', $filters);
        return $response->json();
    }

    /**
     * Export transactions
     */
    public function export(array $params = []): array
    {
        $response = $this->httpClient->post('/transactions/export', $params);
        return $response->json();
    }

    /**
     * Get transaction receipt
     */
    public function getReceipt(string $transactionId, string $format = 'pdf'): array
    {
        $response = $this->httpClient->get("/transactions/{$transactionId}/receipt", ['format' => $format]);
        return $response->json();
    }

    /**
     * Update transaction metadata
     */
    public function updateMetadata(string $transactionId, array $metadata): array
    {
        $response = $this->httpClient->put("/transactions/{$transactionId}/metadata", ['metadata' => $metadata]);
        return $response->json();
    }

    /**
     * Get transaction fees
     */
    public function getFees(string $transactionId): array
    {
        $response = $this->httpClient->get("/transactions/{$transactionId}/fees");
        return $response->json();
    }

    /**
     * Calculate transaction fees
     */
    public function calculateFees(array $transactionData): array
    {
        $response = $this->httpClient->post('/transactions/calculate-fees', $transactionData);
        return $response->json();
    }

    /**
     * Get supported payment methods for amount and currency
     */
    public function getSupportedPaymentMethods(int $amount, string $currency = 'NOK', ?string $country = null): array
    {
        $params = [
            'amount' => $amount,
            'currency' => $currency,
        ];
        
        if ($country) {
            $params['country'] = $country;
        }
        
        $response = $this->httpClient->get('/transactions/payment-methods', $params);
        return $response->json();
    }

    /**
     * Partial capture
     */
    public function partialCapture(string $transactionId, int $amount, array $items = []): array
    {
        $data = [
            'amount' => $amount,
        ];
        
        if (!empty($items)) {
            $data['items'] = $items;
        }
        
        return $this->capture($transactionId, $data);
    }

    /**
     * Full capture
     */
    public function fullCapture(string $transactionId): array
    {
        return $this->capture($transactionId);
    }

    /**
     * Get transaction analytics
     */
    public function getAnalytics(array $params = []): array
    {
        $response = $this->httpClient->get('/transactions/analytics', $params);
        return $response->json();
    }
}