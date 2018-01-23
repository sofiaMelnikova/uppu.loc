<?php

$dbConf = require_once __DIR__ . "/db.php";

return [
	'twig' => function () {
		$loader = new Twig_Loader_Filesystem(__DIR__ . '/../App/Views');
		return new Twig_Environment($loader);
	},
	'Login.Controller' => function () {
		return new \App\Controllers\LoginAction();
	},
	'Registration.Controller' => function () {
		return new \App\Controllers\RegistrationAction();
	},
	'Login.Model' => function () {
		return new \App\Models\LoginModel();
	},
	'Registration.Model' => function () {
		return new \App\Models\RegistrationModel();
	},
	'DataBase' => function () use ($dbConf) {
		return new \Engine\DataBase($dbConf['dbName'], $dbConf['userName'], $dbConf['password'], $dbConf['host'], $dbConf['port']);
	}
];