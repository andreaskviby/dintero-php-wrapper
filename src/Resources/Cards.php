<?php

declare(strict_types=1);

namespace Dintero\Resources;

/**
 * Cards resource for virtual cards, gift cards, and wallet functionality
 */
class Cards extends BaseResource
{
    /**
     * Create a virtual card
     */
    public function create(array $data): array
    {
        $response = $this->httpClient->post('/cards', $this->prepareData($data));
        return $response->json();
    }

    /**
     * Retrieve a card
     */
    public function get(string $cardId): array
    {
        $response = $this->httpClient->get("/cards/{$cardId}");
        return $response->json();
    }

    /**
     * Update a card
     */
    public function update(string $cardId, array $data): array
    {
        $response = $this->httpClient->put("/cards/{$cardId}", $this->prepareData($data));
        return $response->json();
    }

    /**
     * Activate a card
     */
    public function activate(string $cardId, array $activationData = []): array
    {
        $response = $this->httpClient->post("/cards/{$cardId}/activate", $activationData);
        return $response->json();
    }

    /**
     * Deactivate a card
     */
    public function deactivate(string $cardId, ?string $reason = null): array
    {
        $data = [];
        if ($reason) {
            $data['reason'] = $reason;
        }

        $response = $this->httpClient->post("/cards/{$cardId}/deactivate", $data);
        return $response->json();
    }

    /**
     * Block a card
     */
    public function block(string $cardId, ?string $reason = null): array
    {
        $data = [];
        if ($reason) {
            $data['reason'] = $reason;
        }

        $response = $this->httpClient->post("/cards/{$cardId}/block", $data);
        return $response->json();
    }

    /**
     * Unblock a card
     */
    public function unblock(string $cardId): array
    {
        $response = $this->httpClient->post("/cards/{$cardId}/unblock");
        return $response->json();
    }

    /**
     * List cards
     */
    public function list(array $params = []): array
    {
        $response = $this->httpClient->get('/cards', $params);
        return $response->json();
    }

    /**
     * Get all cards (paginated)
     */
    public function all(array $params = []): \Generator
    {
        return $this->paginate('/cards', $params);
    }

    /**
     * Get card balance
     */
    public function getBalance(string $cardId): array
    {
        $response = $this->httpClient->get("/cards/{$cardId}/balance");
        return $response->json();
    }

    /**
     * Load balance to card
     */
    public function loadBalance(string $cardId, int $amount, array $options = []): array
    {
        $data = [
            'amount' => $amount,
            'currency' => $options['currency'] ?? 'NOK',
            'reference' => $options['reference'] ?? null,
            'metadata' => $options['metadata'] ?? [],
        ];

        $response = $this->httpClient->post("/cards/{$cardId}/load", $data);
        return $response->json();
    }

    /**
     * Reserve amount on card
     */
    public function reserve(string $cardId, int $amount, array $options = []): array
    {
        $data = [
            'amount' => $amount,
            'currency' => $options['currency'] ?? 'NOK',
            'reference' => $options['reference'] ?? null,
            'expires_at' => $options['expires_at'] ?? null,
            'metadata' => $options['metadata'] ?? [],
        ];

        $response = $this->httpClient->post("/cards/{$cardId}/reserve", $data);
        return $response->json();
    }

    /**
     * Capture reserved amount
     */
    public function capture(string $cardId, string $reservationId, ?int $amount = null): array
    {
        $data = ['reservation_id' => $reservationId];
        
        if ($amount !== null) {
            $data['amount'] = $amount;
        }

        $response = $this->httpClient->post("/cards/{$cardId}/capture", $data);
        return $response->json();
    }

    /**
     * Void reservation
     */
    public function voidReservation(string $cardId, string $reservationId): array
    {
        $data = ['reservation_id' => $reservationId];

        $response = $this->httpClient->post("/cards/{$cardId}/void", $data);
        return $response->json();
    }

    /**
     * Get card transactions
     */
    public function getTransactions(string $cardId, array $params = []): array
    {
        $response = $this->httpClient->get("/cards/{$cardId}/transactions", $params);
        return $response->json();
    }

    /**
     * Create gift card
     */
    public function createGiftCard(int $amount, array $options = []): array
    {
        $data = [
            'type' => 'gift_card',
            'amount' => $amount,
            'currency' => $options['currency'] ?? 'NOK',
            'expires_at' => $options['expires_at'] ?? null,
            'recipient_email' => $options['recipient_email'] ?? null,
            'recipient_name' => $options['recipient_name'] ?? null,
            'message' => $options['message'] ?? null,
            'design_template' => $options['design_template'] ?? null,
            'metadata' => $options['metadata'] ?? [],
        ];

        return $this->create($data);
    }

    /**
     * Create voucher
     */
    public function createVoucher(array $voucherData): array
    {
        $data = array_merge([
            'type' => 'voucher',
        ], $voucherData);

        return $this->create($data);
    }

    /**
     * Redeem card/voucher
     */
    public function redeem(string $cardCode, int $amount, array $options = []): array
    {
        $data = [
            'card_code' => $cardCode,
            'amount' => $amount,
            'currency' => $options['currency'] ?? 'NOK',
            'metadata' => $options['metadata'] ?? [],
        ];

        $response = $this->httpClient->post('/cards/redeem', $data);
        return $response->json();
    }

    /**
     * Get card by code
     */
    public function getByCode(string $cardCode): array
    {
        $response = $this->httpClient->get('/cards/by-code', ['card_code' => $cardCode]);
        return $response->json();
    }

    /**
     * Validate card code
     */
    public function validateCode(string $cardCode): array
    {
        $response = $this->httpClient->post('/cards/validate', ['card_code' => $cardCode]);
        return $response->json();
    }

    /**
     * Get card status
     */
    public function getStatus(string $cardId): string
    {
        $card = $this->get($cardId);
        return $card['status'] ?? 'unknown';
    }

    /**
     * Check if card is active
     */
    public function isActive(string $cardId): bool
    {
        return $this->getStatus($cardId) === 'active';
    }

    /**
     * Check if card is blocked
     */
    public function isBlocked(string $cardId): bool
    {
        return $this->getStatus($cardId) === 'blocked';
    }

    /**
     * Check if card is expired
     */
    public function isExpired(string $cardId): bool
    {
        $card = $this->get($cardId);
        
        if (isset($card['expires_at'])) {
            return strtotime($card['expires_at']) < time();
        }
        
        return false;
    }

    /**
     * Set card PIN
     */
    public function setPin(string $cardId, string $pin): array
    {
        $data = ['pin' => $pin];

        $response = $this->httpClient->post("/cards/{$cardId}/pin", $data);
        return $response->json();
    }

    /**
     * Change card PIN
     */
    public function changePin(string $cardId, string $currentPin, string $newPin): array
    {
        $data = [
            'current_pin' => $currentPin,
            'new_pin' => $newPin,
        ];

        $response = $this->httpClient->put("/cards/{$cardId}/pin", $data);
        return $response->json();
    }

    /**
     * Reset card PIN
     */
    public function resetPin(string $cardId): array
    {
        $response = $this->httpClient->delete("/cards/{$cardId}/pin");
        return $response->json();
    }

    /**
     * Get card limits
     */
    public function getLimits(string $cardId): array
    {
        $response = $this->httpClient->get("/cards/{$cardId}/limits");
        return $response->json();
    }

    /**
     * Set card limits
     */
    public function setLimits(string $cardId, array $limits): array
    {
        $response = $this->httpClient->put("/cards/{$cardId}/limits", $limits);
        return $response->json();
    }

    /**
     * Generate card statement
     */
    public function generateStatement(string $cardId, array $params = []): array
    {
        $response = $this->httpClient->post("/cards/{$cardId}/statement", $params);
        return $response->json();
    }

    /**
     * Export card data
     */
    public function exportData(string $cardId, array $params = []): array
    {
        $response = $this->httpClient->post("/cards/{$cardId}/export", $params);
        return $response->json();
    }

    /**
     * Bulk create cards
     */
    public function bulkCreate(array $cardsData): array
    {
        $response = $this->httpClient->post('/cards/bulk', ['cards' => $cardsData]);
        return $response->json();
    }

    /**
     * Get card analytics
     */
    public function getAnalytics(array $params = []): array
    {
        $response = $this->httpClient->get('/cards/analytics', $params);
        return $response->json();
    }
}