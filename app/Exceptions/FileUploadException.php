<?php

namespace App\Exceptions;

use Exception;

class FileUploadException extends Exception
{
    protected $fileName;
    protected $fileSize;
    protected $fileType;

    public function __construct(
        string $message = "File upload failed",
        string $fileName = null,
        int $fileSize = null,
        string $fileType = null,
        int $code = 400,
        Exception $previous = null
    ) {
        $this->fileName = $fileName;
        $this->fileSize = $fileSize;
        $this->fileType = $fileType;

        parent::__construct($message, $code, $previous);
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function getFileType(): ?string
    {
        return $this->fileType;
    }
}