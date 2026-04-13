<?php

namespace App\Core;

use App\Exceptions\ConfigurationException;
use App\Exceptions\HttpException;
use Throwable;

class ExceptionHandler
{
    public static function register(): void
    {
        set_exception_handler([self::class, 'handle']);
    }

    public static function handle(Throwable $exception): void
    {
        if ($exception instanceof HttpException) {
            Response::json(['error' => $exception->getMessage()], $exception->getStatusCode());
        }

        if ($exception instanceof ConfigurationException) {
            Response::json(['error' => $exception->getMessage()], 500);
        }

        error_log($exception);
        Response::json(['error' => 'Internal Server Error'], 500);
    }
}
