<?php
ini_set('display_errors', true);
ini_set('display_startup_errors', true);
ini_set("display_errors",1);
ini_set('memory_limit', '2G'); // максимальный объем памяти, который разрешается использовать скрипту
ini_set('max_execution_time', 20000); // максимальное время за которое должен выпоняться скрипт

// значения: max_input_time, upload_max_filesize, post_max_size - нельзя помнять с помощью ini_set (изменены в php.ini, проверить все зачения: phpinfo())
// что бы изменения в php.ini вступили в силу, необходимо перезапустить php-pfm: sudo service php7.0-fpm restart
// ini_set('max_input_time', -1); // максимальное время, в течение которого могут принематься данные на сервер (-1 - будет использоваться )
// ini_set('upload_max_filesize', '2G'); // максимальный размер файла, который допускается для загрузки на сервер
// ini_set('post_max_size', '2G'); // максимальный размер, отправляемых даных

error_reporting(E_ALL);


require_once __DIR__ . '/vendor/autoload.php';

$container = require_once __DIR__ . "/Configs/ConstructContainApp.php";
$app = new \Slim\App($container);

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
	return (new \App\Controllers\FileAction())->newFileAction(
		$app->getContainer()->get('request'),
		$app->getContainer()->get('response'),
		$app->getContainer()->get('DataBase'),
		$app->getContainer()->get('twig')
	);
});

$app->get('/new-file', function () use ($app) {
	return $app->getContainer()->get('response')->getBody()->write($app->getContainer()->get('twig')->render('UploadedFileContent.html'));
});

$app->get('/test', function () use ($app) {
//	var_dump(phpinfo());
//	die();
	return $app->getContainer()->get('response')->getBody()->write($app->getContainer()->get('twig')->render('testContent.html'));
});

$app->run();
