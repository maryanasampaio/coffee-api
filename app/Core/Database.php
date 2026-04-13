<?php

namespace App\Core;

use App\Exceptions\DatabaseException;
use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = require_once __DIR__ . '/../../config/database.php';

            try {
                self::$instance = new PDO(
                    $config['dsn'],
                    $config['user'],
                    $config['password'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                throw new DatabaseException('Database connection failed.');
            }
        }

        return self::$instance;
    }
}
