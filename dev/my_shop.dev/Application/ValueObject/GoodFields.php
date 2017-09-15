<?php

namespace Application\ValueObject;


use Symfony\Component\HttpFoundation\Request;

class GoodFields
{
    private $stokeId = null;
    private $kind = null;
    private $productName = null;
    private $brand = null;
    private $color =null;
    private $size = null;
    private $material = null;
    private $gender = null;
    private $length = null;
    private $width = null;
    private $producer = null;
    private $count = null;
    private $cost = null;

    /**
     * GoodFields constructor.
     * @param Request $request
     */
    public function __construct(Request $request) {
        $keys = ['stokeId', 'kind', 'productName', 'brand', 'color', 'size', 'material', 'gender', 'length', 'width', 'producer', 'count' ,'cost'];
        foreach ($keys as $key) {
            $this->$key = $this->forConstruct($request, $key);
        }
    }

    /**
     * @param Request $request
     * @param string $key
     * @return mixed|null|string
     *
     */
    private function forConstruct (Request $request, string $key) {
        if ($request->request->has($key)) {
            $product = $request->get($key);
            if (empty($product)) {
                return null;
            }
            return $product;
        }
        return '';
    }

    /**
     * @return array
     */
    public  function getAllfields () {
        return ['stokeId' => $this->stokeId,
            'kind' => $this->kind,
            'productName' => $this->productName,
            'brand' => $this->brand,
            'color' => $this->color,
            'size' => $this->size,
            'material' => $this->material,
            'gender' => $this->gender,
            'length' => $this->length,
            'width' => $this->width,
            'producer' => $this->producer,
            'count' => $this->count,
            'cost' => $this->cost];
    }

    /**
     * @return null|string
     */
    public function getKind () {
        return $this->kind;
    }

    /**
     * @return null|string
     */
    public function getProductName () {
        return $this->productName;
    }

    /**
     * @return null|string
     */
    public function getColor () {
        return $this->color;
    }

    /**
     * @return null|string
     */
    public function getSize () {
        return $this->size;
    }

    /**
     * @return null|string
     */
    public function getMaterial () {
        return $this->material;
    }

    /**
     * @return null|string
     */
    public function getGender () {
        return $this->gender;
    }

    /**
     * @return null|string
     */
    public function getCount () {
        return $this->count;
    }

    /**
     * @return null|string
     */
    public function getCost () {
        return $this->cost;
    }

    /**
     * @return null|string
     */
    public function getStokeId () {
        return $this->stokeId;
    }

    /**
     * @return null|string
     */
    public function getLength () {
        return $this->length;
    }

    /**
     * @return null|string
     */
    public function getBrand () {
        return $this->brand;
    }

    /**
     * @return null|string
     */
    public function getWidth () {
        return $this->width;
    }

    /**
     * @return null|string
     */
    public function getProducer () {
        return $this->producer;
    }
}