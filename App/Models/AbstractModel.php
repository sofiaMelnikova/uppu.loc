<?php

namespace App\Models;

use App\TableDataGateway\LoginTdg;
use App\TableDataGateway\RegistrationTdg;
use Engine\DataBase;
use Slim\App;

abstract class AbstractModel {

	/**
	 * @param DataBase $dataBase
	 * @return RegistrationTdg
	 */
	protected function getRegistrationTdg (DataBase $dataBase): RegistrationTdg {
		return new RegistrationTdg($dataBase);
	}

	/**
	 * @param DataBase $dataBase
	 * @return LoginTdg
	 */
	protected function getLoginTdg(DataBase $dataBase): LoginTdg {
		return new LoginTdg($dataBase);
	}

}