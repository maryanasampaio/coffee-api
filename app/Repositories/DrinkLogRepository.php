<?php

namespace App\Repositories;

use App\Core\Database;
use App\Exceptions\DatabaseException;
use PDO;
use Throwable;

class DrinkLogRepository
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function store($userId, $date, $quantity)
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO drink_logs (user_id, date, quantity) VALUES (:user_id, :date, :quantity)');
            return $stmt->execute([
                'user_id' => $userId,
                'date' => $date,
                'quantity' => $quantity
            ]);
        } catch (Throwable $exception) {
            throw new DatabaseException('Failed to store drink log.');
        }
    }

    public function getHistoryByUser($userId)
    {
        try {
            $stmt = $this->db->prepare('SELECT date, SUM(quantity) as quantity FROM drink_logs WHERE user_id = :user_id GROUP BY date ORDER BY date DESC');
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $exception) {
            throw new DatabaseException('Failed to fetch drink history.');
        }
    }


    public function lastDays(string $fromDate): array
    {
        try {
            $stmt = $this->db->prepare("
            SELECT u.name, SUM(dl.quantity) AS quantity
            FROM drink_logs dl
            JOIN users u ON u.id = dl.user_id
            WHERE dl.date >= :from
            GROUP BY u.id, u.name
            ORDER BY quantity DESC
        ");
            $stmt->execute(['from' => $fromDate]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $exception) {
            throw new DatabaseException('Failed to fetch ranking for recent days.');
        }
    }
}
