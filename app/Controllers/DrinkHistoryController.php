<?php
namespace App\Controllers;

use App\Repositories\DrinkLogRepository;
use App\Core\Request;
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
        $tokenUserId = $_REQUEST['auth_user_id'] ?? null;
        if ((int)$tokenUserId !== (int)$iduser) {
            return Response::json(['error' => 'Forbidden'], 403);
        }
        $history = $this->drinkLogRepository->getHistoryByUser($iduser);
        return Response::json($history);
    }
}
