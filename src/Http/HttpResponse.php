<?php

declare(strict_types=1);

namespace Dintero\Http;

use Dintero\Contracts\HttpResponseInterface;

/**
 * HTTP Response wrapper
 */
class HttpResponse implements HttpResponseInterface
{
    private int $statusCode;
    private array $headers;
    private string $body;

    public function __construct(int $statusCode, array $headers, string $body)
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function json(): array
    {
        $decoded = json_decode($this->body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON response: ' . json_last_error_msg());
        }
        
        return $decoded ?? [];
    }

    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function isClientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    public function isServerError(): bool
    {
        return $this->statusCode >= 500;
    }
}