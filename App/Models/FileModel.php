<?php

namespace App\Models;


use App\ValueObject\FileValueObject;
use Engine\DataBase;
use Slim\Http\Cookies;
use Slim\Http\Request;
use Psr\Http\Message\UploadedFileInterface;

class FileModel extends AbstractModel {

	/**
	 * @return FileValueObject
	 */
	private function getFileValueObject(): FileValueObject {
		return new FileValueObject();
	}

	/**
	 * @return UserModel
	 */
	private function getUserModel(): UserModel {
		return new UserModel();
	}

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
	 * @throws \Exception
	 */
	public function uploadedForLogoutUser(string $newFileName, UploadedFileInterface $uploadedFile, Request $request, DataBase $dataBase): array {
		$addedFileCookie = $request->getCookieParam('added_file');

		if (empty($addedFileCookie)) {
			$addedFileCookie = $this->getHelper()->getRandomString();
			$toHeadersCookie = $this->setAddedFileCookie($addedFileCookie);
		}

		$fileValueObject = $this->getFileValueObjectForUploaded($newFileName, $uploadedFile);

		$typeId = $this->getTypeIdForFile($fileValueObject->getType(), $dataBase); // TODO: изменить передачу типа, врменно сохраняем только картинки

		$idFile = (int) $this->getFileTdg($dataBase)->addNewFileAnonym($fileValueObject, (int)$typeId, $addedFileCookie, date('Y-m-d H:i:s'));

		$countDownloadedFile = $this->getCountUploadedFilesForLogoutUser($request, $dataBase);

		$result = [
			'user' =>
				[
					'name'			=> '',
					'countFiles'	=> $countDownloadedFile,
				],
			'file' =>
				[
					'link'		=> "http://uppu.loc/file/$newFileName",
					'name'		=> $fileValueObject->getOriginalName(),
					'idFile'	=> $idFile,
					'savedName'	=> $fileValueObject->getName()
				],
			'toHeadersCookie' => $toHeadersCookie ?? ''
		];

		return $result;
	}

	/**
	 * @param string $newFileName
	 * @param UploadedFileInterface $uploadedFile
	 * @param Request $request
	 * @param DataBase $dataBase
	 * @return array
	 */
	public function uploadedForLoginUser(string $newFileName, UploadedFileInterface $uploadedFile, Request $request, DataBase $dataBase): array {
		$fileValueObject = $this->getFileValueObjectForUploaded($newFileName, $uploadedFile);

		$typeId = $this->getTypeIdForFile($fileValueObject->getType(), $dataBase); // TODO: изменить передачу типа, врменно сохраняем только картинки

		$user = $this->getUserModel()->getIdNameCountFilesByEnterCookie($request, $dataBase);

		$fileId = (int) $this->getFileTdg($dataBase)->addNewFileByLoginUser($fileValueObject, $typeId, $user['id'], date('Y-m-d H:i:s'));

		$result = [
			'user' => $user,
			'file' => [
				'link'		=> "http://uppu.loc/file/$newFileName",
				'name'		=> $fileValueObject->getOriginalName(),
				'idFile'	=> $fileId,
				'savedName'	=> $fileValueObject->getName(),
			],
		];

		return $result;
	}

	/**
	 * @param string $newFileName
	 * @param UploadedFileInterface $uploadedFile
	 * @return FileValueObject
	 */
	private function getFileValueObjectForUploaded(string $newFileName, UploadedFileInterface $uploadedFile): FileValueObject {
		$fileValueObject = $this->getFileValueObject();
		$fileValueObject->setOriginalName($uploadedFile->getClientFilename());
		$fileValueObject->setOriginalExtension($uploadedFile->getClientMediaType());
		$fileValueObject->setPathTo("/Assets/UsersFiles/Image/$newFileName"); // todo: take path from config
		$fileValueObject->setName($newFileName);
		$fileValueObject->setSize($uploadedFile->getSize());
		$fileValueObject->setType('image'); // todo: take type from property
		$fileValueObject->setExpireTime(date('Y-m-d H:i:s', strtotime('+100 days')));
		$fileValueObject->setUpdatedAt(date('Y-m-d H:i:s'));
		return $fileValueObject;
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
		$resultRequest = $this->getFileTdg($dataBase)->getAllFileTypes();
		$types = [];

		foreach ($resultRequest as $value) {
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
	private function getCountFilesByAddedFileCookie(string $addedFileCookie, DataBase $dataBase): int {
		return (int) $this->getFileTdg($dataBase)->getCountFilesByAddedFileCookie($addedFileCookie)['COUNT(*)'];
	}

	/**
	 * @param FileValueObject $fileValueObject
	 * @param DataBase $dataBase
	 * @return int
	 */
	public function updateUploadedFile(FileValueObject $fileValueObject, DataBase $dataBase): int {
		return $this->getFileTdg($dataBase)->updateFile($fileValueObject);
	}

	/**
	 * @param string $name
	 * @param DataBase $dataBase
	 * @return FileValueObject|null
	 */
	public function selectInfoForDownloadingFileByName(string $name, DataBase $dataBase) {
		$sqlResponse = $this->getFileTdg($dataBase)->selectInfoForDownloadingAndShowingFileByName($name);

		if ($sqlResponse) {
			$fileValueObject = $this->getFileValueObject();
			$fileValueObject->setId((int) $sqlResponse['id']);
			$fileValueObject->setPathTo($sqlResponse['path_to']);
			$fileValueObject->setOriginalName($sqlResponse['original_name']);
			$fileValueObject->setOriginalExtension($sqlResponse['original_extension']);
			$fileValueObject->setDescription($sqlResponse['description']);
			$fileValueObject->setLifespanDays((int) $sqlResponse['life_time']);
			$fileValueObject->setDownloadDate($sqlResponse['download_date']);
			$fileValueObject->setSize((int)$sqlResponse['size']);
			return $fileValueObject;
		}

		return null;
	}

	/**
	 * @param int $userId
	 * @param DataBase $dataBase
	 * @return array
	 */
	public function getAllUsersFilesByUserId(int $userId, DataBase $dataBase): array {
		if (empty($userId)) {
			return [];
		}

		$responseSql = $this->getFileTdg($dataBase)->selectInfoForDownloadingAndShowingAllUsersFilesByUserId($userId);
		return $this->getSizeFilesFromByteToKb($responseSql);
	}

	/**
	 * @param Request $request
	 * @param DataBase $dataBase
	 * @return array
	 */
	public function getAllUsersFilesByAddedCookie(Request $request, DataBase $dataBase): array {
		$addedFileCookie = $request->getCookieParam('added_file');

		if (empty($addedFileCookie)) {
			return [];
		}

		$responseSql = $this->getFileTdg($dataBase)->selectInfoForDownloadingAndShowingAllUsersFilesByAddedCookie($addedFileCookie);
		return $this->getSizeFilesFromByteToKb($responseSql);
	}

	/**
	 * @param array $files
	 * @return array
	 */
	private function getSizeFilesFromByteToKb(array $files): array {
		if (empty($files)) {
			return [];
		}

		foreach ($files as $key => $file) {
			$files[$key]['sizeKb'] = round($file['sizeKb'] / 10024, 2);
		}

		return $files;
	}

	/**
	 * @param array $files
	 * @return array
	 */
	private function changeUserParamsForAllFile(array $files): array {
		if (empty($files)) {
			return [];
		}

		$userModel = $this->getUserModel();

		foreach ($files as $key => $file) {
			if (empty($file['userId'])) {
				$files[$key]['hashUserId'] = 'anonymously';
				$files[$key]['userName'] = 'Anonymously';
			} else {
				$files[$key]['hashUserId'] = $userModel->getHashUserIdByUserId($file['userId']);
			}
		}

		return $files;
	}

	/**
	 * @param DataBase $dataBase
	 * @return array
	 */
	public function getAllFilesInfoWithUserHashIdsAndUsersNames(DataBase $dataBase): array {
		$responseSql = $this->getFileTdg($dataBase)->selectInfoForDownloadingAndShowingForAllFiles();
		$files = $this->getSizeFilesFromByteToKb($responseSql);
		$files = $this->changeUserParamsForAllFile($files);
		return $files;
	}


}