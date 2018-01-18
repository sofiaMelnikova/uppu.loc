<?php
ini_set('display_errors', true);
ini_set('display_startup_errors', true);
ini_set("display_errors",1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

$container = require_once __DIR__ . "/Configs/ConstructContainApp.php";
$app = new \Slim\App($container);

$after = function ($request, $response, $next) use ($app) {
//	$response->getBody()->write($app['Login.Controller']->test());
//	$app['Login.Controller']->test();
	$response = $next($request, $response);


	return $response;
};

$app->get('/', function ($request, $response) use ($app) {
	$loader = new Twig_Loader_Filesystem(__DIR__ . '/App/Views');
	$twig = new Twig_Environment($loader);
//	var_dump($app->getContainer()->get('Login.Controller'));
//	die();
//	var_dump($app->getContainer()->get('DataBase')->getConnection());
	// response
//	var_dump($app->getContainer()->get('request'));
//	die();
	return $app->getContainer()->get('response')->getBody()->write($twig->render('Header.html'));
});

$app->post('/login', function () use ($app) {
//	var_dump($app->getContainer()->get('request')->getmethod);
	var_dump($app->getContainer()->get('request')->getParsedBody()); // $_POST
//	var_dump($app->getContainer()->get('request')->getQueryParams()); // $_GET
	die();
	$app->getContainer()->get('Login.Controller')->loginUserAction($app);
});

$app->post('/registration', function () use ($app) {
	$app->getContainer()->get('Registration.Controller')->registrationUserAction($app->getContainer()->get('request'),
		$app->getContainer()->get('response'),
		$app->getContainer()->get('DataBase'));
});

$app->run();
