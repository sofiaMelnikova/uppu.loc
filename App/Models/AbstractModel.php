<?php

namespace App\Models;

use App\TableDataGateway\FileTdg;
use App\TableDataGateway\LoginTdg;
use App\TableDataGateway\RegistrationTdg;
use Engine\DataBase;

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

	/**
	 * @param DataBase $dataBase
	 * @return FileTdg
	 */
	protected function getFileTdg(DataBase $dataBase): FileTdg {
		return new FileTdg($dataBase);
	}

}