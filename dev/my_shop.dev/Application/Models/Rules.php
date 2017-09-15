<?php
/**
 * Created by PhpStorm.
 * User: smelnikova
 * Date: 05.09.17
 * Time: 16:19
 */

namespace Application\Models;

use Symfony\Component\HttpFoundation\Request;


class Rules
{
    /**
     * @param Request $request
     * @return bool
     */
    public function isLoginAdmin (Request $request) {
        $loginModel = new LoginModel();
        $id = $loginModel->isUserLogin($request);
        if (!$id || !($loginModel->isAdmin(intval($id)))) {
            return false;
        }
        return true;
    }
}