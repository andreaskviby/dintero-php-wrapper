<?php

declare(strict_types=1);

namespace Dintero\Support\DTOs;

/**
 * Order data transfer object
 */
class Order
{
    public function __construct(
        public readonly string $amount,
        public readonly string $currency,
        public readonly array $items = [],
        public readonly ?string $vatAmount = null,
        public readonly ?array $shipping = null,
        public readonly ?array $billing = null,
        public readonly array $metadata = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            amount: $data['amount'],
            currency: $data['currency'],
            items: $data['items'] ?? [],
            vatAmount: $data['vat_amount'] ?? null,
            shipping: $data['shipping'] ?? null,
            billing: $data['billing'] ?? null,
            metadata: $data['metadata'] ?? []
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'amount' => $this->amount,
            'currency' => $this->currency,
            'items' => $this->items,
            'vat_amount' => $this->vatAmount,
            'shipping' => $this->shipping,
            'billing' => $this->billing,
            'metadata' => $this->metadata,
        ], fn($value) => $value !== null && $value !== []);
    }

    public function getFormattedAmount(): string
    {
        return number_format((int)$this->amount / 100, 2) . ' ' . $this->currency;
    }

    public function getTotalItems(): int
    {
        return count($this->items);
    }

    public function addItem(array $item): self
    {
        $items = $this->items;
        $items[] = $item;

        return new self(
            $this->amount,
            $this->currency,
            $items,
            $this->vatAmount,
            $this->shipping,
            $this->billing,
            $this->metadata
        );
    }
}