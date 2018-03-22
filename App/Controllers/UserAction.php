<?php

namespace App\Controllers;

use Engine\DataBase;
use Slim\Http\Request;
use Slim\Http\Response;

class UserAction extends AbstractAction {

	/**
	 * @param Request $request
	 * @param Response $response
	 * @param DataBase $dataBase
	 * @param \Twig_Environment $twig
	 * @return mixed
	 */
	public function viewProfileAction(Request $request, Response $response, DataBase $dataBase, \Twig_Environment $twig) {
		if (!$this->getLoginModel()->isLoginUser($request, $dataBase)) {
			return $response->withRedirect('/');
		}

		$user = $this->getUserModel()->getIdNameCountFilesByEnterCookie($request, $dataBase);
		return $response->write($twig->render('ProfileContent.html', ['user' => $user]));
	}

}