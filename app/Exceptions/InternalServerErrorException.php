<?php

namespace App\Exceptions;

class InternalServerErrorException extends HttpException
{
    public function __construct(string $message = 'Internal Server Error')
    {
        parent::__construct($message, 500);
    }
}
