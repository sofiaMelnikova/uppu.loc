<?php

namespace App\Models;

use App\TableDataGateway\RegistrationTdg;
use Engine\DataBase;

class RegistrationModel {

	/**
	 * @param DataBase $dataBase
	 * @return RegistrationTdg
	 */
	private function getRegistrationTdg (DataBase $dataBase) {
		return new RegistrationTdg($dataBase);
	}


	public function issetEmail (string $email, DataBase $dataBase) {
		$registrationTdg = $this->getRegistrationTdg($dataBase);
		$result = $registrationTdg->findEmail($email);
		var_dump($result);
		die();
	}

}