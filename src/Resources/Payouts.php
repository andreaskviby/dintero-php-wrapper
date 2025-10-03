<?php

declare(strict_types=1);

namespace Dintero\Resources;

/**
 * Payouts resource for split payments and marketplace functionality
 */
class Payouts extends BaseResource
{
    /**
     * Create a payout
     */
    public function create(array $data): array
    {
        $response = $this->httpClient->post('/payouts', $this->prepareData($data));
        return $response->json();
    }

    /**
     * Retrieve a payout
     */
    public function get(string $payoutId): array
    {
        $response = $this->httpClient->get("/payouts/{$payoutId}");
        return $response->json();
    }

    /**
     * List payouts
     */
    public function list(array $params = []): array
    {
        $response = $this->httpClient->get('/payouts', $params);
        return $response->json();
    }

    /**
     * Get all payouts (paginated)
     */
    public function all(array $params = []): \Generator
    {
        return $this->paginate('/payouts', $params);
    }

    /**
     * Cancel a payout
     */
    public function cancel(string $payoutId): array
    {
        $response = $this->httpClient->post("/payouts/{$payoutId}/cancel");
        return $response->json();
    }

    /**
     * Get payout status
     */
    public function getStatus(string $payoutId): string
    {
        $payout = $this->get($payoutId);
        return $payout['status'] ?? 'unknown';
    }

    /**
     * Create split payout for marketplace
     */
    public function createSplit(string $transactionId, array $splits, array $options = []): array
    {
        $data = [
            'transaction_id' => $transactionId,
            'splits' => $splits,
            'currency' => $options['currency'] ?? null,
            'metadata' => $options['metadata'] ?? [],
        ];

        return $this->create($data);
    }

    /**
     * Create payout with recipients
     */
    public function createWithRecipients(array $recipients, array $options = []): array
    {
        $data = [
            'recipients' => $recipients,
            'total_amount' => $options['total_amount'] ?? null,
            'currency' => $options['currency'] ?? 'NOK',
            'description' => $options['description'] ?? null,
            'reference' => $options['reference'] ?? null,
            'metadata' => $options['metadata'] ?? [],
        ];

        return $this->create($data);
    }

    /**
     * Get payout recipients
     */
    public function getRecipients(string $payoutId): array
    {
        $response = $this->httpClient->get("/payouts/{$payoutId}/recipients");
        return $response->json();
    }

    /**
     * Update payout recipient
     */
    public function updateRecipient(string $payoutId, string $recipientId, array $data): array
    {
        $response = $this->httpClient->put("/payouts/{$payoutId}/recipients/{$recipientId}", $data);
        return $response->json();
    }

    /**
     * Get payout reports
     */
    public function getReports(array $params = []): array
    {
        $response = $this->httpClient->get('/payouts/reports', $params);
        return $response->json();
    }

    /**
     * Download payout report
     */
    public function downloadReport(string $reportId, string $format = 'csv'): array
    {
        $response = $this->httpClient->get("/payouts/reports/{$reportId}/download", ['format' => $format]);
        return $response->json();
    }

    /**
     * Get payout schedule
     */
    public function getSchedule(): array
    {
        $response = $this->httpClient->get('/payouts/schedule');
        return $response->json();
    }

    /**
     * Update payout schedule
     */
    public function updateSchedule(array $scheduleData): array
    {
        $response = $this->httpClient->put('/payouts/schedule', $scheduleData);
        return $response->json();
    }

    /**
     * Calculate payout splits
     */
    public function calculateSplits(int $totalAmount, array $splitRules): array
    {
        $splits = [];
        $remainingAmount = $totalAmount;

        foreach ($splitRules as $rule) {
            $amount = 0;
            
            if (isset($rule['percentage'])) {
                $amount = (int) round($totalAmount * ($rule['percentage'] / 100));
            } elseif (isset($rule['amount'])) {
                $amount = $rule['amount'];
            }

            if ($amount > 0) {
                $splits[] = [
                    'recipient_id' => $rule['recipient_id'],
                    'amount' => $amount,
                    'description' => $rule['description'] ?? null,
                    'metadata' => $rule['metadata'] ?? [],
                ];
                $remainingAmount -= $amount;
            }
        }

        // Add remaining amount to platform or specified default recipient
        if ($remainingAmount > 0) {
            $splits[] = [
                'recipient_id' => 'platform',
                'amount' => $remainingAmount,
                'description' => 'Platform fee and remaining amount',
            ];
        }

        return $splits;
    }

    /**
     * Validate payout data
     */
    public function validatePayoutData(array $data): array
    {
        $errors = [];

        if (empty($data['recipients']) && empty($data['splits'])) {
            $errors[] = 'Either recipients or splits must be provided';
        }

        if (isset($data['recipients'])) {
            foreach ($data['recipients'] as $index => $recipient) {
                if (empty($recipient['recipient_id'])) {
                    $errors[] = "Recipient {$index}: recipient_id is required";
                }
                if (empty($recipient['amount']) || $recipient['amount'] <= 0) {
                    $errors[] = "Recipient {$index}: amount must be greater than 0";
                }
            }
        }

        return $errors;
    }
}