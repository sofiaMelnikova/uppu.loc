<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
use Engine\Session;
use \Symfony\Component\HttpFoundation\Session\Session as SymfonySession;
use Application\Controllers\Registration;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
require_once __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../src/app.php';
require __DIR__.'/../config/prod.php';
require __DIR__.'/../src/controllers.php';

session_set_cookie_params(1200);

// render with help of Twig
$app->register(new \Silex\Provider\TwigServiceProvider(), ['twig.path' => __DIR__.'/../Application/Views']);
$app->get('/registration', function () use ($app) {
   return $app['twig']->render('registration.php');
});


$app->post('/registration', function (Request $request) {
    $email = $request->get('email');
    $passwordHash = password_hash($request->get('password'), PASSWORD_BCRYPT);
    $registration = new Registration(new Application);
    $registration->registrationAction($email, $passwordHash);
    return 'good';
});

$app->run();
