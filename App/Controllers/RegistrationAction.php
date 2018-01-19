<?php

namespace App\Controllers;

use Engine\DataBase;
use Engine\Validator;
use Slim\Http\Request;
use Slim\Http\Response;

class RegistrationAction {

	/**
	 * @return Validator
	 */
	private function getValidator () {
		return new Validator();
	}

	public function registrationUserAction (Request $request, Response $response, DataBase $dataBase, \Twig_Environment $twig) {
		$validator = $this->getValidator();
		$postParams = $request->getParsedBody();
		$errors = $validator->registrationForm($postParams);

		if (!empty($errors)) {
			return $twig->render('Header.html', $errors);
		}



	}
}