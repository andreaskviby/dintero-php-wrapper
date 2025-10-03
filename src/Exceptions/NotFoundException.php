<?php

declare(strict_types=1);

namespace Dintero\Exceptions;

/**
 * Resource not found exception
 */
class NotFoundException extends DinteroException
{
    public function __construct(string $message = 'Resource not found', int $code = 404, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}