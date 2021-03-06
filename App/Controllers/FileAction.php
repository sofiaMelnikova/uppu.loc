<?php

namespace App\Controllers;

use App\ValueObject\FileValueObject;
use App\ValueObject\UserValueObject;
use Engine\DataBase;
use Slim\Http\Request;
use Slim\Http\Response;

class FileAction extends AbstractAction {

	/**
	 * @return FileValueObject
	 */
	private function getFileValueObject(): FileValueObject {
		return new FileValueObject();
	}

	/**
	 * @param string $name
	 * @param int $countFiles
	 * @return UserValueObject
	 */
	private function getUserValueObject(string $name = '', int $countFiles = 0): UserValueObject {
		return new UserValueObject($name, $countFiles);
	}

	/**
	 * @param Request $request
	 * @param Response $response
	 * @param DataBase $dataBase
	 * @param \Twig_Environment $twig
	 * @return mixed
	 */
	public function uploadedAction (Request $request, Response $response, DataBase $dataBase, \Twig_Environment $twig) {
		$uploadedFile = $request->getUploadedFiles();
		$fileModel = $this->getFileModel();

		$errors = $fileModel->validateUploadedFile($uploadedFile);

		if (!empty($errors)) {
			return $response->getBody()->write($twig->render($errors['name'], $errors['params']));
		}

		$newFileName = $fileModel->moveUploadedFile($uploadedFile['uploadedFile']);

		$result = $this->getLoginModel()->isLoginUser($request, $dataBase) ?
			$fileModel->uploadedForLoginUser($newFileName, $uploadedFile['uploadedFile'], $request, $dataBase) :
			$fileModel->uploadedForLogoutUser($newFileName, $uploadedFile['uploadedFile'], $request, $dataBase);

		$navBar = $this->getNavBarModel()->getParams($request, $dataBase);

		return empty($result['toHeadersCookie']) ?
			$response
//				->withRedirect("/uploaded-file", 302)
					->write($twig->render('UploadedFileContent.html', ['navBar' => $navBar, 'user' => $result['user'], 'file' => $result['file']])) :
			$response
//				->withRedirect("/uploaded-file", 302)
				->withHeader('Set-Cookie', $result['toHeadersCookie'])
					->write($twig->render('UploadedFileContent.html', ['navBar' => $navBar, 'user' => $result['user'], 'file' => $result['file']]));
	}

	/**
	 * @param Request $request
	 * @param Response $response
	 * @param DataBase $dataBase
	 * @param \Twig_Environment $twig
	 * @return mixed
	 */
	public function updateAction (Request $request, Response $response, DataBase $dataBase, \Twig_Environment $twig) {
		$fileModel = $this->getFileModel();
		$postParams = $request->getParsedBody();
		$errors = ($this->getValidator())->validateDescriptionFile($postParams['description']);

		// user
		$userValueObject = $this->getUserValueObject($postParams['user_name'], $postParams['user_countFiles']);
		$userParams = $userValueObject->getParamsAsArray();

		// file
		$fileValueObject = $this->getFileValueObject();
		$fileValueObject->setLink($postParams['file_link']);
		$fileValueObject->setOriginalName($postParams['file_name']);
		$fileValueObject->setDescription($postParams['description']);
		$fileValueObject->setLifespanDays((int)$postParams['lifespanDays']);
		$fileValueObject->setId((int)$postParams['idFile']);

		$fileParams = $fileValueObject->getParamsAsArray([
			'link'			=> 'link',
			'originalName'	=> 'name',
			'description'	=> 'description',
			'lifespanDays'	=> 'lifespanDays',
			'id'			=> 'idFile',
		]);

		$navBar = $this->getNavBarModel()->getParams($request, $dataBase);

		if (!empty($errors)) {
			return $response->write($twig->render('UploadedFileContent.html', ['navBar' => $navBar, 'errors' => ['description' => $errors], 'file' => $fileParams, 'user' => $userParams]));
		}

		$fileModel->updateUploadedFile($fileValueObject, $dataBase);

		return $response->write($twig->render('UploadedFileContent.html', ['navBar' => $navBar, 'file' => $fileParams, 'user' => $userParams]));
	}

	/**
	 * @param string $fileName
	 * @param Request $request
	 * @param Response $response
	 * @param DataBase $dataBase
	 * @param \Twig_Environment $twig
	 * @return mixed
	 */
	public function getEditFileViewAction(string $fileName, Request $request, Response $response, DataBase $dataBase, \Twig_Environment $twig) {
		$fileModel = $this->getFileModel();

		if ($this->getLoginModel()->isLoginUser($request, $dataBase)) {
			$user = $this->getUserModel()->getIdNameCountFilesByEnterCookie($request, $dataBase);
		} else {
			$countUploadedFiles = $fileModel->getCountUploadedFilesForLogoutUser($request, $dataBase);
			$user =	[
				'name' => '',
				'countFiles' => $countUploadedFiles
			];
		}

		$fileValueObject = $fileModel->selectInfoForDownloadingFileByName($fileName, $dataBase);

		if (empty($fileValueObject)) {
			return $response->write($twig->render('FileNotFound.html'));
		}

		$fileValueObject->setLink("http://uppu.loc/file/$fileName");
		$fileValueObject->setName($fileName);

		$file = $fileValueObject->getParamsAsArray([
			'id'			=> 'fileId',
			'originalName'	=> 'name',
			'description'	=> 'description',
			'lifespanDays'	=> 'lifespanDays',
			'link'			=> 'link',
			'name'			=> 'savedName'
		]);

		$navBar = $this->getNavBarModel()->getParams($request, $dataBase);
		return $response->write($twig->render('UploadedFileContent.html', ['navBar' => $navBar, 'file' => $file, 'user' => $user]));
	}

	/**
	 * @param string $fileName
	 * @param Request $request
	 * @param Response $response
	 * @param DataBase $dataBase
	 * @param \Twig_Environment $twig
	 * @return mixed
	 */
	public function viewPageDownloadingFileToUserAction(string $fileName, Request $request, Response $response, DataBase $dataBase, \Twig_Environment $twig) {
		$fileModel = $this->getFileModel();

		if ($this->getLoginModel()->isLoginUser($request, $dataBase)) {
			$userParams = $this->getUserModel()->getIdNameCountFilesByEnterCookie($request, $dataBase);
		} else {
			$userParams['countFiles'] = $fileModel->getCountUploadedFilesForLogoutUser($request, $dataBase);
		}

		$fileValueObject = $fileModel->selectInfoForDownloadingFileByName($fileName, $dataBase);
		$fileValueObject->setName($fileName);

		$fileParams = $fileValueObject->getParamsAsArray([
			'originalName'	=> 'originalName',
			'name'			=> 'name',
			'downloadDate'	=> 'downloadDate',
			'size'			=> 'sizeKb',
			'pathTo'		=> 'pathTo',
		]);

		$fileParams['sizeKb'] = round($fileParams['sizeKb'] / 10024, 2);

		$navBar = $this->getNavBarModel()->getParams($request, $dataBase);
		return $response->write($twig->render('GetFileContent.html', ['navBar' => $navBar, 'file' => $fileParams, 'user' => $userParams]));
	}

	/**
	 * @param string $fileName
	 * @param Response $response
	 * @param DataBase $dataBase
	 * @param \Twig_Environment $twig
	 * @return mixed
	 */
	public function downloadFileToUserAction(string $fileName, Response $response, DataBase $dataBase, \Twig_Environment $twig) {
		$fileModel = $this->getFileModel();
		$fileValueObject = $fileModel->selectInfoForDownloadingFileByName($fileName, $dataBase);

		if (empty($fileValueObject)) {
			return $response->write($twig->render('FileNotFound.html')); // TODO: better show alert, without showing new page
		}

		return $response
			->withHeader('X-Accel-Redirect', $fileValueObject->getPathTo())
			->withHeader('Content-Type', $fileValueObject->getOriginalExtension())
			->withHeader('Content-Disposition', 'attachment; filename=' . $fileValueObject->getOriginalName())
			->withHeader('Content-Length' ,$fileValueObject->getSize())
			->withHeader('Pragma', 'public')
			->withHeader('Expires', '0')
			->withHeader('Content-Transfer-Encoding', 'binary')
			->withRedirect("/file/$fileName");
	}

	/**
	 * @param string $userHashId
	 * @param Request $request
	 * @param Response $response
	 * @param DataBase $dataBase
	 * @param \Twig_Environment $twig
	 * @return mixed
	 */
	public function viewPageUsersFilesToUserAction(string $userHashId, Request $request, Response $response, DataBase $dataBase, \Twig_Environment $twig) {
		$fileModel = $this->getFileModel();
		$userModel = $this->getUserModel();

		if ($userHashId === 'anonymously') {
			$files = $fileModel->getAllUsersFilesByAddedCookie($request, $dataBase);
			$user['countFiles'] = $fileModel->getCountUploadedFilesForLogoutUser($request, $dataBase);
		} else {
			$userId = $userModel->getUserIdByUserIdHash($userHashId, $dataBase);
			$files = $fileModel->getAllUsersFilesByUserId($userId, $dataBase);
			$user = $userModel->getIdNameCountFilesByEnterCookie($request, $dataBase);
		}

		if (empty($user['id']) && isset($userId) || isset($userId) && $user['id'] != $userId) {
			$user = $userModel->getNameCountUploadedFilesById($userId, $dataBase);
		}

		if (empty($files)) {
			return $response->withRedirect('/');
		}

		$navBar = $this->getNavBarModel()->getParams($request, $dataBase);
		return $response->write($twig->render('UsersFiles.html', ['navBar' => $navBar, 'user' => $user, 'files' => $files]));
	}

	/**
	 * @param Request $request
	 * @param Response $response
	 * @param DataBase $dataBase
	 * @param \Twig_Environment $twig
	 * @return mixed
	 */
	public function viewAllUsersFiles(Request $request, Response $response, DataBase $dataBase, \Twig_Environment $twig) {
		$fileModel = $this->getFileModel();
		$userModel = $this->getUserModel();

		if ($this->getLoginModel()->isLoginUser($request, $dataBase)) {
			$actualUserParams = $userModel->getIdNameCountFilesByEnterCookie($request, $dataBase);
		} else {
			$actualUserParams['countFiles'] = $fileModel->getCountUploadedFilesForLogoutUser($request, $dataBase);
		}

		$filesParams = $fileModel->getAllFilesInfoWithUserHashIdsAndUsersNames($dataBase);

		$navBar = $this->getNavBarModel()->getParams($request, $dataBase);
		return $response->write($twig->render('AllFilesContent.html', ['navBar' => $navBar, 'user' => $actualUserParams,'files' => $filesParams]));
	}

}