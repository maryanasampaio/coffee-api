<?php
namespace App\Controllers;

use App\Core\Request;
use App\Repositories\DrinkLogRepository;
use App\Core\Response;

class DrinkHistoryController
{
    private $drinkLogRepository;

    public function __construct()
    {
        $this->drinkLogRepository = new DrinkLogRepository();
    }

    public function history(int $iduser, Request $request)
    {
        $request->ensureAuthenticatedUserOwns($iduser);

        $history = $this->drinkLogRepository->getHistoryByUser($iduser);

        return Response::json($history);
    }
}
