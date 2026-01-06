<?php
namespace App\Controllers;

use App\Services\DrinkService;
use App\Core\Request;
use App\Core\Response;

class DrinkController
{
    private $drinkService;

    public function __construct()
    {
        $this->drinkService = new DrinkService();
    }

    public function increment(int $iduser, Request $request)
    {
        $tokenUserId = $_REQUEST['auth_user_id'] ?? null;
        if ((int)$tokenUserId !== (int)$iduser) {
            return Response::json(['error' => 'Forbidden'], 403);
        }

        $body = $request->getBody();
        if (!isset($body['drink']) || !is_numeric($body['drink']) || $body['drink'] <= 0) {
            return Response::json(['error' => 'Invalid value for drink.'], 400);
        }
        $drink = (int)$body['drink'];

        try {
            $user = $this->drinkService->incrementDrink($iduser, $drink);
            return Response::json([
                'iduser' => $user->iduser,
                'email' => $user->email,
                'name' => $user->name,
                'drinkCounter' => $user->drinkCounter
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'error' => $e->getMessage()
            ], $e->getCode() ?: 400);
        }
    }
}
