<?php
namespace App\Repositories;

use App\Core\Database;
use App\Exceptions\DatabaseException;
use PDO;
use Throwable;

class RankingRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function byDay(string $date): array
    {
        try {
            $stmt = $this->db->prepare("
            SELECT u.name, SUM(dl.quantity) AS quantity
            FROM drink_logs dl
            JOIN users u ON u.id = dl.user_id
            WHERE dl.date = :date
            GROUP BY u.id, u.name
            ORDER BY quantity DESC
        ");
            $stmt->execute(['date' => $date]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $exception) {
            throw new DatabaseException('Failed to fetch ranking by day.');
        }
    }
}
