<?php

namespace App\Core;

use App\Exceptions\UnauthorizedException;
use App\Utils\TokenUtil;

class Auth
{
    public static function check(): void
    {
        $headers = getallheaders();
        $auth = $headers['Authorization'] ?? '';

        if (!preg_match('/Bearer\s(.*)/', $auth, $matches)) {
            self::unauthorized();
        }

        $payload = TokenUtil::validateToken($matches[1]);

        if (!$payload) {
            self::unauthorized();
        }

        if (is_object($payload) && isset($payload->sub)) {
            $_REQUEST['auth_user_id'] = (int)$payload->sub;
        } elseif (is_array($payload) && isset($payload['sub'])) {
            $_REQUEST['auth_user_id'] = (int)$payload['sub'];
        }
    }

    private static function unauthorized(): void
    {
        throw new UnauthorizedException();
    }
}
