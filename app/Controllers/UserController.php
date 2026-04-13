<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Exceptions\ForbiddenException;
use App\Exceptions\HttpException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Services\DrinkService;
use App\Services\UserService;
use App\Utils\TokenUtil;
use App\Validators\Validator;

class UserController
{
    private $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function create(Request $request)
    {
        $data = $request->requireBodyFields(['name', 'email', 'password'], 'Missing required fields.');

        if (!Validator::email($data['email'])) {
            throw new ValidationException('Invalid email.');
        }

        $user = $this->userService->createUser($data['name'], $data['email'], $data['password']);
        $token = TokenUtil::generateToken([
            'sub' => (int) $user->iduser,
            'exp' => time() + 60 * 60 * 24,
        ]);

        return Response::json([
            'token' => $token,
            'iduser' => (int) $user->iduser,
            'email' => $user->email,
            'name' => $user->name,
            'drinkCounter' => (int) $user->drinkCounter,
        ], 201);
    }

    public function get($iduser)
    {
        $user = $this->userService->getUserById($iduser);

        if (!$user) {
            throw new NotFoundException('User not found.');
        }

        return Response::json([
            'iduser' => (int) $user->iduser,
            'name' => $user->name,
            'email' => $user->email,
            'drinkCounter' => (int) $user->drinkCounter,
        ]);
    }

    public function list(Request $request)
    {
        $page = $request->getPositiveIntQueryParam('page', 1, 'Page must be a positive integer.');
        $perPage = $request->getPositiveIntQueryParam('per_page', 10, 'Per page must be a positive integer.');

        $result = $this->userService->getAllUsersPaginated($page, $perPage);
        $users = array_map(function ($user) {
            return [
                'iduser' => $user->iduser,
                'name' => $user->name,
                'email' => $user->email,
                'drinkCounter' => $user->drinkCounter,
            ];
        }, $result['data']);

        return Response::json([
            'data' => $users,
            'total' => $result['total'],
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($result['total'] / $perPage),
        ]);
    }

    public function update(Request $request, $iduser)
    {
        $this->ensureAuthenticatedUserOwns((int) $iduser);

        $data = $request->getBody();
        if (isset($data['email']) && !Validator::email($data['email'])) {
            throw new ValidationException('Invalid email.');
        }

        $user = $this->userService->updateUser($iduser, $data);

        return Response::json([
            'iduser' => (int) $user->iduser,
            'name' => $user->name,
            'email' => $user->email,
            'drinkCounter' => (int) $user->drinkCounter,
        ]);
    }

    public function delete($iduser)
    {
        $this->ensureAuthenticatedUserOwns((int) $iduser);

        $ok = $this->userService->deleteUser($iduser);
        if (!$ok) {
            throw new HttpException('Error deleting user.', 500);
        }

        return Response::json(['message' => 'User deleted successfully.']);
    }

    public function drink($params, Request $request)
    {
        $iduser = $params['iduser'] ?? null;
        $drink = $request->getPositiveIntBodyField('drink', 1, 'Invalid value for drink.');
        $drinkService = new DrinkService();
        $user = $drinkService->incrementDrink($iduser, $drink);

        return Response::json([
            'iduser' => $user->iduser,
            'email' => $user->email,
            'name' => $user->name,
            'drinkCounter' => $user->drinkCounter,
        ]);
    }

    private function ensureAuthenticatedUserOwns(int $iduser): void
    {
        $tokenUserId = $_REQUEST['auth_user_id'] ?? null;

        if ((int) $tokenUserId !== $iduser) {
            throw new ForbiddenException();
        }
    }
}
