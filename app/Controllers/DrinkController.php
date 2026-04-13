<?php
namespace App\Controllers;

use App\Exceptions\ForbiddenException;
use App\Core\Request;
use App\Core\Response;
use App\Services\DrinkService;

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
        if ((int) $tokenUserId !== (int) $iduser) {
            throw new ForbiddenException();
        }

        $drink = $request->getPositiveIntBodyField('drink', null, 'Invalid value for drink.');

        $user = $this->drinkService->incrementDrink($iduser, $drink);

        return Response::json([
            'iduser' => $user->iduser,
            'email' => $user->email,
            'name' => $user->name,
            'drinkCounter' => $user->drinkCounter,
        ]);
    }
}
