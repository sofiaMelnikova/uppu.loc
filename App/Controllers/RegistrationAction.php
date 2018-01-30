<?php

namespace App\Controllers;

use App\Models\LoginModel;
use App\Models\RegistrationModel;
use Engine\DataBase;
use Engine\Helper;
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

	/**
	 * @return RegistrationModel
	 */
	private function getRegistrationModel () {
		return new RegistrationModel();
	}

	/**
	 * @param Request $request
	 * @param Response $response
	 * @param DataBase $dataBase
	 * @param \Twig_Environment $twig
	 * @return Response
	 */
	public function registrationUserAction (Request $request, Response $response, DataBase $dataBase, \Twig_Environment $twig): Response {
		$validator = $this->getValidator();
		$postParams = $request->getParsedBody();
		$errors = $validator->registrationForm($postParams);

		if (!empty($errors)) {
			return $response->write($twig->render('RegistrationContent.html', ['errors' => $errors, 'values' => $postParams]));
		}

		$registrationModel = $this->getRegistrationModel();
		$existEmail = $registrationModel->isSetActiveEmail($postParams['email'], $dataBase);

		if ($existEmail) {
			return $response->write($twig->render('RegistrationContent.html', ['errors' => ['email' => 'User already exist with this e-mail'], 'values' => $postParams]));
		}

		$initCookieString = (new Helper())->getRandomString();

		$registrationModel->addNewUser($postParams['userName'], $postParams['email'], $postParams['password'], $initCookieString, $dataBase);

		$toHeadersCookie = (new LoginModel())->setInitCookie($initCookieString);

		return $response->withHeader('Set-Cookie', $toHeadersCookie)->withRedirect('/profile');
	}
}