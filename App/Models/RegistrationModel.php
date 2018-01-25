<?php

namespace App\Models;

use Engine\DataBase;

class RegistrationModel extends AbstractModel {

	/**
	 * @param string $email
	 * @param DataBase $dataBase
	 * @return bool
	 */
	public function isSetActiveEmail (string $email, DataBase $dataBase): bool {
		$registrationTdg = $this->getRegistrationTdg($dataBase);
		$result = $registrationTdg->findEmail($email);
		return (bool) array_shift($result);
	}

	/**
	 * @param string $userName
	 * @param string $email
	 * @param string $password
	 * @param string $initCookie
	 * @param DataBase $dataBase
	 * @return int
	 */
	public function addNewUser (string $userName, string $email, string $password, string $initCookie, DataBase $dataBase): int {
		$passwordHash = password_hash($password, PASSWORD_BCRYPT);
		$registrationTdg = $this->getRegistrationTdg($dataBase);
		$now = date('Y-m-d H:i:s');
		return (int) $registrationTdg->addNewUser($userName, $email, $passwordHash, 'base', $initCookie, $now, $now);
	}

}