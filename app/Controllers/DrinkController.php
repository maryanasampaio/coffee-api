<?php
namespace App\Controllers;

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
        $request->ensureAuthenticatedUserOwns($iduser);

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
