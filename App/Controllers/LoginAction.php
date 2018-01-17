<?php

namespace App\Controllers;

use Slim\App;

class LoginAction {

	public function test () {
		var_dump(123);
	}

	public function loginUserAction (App $app) {
		echo 'Login user!';
	}
}