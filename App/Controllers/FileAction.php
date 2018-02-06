<?php

namespace App\Controllers;

use Engine\DataBase;
use Slim\Http\Request;
use Slim\Http\Response;

class FileAction extends AbstractAction {

	public function newFileAction (Request $request, Response $response, DataBase $dataBase, \Twig_Environment $twig) {
		$uploadedFile = $request->getUploadedFiles();

		if (empty($uploadedFile) || empty($uploadedFile['uploadedFile'])) {
			return $response->getBody()->write($twig->render('MainContent.html', ['errors' => ['file' => 'Something went wrong. File was not uploaded, please, try again.']]));
		}

		$errorString = $this->getValidator()->file($uploadedFile['uploadedFile']);

		if ($errorString) {
			return $response->getBody()->write($twig->render('MainContent.html', ['errors' => ['file' => $errorString]]));
		}


		// TODO

		return $response->withRedirect('/new_file');

	}

}