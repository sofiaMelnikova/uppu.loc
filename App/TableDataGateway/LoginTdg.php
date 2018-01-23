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
	 * @return int
	 */
	public function updateEnterCookie (string $email, string $value): int {
		$query = "UPDATE `users` SET `enter_cookie` = :newValue WHERE `email` = :email AND `is_delete` = 0";
		$params = [':email' => $email,
					':newValue' => $value];
		return $this->dataBase->update($query, $params);
	}

}