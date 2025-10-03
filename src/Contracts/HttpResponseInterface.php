<?php

declare(strict_types=1);

namespace Dintero\Contracts;

interface HttpResponseInterface
{
    public function getStatusCode(): int;
    public function getHeaders(): array;
    public function getBody(): string;
    public function json(): array;
    public function isSuccessful(): bool;
}