<?php

namespace App\Exceptions;

use Exception;

class BusinessRuleException extends Exception
{
    public function __construct(string $message = 'Business rule violation')
    {
        parent::__construct($message, 422);
    }
}
