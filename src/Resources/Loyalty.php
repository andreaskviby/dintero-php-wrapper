<?php

declare(strict_types=1);

namespace Dintero\Resources;

/**
 * Loyalty resource for discounts, coupons, and loyalty programs
 */
class Loyalty extends BaseResource
{
    /**
     * Create a discount code
     */
    public function createDiscount(array $data): array
    {
        $response = $this->httpClient->post('/loyalty/discounts', $this->prepareData($data));
        return $response->json();
    }

    /**
     * Retrieve a discount
     */
    public function getDiscount(string $discountId): array
    {
        $response = $this->httpClient->get("/loyalty/discounts/{$discountId}");
        return $response->json();
    }

    /**
     * Update a discount
     */
    public function updateDiscount(string $discountId, array $data): array
    {
        $response = $this->httpClient->put("/loyalty/discounts/{$discountId}", $this->prepareData($data));
        return $response->json();
    }

    /**
     * Delete a discount
     */
    public function deleteDiscount(string $discountId): void
    {
        $this->httpClient->delete("/loyalty/discounts/{$discountId}");
    }

    /**
     * List discounts
     */
    public function listDiscounts(array $params = []): array
    {
        $response = $this->httpClient->get('/loyalty/discounts', $params);
        return $response->json();
    }

    /**
     * Validate discount code
     */
    public function validateDiscount(string $code, array $orderData): array
    {
        $data = [
            'code' => $code,
            'order' => $orderData,
        ];

        $response = $this->httpClient->post('/loyalty/discounts/validate', $data);
        return $response->json();
    }

    /**
     * Apply discount to order
     */
    public function applyDiscount(string $code, array $orderData): array
    {
        $data = [
            'code' => $code,
            'order' => $orderData,
        ];

        $response = $this->httpClient->post('/loyalty/discounts/apply', $data);
        return $response->json();
    }

    /**
     * Create a loyalty program
     */
    public function createProgram(array $data): array
    {
        $response = $this->httpClient->post('/loyalty/programs', $this->prepareData($data));
        return $response->json();
    }

    /**
     * Retrieve a loyalty program
     */
    public function getProgram(string $programId): array
    {
        $response = $this->httpClient->get("/loyalty/programs/{$programId}");
        return $response->json();
    }

    /**
     * Update a loyalty program
     */
    public function updateProgram(string $programId, array $data): array
    {
        $response = $this->httpClient->put("/loyalty/programs/{$programId}", $this->prepareData($data));
        return $response->json();
    }

    /**
     * Delete a loyalty program
     */
    public function deleteProgram(string $programId): void
    {
        $this->httpClient->delete("/loyalty/programs/{$programId}");
    }

    /**
     * List loyalty programs
     */
    public function listPrograms(array $params = []): array
    {
        $response = $this->httpClient->get('/loyalty/programs', $params);
        return $response->json();
    }

    /**
     * Enroll customer in loyalty program
     */
    public function enrollCustomer(string $programId, string $customerId, array $options = []): array
    {
        $data = [
            'customer_id' => $customerId,
            'enrollment_date' => $options['enrollment_date'] ?? date('Y-m-d H:i:s'),
            'metadata' => $options['metadata'] ?? [],
        ];

        $response = $this->httpClient->post("/loyalty/programs/{$programId}/enroll", $data);
        return $response->json();
    }

    /**
     * Unenroll customer from loyalty program
     */
    public function unenrollCustomer(string $programId, string $customerId): array
    {
        $data = ['customer_id' => $customerId];

        $response = $this->httpClient->post("/loyalty/programs/{$programId}/unenroll", $data);
        return $response->json();
    }

    /**
     * Get customer loyalty status
     */
    public function getCustomerLoyalty(string $customerId, ?string $programId = null): array
    {
        $params = [];
        if ($programId) {
            $params['program_id'] = $programId;
        }

        $response = $this->httpClient->get("/loyalty/customers/{$customerId}", $params);
        return $response->json();
    }

    /**
     * Award loyalty points
     */
    public function awardPoints(string $customerId, int $points, array $options = []): array
    {
        $data = [
            'customer_id' => $customerId,
            'points' => $points,
            'reason' => $options['reason'] ?? 'Points awarded',
            'transaction_id' => $options['transaction_id'] ?? null,
            'expires_at' => $options['expires_at'] ?? null,
            'metadata' => $options['metadata'] ?? [],
        ];

        $response = $this->httpClient->post('/loyalty/points/award', $data);
        return $response->json();
    }

    /**
     * Redeem loyalty points
     */
    public function redeemPoints(string $customerId, int $points, array $options = []): array
    {
        $data = [
            'customer_id' => $customerId,
            'points' => $points,
            'reason' => $options['reason'] ?? 'Points redeemed',
            'order_id' => $options['order_id'] ?? null,
            'metadata' => $options['metadata'] ?? [],
        ];

        $response = $this->httpClient->post('/loyalty/points/redeem', $data);
        return $response->json();
    }

    /**
     * Get customer points balance
     */
    public function getPointsBalance(string $customerId): array
    {
        $response = $this->httpClient->get("/loyalty/customers/{$customerId}/points");
        return $response->json();
    }

    /**
     * Get customer points history
     */
    public function getPointsHistory(string $customerId, array $params = []): array
    {
        $response = $this->httpClient->get("/loyalty/customers/{$customerId}/points/history", $params);
        return $response->json();
    }

    /**
     * Create stamp card
     */
    public function createStampCard(array $data): array
    {
        $response = $this->httpClient->post('/loyalty/stamp-cards', $this->prepareData($data));
        return $response->json();
    }

    /**
     * Get stamp card
     */
    public function getStampCard(string $cardId): array
    {
        $response = $this->httpClient->get("/loyalty/stamp-cards/{$cardId}");
        return $response->json();
    }

    /**
     * Add stamp to card
     */
    public function addStamp(string $cardId, array $options = []): array
    {
        $data = [
            'transaction_id' => $options['transaction_id'] ?? null,
            'location' => $options['location'] ?? null,
            'metadata' => $options['metadata'] ?? [],
        ];

        $response = $this->httpClient->post("/loyalty/stamp-cards/{$cardId}/stamp", $data);
        return $response->json();
    }

    /**
     * Redeem stamp card reward
     */
    public function redeemStampCard(string $cardId): array
    {
        $response = $this->httpClient->post("/loyalty/stamp-cards/{$cardId}/redeem");
        return $response->json();
    }

    /**
     * Get customer stamp cards
     */
    public function getCustomerStampCards(string $customerId): array
    {
        $response = $this->httpClient->get("/loyalty/customers/{$customerId}/stamp-cards");
        return $response->json();
    }

    /**
     * Create coupon campaign
     */
    public function createCampaign(array $data): array
    {
        $response = $this->httpClient->post('/loyalty/campaigns', $this->prepareData($data));
        return $response->json();
    }

    /**
     * Get campaign
     */
    public function getCampaign(string $campaignId): array
    {
        $response = $this->httpClient->get("/loyalty/campaigns/{$campaignId}");
        return $response->json();
    }

    /**
     * Update campaign
     */
    public function updateCampaign(string $campaignId, array $data): array
    {
        $response = $this->httpClient->put("/loyalty/campaigns/{$campaignId}", $this->prepareData($data));
        return $response->json();
    }

    /**
     * List campaigns
     */
    public function listCampaigns(array $params = []): array
    {
        $response = $this->httpClient->get('/loyalty/campaigns', $params);
        return $response->json();
    }

    /**
     * Generate coupon codes
     */
    public function generateCoupons(string $campaignId, int $quantity, array $options = []): array
    {
        $data = [
            'quantity' => $quantity,
            'prefix' => $options['prefix'] ?? null,
            'length' => $options['length'] ?? 8,
            'expires_at' => $options['expires_at'] ?? null,
        ];

        $response = $this->httpClient->post("/loyalty/campaigns/{$campaignId}/generate", $data);
        return $response->json();
    }

    /**
     * Get campaign analytics
     */
    public function getCampaignAnalytics(string $campaignId, array $params = []): array
    {
        $response = $this->httpClient->get("/loyalty/campaigns/{$campaignId}/analytics", $params);
        return $response->json();
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(array $discountRules, array $orderData): int
    {
        $discountAmount = 0;
        $orderTotal = $orderData['amount'] ?? 0;

        foreach ($discountRules as $rule) {
            switch ($rule['type']) {
                case 'percentage':
                    $discountAmount += (int) round($orderTotal * ($rule['value'] / 100));
                    break;
                case 'fixed_amount':
                    $discountAmount += $rule['value'];
                    break;
                case 'buy_x_get_y':
                    // Custom logic for buy X get Y offers
                    $discountAmount += $this->calculateBuyXGetY($rule, $orderData);
                    break;
            }
        }

        return min($discountAmount, $orderTotal);
    }

    /**
     * Calculate buy X get Y discount
     */
    private function calculateBuyXGetY(array $rule, array $orderData): int
    {
        $items = $orderData['items'] ?? [];
        $buyQuantity = $rule['buy_quantity'] ?? 1;
        $getQuantity = $rule['get_quantity'] ?? 1;
        $discountAmount = 0;

        foreach ($items as $item) {
            if (isset($rule['product_id']) && $item['product_id'] !== $rule['product_id']) {
                continue;
            }

            $quantity = $item['quantity'] ?? 1;
            $freeItems = intval($quantity / $buyQuantity) * $getQuantity;
            $discountAmount += $freeItems * ($item['price'] ?? 0);
        }

        return $discountAmount;
    }

    /**
     * Get loyalty analytics
     */
    public function getAnalytics(array $params = []): array
    {
        $response = $this->httpClient->get('/loyalty/analytics', $params);
        return $response->json();
    }

    /**
     * Export loyalty data
     */
    public function exportData(array $params = []): array
    {
        $response = $this->httpClient->post('/loyalty/export', $params);
        return $response->json();
    }
}