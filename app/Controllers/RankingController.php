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
        $days = $request->getPositiveIntQueryParam('days', 7, 'Invalid days.');

        $result = $this->rankingService->rankingLastDays($days);

        return Response::json($result);
    }

    public function byDay(Request $request)
    {
        $date = $request->getDateQueryParam('date', null, 'Date is required.', 'Invalid date.');

        $result = $this->rankingService->rankingByDay($date);

        return Response::json($result);
    }
}
