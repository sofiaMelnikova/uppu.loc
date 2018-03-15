<?php

namespace App\Controllers;

use App\Models\FileModel;
use App\Models\UserModel;
use Engine\Helper;
use Engine\Validator;
use App\Models\RegistrationModel;
use App\Models\LoginModel;

abstract class AbstractAction {

	/**
	 * @return LoginModel
	 */
	protected function getLoginModel(): LoginModel {
		return new LoginModel();
	}

	/**
	 * @return RegistrationModel
	 */
	protected function getRegistrationModel(): RegistrationModel {
		return new RegistrationModel();
	}

	/**
	 * @return FileModel
	 */
	protected function getFileModel(): FileModel {
		return new FileModel();
	}

	/**
	 * @return UserModel
	 */
	protected function getUserModel(): UserModel {
		return new UserModel();
	}

	/**
	 * @return Validator
	 */
	protected function getValidator(): Validator {
		return new Validator();
	}

	/**
	 * @return Helper
	 */
	protected function getHelper(): Helper {
		return new Helper();
	}

}