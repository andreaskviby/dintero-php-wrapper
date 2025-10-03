<?php

declare(strict_types=1);

namespace Dintero\Exceptions;

use Exception;

/**
 * Base Dintero exception
 */
class DinteroException extends Exception
{
    protected array $context = [];

    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}