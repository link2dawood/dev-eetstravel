<?php

namespace App\Exceptions;

use Exception;

class BusinessLogicException extends Exception
{
    protected $context;

    public function __construct(string $message = "", array $context = [], int $code = 400, Exception $previous = null)
    {
        $this->context = $context;
        parent::__construct($message, $code, $previous);
    }

    public function getContext(): array
    {
        return $this->context;
    }
}