<?php

namespace App\Exceptions;

class DatabaseException extends InternalServerErrorException
{
    public function __construct(string $message = 'Database operation failed.')
    {
        parent::__construct($message);
    }
}
