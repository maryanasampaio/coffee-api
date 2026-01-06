<?php
namespace App\Controllers;

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
            return Response::json(['error' => 'Invalid days.'], 400);
        }
        try {
            $result = $this->rankingService->rankingLastDays($days);
            return Response::json($result);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }

    public function byDay(Request $request)
    {
        $date = $request->getQueryParam('date');
        if (!$date) {
            return Response::json(['error' => 'Date is required.'], 400);
        }
        try {
            $result = $this->rankingService->rankingByDay($date);
            return Response::json($result);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }
}
