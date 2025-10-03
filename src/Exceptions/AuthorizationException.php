<?php

declare(strict_types=1);

namespace Dintero\Exceptions;

/**
 * Authorization exception for insufficient permissions
 */
class AuthorizationException extends DinteroException
{
    public function __construct(string $message = 'Insufficient permissions', int $code = 403, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}