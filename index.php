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

use Slim\Http\Request;
use Slim\Http\Response;

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

$app->get('/registration', function (Request $request, Response $response) use ($app) {
	$navBarParams = $app->getContainer()->get('NavBar.Model')->getParams($request, $app->getContainer()->get('DataBase'));
	return $response->getBody()->write($app->getContainer()->get('twig')->render('RegistrationContent.html', ['navBar' => $navBarParams]));
});

$app->get('/login', function (Request $request, Response $response) use ($app) {
	$navBarParams = $app->getContainer()->get('NavBar.Model')->getParams($request, $app->getContainer()->get('DataBase'));
	return $response->getBody()->write($app->getContainer()->get('twig')->render('LoginContent.html', ['navBar' => $navBarParams]));
});

$app->get('/profile', function () use ($app) {
	return $app->getContainer()->get('User.Controller')
		->viewProfileAction($app->getContainer()->get('request'),
			$app->getContainer()->get('response'),
			$app->getContainer()->get('DataBase'),
			$app->getContainer()->get('twig'));
});

$app->get('/', function (Request $request, Response $response) use ($app) {
	$navBarParams = $app->getContainer()->get('NavBar.Model')->getParams($request, $app->getContainer()->get('DataBase'));
	return $response->getBody()->write($app->getContainer()->get('twig')->render('MainContent.html', ['navBar' => $navBarParams]));
});

$app->post('/download', function () use ($app) {
	return $app->getContainer()->get('File.Controller')->uploadedAction(
		$app->getContainer()->get('request'),
		$app->getContainer()->get('response'),
		$app->getContainer()->get('DataBase'),
		$app->getContainer()->get('twig')
	);
});

$app->get('/uploaded-file', function (Request $request, Response $response) use ($app) {
	$navBarParams = $app->getContainer()->get('NavBar.Model')->getParams($request, $app->getContainer()->get('DataBase'));
	return $response->getBody()->write($app->getContainer()->get('twig')->render('UploadedFileContent.html', ['navBar' => $navBarParams]));
});

$app->get('/edit-file/{name}', function ($request, $response, $args) use ($app) {
	return $app->getContainer()->get('File.Controller')
		->getEditFileViewAction(
			$args['name'],
			$request,
			$response,
			$app->getContainer()->get('DataBase'),
			$app->getContainer()->get('twig')
		);
});

$app->get('/file/{name}', function (Request $request, Response $response, array $args) use ($app) {
	return $app->getContainer()->get('File.Controller')
		->viewPageDownloadingFileToUserAction(
			$args['name'],
			$request,
			$response,
			$app->getContainer()->get('DataBase'),
			$app->getContainer()->get('twig')
		);
});

$app->get('/get-file/{name}', function (Request $request, Response $response, array $args) use ($app) {
	return $app->getContainer()->get('File.Controller')
		->downloadFileToUserAction(
			$args['name'],
			$response,
			$app->getContainer()->get('DataBase'),
			$app->getContainer()->get('twig')
		);
});

$app->post('/update-file', function () use ($app) {
	return $app->getContainer()->get('File.Controller')->updateAction(
		$app->getContainer()->get('request'),
		$app->getContainer()->get('response'),
		$app->getContainer()->get('DataBase'),
		$app->getContainer()->get('twig')
	);
});

$app->get('/users-files/{userHashId}', function (Request $request, Response $response, array $args) use ($app) {
	return $app->getContainer()->get('File.Controller')->viewPageUsersFilesToUserAction(
		str_replace('__to_slash__','/', $args['userHashId']),
		$request,
		$response,
		$app->getContainer()->get('DataBase'),
		$app->getContainer()->get('twig')
	);
});

$app->get('/files', function (Request $request, Response $response) use ($app) {
	return $app->getContainer()->get('File.Controller')->viewAllUsersFiles(
		$request,
		$response,
		$app->getContainer()->get('DataBase'),
		$app->getContainer()->get('twig')
	);
});

//$app->get('/uploaded-file/{userName}/{userCountFiles}/{fileLink}/{fileName}', function ($request, \Slim\Http\Response $response, $args) use ($app) {
//	return $response->write($app->getContainer()->get('twig')
//		->render('UploadedFileContent.html',
//			[
//				'user' => ['name' => $args['userName'], 'countFiles' => $args['userCountFiles']],
//				'file' => ['link' => $args['fileLink'], 'name' => $args['fileName']]
//			]));
//})->prepare();

$mw = function ($request, $response, Slim\Route $next) {
	$next->setArgument('key', 'value');

//	$response->getBody()->write('BEFORE');
//	$response = $next($request, $response);
//	$response->getBody()->write('AFTER');

	return $next;
};

$app->get('/test', function (Request $request, Response $response, array $args) use ($app) {


//	$response->write($app->getContainer()->get('twig')->render('UsersFiles.html'));
//	var_dump(phpinfo());
//	die();
//	return $app->getContainer()->get('response')->getBody()->write($app->getContainer()->get('twig')->render('testContent.html'));
})->getArgument('key', 'dfg');

$app->run();
