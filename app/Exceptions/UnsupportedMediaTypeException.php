<?php

namespace App\Exceptions;

class UnsupportedMediaTypeException extends HttpException
{
    public function __construct(string $message = 'Unsupported Media Type')
    {
        parent::__construct($message, 415);
    }
}
