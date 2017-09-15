<?php

namespace Application\Models;


use Application\Controllers\GoodsController;
use Application\ValueObject\GoodFields;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Application\Helper;

class GoodModel extends BaseModel
{

    /**
     * @param string $picture
     * @param GoodFields $goodFields
     * @return bool
     */
    public function addGood (string $picture, GoodFields $goodFields) {
        return ($this->newGoods())->addGood($picture, $goodFields);
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function savePhoto (UploadedFile $file) {
        $uploaddir = '/home/smelnikova/dev/my_shop.dev/web/pictures';
        $uploadfile = time() . $file->getClientOriginalName();
        $file->move($uploaddir, $uploadfile);
        $filePath = 'pictures/' . $uploadfile;
        return $filePath;
    }

    /**
     * @param string $kind
     * @param int $startElement
     * @param int $countElements
     * @return array|mixed
     */
    public function getNamePicturePriceOfKind (string $kind, int $startElement, int $countElements) {
        return ($this->newGoods())->getNamePicturePriceOfKind($kind, $startElement, $countElements);
    }

    /**
     * @param int $idStoke
     * @return array|mixed
     */
    public function getAllOfProduct (int $idStoke) {
        return ($this->newGoods())->getAllOfProduct($idStoke);
    }

    /**
     * @param string|null $kind
     * @return int
     */
    public function getCountProducts (string $kind = null) {
        $result = ($this->newGoods())->getCountProducts($kind);
        return intval(array_shift($result));
    }

    /**
     * @param int $startElement
     * @param int $countElements
     * @return array|mixed
     */
    public function getPictureNameProduct (int $startElement, int $countElements) {
        return ($this->newGoods())->getPictureNameProduct($startElement, $countElements);
    }

    /**
     * @param int $stokeId
     */
    public function deleteProduct (int $stokeId) {
        ($this->newGoods())->deleteProduct($stokeId);
    }

    /**
     * @param array $product
     * @return bool
     */
    public function updateProduct (array $product) {
        return ($this->newGoods())->updateProduct($product);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getContentForShowingBasketForLogoutUser (Request $request) {
        $result = $this->getContentForShowingBasket($this->getProductsFromBasketForLogoutUser($request));
        $result['logout'] = true;
        return $result;
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getContentForShowingBasketForLoginUser (int $userId) {
        $result['logout'] = false;
        return $this->getContentForShowingBasket($this->getProductsFromBasketForLoginUser($userId));
    }

    /**
     * @param array $basketProducts
     * @return array
     */
    private function getContentForShowingBasket (array $basketProducts) {
        $resultSum = 0;
        $products = [];
        foreach ($basketProducts as $key => $value) {
            $product = $this->getAllOfProduct($key);
            $resultSum = $resultSum + $value['sum'];
            $product = array_merge($product, ['countInBasket' => $value['count'], 'sum' => $value['sum']]);
            array_push($products, $product);
        }
        return ['products' => $products,'resultSum' => $resultSum];
    }


    /**
     * @param int $numberOrder
     */
    public function executedOrderForLoginUser (int $numberOrder) {
        $this->newGoods()->formBusketToExecuted($numberOrder);
    }

    /**
     * @param int $userId
     * @param array $basket
     */
    public function executedOrderForLogoutUser (int $userId, array $basket) {
        $this->newGoods()->createAndExecuteNewOrder($userId, $basket);
    }

    /**
     * @param int $userId
     * @return int
     */
    public function getNumberOrdesInBasketForUser (int $userId) {
        $userId = $this->newGoods()->getNumberOrdesInBasketForUser($userId)['id'];
        return intval($userId);
    }

    /**
     * @param Request $request
     * @return int
     */
    public function countProductsInBasketForLogoutUser (Request $request) {
        if (!key_exists('products', ($request->cookies->all()))) {;
            return 0;
        }
        $products = ($request->cookies->all())['products'];
        $products = json_decode($products, true);
        $count = 0;
        foreach ($products as $value) {
            $count = ++$count;
        }
        return $count;
    }

    /**
     * @param int $userId
     * @return int
     */
    public function countProductsInBasketForLoginUser (int $userId) {
        $goods = $this->newGoods();
        $numberOrder = $this->getNumberOrdesInBasketForUser($userId);
        $countProducts = $goods->getCountProductsInBasket($numberOrder);
        return intval($countProducts['COUNT(*)']);
    }

    /**
     * @param int $userId
     * @param array $product
     */
    public function addProductInBasketForLoginUser (int $userId, array $product) {
        $goods = $this->newGoods();
        $numberOrder = $this->getNumberOrdesInBasketForUser($userId);
        if (!empty($numberOrder)) {
            $goods->createNewOrder($userId, $product);
        }
        $goods->addToOrderBasketProduct($numberOrder, $product);
    }

    /**
     * @param Response $response
     * @param Request $request
     * @param array $product
     * @return Response
     */
    public function addProductInBasketForLogoutUser (Response $response, Request $request, array $product) {
        $products = [];
        $cookie = $request->cookies->all();
        if (key_exists('products', $cookie)) {
            $products = json_decode($cookie['products'], true);
        }
        array_push($products, ['cost' => $product['cost'], 'id' => $product['id']]);
        $cookie = new Cookie('products', json_encode($products));
        $response->headers->setCookie($cookie);
        $response->send();
        return $response;
    }

    /**
     * @param Request $request
     * @param bool $forShowBasket
     * @return array|mixed
     */
    public function getProductsFromBasketForLogoutUser (Request $request, bool $forShowBasket = true) {
        if (!key_exists('products', ($request->cookies->all()))) {
            return [];
        }
        if (!$forShowBasket) {
            $products = ($request->cookies->all())['products'];
            return json_decode($products, true);
        }
        $products = ($request->cookies->all())['products'];
        $products = json_decode($products, true);
        $result = [];
        foreach ($products as $key => $value) {
            if (array_key_exists($value['id'], $result)) {
                ++$result[$value['id']]['count'];
                $result[$value['id']]['sum'] += $value['cost'];
            } else {
                $result[$value['id']] = ['count' => 1, 'sum' => $value['cost']];
            }
        }
        return $result;
    }

    /**
     * @param int $userId
     * @return array|mixed
     */
    public function getProductsFromBasketForLoginUser (int $userId) {
        $goods = $this->newGoods();
        $numberOrder = $this->getNumberOrdesInBasketForUser($userId);
        if (empty($numberOrder)) {
            return [];
        }
        $products = $goods->getProductsFromBasket($numberOrder);
        $result = [];
        foreach ($products as $key => $value) {
            if (array_key_exists($value['stoke_id'], $result)) {
                ++$result[$value['stoke_id']]['count'];
                $result[$value['stoke_id']]['sum'] += $value['actual_cost'];
            } else {
                $result[$value['stoke_id']] = ['count' => 1, 'sum' => $value['actual_cost']];
            }
        }
        return $result;
    }


    /**
     * @param int|null $stokeId
     * @param Response $response
     * @param Request|null $request
     * @return Response
     */
    public function deleteProductFromBasketForLogoutUser (Response $response, int $stokeId = null, Request $request = null) {
        if (empty($stokeId)) {
            $response->headers->clearCookie('products');
            return $response;
        }
        $products = ($request->cookies->all())['products'];
        $products = json_decode($products, true);
        foreach ($products as $key => $value) {
            if ($value['id'] == $stokeId) {
                unset($products[$key]);
                $cookie = new Cookie('products', json_encode($products));
                $response->headers->setCookie($cookie);
                $response->send();
                return $response;
            }
        }
    }

    /**
     * @param int $userId
     * @param int|null $stokeId
     */
    public function deleteProductFromBasketForLoginUser (int $userId, int $stokeId = null) {
        $goods = $this->newGoods();
        $numberOrder = $this->getNumberOrdesInBasketForUser($userId);
        $goods->deleteFromBasket($numberOrder, $stokeId);
    }

    /**
     * @param string $kind
     * @return array|null
     */
    public function getFieldsByKindForAddForm (string $kind) {
        $properties = null;
        if ($kind === 'shoes') {
            $properties = ['size' => ['min' => 36, 'max' => 46], 'brand' => true, 'gender' => true, 'color' => true,
                'material' => true, 'producer' => true, 'kind' => 'shoes'];
        }
        if ($kind === 'jacket') {
            $properties = ['size' => ['min' => 38, 'max' => 56], 'brand' => true, 'gender' =>true, 'color' => true,
                'material' => true, 'producer' => true, 'kind' => 'jacket'];
        }
        if ($kind === 'plaid') {
            $properties = ['length' => true, 'width' => true, 'color' => true, 'material' => true, 'producer' => true, 'kind' => 'plaid'];
        }
        return $properties;
    }

}