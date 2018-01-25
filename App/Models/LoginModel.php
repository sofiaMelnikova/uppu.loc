<?php

namespace App\Models;

use Engine\DataBase;
use Slim\Http\Cookies;
use Slim\Http\Request;
use Twig\Sandbox\SecurityNotAllowedTagError;

class LoginModel extends AbstractModel {

	/**
	 * @param string $value
	 * @return string[]
	 */
	public function setInitCookie (string $value) {
		$cookies = new Cookies();
		$cookies->set('login', ['value' => $value, 'expires' => '+20 minutes']); //date(‘Y-m-d H:i:s’, time() + 1200)
		return $cookies->toHeaders();
	}

	/**
	 * @param string $email
	 * @param DataBase $dataBase
	 * @return string
	 */
	public function getPasswordHash (string $email, DataBase $dataBase): string {
		$loginTdg = $this->getLoginTdg($dataBase);
		$passwordHash = $loginTdg->getPasswordHash($email);
		return $passwordHash ? array_shift($passwordHash) : '';
	}

	/**
	 * @param string $email
	 * @param string $password
	 * @param DataBase $dataBase
	 * @return array
	 */
	public function checkPassword (string $email, string $password, DataBase $dataBase): array {
		$passwordHash = $this->getPasswordHash($email, $dataBase);

		if (!$passwordHash) {
			return ['email' => 'User absences with this e-mail'];
		}

		if (!password_verify($password, $passwordHash)) {
			return ['password' => 'Incorrect Password'];
		}

		return [];
	}

	/**
	 * @param string $email
	 * @param string $newCookieValue
	 * @param DataBase $dataBase
	 * @return int - count updated strings
	 */
	public function updateEnterCookie (string $email, string $newCookieValue, DataBase $dataBase): int {
		$loginTdg = $this->getLoginTdg($dataBase);
		return $loginTdg->updateEnterCookie($email, $newCookieValue, date('Y-m-d H:i:s'));
	}

	/**
	 * @param Request $request
	 * @return string
	 */
	public function getLoginCookie (Request $request): string {
		return $request->getCookieParam('login', '');
	}

	/**
	 * @param string $loginCookie
	 * @param DataBase $dataBase
	 * @return int - 0, if user absent
	 */
	public function getUserIdByCookie (string $loginCookie, DataBase $dataBase): int {
		$loginTdg = $this->getLoginTdg($dataBase);
		$result = $loginTdg->findUserByLoginCookie($loginCookie);
		return $result ? (int) array_shift($result) : 0;
	}

	/**
	 * @param Request $request
	 * @param DataBase $dataBase
	 * @return bool
	 */
	public function isLoginUser (Request $request, DataBase $dataBase):bool {
		$loginCookie = $this->getLoginCookie($request);

		if (!$loginCookie) {
			return false;
		}

		$userId = $this->getUserIdByCookie($loginCookie, $dataBase);

		return $userId ? true : false;
	}

	/**
	 * @param Request $request
	 * @param DataBase $dataBase
	 * @return int - 0, if user absent or user was logout
	 */
	public function getActiveUserId (Request $request, DataBase $dataBase): int {
		$loginCookie = $this->getLoginCookie($request);

		if (!$loginCookie) {
			return 0;
		}

		return $this->getUserIdByCookie($loginCookie, $dataBase);
	}

}