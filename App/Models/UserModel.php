<?php

namespace App\Models;


use Engine\DataBase;
use Slim\Http\Request;

class UserModel extends AbstractModel {

	/**
	 * @param Request $request
	 * @param DataBase $dataBase
	 * @return array
	 */
	public function getIdNameCountFilesByEnterCookie(Request $request, DataBase $dataBase): array {
		$enterCookie = $request->getCookieParam('login', '');

		if (empty($enterCookie)) {
			return [];
		}

		$sqlResponse = $this->getUserTdg($dataBase)->selectIdNameCountUploadedFilesByEnterCookie($enterCookie);
		return [
			'id'		=> $sqlResponse['id'],
			'name'		=> $sqlResponse['name'],
			'countFiles'	=> $sqlResponse['COUNT(*)']
		];
	}

}