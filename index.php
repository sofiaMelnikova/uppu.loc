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

$app->get('/', function () use ($app) {
	return $app->getContainer()->get('response')->getBody()->write($app->getContainer()->get('twig')->render('Header.html'));
});

//$app->post('/login', function () use ($app) {
//	var_dump($app->getContainer()->get('request')->getmethod);
//	var_dump($app->getContainer()->get('request')->getParsedBody()); // $_POST
//	var_dump($app->getContainer()->get('request')->getQueryParams()); // $_GET
//	die();
//	$app->getContainer()->get('Login.Controller')->loginUserAction($app);
//});

$app->post('/login', function () use ($app) {
	return $app->getContainer()->get('Login.Controller')->loginUserAction($app->getContainer()->get('request'),
		$app->getContainer()->get('response'),
		$app->getContainer()->get('DataBase'),
		$app->getContainer()->get('twig'));
});

$app->post('/registration', function () use ($app) {
	return $app->getContainer()->get('Registration.Controller')->registrationUserAction($app->getContainer()->get('request'),
		$app->getContainer()->get('response'),
		$app->getContainer()->get('DataBase'),
		$app->getContainer()->get('twig'));
});

$app->get('/registration', function () use ($app) {
	return $app->getContainer()->get('response')->getBody()->write($app->getContainer()->get('twig')->render('Registration.html'));
});

$app->get('/login', function () use ($app) {
	return $app->getContainer()->get('response')->getBody()->write($app->getContainer()->get('twig')->render('Login.html'));
});

$app->get('/profile', function () use ($app) {
	return $app->getContainer()->get('response')->getBody()->write($app->getContainer()->get('twig')->render('Profile.html'));
});

$app->get('/test', function () use ($app) {
	$res = (new \App\Models\LoginModel())->getActiveUserId($app->getContainer()->get('request'), $app->getContainer()->get('DataBase'));
	var_dump($res);
	die();
});

$app->run();
