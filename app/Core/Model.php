<?php
namespace App\Core;

use PDO;

class Model
{
	protected static $db;

	public function __construct()
	{
		if (!self::$db) {
			$config = require __DIR__ . '/../../config/database.php';
			self::$db = new PDO($config['dsn'], $config['user'], $config['password']);
			self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
	}

	protected function db()
	{
		return self::$db;
	}
}
