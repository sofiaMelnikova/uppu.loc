<?php

namespace App\TableDataGateway;


use Engine\DataBase;

class RegistrationTdg {

	/**
	 * @var DataBase
	 */
	private $dataBase;

	/**
	 * RegistrationTdg constructor.
	 * @param DataBase $dataBase
	 */
	public function __construct(DataBase $dataBase) {
		$this->dataBase = $dataBase;
	}

	/**
	 * @param string $email
	 * @return array|bool
	 */
	public function findEmail (string $email) {
		$query = "SELECT COUNT(`users`.`id`) FROM `users` WHERE `users`.`email` =  :email AND `users`.`is_delete` = 0";
		$params = [':email' => $email];
		return $this->dataBase->select($query, $params, false);
	}

}