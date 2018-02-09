<?php

namespace App\Controllers;

use App\Models\FileModel;
use Engine\Helper;
use Engine\Validator;
use App\Models\RegistrationModel;
use App\Models\LoginModel;

abstract class AbstractAction {

	/**
	 * @return LoginModel
	 */
	protected function getLoginModel() {
		return new LoginModel();
	}

	/**
	 * @return RegistrationModel
	 */
	protected function getRegistrationModel() {
		return new RegistrationModel();
	}

	/**
	 * @return FileModel
	 */
	protected function getFileModel() {
		return new FileModel();
	}

	/**
	 * @return Validator
	 */
	protected function getValidator() {
		return new Validator();
	}

	/**
	 * @return Helper
	 */
	protected function getHelper() {
		return new Helper();
	}

}