<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Exceptions\ValidationException;
use App\Services\AuthService;
use App\Validators\Validator;

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
            throw new ValidationException('Missing required fields.');
        }

        if (!Validator::email($data['email'])) {
            throw new ValidationException('Invalid email.');
        }

        $result = $this->authService->authenticate($data['email'], $data['password']);

        return Response::json($result);
    }
}
