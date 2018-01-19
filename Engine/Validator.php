<?php

namespace Engine;


class Validator {

	/**
	 * @var array
	 */
	private $errors;

	/**
	 * @param $params
	 * @return array
	 */
	public function registrationForm ($params): array {
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
		if (!$this->pregMatch('/.+@.+\..+/', $email)) {
			$this->errors['email'] = 'Incorrect e-mail.';
		}

		return $this;
	}

	/**
	 * @param string $password
	 * @return Validator
	 */
	private function password (string $password): Validator {
		if (!$this->pregMatch('/(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}', $password)) {
			$this->errors['password'] = 'Incorrect password. Password must have consist of capital letter, lowercase letter and number. It`s must have length 8 jr more symbols.';
		}

		return $this;
	}

	/**
	 * @param string $name
	 * @return Validator
	 */
	private function name (string $name): Validator {
		if ((strlen($name) < 3)) {
			$this->errors['name'] = 'Name must have 3 or more symbols.';
		}

		return $this;
	}

	/**
	 * @param string $password
	 * @param string $passwordRepeater
	 * @return Validator
	 */
	private function passwordRepeater (string $password, string $passwordRepeater): Validator {
		if ($password != $passwordRepeater) {
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