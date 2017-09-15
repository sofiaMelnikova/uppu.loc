<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

use Application\Controllers\RegistrationController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \Application\Controllers\LoginController;
use \Application\Controllers\GoodsController;
use Application\Controllers\GoodsAdminController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Application\Models\Rules;

require_once __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../src/app.php';
require __DIR__.'/../config/prod.php';
require __DIR__.'/../src/controllers.php';


$app['session']->start();

$app->register(new \Silex\Provider\TwigServiceProvider(), ['twig.path' => __DIR__ . '/../Application/Views']);

$app->get('/registration', function () use ($app) {
   return $app['twig']->render('registration.php');
});

$app->post('/registration', function (Request $request) use($app) {
    $result = (new RegistrationController($app))->addUserAction($app, $request);
    if (is_array($result)) {
        return $app['twig']->render('registration.php', $result);
    }
    return $result;
});

$app->get('/login', function() use ($app) {
    return $app['twig']->render('login.php');
});

$app->post('/login', function (Request $request) use ($app) {
    $result = (new LoginController())->userLoginAction($request);
    if (is_array($result)) {
        return $app['twig']->render('login.php', $result);
    }
    return $result;
});

$app->get('/catalogue/{kind}/{page}', function ($kind, $page, Request $request) use ($app) {
    $page = intval($page);
    $a = (new GoodsController())->showCatalogAction($kind, $page, $request);
    return $app['twig']->render('catalogue.php', $a);
})
    ->value('kind', 'shoes')
    ->value('page', 1);

$app->get('/addGood', function (Request $request) use ($app){
    $result = (new GoodsAdminController())->showFormAddGood($request);
    return $app['twig']->render('addGood.php', $result);
})->before(function (Request $request) {
    $result = (new Rules())->isLoginAdmin($request);
    if (!$result) {
        return new RedirectResponse('/catalogue');
    }
});

$app->post('/addGood', function (Request $request) use ($app) {
    $result = (new GoodsAdminController())->addGoodAction($app, $request);
    if (!empty($result)) {
        return $app['twig']->render('addGood.php', $result);
    }
    $response = Response::create('', 302, ['Location' => 'http://127.0.0.1/adminGoods']);
    return $response;
})->before(function (Request $request) {
    $result = (new Rules())->isLoginAdmin($request);
    if (!$result) {
        return new RedirectResponse('/catalogue');
    }
});


$app->get('/product' , function (Request $request) use ($app){
    $result = (new GoodsController())->showProductInfoAction($request);
    return $app['twig']->render('productInfo.php', $result);
});

$app->get('/adminGoods/{page}', function ($page) use ($app) {
    $page = intval($page);
    $result = (new GoodsAdminController())->showAdminGoodsAction($page);
    return $app['twig']->render('adminGoods.php', $result);
})->value('page',1)
    ->before(function (Request $request) {
        $result = (new Rules())->isLoginAdmin($request);
        if (!$result) {
            return new RedirectResponse('/catalogue');
        }
    });

$app->post('/deleteProduct', function (Request $request) use ($app) {
    (new GoodsAdminController())->deleteProductAction($request);
    return Response::create('', 302, ['Location' => 'http://127.0.0.1/adminGoods']);
})->before(function (Request $request) {
    $result = (new Rules())->isLoginAdmin($request);
    if (!$result) {
        return new RedirectResponse('/catalogue');
    }
});

$app->get('/editProduct', function (Request $request) use ($app) {
    $result = (new GoodsAdminController())->changeProductAction($request);
    return $app['twig']->render('editProduct.php', $result);
})->before(function (Request $request) {
    $result = (new Rules())->isLoginAdmin($request);
    if (!$result) {
        return new RedirectResponse('/catalogue');
    }
});

$app->post('/saveChangeProduct', function (Request $request) use ($app) {
    $result = (new GoodsAdminController())->saveChangeProductAction($app, $request);
    return $app['twig']->render('editProduct.php', $result);
})->before(function (Request $request) {
    $result = (new Rules())->isLoginAdmin($request);
    if (!$result) {
        return new RedirectResponse('/catalogue');
    }
});
$app->get('/takeToTheBasket', function (Request $request) use ($app) {
    $response = Response::create('', 302, ['Location' => $_SERVER['HTTP_REFERER']]);
    $result = (new GoodsController())->takeToTheBasketAction($request, $response);
    if (!$result) {
        return $app['twig']->render('login.php', ['error' => 'Please, log in before you will be add goods to the basket.']);
    }
    return $result;
});

$app->get('/showBasket', function (Request $request) use ($app) {
    $result = (new GoodsController())->showBasketAction($request);
    return $app['twig']->render('basket.php', $result);
});

$app->get('/deleteProductFromBasket', function (Request $request) use ($app) {
    $response = Response::create('', 302, ['Location' => $_SERVER['HTTP_REFERER']]);
    $result = (new GoodsController())->deleteFormBasketAction($response, $request);
    return $result;
});

$app->post('/createOrder', function (Request $request) use ($app) {
    $response = Response::create('', 302, ['Location' => 'http://127.0.0.1/catalogue']);
    $result = (new GoodsController())->createOrderAction($request, $response);
//    if (is_array($result)) {
//        $result = (new GoodsController())->showBasketAction($request, $result);
//        return $app['twig']->render('basket.php', $result);
//    }
    return $response;

});

$app->get('/logout', function (Request $request) use ($app) {
    $response = Response::create('', 302, ['Location' => 'http://127.0.0.1/login']);
    return (new LoginController())->logoutAction($response, $request);
});

$app->get('/historyOfOrders', function () use ($app) {
    return $app['twig']->render('historyOfOrders.php');
});

$app->get('/test', function (Request $request) use ($app){
    $registrationModel = new \Application\TableDataGateway\Registration(new \Engine\DbQuery());
    $result = $registrationModel->addNewUserByPhone('88003332200');
    var_dump($result);
    return 'good';
});


$app->run();
