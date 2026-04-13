<?php
namespace App\Services;

use App\Exceptions\ValidationException;
use App\Repositories\DrinkLogRepository;
use App\Repositories\RankingRepository;

class RankingService
{
    private $drinkLogRepository;
    private $rankingRepository;

    public function __construct()
    {
        $this->drinkLogRepository = new DrinkLogRepository();
        $this->rankingRepository = new RankingRepository();
    }

    public function rankingLastDays(int $days): array
    {
        if ($days <= 0) {
            throw new ValidationException('Invalid days.');
        }
        $from = date('Y-m-d', strtotime("-{$days} days"));
        return $this->drinkLogRepository->lastDays($from);
    }

    public function rankingByDay(string $date): array
    {
        if (!strtotime($date)) {
            throw new ValidationException('Invalid date.');
        }
        return $this->rankingRepository->byDay($date);
    }
}
