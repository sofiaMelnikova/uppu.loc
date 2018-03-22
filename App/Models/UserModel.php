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
			'countFiles'	=> $sqlResponse['COUNT(*)'],
			'hashUserId' => str_replace('/', '_', password_hash($sqlResponse['id'], PASSWORD_BCRYPT))
		];
	}

	/**
	 * @param string $hashId
	 * @param DataBase $dataBase
	 * @return int
	 */
	public function getUserIdByUserIdHash(string $hashId, DataBase $dataBase): int {
		$sqlResult = $this->getUserTdg($dataBase)->selectActiveUsersId();
		$usersIds = array_column($sqlResult, 'id');
		$actualUserId = 0;

		foreach ($usersIds as $userId) {
			if (password_verify($userId, $hashId)) {
				$actualUserId = (int) $userId;
				break;
			}
		}

		return $actualUserId;
	}

}