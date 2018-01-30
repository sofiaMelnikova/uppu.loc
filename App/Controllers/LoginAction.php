<?php

namespace App\Controllers;

use App\Models\LoginModel;
use Engine\DataBase;
use Engine\Helper;
use Slim\Http\Request;
use Slim\Http\Response;

class LoginAction {

	/**
	 * @return LoginModel
	 */
	public function getLoginModel () {
		return new LoginModel();
	}

	/**
	 * @param Request $request
	 * @param Response $response
	 * @param DataBase $dataBase
	 * @param \Twig_Environment $twig
	 * @return Response
	 */
	public function loginUserAction (Request $request, Response $response, DataBase $dataBase, \Twig_Environment $twig): Response {
		$postParams = $request->getParsedBody();
		$loginModel = $this->getLoginModel();
		$errors = $loginModel->checkPassword($postParams['email'], $postParams['password'], $dataBase);

		if (empty($errors)) {
			$initCookieString = (new Helper())->getRandomString();
			$loginModel->updateEnterCookie($postParams['email'], $initCookieString, $dataBase);
			$loginModel->setInitCookie($initCookieString);
			$toHeadersCookie = $loginModel->setInitCookie($initCookieString);
			return $response->withHeader('Set-Cookie', $toHeadersCookie)->withRedirect('/profile');
		}
		
		return $response->write($twig->render('LoginContent.html', ['errors' => $errors, 'values' => $postParams]));
	}
}