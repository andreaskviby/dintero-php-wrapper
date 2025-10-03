<?php

declare(strict_types=1);

namespace Dintero\Resources;

/**
 * Receipts resource for digital receipt management and purchase history
 */
class Receipts extends BaseResource
{
    /**
     * Create a digital receipt
     */
    public function create(array $data): array
    {
        $response = $this->httpClient->post('/receipts', $this->prepareData($data));
        return $response->json();
    }

    /**
     * Retrieve a receipt
     */
    public function get(string $receiptId): array
    {
        $response = $this->httpClient->get("/receipts/{$receiptId}");
        return $response->json();
    }

    /**
     * Update a receipt
     */
    public function update(string $receiptId, array $data): array
    {
        $response = $this->httpClient->put("/receipts/{$receiptId}", $this->prepareData($data));
        return $response->json();
    }

    /**
     * Delete a receipt
     */
    public function delete(string $receiptId): void
    {
        $this->httpClient->delete("/receipts/{$receiptId}");
    }

    /**
     * List receipts
     */
    public function list(array $params = []): array
    {
        $response = $this->httpClient->get('/receipts', $params);
        return $response->json();
    }

    /**
     * Get all receipts (paginated)
     */
    public function all(array $params = []): \Generator
    {
        return $this->paginate('/receipts', $params);
    }

    /**
     * Get customer receipts
     */
    public function getCustomerReceipts(string $customerId, array $params = []): array
    {
        $response = $this->httpClient->get("/customers/{$customerId}/receipts", $params);
        return $response->json();
    }

    /**
     * Get receipt by transaction
     */
    public function getByTransaction(string $transactionId): array
    {
        $response = $this->httpClient->get('/receipts/by-transaction', ['transaction_id' => $transactionId]);
        return $response->json();
    }

    /**
     * Generate receipt PDF
     */
    public function generatePdf(string $receiptId, array $options = []): array
    {
        $params = [
            'format' => 'pdf',
            'language' => $options['language'] ?? 'en',
            'template' => $options['template'] ?? 'default',
        ];

        $response = $this->httpClient->get("/receipts/{$receiptId}/download", $params);
        return $response->json();
    }

    /**
     * Send receipt via email
     */
    public function sendEmail(string $receiptId, array $emailData): array
    {
        $data = [
            'recipient_email' => $emailData['recipient_email'],
            'subject' => $emailData['subject'] ?? 'Your Receipt',
            'message' => $emailData['message'] ?? null,
            'template' => $emailData['template'] ?? 'default',
        ];

        $response = $this->httpClient->post("/receipts/{$receiptId}/send-email", $data);
        return $response->json();
    }

    /**
     * Send receipt via SMS
     */
    public function sendSms(string $receiptId, array $smsData): array
    {
        $data = [
            'recipient_phone' => $smsData['recipient_phone'],
            'message' => $smsData['message'] ?? 'Your receipt is ready',
        ];

        $response = $this->httpClient->post("/receipts/{$receiptId}/send-sms", $data);
        return $response->json();
    }

    /**
     * Create receipt from transaction
     */
    public function createFromTransaction(string $transactionId, array $options = []): array
    {
        $data = [
            'transaction_id' => $transactionId,
            'include_customer_details' => $options['include_customer_details'] ?? true,
            'include_payment_method' => $options['include_payment_method'] ?? true,
            'template' => $options['template'] ?? 'default',
            'language' => $options['language'] ?? 'en',
            'metadata' => $options['metadata'] ?? [],
        ];

        return $this->create($data);
    }

    /**
     * Get receipt templates
     */
    public function getTemplates(): array
    {
        $response = $this->httpClient->get('/receipts/templates');
        return $response->json();
    }

    /**
     * Create custom receipt template
     */
    public function createTemplate(array $templateData): array
    {
        $response = $this->httpClient->post('/receipts/templates', $templateData);
        return $response->json();
    }

    /**
     * Update receipt template
     */
    public function updateTemplate(string $templateId, array $templateData): array
    {
        $response = $this->httpClient->put("/receipts/templates/{$templateId}", $templateData);
        return $response->json();
    }

    /**
     * Get purchase history for customer
     */
    public function getPurchaseHistory(string $customerId, array $params = []): array
    {
        $response = $this->httpClient->get("/customers/{$customerId}/purchase-history", $params);
        return $response->json();
    }

    /**
     * Search receipts
     */
    public function search(array $criteria): array
    {
        $response = $this->httpClient->post('/receipts/search', $criteria);
        return $response->json();
    }

    /**
     * Get receipt analytics
     */
    public function getAnalytics(array $params = []): array
    {
        $response = $this->httpClient->get('/receipts/analytics', $params);
        return $response->json();
    }

    /**
     * Archive receipt
     */
    public function archive(string $receiptId): array
    {
        $response = $this->httpClient->post("/receipts/{$receiptId}/archive");
        return $response->json();
    }

    /**
     * Unarchive receipt
     */
    public function unarchive(string $receiptId): array
    {
        $response = $this->httpClient->post("/receipts/{$receiptId}/unarchive");
        return $response->json();
    }

    /**
     * Bulk create receipts
     */
    public function bulkCreate(array $receiptsData): array
    {
        $response = $this->httpClient->post('/receipts/bulk', ['receipts' => $receiptsData]);
        return $response->json();
    }

    /**
     * Export receipts
     */
    public function export(array $params = []): array
    {
        $response = $this->httpClient->post('/receipts/export', $params);
        return $response->json();
    }

    /**
     * Get receipt status
     */
    public function getStatus(string $receiptId): string
    {
        $receipt = $this->get($receiptId);
        return $receipt['status'] ?? 'unknown';
    }

    /**
     * Check if receipt is archived
     */
    public function isArchived(string $receiptId): bool
    {
        return $this->getStatus($receiptId) === 'archived';
    }

    /**
     * Get receipt sharing link
     */
    public function getSharingLink(string $receiptId, array $options = []): array
    {
        $params = [
            'expires_at' => $options['expires_at'] ?? date('Y-m-d H:i:s', strtotime('+30 days')),
            'password_protected' => $options['password_protected'] ?? false,
        ];

        $response = $this->httpClient->post("/receipts/{$receiptId}/sharing-link", $params);
        return $response->json();
    }

    /**
     * Validate receipt data
     */
    public function validateReceiptData(array $data): array
    {
        $errors = [];

        if (empty($data['transaction_id']) && empty($data['order_id'])) {
            $errors[] = 'Either transaction_id or order_id is required';
        }

        if (empty($data['items']) || !is_array($data['items'])) {
            $errors[] = 'Items array is required';
        }

        if (isset($data['customer_email']) && !filter_var($data['customer_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid customer email format';
        }

        return $errors;
    }
}