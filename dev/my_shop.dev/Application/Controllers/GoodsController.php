<?php

namespace Application\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Engine\Pagination;
use Application\Models\LoginModel;
use Symfony\Component\HttpFoundation\Response;

class GoodsController extends BaseController
{
    private $productsOnPage = 4;
    private $showPages = 3;

    /**
     * @param string $kind
     * @param int $actualPage
     * @return array
     */
    public function showCatalogAction (string $kind, int $actualPage, Request $request) { // good =)
        $loginModel = $this->newLoginModel();
        $goodModel = $this->newGoodModel();
        $countProducts = $goodModel->getCountProducts($kind);
        $pagination = new Pagination();
        $countPages = $pagination->getCountPagesOrGroups($countProducts, $this->productsOnPage);
        if ($actualPage > $countPages) {
            $actualPage = $countPages;
        }
        $pagesMinMax = $pagination->getMainMaxPages($actualPage, $this->showPages, $countPages);
        $productsMinMax = $pagination->getMinMaxElementsOnPage($actualPage, $this->productsOnPage);
        $products = $goodModel->getNamePicturePriceOfKind($kind, $productsMinMax['min'], $this->productsOnPage);
        $id = $loginModel->isUserLogin($request);
        $admin = false;
        $login = false;
        if ($id) {
            $countProductsInBasket = $goodModel->countProductsInBasketForLoginUser($id);
            $admin = $loginModel->isAdmin($id);
            $login = $loginModel->getLogin($id);
        } else {
            $countProductsInBasket = $goodModel->countProductsInBasketForLogoutUser($request);
        }
        return ['products' => $products, 'pages' => $pagesMinMax, 'kind' => $kind, 'sumPages' => $countPages,
            'countProductsInBasket' => $countProductsInBasket, 'admin' => $admin, 'login' => $login];

    }

    /**
     * @param Request $request
     * @return array
     */
    public function showProductInfoAction (Request $request) {
        $stokeId = intval($request->get('id'));
        $goodModel = $this->newGoodModel();
        $product = $goodModel->getAllOfProduct($stokeId);
        return ['product' => $product];
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return bool|Response
     */
    public function takeToTheBasketAction (Request $request, Response $response) { // good =)
        $goodModel = $this->newGoodModel();
        $loginModel = $this->newLoginModel();
        $stokeId = intval($request->get('id'));
        $userId = $loginModel->isUserLogin($request);
        $product = $goodModel->getAllOfProduct($stokeId);
        if (!$userId) {
            // return false; // Error: user is not login return loginPage
            return $goodModel->addProductInBasketForLogoutUser($response, $request, $product);
        }
        $goodModel->addProductInBasketForLoginUser($userId, $product);
        return $response;
    }

    /**
     * @param Response $response
     * @param Request $request
     * @return Response
     */
    public function deleteFormBasketAction (Response $response, Request $request) { // good =)
        $loginModel = $this->newLoginModel();
        $userId = $loginModel->isUserLogin($request);
        $goodModel = $this->newGoodModel();
        $stokeId = intval($request->get('id'));
        if ($userId) {
            $goodModel->deleteProductFromBasketForLoginUser($userId, $stokeId);
            $content = $goodModel->getContentForShowingBasketForLoginUser($userId);
        } else {
            $response = $goodModel->deleteProductFromBasketForLogoutUser($response, $stokeId, $request);
            $content = $goodModel->getContentForShowingBasketForLogoutUser($request);
        }
        $response->setContent($content);
        return $response;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function showBasketAction (Request $request) { // good =)
        $userId = $this->newLoginModel()->isUserLogin($request);
        if ($userId) {
            return $this->newGoodModel()->getContentForShowingBasketForLoginUser($userId);
    }
        return $this->newGoodModel()->getContentForShowingBasketForLogoutUser($request);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return array|int|Response
     */
    public function createOrderAction (Request $request, Response $response) { // good =)
        $userId = $this->newLoginModel()->isUserLogin($request);
        $goodModel= $this->newGoodModel();
        if ($userId) {
            $goodModel->executedOrderForLoginUser($userId);
        }
        $registrationModel = $this->newRegistrationModel();
        $userId = $registrationModel->registrateNewUserByPhone($request->get('phone'));
        if (is_array($userId)) {
            return $userId;
        }
        $basket =$goodModel->getProductsFromBasketForLogoutUser($request, false);
        $goodModel->executedOrderForLogoutUser($userId, $basket);
        return $goodModel->deleteProductFromBasketForLogoutUser($response);
    }


}