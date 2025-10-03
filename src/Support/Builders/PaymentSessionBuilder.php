<?php

declare(strict_types=1);

namespace Dintero\Support\Builders;

use Dintero\Support\DTOs\Order;

/**
 * Payment session builder for fluent API
 */
class PaymentSessionBuilder
{
    private array $data = [];

    public function __construct()
    {
        $this->data = [
            'url' => [],
            'order' => [],
            'customer' => [],
            'metadata' => [],
        ];
    }

    public function withReturnUrl(string $url): self
    {
        $this->data['url']['return_url'] = $url;
        return $this;
    }

    public function withCallbackUrl(string $url): self
    {
        $this->data['url']['callback_url'] = $url;
        return $this;
    }

    public function withOrder(Order $order): self
    {
        $this->data['order'] = $order->toArray();
        return $this;
    }

    public function withOrderData(array $orderData): self
    {
        $this->data['order'] = $orderData;
        return $this;
    }

    public function withCustomer(array $customerData): self
    {
        $this->data['customer'] = $customerData;
        return $this;
    }

    public function withCustomerEmail(string $email): self
    {
        $this->data['customer']['email'] = $email;
        return $this;
    }

    public function withCustomerName(string $firstName, string $lastName): self
    {
        $this->data['customer']['first_name'] = $firstName;
        $this->data['customer']['last_name'] = $lastName;
        return $this;
    }

    public function withProfileId(string $profileId): self
    {
        $this->data['profile_id'] = $profileId;
        return $this;
    }

    public function withExpiresAt(string $expiresAt): self
    {
        $this->data['expires_at'] = $expiresAt;
        return $this;
    }

    public function withMetadata(array $metadata): self
    {
        $this->data['metadata'] = array_merge($this->data['metadata'], $metadata);
        return $this;
    }

    public function withMetadataItem(string $key, mixed $value): self
    {
        $this->data['metadata'][$key] = $value;
        return $this;
    }

    public function build(): array
    {
        return array_filter($this->data, function ($value) {
            return $value !== null && $value !== [] && $value !== '';
        });
    }
}