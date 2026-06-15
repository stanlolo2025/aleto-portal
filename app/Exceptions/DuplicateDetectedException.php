<?php

namespace App\Exceptions;

use Exception;

class DuplicateDetectedException extends Exception
{
    public array $duplicateData;

    public function __construct(string $message, array $duplicateData)
    {
        parent::__construct($message, 409);
        $this->duplicateData = $duplicateData;
    }
}
