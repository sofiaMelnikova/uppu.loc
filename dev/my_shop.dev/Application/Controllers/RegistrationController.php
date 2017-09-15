<?php

namespace Application\Controllers;

use Application\Models\LoginModel;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Application\Models\RegistrationModel;
use Engine\DbQuery;
use Engine\Validate;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends BaseController
{
    private $app = null;

    /**
     * RegistrationController constructor.
     * @param Application $app
     */
    public function __construct(Application $app) {
        $this->app = $app;
    }

    /**
     * @return RegistrationModel
     */
    public  function newRegistrationModel() {
        return new RegistrationModel();
    }


    /**
     * @param Application $app
     * @param Request $request
     * @return array|static
     */
    public function addUserAction(Application $app, Request $request) {
        $loginModel = new LoginModel();

        $email = $request->get('email');
        $passwordHash = password_hash($request->get('password'), PASSWORD_BCRYPT);
        $phone = $request->get('phone');

        $registrationModel = $this->newRegistrationModel();
        $isUserExist = $registrationModel->isLoginExist($email);
        if ($isUserExist) {
            return ['error' => 'Error: User already exist whith this login.'];
        }

        $validate = new Validate();
        $result = $validate->isEmailValid($app, $email);

        if (!$result) {
            return ['error' => 'Error: Login is not corrected.'];
        }

        if (!is_numeric($phone) || (strlen($phone) != 11)) {
            return ['error' => 'Error: phone is not corrected. '];
        }
        // add method: user done order before only by number and this user want to registrate
        $userId = $registrationModel->isPhoneExist($phone);
        if ($userId) {
            return ['error' => 'Error: phone already exist.'];
        }

        $registrationModel = $this->newRegistrationModel();
        $userId = $registrationModel->saveNewUser($email, $phone, $passwordHash);
        if ($userId === false) {
            return ['error' => 'Error: new user was not created.'];
        }

        $response = Response::create('', 302, ['Location' => 'http://127.0.0.1/catalogue']);
        $token = $loginModel->createTokenForUser();
        $endTokenTime = date("Y-m-d H:i:s", strtotime('now + 60 minutes'));
        $loginModel->addTokenForUser($token, $endTokenTime, $userId);
        $response = $loginModel->createLoginCookie($token, $response);
        return $response;
    }

}