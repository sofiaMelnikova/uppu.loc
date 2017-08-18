<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

use Application\Controllers\RegistrationController;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../src/app.php';
require __DIR__.'/../config/prod.php';
require __DIR__.'/../src/controllers.php';

$app['session']->start();

// render with help of Twig
$app->register(new \Silex\Provider\TwigServiceProvider(), ['twig.path' => __DIR__ . '/../Application/Views']);
$app->get('/registration', function () use ($app) {
   return $app['twig']->render('registration.php');
});


$app->post('/registration', function (Request $request) use($app) {
    (new RegistrationController($app))->addUserAction($request);
    return 'good';
});

$app->run();
