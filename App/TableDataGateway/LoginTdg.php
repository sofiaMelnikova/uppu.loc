<?php

namespace App\TableDataGateway;


class LoginTdg extends AbstractTableDataGateway {

	/**
	 * @param string $email
	 * @return array|bool
	 */
	public function getPasswordHash (string $email) {
		$query = "SELECT `password_hash` FROM `users` WHERE `users`.`email` = :email AND `is_delete` = 0";
		$params = [':email' => $email];
		return $this->dataBase->select($query, $params, false);
	}

	/**
	 * @param string $email
	 * @param string $value
	 * @param string $actualDateTime
	 * @return int
	 */
	public function updateEnterCookie (string $email, string $value, string $actualDateTime): int {
		$query = "UPDATE `users` SET `enter_cookie` = :newValue, `updated_at` = :actualDateTime WHERE `email` = :email AND `is_delete` = 0";
		$params = [':email' => $email,
					':newValue' => $value,
					':actualDateTime' => $actualDateTime];
		return $this->dataBase->update($query, $params);
	}

	/**
	 * @param string $loginCookie
	 * @return array|bool
	 */
	public function findUserByLoginCookie (string $loginCookie) {
		$query = "SELECT `users`.`id` FROM `users` WHERE `users`.`enter_cookie` = :loginCookie";
		$params = [':loginCookie' => $loginCookie];
		return $this->dataBase->select($query, $params, false);
	}

}