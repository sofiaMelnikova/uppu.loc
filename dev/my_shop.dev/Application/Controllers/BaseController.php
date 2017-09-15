<?php

namespace Application\Controllers;

use Application\Models\LoginModel;
use Application\Models\GoodModel;
use Application\Models\RegistrationModel;

class BaseController
{
    /**
     * @return LoginModel
     */
    public function newLoginModel () {
        return new LoginModel();
    }

    /**
     * @return GoodModel
     */
    public function newGoodModel () {
        return new GoodModel();
    }

    public function newRegistrationModel () {
        return new RegistrationModel();
    }


}