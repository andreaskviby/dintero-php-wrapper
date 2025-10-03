<?php

declare(strict_types=1);

namespace Dintero\Resources;

/**
 * Payment Links resource for generating payment URLs and QR codes
 */
class PaymentLinks extends BaseResource
{
    /**
     * Create a payment link
     */
    public function create(array $data): array
    {
        $response = $this->httpClient->post('/payment-links', $this->prepareData($data));
        return $response->json();
    }

    /**
     * Retrieve a payment link
     */
    public function get(string $linkId): array
    {
        $response = $this->httpClient->get("/payment-links/{$linkId}");
        return $response->json();
    }

    /**
     * Update a payment link
     */
    public function update(string $linkId, array $data): array
    {
        $response = $this->httpClient->put("/payment-links/{$linkId}", $this->prepareData($data));
        return $response->json();
    }

    /**
     * Delete a payment link
     */
    public function delete(string $linkId): void
    {
        $this->httpClient->delete("/payment-links/{$linkId}");
    }

    /**
     * List payment links
     */
    public function list(array $params = []): array
    {
        $response = $this->httpClient->get('/payment-links', $params);
        return $response->json();
    }

    /**
     * Get all payment links (paginated)
     */
    public function all(array $params = []): \Generator
    {
        return $this->paginate('/payment-links', $params);
    }

    /**
     * Activate a payment link
     */
    public function activate(string $linkId): array
    {
        $response = $this->httpClient->post("/payment-links/{$linkId}/activate");
        return $response->json();
    }

    /**
     * Deactivate a payment link
     */
    public function deactivate(string $linkId): array
    {
        $response = $this->httpClient->post("/payment-links/{$linkId}/deactivate");
        return $response->json();
    }

    /**
     * Get payment link QR code
     */
    public function getQrCode(string $linkId, array $options = []): array
    {
        $params = [
            'size' => $options['size'] ?? '200x200',
            'format' => $options['format'] ?? 'png',
        ];
        
        $response = $this->httpClient->get("/payment-links/{$linkId}/qr-code", $params);
        return $response->json();
    }

    /**
     * Get payment link analytics
     */
    public function getAnalytics(string $linkId, array $params = []): array
    {
        $response = $this->httpClient->get("/payment-links/{$linkId}/analytics", $params);
        return $response->json();
    }

    /**
     * Get payment link transactions
     */
    public function getTransactions(string $linkId, array $params = []): array
    {
        $response = $this->httpClient->get("/payment-links/{$linkId}/transactions", $params);
        return $response->json();
    }

    /**
     * Create quick payment link
     */
    public function createQuick(int $amount, string $currency = 'NOK', array $options = []): array
    {
        $data = [
            'amount' => $amount,
            'currency' => $currency,
            'description' => $options['description'] ?? "Payment of {$amount} {$currency}",
            'expires_at' => $options['expires_at'] ?? null,
            'metadata' => $options['metadata'] ?? [],
        ];

        return $this->create($data);
    }

    /**
     * Create payment link with items
     */
    public function createWithItems(array $items, array $options = []): array
    {
        $totalAmount = array_sum(array_column($items, 'amount'));
        
        $data = [
            'amount' => $totalAmount,
            'currency' => $options['currency'] ?? 'NOK',
            'items' => $items,
            'description' => $options['description'] ?? 'Payment for multiple items',
            'return_url' => $options['return_url'] ?? null,
            'callback_url' => $options['callback_url'] ?? null,
            'expires_at' => $options['expires_at'] ?? null,
            'metadata' => $options['metadata'] ?? [],
        ];

        return $this->create($data);
    }

    /**
     * Create recurring payment link
     */
    public function createRecurring(array $recurringData, array $options = []): array
    {
        $data = [
            'amount' => $recurringData['amount'],
            'currency' => $recurringData['currency'] ?? 'NOK',
            'recurring' => [
                'interval' => $recurringData['interval'], // daily, weekly, monthly, yearly
                'interval_count' => $recurringData['interval_count'] ?? 1,
                'trial_period_days' => $recurringData['trial_period_days'] ?? null,
                'end_date' => $recurringData['end_date'] ?? null,
            ],
            'description' => $options['description'] ?? 'Recurring payment',
            'return_url' => $options['return_url'] ?? null,
            'callback_url' => $options['callback_url'] ?? null,
            'metadata' => $options['metadata'] ?? [],
        ];

        return $this->create($data);
    }

    /**
     * Create payment link for donation
     */
    public function createDonation(array $suggestedAmounts, array $options = []): array
    {
        $data = [
            'type' => 'donation',
            'suggested_amounts' => $suggestedAmounts,
            'currency' => $options['currency'] ?? 'NOK',
            'allow_custom_amount' => $options['allow_custom_amount'] ?? true,
            'min_amount' => $options['min_amount'] ?? null,
            'max_amount' => $options['max_amount'] ?? null,
            'description' => $options['description'] ?? 'Donation',
            'return_url' => $options['return_url'] ?? null,
            'callback_url' => $options['callback_url'] ?? null,
            'metadata' => $options['metadata'] ?? [],
        ];

        return $this->create($data);
    }

    /**
     * Share payment link via email
     */
    public function shareViaEmail(string $linkId, array $emailData): array
    {
        $data = [
            'recipient_email' => $emailData['recipient_email'],
            'subject' => $emailData['subject'] ?? 'Payment Request',
            'message' => $emailData['message'] ?? null,
            'sender_name' => $emailData['sender_name'] ?? null,
        ];

        $response = $this->httpClient->post("/payment-links/{$linkId}/share/email", $data);
        return $response->json();
    }

    /**
     * Share payment link via SMS
     */
    public function shareViaSms(string $linkId, array $smsData): array
    {
        $data = [
            'recipient_phone' => $smsData['recipient_phone'],
            'message' => $smsData['message'] ?? 'You have a payment request',
        ];

        $response = $this->httpClient->post("/payment-links/{$linkId}/share/sms", $data);
        return $response->json();
    }

    /**
     * Get payment link status
     */
    public function getStatus(string $linkId): string
    {
        $link = $this->get($linkId);
        return $link['status'] ?? 'unknown';
    }

    /**
     * Check if payment link is active
     */
    public function isActive(string $linkId): bool
    {
        return $this->getStatus($linkId) === 'active';
    }

    /**
     * Check if payment link is expired
     */
    public function isExpired(string $linkId): bool
    {
        $link = $this->get($linkId);
        
        if (isset($link['expires_at'])) {
            return strtotime($link['expires_at']) < time();
        }
        
        return false;
    }

    /**
     * Generate short URL for payment link
     */
    public function generateShortUrl(string $linkId): array
    {
        $response = $this->httpClient->post("/payment-links/{$linkId}/short-url");
        return $response->json();
    }

    /**
     * Customize payment link appearance
     */
    public function customize(string $linkId, array $customization): array
    {
        $response = $this->httpClient->put("/payment-links/{$linkId}/customize", $customization);
        return $response->json();
    }
}