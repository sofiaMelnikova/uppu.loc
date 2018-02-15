<?php

namespace App\Models;


use Engine\DataBase;
use Slim\Http\Cookies;

class FileModel extends AbstractModel {

	/**
	 * @param string $value
	 * @return string[]
	 */
	public function setAddedFileCookie(string $value) {
		$cookies = new Cookies();
		$cookies->set('added_file', ['value' => $value, 'expires' => '+20 minutes']); //date(‘Y-m-d H:i:s’, time() + 1200)
		return $cookies->toHeaders();
	}

	/**
	 * @param DataBase $dataBase
	 * @param string $type
	 * @param array $file
	 * @param array $downloadInfo
	 * @return int (count string add in table: "downloads_info")
	 * @throws \Exception
	 */
	public function addNewFileAnonym(DataBase $dataBase, string $type, array $file, array $downloadInfo):int {
		$typeId = $this->getTypeIdForFile($type, $dataBase);

		if ($typeId === '') {
			throw new \Exception('Type of uploaded file in absent!');
		}

		$file['typeId'] = $typeId;

		$fieldsForFile = ['originalName', 'originalExtension', 'pathTo', 'size', 'typeId', 'expireTime', 'updatedAt'];
		$fieldsDownloadInfo = ['addedFileCookie', 'downloadDate'];

		$allFieldsForFile = true;
		$allFieldsForDownloadInfo = true;

		foreach ($fieldsForFile as $fileField) {
			$allFieldsForFile *= array_key_exists($fileField, $file);
		}
		unset($fileField);

		foreach ($fieldsDownloadInfo as $downloadInfoField) {
			$allFieldsForDownloadInfo *= array_key_exists($downloadInfoField, $downloadInfo);
		}
		unset($downloadInfoField);

		if (!$allFieldsForFile || !$allFieldsForDownloadInfo) {
			throw new \Exception("Can not save in to DB this params: fields params error.");
		}

		return $this->getFileTdg($dataBase)->addNewFileAnonym($file, $downloadInfo);
	}

	/**
	 * @param string $typeName
	 * @param DataBase $dataBase
	 * @return string ('' - if typeName absent in data base)
	 */
	public function getTypeIdForFile(string $typeName, DataBase $dataBase): string {
		$allTypes = $this->getAllFileTypes($dataBase);
		return (in_array($typeName, $allTypes)) ? $this->getFileTdg($dataBase)->getTypeIdForFile($typeName)['id'] : '';
	}

	/**
	 * @param DataBase $dataBase
	 * @return array
	 */
	public function getAllFileTypes(DataBase $dataBase): array {
		$resuluRequest = $this->getFileTdg($dataBase)->getAllFileTypes();
		$types = [];

		foreach ($resuluRequest as $value) {
			array_push($types, $value['type']);
		}

		return $types;
	}

	/**
	 * @param int $userId
	 * @param string $addedFileCookie
	 * @param DataBase $dataBase
	 * @return int
	 */
	public function addUserIdToDownloadsInfoByCookie(int $userId, string $addedFileCookie, DataBase $dataBase): int {
		return $this->getFileTdg($dataBase)->addUserIdToDownloadsInfoByCookie($userId, $addedFileCookie);
	}

	/**
	 * @param string $addedFileCookie
	 * @param DataBase $dataBase
	 * @return int
	 */
	public function getCountFilesByAddedFileCookie(string $addedFileCookie, DataBase $dataBase): int {
		return (int) $this->getFileTdg($dataBase)->getCountFilesByAddedFileCookie($addedFileCookie)['COUNT(*)'];
	}



}