<?php
namespace App\Services;

use App\Exceptions\ConflictException;
use App\Exceptions\NotFoundException;
use App\Repositories\UserRepository;
use App\Models\User;

class UserService
{
    private $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function createUser($name, $email, $password)
    {
        if ($this->userRepository->findByEmail($email)) {
            throw new ConflictException('User already exists.');
        }
        $user = new User([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'drinkCounter' => 0
        ]);
        return $this->userRepository->create($user);
    }

    public function getUserById($iduser)
    {
        return $this->userRepository->findById($iduser);
    }

    public function getAllUsersPaginated($page = 1, $perPage = 10)
    {
        return $this->userRepository->getAllPaginated($page, $perPage);
    }

    public function updateUser($iduser, $data)
    {
        $user = $this->userRepository->findById($iduser);
        if (!$user) {
            throw new NotFoundException('User not found.');
        }
        if (isset($data['name'])) {
            $user->name = $data['name'];
        }
        if (isset($data['email'])) {
            $user->email = $data['email'];
        }
        if (isset($data['password'])) {
            $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $this->userRepository->update($user);
        return $user;
    }

    public function deleteUser($iduser)
    {
        $user = $this->userRepository->findById($iduser);
        if (!$user) {
            throw new NotFoundException('User not found.');
        }
        return $this->userRepository->delete($iduser);
    }
}
