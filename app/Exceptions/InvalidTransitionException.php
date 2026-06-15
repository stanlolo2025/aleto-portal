<?php

namespace App\Exceptions;

use Exception;

class InvalidTransitionException extends Exception
{
    public function __construct(string $message = 'Invalid status transition')
    {
        parent::__construct($message, 422);
    }
}
