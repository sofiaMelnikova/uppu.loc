<?php

namespace Application\Models;

use ClassesWithParents\D;
use Engine\DbQuery;
use Application\TableDataGateway\Login;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Application\Helper;
class LoginModel
{
    /**
     * @param DbQuery $dbQuery
     * @return Login
     */
    public function newLogin (DbQuery $dbQuery) {
        return new Login($dbQuery);
    }

    /**
     * @param string $token
     * @return bool|int
     */
    public function getUserIdByToket (string $token) {
        $result = ($this->newLogin(new DbQuery()))->getUserIdByToken($token);
        if ($result) {
            return intval($result['id']);
        }
        return false;
    }

    /**
     * @param string $login
     * @param string $password
     * @return bool|int
     */
    public function isUserExist (string $login, string $password) {
        $user = ($this->newLogin(new DbQuery()))->isUserExist($login);
        if (!$user) {
            return false;
        }

        if (password_verify($password, $user['password_hash'])) {
            return intval($user['id']);
        }
        return false;
    }

    /**
     * @param Request $request
     * @return bool|int
     */
    public function isUserLogin (Request $request) {
        if (!key_exists('user', ($request->cookies->all()))) {
            return false;
        }
        $token = ($request->cookies->all())['user'];
        $userId = $this->getUserIdByToket($token);
        if ($userId) {
            return $userId;
        }
        return false;
    }

    /**
     * @param int $useId
     * @return bool
     */
    public function isAdmin (int $useId) {
        $admin = ($this->newLogin(new DbQuery()))->isAdmin($useId);
        $admin = array_shift($admin);
        if ($admin === '1') {
            return true;
        }
        return false;
    }

    /**
     * @param string $token
     * @param Response $response
     * @return Response
     */
    public function createLoginCookie(string $token, Response $response) {
        $cookie = new \Symfony\Component\HttpFoundation\Cookie('user', $token, strtotime('now + 60 minutes'));
        $response->headers->setCookie($cookie);
        $response->send();
        return $response;
    }

    /**
     * @param int $userId
     * @return mixed
     */
    public function getLogin (int $userId) {
        $result = ($this->newLogin(new DbQuery))->getLogin($userId);
        return $result['login'];
    }

    /**
     * @return bool|string
     */
    public function createTokenForUser () {
        $time = 1;
        while ($time <= 3) {
            $helper = new Helper();
            $randomString = $helper->generateRandomString();
            $login = $this->newLogin(new DbQuery);
            if (!($login->getUserIdByToken($randomString))) {
                return $randomString;
            }
            $time++;
        }
        return false;
    }

    /**
     * @param string $token
     * @param string $endTokenTime
     * @param int $userId
     */
    public function addTokenForUser (string $token, string $endTokenTime, int $userId) {
        ($this->newLogin(new DbQuery()))->addTokenForUser($token, $endTokenTime, $userId);
    }

    /**
     * @param string $token
     */
    public function sendNowTimeForToken (string $token) {
        $nowTime = date("Y-m-d H:i:s", strtotime("now"));
        ($this->newLogin(new DbQuery()))->updateTimeForToken($token, $nowTime);
    }
}