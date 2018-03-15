<?php

namespace App\Models;


use Engine\DataBase;
use Slim\Http\Cookies;
use Slim\Http\UploadedFile;
use Slim\Http\Request;
use Psr\Http\Message\UploadedFileInterface;

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
	 * @param array $uploadedFile
	 * @return array
	 */
	public function validateUploadedFile(array $uploadedFile): array {
		if (empty($uploadedFile) || empty($uploadedFile['uploadedFile'])) {
			return [
				'name' 		=> 'MainContent.html',
				'params'	=> ['errors' => ['file' => 'Something went wrong. File was not uploaded, please, try again.']]
			];
		}

		$errorString = $this->getValidator()->file($uploadedFile['uploadedFile'], '200M');

		if ($errorString) {
			return [
				'name'		=> 'MainContent.html',
				'params'	=> ['errors' => ['file' => $errorString]]
			];
		}

		return [];
	}

	/**
	 * @param UploadedFileInterface $uploadedFile
	 * @return string
	 */
	public function moveUploadedFile(UploadedFileInterface $uploadedFile): string {
		$newFileName = $this->getHelper()->getRandomString([], 7) . time();
		$uploadedFile->moveTo('Assets/UsersFiles/Image/' . $newFileName);
		return $newFileName;
	}

	/**
	 * @param string $newFileName
	 * @param UploadedFileInterface $uploadedFile
	 * @param Request $request
	 * @param DataBase $dataBase
	 * @return array
	 */
	public function uploadedForLogoutUser(string $newFileName, UploadedFileInterface $uploadedFile, Request $request, DataBase $dataBase): array {
		$addedFileCookie = $request->getCookieParam('added_file');

		if (empty($addedFileCookie)) {
			$addedFileCookie = $this->getHelper()->getRandomString();
			$toHeadersCookie = $this->setAddedFileCookie($addedFileCookie);
		}

		$file = [
			'originalName' => $uploadedFile->getClientFilename(),
			'originalExtension' => $uploadedFile->getClientMediaType(),
			'pathTo' => 'Assets/UsersFiles/Image/',
			'name'	=> $newFileName,
			'size' => $uploadedFile->getSize(), // File`s size in bytes
			'type' => 'image',
			'expireTime' => date('Y-m-d H:i:s', strtotime('+100 days')),
			'updatedAt' => date('Y-m-d H:i:s')
		];

		$downloadInfo = [
			'addedFileCookie' => $addedFileCookie,
			'downloadDate' => date('Y-m-d H:i:s')
		];

		$idFile = $this->addNewFileAnonym($dataBase, 'image', $file, $downloadInfo);
		$countDownloadedFile = $this->getCountFilesByAddedFileCookie($addedFileCookie, $dataBase);

		$result = [
			'user' =>
				[
					'name' => '',
					'countFiles' => $countDownloadedFile ?? '',
				],
			'file' =>
				[
					'link' => "http://uppu.loc/file/$newFileName",
					'name' => $file['originalName'],
					'idFile' => $idFile,
				],
			'toHeadersCookie' => $toHeadersCookie ?? ''
		];

		return $result;
	}

	public function uploadedForLoginUser() {

	}

	/**
	 * @param Request $request
	 * @param DataBase $dataBase
	 * @return int
	 */
	public function getCountUploadedFilesForLogoutUser(Request $request, DataBase $dataBase) {
		$addedFileCookie = $request->getCookieParam('added_file');
		return $addedFileCookie ? $this->getCountFilesByAddedFileCookie($addedFileCookie, $dataBase) : 0;
	}

	/**
	 * @param DataBase $dataBase
	 * @param string $type
	 * @param array $file
	 * @param array $downloadInfo
	 * @return int (id new file in table Files)
	 * @throws \Exception
	 */
	public function addNewFileAnonym(DataBase $dataBase, string $type, array $file, array $downloadInfo):int {
		$typeId = $this->getTypeIdForFile($type, $dataBase);

		if ($typeId === '') {
			throw new \Exception('Type of uploaded file in absent!');
		}

		$file['typeId'] = $typeId;

		$fieldsForFile = ['originalName', 'originalExtension', 'pathTo', 'name', 'size', 'typeId', 'expireTime', 'updatedAt'];
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

		return (int) $this->getFileTdg($dataBase)->addNewFileAnonym($file, $downloadInfo);
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

	/**
	 * @param int $fileId
	 * @param string $description
	 * @param int $saveFileOnDays
	 * @param DataBase $dataBase
	 * @return int - count updated string
	 */
	public function updateUploadedFile(int $fileId, string $description, int $saveFileOnDays, DataBase $dataBase): int {
		return $this->getFileTdg($dataBase)->updateFile($fileId, $description, $saveFileOnDays);
	}

	/**
	 * @param string $name
	 * @param DataBase $dataBase
	 * @return array // todo: if return empty array - show page: "File not found. May be the shelf life of the file has expired".
	 */
	public function getIdPathToOriginalNameOriginalExtensionDescriptionLifeTimeFilesByName(string $name, DataBase $dataBase): array {
		$sqlResponse = $this->getFileTdg($dataBase)->selectIdPathToOriginalNameOriginalExtensionDescriptionLifeTimeFilesByName($name);
		return $sqlResponse ? [
			'fileId'		=> (int) $sqlResponse['id'],
			'pathTo'		=> $sqlResponse['path_to'] . $name,
			'name'	=> $sqlResponse['original_name'],
			'mimeType'		=> $sqlResponse['original_extension'],
			'description'	=> $sqlResponse['description'],
			'lifespanDays'	=> (int) $sqlResponse['life_time']
		] : [];
	}


}