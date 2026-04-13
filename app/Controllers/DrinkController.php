<?php
namespace App\Controllers;

use App\Exceptions\ForbiddenException;
use App\Exceptions\ValidationException;
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
            throw new ForbiddenException();
        }

        $body = $request->getBody();
        if (!isset($body['drink']) || !is_numeric($body['drink']) || $body['drink'] <= 0) {
            throw new ValidationException('Invalid value for drink.');
        }
        $drink = (int)$body['drink'];

        $user = $this->drinkService->incrementDrink($iduser, $drink);

        return Response::json([
            'iduser' => $user->iduser,
            'email' => $user->email,
            'name' => $user->name,
            'drinkCounter' => $user->drinkCounter
        ]);
    }
}
