<?php

namespace App\Exceptions;

use Exception;

class ResourceNotFoundException extends Exception
{
    protected $resourceType;
    protected $resourceId;

    public function __construct(string $resourceType, $resourceId = null, string $message = "", int $code = 404, Exception $previous = null)
    {
        $this->resourceType = $resourceType;
        $this->resourceId = $resourceId;

        if (empty($message)) {
            $message = "The requested {$resourceType}" . ($resourceId ? " with ID {$resourceId}" : "") . " could not be found.";
        }

        parent::__construct($message, $code, $previous);
    }

    public function getResourceType(): string
    {
        return $this->resourceType;
    }

    public function getResourceId()
    {
        return $this->resourceId;
    }
}