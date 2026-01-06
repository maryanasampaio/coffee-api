<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\UserService;
use App\Helpers\JwtHelper;
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
		$data = $request->getBody();
		if (!Validator::required($data, ['name', 'email', 'password'])) {
			return Response::json(['error' => 'Missing required fields.'], 400);
		}
		if (!Validator::email($data['email'])) {
			return Response::json(['error' => 'Invalid email.'], 400);
		}
		try {
			$user = $this->userService->createUser($data['name'], $data['email'], $data['password']);
			$token = TokenUtil::generateToken([
				'sub' => (int)$user->iduser,
				'exp' => time() + 60 * 60 * 24 
			]);
			return Response::json([
				'token' => $token,
				'iduser' => (int)$user->iduser,
				'email' => $user->email,
				'name' => $user->name,
				'drinkCounter' => (int)$user->drinkCounter
			], 201);
		} catch (\Exception $e) {
			return Response::json(['error' => $e->getMessage()], 400);
		}
	}

	public function get($iduser)
	{
		$user = $this->userService->getUserById($iduser);
		if (!$user) {
			return Response::json(['error' => 'User not found.'], 404);
		}
		return Response::json([
			'iduser' => (int)$user->iduser,
			'name' => $user->name,
			'email' => $user->email,
			'drinkCounter' => (int)$user->drinkCounter
		]);
	}

	public function list(Request $request)
	{
		$page = (int)$request->getQueryParam('page', 1);
		$perPage = (int)$request->getQueryParam('per_page', 10);
		if ($page < 1) $page = 1;
		if ($perPage < 1) $perPage = 10;
		$result = $this->userService->getAllUsersPaginated($page, $perPage);
		$users = array_map(function($user) {
			return [
				'iduser' => $user->iduser,
				'name' => $user->name,
				'email' => $user->email,
				'drinkCounter' => $user->drinkCounter
			];
		}, $result['data']);
		return Response::json([
			'data' => $users,
			'total' => $result['total'],
			'page' => $page,
			'per_page' => $perPage,
			'total_pages' => ceil($result['total'] / $perPage)
		]);
	}

	public function update(Request $request, $iduser)
	{
		$data = $request->getBody();
		try {
			if (isset($data['email']) && !Validator::email($data['email'])) {
				return Response::json(['error' => 'Invalid email.'], 400);
			}
			$user = $this->userService->updateUser($iduser, $data);
			return Response::json([
				'iduser' => (int)$user->iduser,
				'name' => $user->name,
				'email' => $user->email,
				'drinkCounter' => (int)$user->drinkCounter
			]);
		} catch (\Exception $e) {
			return Response::json(['error' => $e->getMessage()], 400);
		}
	}

	public function delete($iduser)
	{
		try {
			$ok = $this->userService->deleteUser($iduser);
			if ($ok) {
				return Response::json(['message' => 'User deleted successfully.']);
			}
			return Response::json(['error' => 'Error deleting user.'], 500);
		} catch (\Exception $e) {
			$code = $e->getCode() ?: 400;
			return Response::json(['error' => $e->getMessage()], $code);
		}
	}

	public function drink($params, Request $request)
	{
		$iduser = $params['iduser'] ?? null;
		$body = $request->getBody();
		$drink = isset($body['drink']) ? (int)$body['drink'] : 1;
		try {
			$drinkService = new \App\Services\DrinkService();
			$user = $drinkService->incrementDrink($iduser, $drink);
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
