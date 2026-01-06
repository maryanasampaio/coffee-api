<?php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
	public $iduser;
	public $name;
	public $email;
	public $password;
	public $drinkCounter;
	public $created_at;
	public $updated_at;

	public function __construct($data = [])
	{
		parent::__construct();
		foreach ($data as $key => $value) {
			if (property_exists($this, $key)) {
				$this->$key = $value;
			}
		}
	}
}
