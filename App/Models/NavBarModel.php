<?php

namespace App\Models;


use Engine\DataBase;
use Slim\Http\Request;

class NavBarModel extends AbstractModel {

	/**
	 * @param Request $request
	 * @param DataBase $dataBase
	 * @return array
	 */
	public function getParams(Request $request, DataBase $dataBase): array {
		if ((new LoginModel())->isLoginUser($request, $dataBase)) {
			$user = (new UserModel())->getIdNameCountFilesByEnterCookie($request, $dataBase);
			return [
				'userLogin'		=> true,
				'hashUserId'	=> $user['hashUserId'],
			];
		}

		if ((new FileModel())->getCountUploadedFilesForLogoutUser($request, $dataBase)) {
			return [
				'userLogout'	=> 'true',
				'hashUserId'	=> 'anonymously',
			];
		}

		return ['defaultNavBar' => true, 'userLogout'	=> 'true'];
	}

}