<?php

declare(strict_types=1);

namespace Dintero\Exceptions;

/**
 * Validation exception
 */
class ValidationException extends DinteroException
{
    private array $errors = [];

    public function __construct(string $message = '', int $code = 0, ?\Exception $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous, $context);
        $this->errors = $context['errors'] ?? [];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}