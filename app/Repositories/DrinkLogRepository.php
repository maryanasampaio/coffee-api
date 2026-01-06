<?php

namespace App\Repositories;
use App\Core\Database;
use PDO;

class DrinkLogRepository
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function store($userId, $date, $quantity)
    {
        $stmt = $this->db->prepare('INSERT INTO drink_logs (user_id, date, quantity) VALUES (:user_id, :date, :quantity)');
        return $stmt->execute([
            'user_id' => $userId,
            'date' => $date,
            'quantity' => $quantity
        ]);
    }

    public function getHistoryByUser($userId)
    {
        $stmt = $this->db->prepare('SELECT date, SUM(quantity) as quantity FROM drink_logs WHERE user_id = :user_id GROUP BY date ORDER BY date DESC');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function lastDays(string $fromDate): array
    {
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
    }
}
