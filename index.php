<?php
ini_set('display_errors', true);
ini_set('display_startup_errors', true);
ini_set("display_errors",1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
//
$app = new \Slim\App();
$app->get('/', function ($request, $response) {
	$loader = new Twig_Loader_Filesystem(__DIR__ . '/App/Views');
	$twig = new Twig_Environment($loader);
	return $response->getBody()->write($twig->render('Login.html'));
});

$app->run();
