<?php

namespace App\Exceptions;

use Exception;

class RecordNotFoundException extends Exception
{
    public function __construct(string $message = 'Record not found')
    {
        parent::__construct($message, 404);
    }
}
