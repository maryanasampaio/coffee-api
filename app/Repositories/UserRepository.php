<?php

namespace App\Repositories;

use App\Core\Database;
use App\Exceptions\DatabaseException;
use App\Models\User;
use PDO;
use Throwable;

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
                $data['iduser'] = $data['id'];
                unset($data['id']);
                $data['drinkCounter'] = $data['drink_counter'];
                unset($data['drink_counter']);

                return new User($data);
            }

            return null;
        } catch (Throwable $exception) {
            throw new DatabaseException('Failed to fetch user by email.');
        }
    }

    public function findById($iduser)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id');
            $stmt->execute(['id' => $iduser]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $data['iduser'] = $data['id'];
                unset($data['id']);
                $data['drinkCounter'] = $data['drink_counter'];
                unset($data['drink_counter']);

                return new User($data);
            }

            return null;
        } catch (Throwable $exception) {
            throw new DatabaseException('Failed to fetch user by id.');
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
                'drink_counter' => $user->drinkCounter ?? 0,
            ]);
            $user->iduser = $this->db->lastInsertId();

            return $user;
        } catch (Throwable $exception) {
            throw new DatabaseException('Failed to create user.');
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
                'id' => $user->iduser,
            ]);
        } catch (Throwable $exception) {
            throw new DatabaseException('Failed to update user.');
        }
    }

    public function incrementDrinkCounter($iduser, int $drink): bool
    {
        try {
            $stmt = $this->db->prepare('UPDATE users SET drink_counter = drink_counter + :drink WHERE id = :id');
            $stmt->execute([
                'drink' => $drink,
                'id' => $iduser,
            ]);

            return $stmt->rowCount() > 0;
        } catch (Throwable $exception) {
            throw new DatabaseException('Failed to increment drink counter.');
        }
    }

    public function delete($iduser)
    {
        try {
            $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');

            return $stmt->execute(['id' => $iduser]);
        } catch (Throwable $exception) {
            throw new DatabaseException('Failed to delete user.');
        }
    }

    public function getAllPaginated($page = 1, $perPage = 10)
    {
        try {
            $offset = ($page - 1) * $perPage;
            $stmt = $this->db->prepare('SELECT * FROM users LIMIT :limit OFFSET :offset');
            $stmt->bindValue(':limit', (int) $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
            $stmt->execute();
            $users = [];

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data['iduser'] = $data['id'];
                unset($data['id']);
                $data['drinkCounter'] = $data['drink_counter'];
                unset($data['drink_counter']);
                $users[] = new User($data);
            }

            $total = $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();

            return [
                'data' => $users,
                'total' => (int) $total,
            ];
        } catch (Throwable $exception) {
            throw new DatabaseException('Failed to fetch paginated users.');
        }
    }
}
