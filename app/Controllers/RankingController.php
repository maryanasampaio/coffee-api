<?php
namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Core\Request;
use App\Core\Response;
use App\Services\RankingService;

class RankingController
{
    private $rankingService;

    public function __construct()
    {
        $this->rankingService = new RankingService();
    }

    public function lastDays(Request $request)
    {
        $days = (int)$request->getQueryParam('days', 7);
        if ($days <= 0) {
            throw new ValidationException('Invalid days.');
        }

        $result = $this->rankingService->rankingLastDays($days);

        return Response::json($result);
    }

    public function byDay(Request $request)
    {
        $date = $request->getQueryParam('date');
        if (!$date) {
            throw new ValidationException('Date is required.');
        }

        $result = $this->rankingService->rankingByDay($date);

        return Response::json($result);
    }
}
