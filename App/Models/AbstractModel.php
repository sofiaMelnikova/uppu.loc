<?php

namespace App\Models;

use App\TableDataGateway\FileTdg;
use App\TableDataGateway\LoginTdg;
use App\TableDataGateway\RegistrationTdg;
use App\TableDataGateway\UserTdg;
use Engine\DataBase;
use Engine\Helper;
use Engine\Validator;

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

	/**
	 * @param DataBase $dataBase
	 * @return UserTdg
	 */
	protected function getUserTdg(DataBase $dataBase): UserTdg {
		return new UserTdg($dataBase);
	}

	/**
	 * @return Helper
	 */
	protected function getHelper() {
		return new Helper();
	}

	/**
	 * @return Validator
	 */
	protected function getValidator() {
		return new Validator();
	}

}