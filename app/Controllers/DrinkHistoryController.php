<?php
namespace App\Controllers;

use App\Exceptions\ForbiddenException;
use App\Repositories\DrinkLogRepository;
use App\Core\Response;

class DrinkHistoryController
{
    private $drinkLogRepository;

    public function __construct()
    {
        $this->drinkLogRepository = new DrinkLogRepository();
    }

    public function history(int $iduser)
    {
        $tokenUserId = $_REQUEST['auth_user_id'] ?? null;
        if ((int)$tokenUserId !== (int)$iduser) {
            throw new ForbiddenException();
        }
        $history = $this->drinkLogRepository->getHistoryByUser($iduser);
        return Response::json($history);
    }
}
