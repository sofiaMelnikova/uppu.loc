<?php
ini_set('display_errors', true);
ini_set('display_startup_errors', true);
ini_set("display_errors",1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
//
$app = new \Slim\App();
$app->get('/', function ($request, $response) {
	return $response->getBody()->write('Hello World');
});

$app->run();
