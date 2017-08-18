<?php

namespace Application\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Application\Models\RegistrationModel;
use Engine\DbQuery;
use Symfony\Component\HttpFoundation\Cookie;


class RegistrationController
{
    private $app = null;

    public function __construct(Application $app) {
        $this->app = $app;
    }

    public  function newRegistrationModel () {
        return new RegistrationModel(new DbQuery());
    }

    public function addUserAction (Request $request) {
        $email = $request->get('email');
        $passwordHash = password_hash($request->get('password'), PASSWORD_BCRYPT);
        $registrationModel = new RegistrationModel(new DbQuery());
        $result = $registrationModel->saveNewUser($email, $passwordHash);
        if ($result) {
            $this->createLoginCookie();
        }
        // return false; // Error
    }

    public function createLoginCookie () {
        $cookie = new Cookie('');
        echo $this->app['session']->getId();

    }
}