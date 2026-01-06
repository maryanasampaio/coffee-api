<?php
namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\DrinkLogRepository;

class DrinkService
{
	private $userRepository;
	private $drinkLogRepository;

	public function __construct()
	{
		$this->userRepository = new UserRepository();
		$this->drinkLogRepository = new DrinkLogRepository();
	}

	public function incrementDrink($iduser, $drink = 1)
	{
		if ($drink <= 0) {
			throw new \Exception('Invalid quantity.', 400);
		}
		$user = $this->userRepository->findById($iduser);
		if (!$user) {
			throw new \Exception('User not found.', 404);
		}
		$user->drinkCounter += (int)$drink;
		$this->userRepository->update($user);
		$this->drinkLogRepository->store($iduser, date('Y-m-d'), (int)$drink);
		return $user;
	}
}
