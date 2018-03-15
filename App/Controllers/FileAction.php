<?php

namespace App\Controllers;

use Engine\DataBase;
use Slim\Http\Request;
use Slim\Http\Response;

class FileAction extends AbstractAction {

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

		$loginModel = $this->getLoginModel();

		$loginModel->isLoginUser($request, $dataBase);

		//////////////////////////////////////_uploadedForLogoutUser_MODEL_FILE////////////////////

		$result = $fileModel->uploadedForLogoutUser($newFileName, $uploadedFile['uploadedFile'], $request, $dataBase);

		////////////////////////////////////////////////////////////////////////////////////////////

		// TODO: add user params for login user and downloading for login user


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

		$user = [
			'name' => $postParams['user_name'],
			'countFiles' => $postParams['user_countFiles'],
		];

		$file = [
			'link' => $postParams['file_link'], // TODO: create download link end sav to database before show UploadedFileContent page
			'name' => $postParams['file_name'],
			'description' => $postParams['description'],
			'lifespanDays' => $postParams['lifespanDays'],
			'idFile' => $postParams['idFile'],
		];

		if (!empty($errors)) {
			return $response->write($twig->render('UploadedFileContent.html', ['errors' => ['description' => $errors], 'file' => $file, 'user' => $user]));
		}

		$fileModel->updateUploadedFile($file['idFile'], $file['description'], $file['lifespanDays'], $dataBase);

		return $response->write($twig->render('UploadedFileContent.html', ['file' => $file, 'user' => $user]));
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