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

//$app->post('/login', function () use ($app) {
//	var_dump($app->getContainer()->get('request')->getmethod);
//	var_dump($app->getContainer()->get('request')->getParsedBody()); // $_POST
//	var_dump($app->getContainer()->get('request')->getQueryParams()); // $_GET
//	die();
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
	return $app->getContainer()->get('response')->getBody()->write($app->getContainer()->get('twig')->render('RegistrationContent.html'));
});

$app->get('/login', function () use ($app) {
	return $app->getContainer()->get('response')->getBody()->write($app->getContainer()->get('twig')->render('LoginContent.html'));
});

$app->get('/profile', function () use ($app) {
	return $app->getContainer()->get('response')->getBody()->write($app->getContainer()->get('twig')->render('ProfileContent.html'));
});

$app->get('/', function () use ($app) {
	return $app->getContainer()->get('response')->getBody()->write($app->getContainer()->get('twig')->render('MainContent.html'));
});

$app->post('/download', function () use ($app) {
	$uploadFile = $app->getContainer()->get('request')->getUploadedFiles();
	var_dump($uploadFile['downloadFile']->getClientMediaType());
//	Slim\Http\UploadedFile();
	die();
});

$app->get('/test', function () use ($app) {
	return $app->getContainer()->get('response')->getBody()->write($app->getContainer()->get('twig')->render('testContent.html'));
});

$app->run();
