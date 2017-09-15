<?php

namespace Application\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Engine\Validate;
use Application\ValueObject\GoodFields;
use Engine\Pagination;

class GoodsAdminController extends BaseController
{
    private $productsOnPageAdmin = 4;
    private $showPagesAdmin = 3;

    /**
     * @param Request $request
     * @param string|null $kind
     * @return array
     */
    public function showFormAddGood (Request $request, string $kind = null) {
        if (empty($kind)) {
            $kind = $request->get('kind');
        }
        $goodModel = $this->newGoodModel();
        $properties = $goodModel->getFieldsByKindForAddForm($kind);
        return ['properties' => $properties];
    }

    /**
     * @param Application $app
     * @param Request $request
     * @return array|string
     */
    public function addGoodAction (Application $app, Request $request) {
        $goodFields = new GoodFields($request);
        $validate = new Validate();
        $goodModel = $this->newGoodModel();
        $result = $validate->formValidate($app, $goodFields->getAllfields());
        if (!empty($result)) {
            $properties = $goodModel->getFieldsByKindForAddForm($goodFields->getKind());
            $allFields = $goodFields->getAllfields();
            return ['properties' => $properties, 'product' => $allFields, 'error' => $result];
        }
        $file = $request->files->get('photo');
        $filePath = 'pictures/addPhoto.png';
        if (!empty($file)) {
            $filePath = $goodModel->savePhoto($file);
        }
        $goodModel->addGood($filePath, $goodFields);
        return [];
    }

    /**
     * @param int $actualPage
     * @return array
     */
    public function showAdminGoodsAction (int $actualPage) {
        $goodModel = $this->newGoodModel();
        $countProducts = $goodModel->getCountProducts();
        $pagination = new Pagination();
        $countPages = $pagination->getCountPagesOrGroups($countProducts, $this->productsOnPageAdmin);
        if ($actualPage > $countPages) {
            $actualPage = $countPages;
        }
        $pagesMinMax = $pagination->getMainMaxPages($actualPage, $this->showPagesAdmin, $countPages);
        $productsMinMax = $pagination->getMinMaxElementsOnPage($actualPage, $this->productsOnPageAdmin);
        $products = $goodModel->getPictureNameProduct($productsMinMax['min'], $this->productsOnPageAdmin);
        return ['products' => $products, 'pages' => $pagesMinMax, 'sumPages' => $countPages];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function changeProductAction (Request $request) {
        $stokeId = intval($request->get('id'));
        $goodModel = $this->newGoodModel();
        $product = $goodModel->getAllOfProduct($stokeId);
        return ['product' => $product];
    }

    /**
     * @param Request $request
     */
    public function deleteProductAction (Request $request) {
        $stokeId = intval($request->get('id'));
        $goodModel = $this->newGoodModel();
        $goodModel->deleteProduct($stokeId);
    }

    /**
     * @param Application $app
     * @param Request $request
     * @return array|string
     */
    public function saveChangeProductAction (Application $app, Request $request) {
        $goodFields = new GoodFields($request);
        $validate = new Validate();
        $goodModel = $this->newGoodModel();
        $result = $validate->formValidate($app, $goodFields->getAllfields());
        if (!empty($result)) {
            $result = array_merge($goodFields->getAllfields(), ['error' => $result, 'product' => $goodModel->getAllOfProduct(intval($goodFields->getStokeId()))]);
            return $result;
        }
        $file = $request->files->get('photo');
        $filePath = '';
        if (!empty($file)) {
            $filePath = $goodModel->savePhoto($file);
        }
        $product = $goodFields->getAllfields();
        $product = array_merge($product, ['picture' => $filePath]);
        $goodModel->updateProduct($product);
        return ['product' => $goodModel->getAllOfProduct(intval($goodFields->getStokeId()))];
    }


}