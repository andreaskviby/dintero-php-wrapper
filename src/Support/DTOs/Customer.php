<?php

declare(strict_types=1);

namespace Dintero\Support\DTOs;

/**
 * Customer data transfer object
 */
class Customer
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $email = null,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
        public readonly ?string $phoneNumber = null,
        public readonly ?array $address = null,
        public readonly array $metadata = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            email: $data['email'] ?? null,
            firstName: $data['first_name'] ?? null,
            lastName: $data['last_name'] ?? null,
            phoneNumber: $data['phone_number'] ?? null,
            address: $data['address'] ?? null,
            metadata: $data['metadata'] ?? []
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'email' => $this->email,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'phone_number' => $this->phoneNumber,
            'address' => $this->address,
            'metadata' => $this->metadata,
        ], fn($value) => $value !== null);
    }

    public function getFullName(): ?string
    {
        if ($this->firstName && $this->lastName) {
            return trim($this->firstName . ' ' . $this->lastName);
        }

        return $this->firstName ?? $this->lastName;
    }
}