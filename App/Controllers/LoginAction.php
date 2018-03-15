<?php

namespace App\Controllers;

use Engine\DataBase;
use Slim\Http\Request;
use Slim\Http\Response;

class LoginAction extends AbstractAction {

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
			$initCookieString = $loginModel->generateLoginCookieString();
			$loginModel->updateEnterCookie($postParams['email'], $initCookieString, $dataBase);
			$toHeadersCookie = $loginModel->setInitCookie($initCookieString);

			$userId = $loginModel->getUserIdByCookie($initCookieString, $dataBase);

			$addedFileCookie = $request->getCookieParam('added_file');

			if (isset($addedFileCookie)) {
				$this->getFileModel()->addUserIdToDownloadsInfoByCookie($userId, $addedFileCookie, $dataBase);
			}

			return $response->withHeader('Set-Cookie', $toHeadersCookie)->withRedirect('/profile');
		}
		
		return $response->write($twig->render('LoginContent.html', ['errors' => $errors, 'values' => $postParams]));
	}
}