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
			'hashUserId' => $this->getHashUserIdByUserId((int) $sqlResponse['id'])
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

	/**
	 * @param int $id
	 * @return string
	 */
	public function getHashUserIdByUserId(int $id): string {
		return str_replace('/', '__to_slash__', password_hash($id, PASSWORD_BCRYPT));
	}

	/**
	 * @param int $userId
	 * @param DataBase $dataBase
	 * @return array
	 */
	public function getNameCountUploadedFilesById(int $userId, DataBase $dataBase): array {
		$sqlResponse = $this->getUserTdg($dataBase)->selectNameCountUploadedFilesById($userId);
		return [
			'id'		=> $userId,
			'name'		=> $sqlResponse['name'],
			'countFiles'	=> $sqlResponse['COUNT(*)'],
			'hashUserId' => $this->getHashUserIdByUserId((int) $sqlResponse['id'])
		];
	}

}