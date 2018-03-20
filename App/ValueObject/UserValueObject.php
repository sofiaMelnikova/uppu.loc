<?php

namespace App\ValueObject;


class UserValueObject {

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var int
	 */
	private $countFiles;

	/**
	 * UserValueObject constructor.
	 * @param string $name
	 * @param int $countFiles
	 */
	public function __construct(string $name = '', int $countFiles = 0) {
		$this->name = $name;
		$this->countFiles = $countFiles;
	}

	/*******************************************************************************
	 * Setters
	 ******************************************************************************/

	/**
	 * @param string $name
	 */
	public function setName(string $name) {
		$this->name = $name;
	}

	/**
	 * @param int $countFiles
	 */
	public function setCountFiles(int $countFiles) {
		$this->countFiles = $countFiles;
	}

	/*******************************************************************************
	 * Getters
	 ******************************************************************************/

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @return int
	 */
	public function getCountFiles(): int {
		return $this->countFiles;
	}

	/*******************************************************************************
	 * Handlers
	 ******************************************************************************/

	/**
	 * @param array $params
	 * @return array [$key => $value], $key - name param in ValueObject,
	 * $value - as you want, named this param (this name will be key of param in result array)
	 */
	public function getParamsAsArray($params = ['name' => 'name', 'countFiles' => 'countFiles']): array {
		$result = [];

		foreach ($params as $nameParam => $newNameParam) {
			$result[$newNameParam] = $this->$nameParam;
		}

		return $result;
	}

}