<?php
namespace App\Services;

use App\Core\Database;
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

		$db = Database::getInstance();

		try {
			$db->beginTransaction();
			$user = $this->userRepository->findById($iduser);

			if (!$user) {
				throw new \Exception('User not found.', 404);
			}

			$updated = $this->userRepository->incrementDrinkCounter($iduser, (int)$drink);

			if (!$updated) {
				throw new \Exception('User not found.', 404);
			}

			$this->drinkLogRepository->store($iduser, date('Y-m-d'), (int)$drink);
			$updatedUser = $this->userRepository->findById($iduser);
			$db->commit();

			return $updatedUser;
		} catch (\Throwable $e) {
			if ($db->inTransaction()) {
				$db->rollBack();
			}

			throw $e;
		}
	}
}
