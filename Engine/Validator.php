<?php

namespace Engine;


class Validator {

	private $errors;

	public function registrationForm ($params): array {
		$this->errors = [];
		$this->email($params['email'])->password($params['password'])->name($params['userName']);
		return $this->errors;
	}

	/**
	 * @param string $email
	 * @return $this
	 */
	public function email (string $email): Validator {
		if (!$this->pregMatch('/.+@.+\..+/', $email)) {
			$this->errors['email'] = 'Incorrect e-mail.';
		}

		return $this;
	}

	/**
	 * @param string $password
	 * @return Validator
	 */
	public function password (string $password): Validator {
		if (!$this->pregMatch('/(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}', $password)) {
			$this->errors['password'] = 'Incorrect password. Password must have consist of capital letter, lowercase letter and number. It`s must have length 8 jr more symbols.';
		}

		return $this;
	}

	/**
	 * @param string $name
	 * @return Validator
	 */
	public function name (string $name): Validator {
		if ((strlen($name) < 3)) {
			$this->errors['name'] = 'Name must have 3 or more symbols.';
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