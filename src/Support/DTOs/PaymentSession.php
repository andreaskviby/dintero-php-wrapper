<?php

declare(strict_types=1);

namespace Dintero\Support\DTOs;

/**
 * Payment session data transfer object
 */
class PaymentSession
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly array $order,
        public readonly ?array $customer = null,
        public readonly ?array $url = null,
        public readonly ?string $expiresAt = null,
        public readonly array $metadata = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            status: $data['status'],
            order: $data['order'] ?? [],
            customer: $data['customer'] ?? null,
            url: $data['url'] ?? null,
            expiresAt: $data['expires_at'] ?? null,
            metadata: $data['metadata'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'order' => $this->order,
            'customer' => $this->customer,
            'url' => $this->url,
            'expires_at' => $this->expiresAt,
            'metadata' => $this->metadata,
        ];
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }
}