<?php
namespace App\Utils;

use App\Exceptions\ConfigurationException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenUtil
{
    public static function generateToken($payload)
    {
        $config = self::getConfig();

        return JWT::encode($payload, $config['jwt_key'], $config['jwt_alg']);
    }

    public static function validateToken($token)
    {
        $config = self::getConfig();

        try {
            return JWT::decode($token, new Key($config['jwt_key'], $config['jwt_alg']));
        } catch (\Exception $e) {
            return false;
        }
    }

    private static function getConfig(): array
    {
        $config = require_once __DIR__ . '/../../config/auth.php';

        if (empty($config['jwt_key'])) {
            throw new ConfigurationException('JWT secret is not configured. Define JWT_SECRET in the environment.');
        }

        return $config;
    }
}
