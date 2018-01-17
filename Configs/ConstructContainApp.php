<?php

$dbConf = require_once __DIR__ . "/db.php";

return [
	'Login.Controller' => new \App\Controllers\LoginAction(),
	'Registration.Controller' => new \App\Controllers\RegistrationAction(),
	'Login.Model' => new \App\Models\LoginModel(),
	'Registration.Model' => new \App\Models\RegistrationModel(),
	'DataBase' => new \Engine\DataBase($dbConf['dbName'], $dbConf['userName'], $dbConf['password'], $dbConf['host'], $dbConf['port'])
];