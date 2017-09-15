<?php
/**
 * Created by PhpStorm.
 * User: smelnikova
 * Date: 20.08.17
 * Time: 9:51
 */

namespace Engine;

use Silex\Application;
use Symfony\Component\Validator\Constraints as Assert;

class Validate
{
    /**
     * @param Application $app
     * @param $email
     * @return bool
     */
    public function isEmailValid (Application $app, $email) {
        $errors = $app['validator']->validate($email, new Assert\Email());
        if (count($errors) > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param Application $app
     * @param array $values
     * @return string
     */
    public function formValidate (Application $app, array $values) {
        $err = '';
        $constraint = new Assert\Collection([
            'stokeId' => new Assert\Regex(['pattern' => '/^[0-9]{0,11}$/', 'message' => 'Error: id incorrect.']),
            'kind' => $this->forKind(),
            'productName' => $this->forProductName(),
            'brand' => $this->forBrand($values['brand']),
            'color' => $this->forColor($values['color']),
            'size' => $this->forSize($values['kind']),
            'material' => $this->forMaterial($values['material']),
            'gender' => $this->forGender($values['gender']),
            'length' => $this->forLength($values['length']),
            'width' => $this->forLength($values['width']),
            'producer' => $this->forProducer($values['producer']),
            'count' => $this->forCount(),
            'cost' => $this->forCost()
        ]);
        $errors = $app['validator']->validate($values, $constraint);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $err = $err . $error->getPropertyPath() . ' ' . $error->getMessage() . "\n";
            }
        }
        return $err;
    }

    /**
     * @return Assert\NotNull
     */
    private function forKind () {
        return new Assert\NotNull(['message' => 'Error: kind is empty.']);
    }

    /**
     * @return array
     */
    private function forProductName () {
        return [new Assert\NotNull(['message' => 'Error: Product`s Name incorrect.']),
            new Assert\Type(['type' => 'string', 'message' => 'Error: Product`s Name must be string'])];
    }
    /**
     * @param $kind
     * @return Assert\NotNull|Assert\Range|Assert\Type
     */
    private function forSize ($kind) {
        if ($kind === 'shoes') {
            return new Assert\Range(['min' => 36, 'max' => 46,
                'invalidMessage' => 'Shoes size mast have range in between from 36 to 46']);
        }
        if ($kind === 'jacket') {
            return new Assert\Range(['min' => 38, 'max' => 56,
                'invalidMessage' => 'Jacket size mast have range in between from 38 to 56']);
        }
        if (($kind === 'plaid') || ($kind === '')) {
            return new Assert\Type(['type' => 'string']);
        }
        return new Assert\NotNull(['message' => 'Error: size is empty.']);
    }


    /**
     * @param $brand
     * @return Assert\NotNull|Assert\Type
     */
    private function forBrand ($brand) {
        if (is_null($brand)) {
            return new Assert\NotNull(['message' => 'Error: brand is empty.']);
        }
        if ($brand === '') {
            return new Assert\Type(['type' => 'string']);
        }
        return new Assert\Type(['type' => 'string', 'message' => 'Error: brand must be string']);

    }

    /**
     * @param $gender
     * @return Assert\EqualTo|Assert\NotNull|Assert\Type
     */
    private function forGender ($gender) {
        if (is_null($gender)) {
            return new Assert\NotNull(['message' => 'Error: gender is empty.']);
        }
        if ($gender === '') {
            return new Assert\Type(['type' => 'string']);
        }
        if ($gender != 'man') {
            return new Assert\EqualTo(['value' => 'woman', 'message' => 'Error: gender must be man or woman']);
        }
    }

    /**
     * @param $color
     * @return Assert\NotNull|Assert\Type
     */
    private function forColor ($color) {
        if (is_null($color)) {
            return new Assert\NotNull(['message' => 'Error: color is empty.']);
        }
        return new Assert\Type(['type' => 'string']);
    }

    /**
     * @param $material
     * @return Assert\NotNull|Assert\Type
     */
    private function forMaterial ($material) {
        if (is_null($material)) {
            return new Assert\NotNull(['message' => 'Error: material is empty.']);
        }
        return new Assert\Type(['type' => 'string']);
    }

    /**
     * @param $producer
     * @return Assert\NotNull|Assert\Type
     */
    private function forProducer ($producer) {
        if (is_null($producer)) {
            return new Assert\NotNull(['message' => 'Error: producer is empty.']);
        }
        return new Assert\Type(['type' => 'string']);
    }

    /**
     * @param $lengthOrWidth
     * @return Assert\NotNull|Assert\Type
     */
    private function forLength ($lengthOrWidth) {
        if (is_null($lengthOrWidth)) {
            return new Assert\NotNull(['message' => 'Error: length is empty.']);
        }
        if ($lengthOrWidth === '') {
            return new Assert\Type(['type' => 'string']);
        }
        return new Assert\Type(['type' => 'numeric', 'message' => 'Error: length must be number']);

    }

    /**
     * @return array
     */
    private function forCost () {
        return [new Assert\NotNull(['message' => 'Error: Cost incorrect.']),
            new Assert\Type(['type' => 'numeric', 'message' => 'Error: Cost must be number'])];
    }

    /**
     * @return array
     */
    private function forCount () {
        return [new Assert\NotNull(['message' => 'Error: count incorrect.']),
            new Assert\Type(['type' => 'numeric', 'message' => 'Error: Count must be number'])];
    }

}