<?php
namespace App\Services;

use App\Exceptions\UnauthorizedException;
use App\Repositories\UserRepository;
use App\Utils\TokenUtil;

class AuthService
{
    private $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function authenticate($email, $password)
    {
        $user = $this->userRepository->findByEmail($email);
        if (!$user || !password_verify($password, $user->password)) {
            throw new UnauthorizedException('Invalid credentials.');
        }

        $token = TokenUtil::generateToken([
            'sub' => (int) $user->iduser,
            'exp' => time() + 60 * 60 * 24,
        ]);

        return [
            'token' => $token,
            'iduser' => (int) $user->iduser,
            'email' => $user->email,
            'name' => $user->name,
            'drinkCounter' => (int) $user->drinkCounter,
        ];
    }
}
