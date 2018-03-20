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

		return empty($result['toHeadersCookie']) ?
			$response
//				->withRedirect("/uploaded-file", 302)
					->write($twig->render('UploadedFileContent.html', ['user' => $result['user'], 'file' => $result['file']])) :
			$response
//				->withRedirect("/uploaded-file", 302)
				->withHeader('Set-Cookie', $result['toHeadersCookie'])
					->write($twig->render('UploadedFileContent.html', ['user' => $result['user'], 'file' => $result['file']]));
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
		$fileValueObject->setName($postParams['file_name']);
		$fileValueObject->setDescription($postParams['description']);
		$fileValueObject->setLifespanDays((int)$postParams['lifespanDays']);
		$fileValueObject->setId((int)$postParams['idFile']);

		$fileParams = $fileValueObject->getParamsAsArray([
			'link'			=> 'link',
			'name'			=> 'name',
			'description'	=> 'description',
			'lifespanDays'	=> 'lifespanDays',
			'id'			=> 'idFile',
		]);

		if (!empty($errors)) {
			return $response->write($twig->render('UploadedFileContent.html', ['errors' => ['description' => $errors], 'file' => $fileParams, 'user' => $userParams]));
		}

		$fileModel->updateUploadedFile($fileValueObject, $dataBase);

		return $response->write($twig->render('UploadedFileContent.html', ['file' => $fileParams, 'user' => $userParams]));
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
		$file = $fileModel->getIdPathToOriginalNameOriginalExtensionDescriptionLifeTimeFilesByName($fileName, $dataBase);

		if (empty($file)) {
			return $response->write($twig->render('FileNotFound.html'));
		}

		$file['link'] = "http://uppu.loc/file/$fileName";

		return $response->write($twig->render('UploadedFileContent.html', ['file' => $file, 'user' => $user]));
	}

	public function downloadFileToUserAction(string $fileName, Response $response, DataBase $dataBase) {

//		header('X-Accel-Redirect: ' . $file);
//		header('Content-Type: application/octet-stream');
//		header('Content-Disposition: attachment; filename=' . basename($file));
	}

}