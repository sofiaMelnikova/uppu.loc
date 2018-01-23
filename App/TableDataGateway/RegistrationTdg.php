<?php

namespace App\TableDataGateway;

use Engine\DataBase;

class RegistrationTdg extends AbstractTableDataGateway {

	/**
	 * @param string $email
	 * @return array|bool
	 */
	public function findEmail (string $email) {
		$query = "SELECT COUNT(`users`.`id`) FROM `users` WHERE `users`.`email` =  :email AND `users`.`is_delete` = 0";
		$params = [':email' => $email];
		return $this->dataBase->select($query, $params, false);
	}

	/**
	 * @param string $userName
	 * @param string $email
	 * @param string $passwordHash
	 * @param string $userType
	 * @param string $initCookie
	 * @return string
	 */
	public function addNewUser (string $userName, string $email, string $passwordHash, string $userType, string $initCookie): string {
		$query = "INSERT INTO `uppu`.`users` (`name`, `email`, `password_hash`, `user_type`, `enter_cookie`) 
					VALUES (:userName, :email, :passwordHash, :userType, :enterCookie)";
		$params = [':userName' => $userName,
					':email' => $email,
					':passwordHash' => $passwordHash,
					':userType' => $userType,
					':enterCookie' => $initCookie];
		return $this->dataBase->insert($query, $params, true);
	}



}