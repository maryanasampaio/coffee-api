<?php
namespace App\Controllers;

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
        $data = $request->requireBodyFields(['email', 'password'], 'Missing required fields.');
        $data['email'] = $request->requireEmailBodyField('email', 'Missing required fields.', 'Invalid email.');

        $result = $this->authService->authenticate($data['email'], $data['password']);

        return Response::json($result);
    }
}
