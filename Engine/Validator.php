<?php

namespace Engine;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\File;
use Slim\Http\UploadedFile;

class Validator {

	/**
	 * @var array
	 */
	private $errors;


	/*******************************************************************************
	 * File
	 ******************************************************************************/

	/**
	 * @param UploadedFile $uploadedFile
	 * @param string $maxSize
	 * @param array $mimeType
	 * @param bool $binaryFormat
	 * @return string
	 */
	public function file (UploadedFile $uploadedFile, string $maxSize = '100M', $mimeType = ['image/png', 'image/jpeg', 'image/pjpeg', 'image/gif'], bool $binaryFormat = false): string {
		$validator = Validation::createValidator();
//		$mimeType = $uploadedFile->getClientMediaType() ?? ''; // verification of the specified type
		$errors = $validator->validate($uploadedFile->file , [
			new File([
				'maxSize' => $maxSize,
				'binaryFormat' => $binaryFormat,
				'mimeTypes' => $mimeType
			])
		]);

		if ($errors->has(0)) {
			return $errors->get(0)->getMessage();
		}

		return '';
	}


	/*******************************************************************************
	 * Forms
	 ******************************************************************************/

	/**
	 * @param array $params
	 * @return array ([] - if errors absent, else they are)
	 */
	public function registrationForm (array $params): array {
		$this->errors = [];

		$this->isEmpty('userName', $params['userName'], 'Name')
			->isEmpty('email', $params['email'], 'E-mail')
			->isEmpty('password', $params['password'], 'Password')
			->isEmpty('passwordRepeater', $params['passwordRepeater'], 'Repeated password');

		$this->name($params['userName'])
			->email($params['email'])
			->password($params['password'])
			->passwordRepeater($params['password'], $params['passwordRepeater']);

		return $this->errors;
	}

	/**
	 * @param string $email
	 * @return $this
	 */
	private function email (string $email): Validator {
		if (!$this->pregMatch('/.+@.+\..+/', $email) && empty($this->errors['email'])) {
			$this->errors['email'] = 'Incorrect e-mail.';
		}

		return $this;
	}

	/**
	 * @param string $password
	 * @return Validator
	 */
	private function password (string $password): Validator {
		if (!$this->pregMatch('/(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}/', $password) && empty($this->errors['password'])) {
			$this->errors['password'] = 'Incorrect password. Password must have consist of capital letter, lowercase letter and number. It`s must have length 8 or more symbols.';
		}

		return $this;
	}

	/**
	 * @param string $name
	 * @return Validator
	 */
	private function name (string $name): Validator {
		if ((iconv_strlen($name) < 3) && empty($this->errors['userName'])) {
			$this->errors['userName'] = 'Name must have 3 or more symbols.';
		}

		return $this;
	}

	/**
	 * @param string $password
	 * @param string $passwordRepeater
	 * @return Validator
	 */
	private function passwordRepeater (string $password, string $passwordRepeater): Validator {
		if (($password != $passwordRepeater) && empty($this->errors['passwordRepeater'])) {
			$this->errors['passwordRepeater'] = 'Repeated password and password are not equal.';
		}

		return $this;
	}

	/**
	 * @param string $keyValue
	 * @param string $value
	 * @param string $nameField
	 * @return Validator
	 */
	private function isEmpty (string $keyValue, string $value, string $nameField = ''): Validator {
		if (empty($value)) {
			$this->errors[$keyValue] = ($nameField ?: $keyValue) . " must not be empty!!!";
		}

		return $this;
	}

	/**
	 * @param string $pattern
	 * @param string $subject
	 * @return bool
	 */
	private function pregMatch (string $pattern, string $subject): bool {
		return preg_match($pattern, $subject) ? true : false;
	}

}