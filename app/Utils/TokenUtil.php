<?php
namespace App\Utils;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenUtil
{
    private static $key = 'your_secret_key';
    private static $alg = 'HS256';

    public static function generateToken($payload)
    {
        return JWT::encode($payload, self::$key, self::$alg);
    }

    public static function validateToken($token)
    {
        try {
            return JWT::decode($token, new Key(self::$key, self::$alg));
        } catch (\Exception $e) {
            return false;
        }
    }
}
