<?php

namespace App\Controllers;

use Engine\DataBase;
use Slim\Http\Request;
use Slim\Http\Response;

class RegistrationAction extends AbstractAction {

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
		$navBar = $this->getNavBarModel()->getParams($request, $dataBase);

		if (!empty($errors)) {
			return $response->write($twig->render('RegistrationContent.html', ['navBar' => $navBar, 'errors' => $errors, 'values' => $postParams]));
		}

		$registrationModel = $this->getRegistrationModel();
		$existEmail = $registrationModel->isSetActiveEmail($postParams['email'], $dataBase);

		if ($existEmail) {
			return $response->write($twig->render('RegistrationContent.html', ['navBar' => $navBar, 'errors' => ['email' => 'User already exist with this e-mail'], 'values' => $postParams]));
		}

		$loginModel = $this->getLoginModel();

		$initCookieString = $loginModel->generateLoginCookieString();

		$newUserId = $registrationModel->addNewUser($postParams['userName'], $postParams['email'], $postParams['password'], $initCookieString, $dataBase);

		$toHeadersCookie = $loginModel->setInitCookie($initCookieString);

		$addedFileCookie = $request->getCookieParam('added_file');

		if (isset($addedFileCookie)) {
			$this->getFileModel()->addUserIdToDownloadsInfoByCookie($newUserId, $addedFileCookie, $dataBase);
		}

		return $response->withHeader('Set-Cookie', $toHeadersCookie)->withRedirect('/profile');
	}
}