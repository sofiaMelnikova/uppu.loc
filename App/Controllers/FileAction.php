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
	public function newFileAction (Request $request, Response $response, DataBase $dataBase, \Twig_Environment $twig) {
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

		if (empty($addedFileCookieLAST)) {
			$addedFileCookie = $helper->getRandomString();
			$toHeadersCookie = $fileModel->setAddedFileCookie($addedFileCookie);
		}

		$file = ['originalName' => $uploadedFile['uploadedFile']->getClientFilename(),
			'originalExtension' => $uploadedFile['uploadedFile']->getClientMediaType(),
			'pathTo' => 'Assets/UsersFiles/Image/' . $newFileName,
			'size' => $uploadedFile['uploadedFile']->getSize(),
			'type' => 'image',
			'expireTime' => date('Y-m-d H:i:s', strtotime('+100 days')),
			'updatedAt' => date('Y-m-d H:i:s')];
		$downloadInfo = ['addedFileCookie' => $addedFileCookie,
			'downloadDate' => date('Y-m-d H:i:s')];

		$fileModel->addNewFileAnonym($dataBase, 'image', $file, $downloadInfo);

		return isset($toHeadersCookie) ? $response->withHeader('Set-Cookie', $toHeadersCookie)->withRedirect('/new-file') : $response->withRedirect('/new-file');
	}

}