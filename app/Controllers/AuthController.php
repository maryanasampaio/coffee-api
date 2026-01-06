<?php
namespace App\Controllers;

use App\Validators\Validator;

use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;

class AuthController
{

    private $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

	public function login(Request $request)
	{
		$data = $request->getBody();

		if (!Validator::required($data, ['email', 'password'])) {
			return Response::json(['error' => 'Missing required fields.'], 400);
		}
		if (!Validator::email($data['email'])) {
			return Response::json(['error' => 'Invalid email.'], 400);
		}
		try {
			$result = $this->authService->authenticate($data['email'], $data['password']);
			return Response::json($result);
		} catch (\Exception $e) {
			$code = $e->getCode() ?: 401;
			return Response::json(['error' => $e->getMessage()], $code);
		}
	}
}