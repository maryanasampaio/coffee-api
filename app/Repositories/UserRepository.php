<?php
namespace App\Repositories;

use App\Models\User;
use App\Core\Database;

use PDO;

class UserRepository
{
	protected $db;

	public function __construct()
	{
		$this->db = Database::getInstance();
	}

	public function findByEmail($email)
	{
		try {
			$stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email');
			$stmt->execute(['email' => $email]);
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($data) {
				$data['iduser'] = $data['id']; unset($data['id']);
				$data['drinkCounter'] = $data['drink_counter']; unset($data['drink_counter']);
				return new User($data);
			}
			return null;
		} catch (\Exception $e) {
			throw $e;
		} catch (\Throwable $t) {
			throw new \Exception('Unexpected error in findByEmail.', 500);
		}
	}

	public function findById($iduser)
	{
		try {
			$stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id');
			$stmt->execute(['id' => $iduser]);
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($data) {
				$data['iduser'] = $data['id']; unset($data['id']);
				$data['drinkCounter'] = $data['drink_counter']; unset($data['drink_counter']);
				return new User($data);
			}
			return null;
		} catch (\Exception $e) {
			throw $e;
		} catch (\Throwable $t) {
			throw new \Exception('Unexpected error in findById.', 500);
		}
	}

	public function create($user)
	{
		try {
			$stmt = $this->db->prepare('INSERT INTO users (name, email, password, drink_counter) VALUES (:name, :email, :password, :drink_counter)');
			$stmt->execute([
				'name' => $user->name,
				'email' => $user->email,
				'password' => $user->password,
				'drink_counter' => $user->drinkCounter ?? 0
			]);
			$user->iduser = $this->db->lastInsertId();
			return $user;
		} catch (\Exception $e) {
			throw $e;
		} catch (\Throwable $t) {
			throw new \Exception('Unexpected error in create.', 500);
		}
	}

	public function update($user)
	{
		try {
			$stmt = $this->db->prepare('UPDATE users SET name = :name, email = :email, password = :password, drink_counter = :drink_counter WHERE id = :id');
			return $stmt->execute([
				'name' => $user->name,
				'email' => $user->email,
				'password' => $user->password,
				'drink_counter' => $user->drinkCounter,
				'id' => $user->iduser
			]);
		} catch (\Exception $e) {
			throw $e;
		} catch (\Throwable $t) {
			throw new \Exception('Unexpected error in update.', 500);
		}
	}

	public function incrementDrinkCounter($iduser, int $drink): bool
	{
		try {
			$stmt = $this->db->prepare('UPDATE users SET drink_counter = drink_counter + :drink WHERE id = :id');
			$stmt->execute([
				'drink' => $drink,
				'id' => $iduser
			]);

			return $stmt->rowCount() > 0;
		} catch (\Exception $e) {
			throw $e;
		} catch (\Throwable $t) {
			throw new \Exception('Unexpected error in incrementDrinkCounter.', 500);
		}
	}

	public function delete($iduser)
	{
		try {
			$stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
			return $stmt->execute(['id' => $iduser]);
		} catch (\Exception $e) {
			throw $e;
		} catch (\Throwable $t) {
			throw new \Exception('Unexpected error in delete.', 500);
		}
	}

	public function getAllPaginated($page = 1, $perPage = 10)
	{
		$offset = ($page - 1) * $perPage;
		$stmt = $this->db->prepare('SELECT SQL_CALC_FOUND_ROWS * FROM users LIMIT :limit OFFSET :offset');
		$stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
		$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
		$stmt->execute();
		$users = [];
		while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$data['iduser'] = $data['id']; unset($data['id']);
			$data['drinkCounter'] = $data['drink_counter']; unset($data['drink_counter']);
			$users[] = new User($data);
		}
		$total = $this->db->query('SELECT FOUND_ROWS()')->fetchColumn();
		return [
			'data' => $users,
			'total' => (int)$total
		];
	}
}
