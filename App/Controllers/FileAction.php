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
	public function uploadedFileAction (Request $request, Response $response, DataBase $dataBase, \Twig_Environment $twig) {
		$uploadedFile = $request->getUploadedFiles();

		if (empty($uploadedFile) || empty($uploadedFile['uploadedFile'])) {
			return $response->getBody()->write($twig->render('MainContent.html', ['errors' => ['file' => 'Something went wrong. File was not uploaded, please, try again.']]));
		}

		$errorString = $this->getValidator()->file($uploadedFile['uploadedFile'], '200M');

		if ($errorString) {
			return $response->getBody()->write($twig->render('MainContent.html', ['errors' => ['file' => $errorString]]));
		}

		$helper = $this->getHelper();

		$newFileName = $helper->getRandomString([], 5) . time();

		$uploadedFile['uploadedFile']->moveTo('Assets/UsersFiles/Image/' . $newFileName);

		$fileModel = $this->getFileModel();

		$addedFileCookie = $request->getCookieParam('added_file');

		if (empty($addedFileCookie)) {
			$addedFileCookie = $helper->getRandomString();
			$toHeadersCookie = $fileModel->setAddedFileCookie($addedFileCookie);
		}

		$file = [
			'originalName' => $uploadedFile['uploadedFile']->getClientFilename(),
			'originalExtension' => $uploadedFile['uploadedFile']->getClientMediaType(),
			'pathTo' => 'Assets/UsersFiles/Image/' . $newFileName,
			'size' => $uploadedFile['uploadedFile']->getSize(),
			'type' => 'image',
			'expireTime' => date('Y-m-d H:i:s', strtotime('+100 days')),
			'updatedAt' => date('Y-m-d H:i:s')
		];

		$downloadInfo = [
			'addedFileCookie' => $addedFileCookie,
			'downloadDate' => date('Y-m-d H:i:s')
		];

		$fileModel->addNewFileAnonym($dataBase, 'image', $file, $downloadInfo);

		$countDownloadedFile = $fileModel->getCountFilesByAddedFileCookie($addedFileCookie, $dataBase);

		// TODO: add user params for login user and downloading for login user


		$printedUserParams = [
			'name' => '',
			'countFiles' => $countDownloadedFile ?? '',
		];
//		$userName = '';
//		$userCountFiles = $countDownloadedFile ?? '';

		$printedFileParams = [
			'link' => 'todo: generate download link', // TODO: create download link
			'name' => $file['originalName'],
			];

//		$fileLink = 'todo: generate download link';
//		$fileName = $file['originalName'];

//		return isset($toHeadersCookie) ?
//				$response
//					->withHeader('Set-Cookie', $toHeadersCookie)
//					->withRedirect("/uploaded-file/$userName/$userCountFiles/$fileLink/$fileName", 302) :
//				$response
//					->withRedirect("/uploaded-file/$userName/$userCountFiles/$fileLink/$fileName", 302);

		return isset($toHeadersCookie) ?
			$response
				->withHeader('Set-Cookie', $toHeadersCookie)
//				->withRedirect("/uploaded-file", 302)
					->write($twig->render('UploadedFileContent.html', ['user' => $printedUserParams, 'file' => $printedFileParams])) :
			$response
//				->withRedirect("/uploaded-file", 302)
					->write($twig->render('UploadedFileContent.html', ['user' => $printedUserParams, 'file' => $printedFileParams]));
	}

}