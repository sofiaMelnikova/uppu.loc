<?php

namespace App\Controllers;

use Engine\DataBase;
use Slim\Http\Request;
use Slim\Http\Response;

class RegistrationAction {

	public function registrationUserAction (Request $request, Response $response, DataBase $dataBase) {
		$postParams = $request->getParsedBody();
	}
}