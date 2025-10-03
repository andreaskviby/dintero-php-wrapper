<?php

declare(strict_types=1);

namespace Dintero\Exceptions;

/**
 * Network exception for connection and network errors
 */
class NetworkException extends DinteroException
{
    public function __construct(string $message = 'Network error occurred', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}