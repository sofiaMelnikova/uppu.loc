<?php

namespace App\ValueObject;


class FileValueObject {

	/**
	 * @var int
	 */
	private $id = 0;

	/**
	 * @var string
	 */
	private $originalName = '';

	/**
	 * @var string
	 */
	private $originalExtension = '';

	/**
	 * @var string
	 */
	private $pathTo = '';

	/**
	 * @var string
	 */
	private $name = '';

	/**
	 * @var int
	 */
	private $size = 0;

	/**
	 * @var string
	 */
	private $type = 'image';

	/**
	 * @var string
	 */
	private $expireTime = '';

	/**
	 * @var int
	 */
	private $lifespanDays = 0;

	/**
	 * @var string
	 */
	private $description = '';

	/**
	 * @var string
	 */
	private $link = '';

	/**
	 * @var string
	 */
	private $updatedAt = '';


	/*******************************************************************************
	 * Setters
	 ******************************************************************************/

	/**
	 * @param int $id
	 */
	public function setId(int $id) {
		$this->id = $id;
	}

	/**
	 * @param string $originalName
	 */
	public function setOriginalName(string $originalName) {
		$this->originalName = $originalName;
	}

	/**
	 * @param string $originalExtension
	 */
	public function setOriginalExtension(string $originalExtension) {
		$this->originalExtension = $originalExtension;
	}

	/**
	 * @param string $pathTo
	 */
	public function setPathTo(string $pathTo) {
		$this->pathTo = $pathTo;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name) {
		$this->name = $name;
	}

	/**
	 * @param int $size
	 */
	public function setSize(int $size) {
		$this->size = $size;
	}

	/**
	 * @param string $type
	 */
	public function setType(string $type) {
		$this->type = $type;
	}

	/**
	 * @param string $expireTime
	 */
	public function setExpireTime(string $expireTime) {
		$this->expireTime = $expireTime;
	}

	/**
	 * @param string $lifespanDays
	 */
	public function setLifespanDays(string $lifespanDays) {
		$this->lifespanDays = $lifespanDays;
	}

	/**
	 * @param string $description
	 */
	public function setDescription(string $description) {
		$this->description = $description;
	}

	/**
	 * @param string $link
	 */
	public function setLink(string $link) {
		$this->link = $link;
	}

	/**
	 * @param string $updatedAt
	 */
	public function setUpdatedAt(string $updatedAt) {
		$this->updatedAt = $updatedAt;
	}

	/*******************************************************************************
	 * Getters
	 ******************************************************************************/

	/**
	 * @return int
	 */
	public function getId(): int {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getOriginalName(): string {
		return $this->originalName;
	}

	/**
	 * @return string
	 */
	public function getOriginalExtension(): string {
		return $this->originalExtension;
	}

	/**
	 * @return string
	 */
	public function getPathTo(): string {
		return $this->pathTo;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @return int
	 */
	public function getSize(): int {
		return $this->size;
	}

	/**
	 * @return string
	 */
	public function getType(): string {
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getExpireTime(): string {
		return $this->expireTime;
	}

	/**
	 * @return int
	 */
	public function getLifespanDays(): int {
		return $this->lifespanDays;
	}

	/**
	 * @return string
	 */
	public function getDescription(): string {
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function getLink(): string {
		return $this->link;
	}

	/**
	 * @return string
	 */
	public function getUpdatedAt(): string {
		return $this->updatedAt;
	}

	/*******************************************************************************
	 * Handlers
	 ******************************************************************************/

	/**
	 * @param array $params
	 * @return array [$key => $value], $key - name param in ValueObject,
	 * $value - as you want, named this param (this name will be key of param in result array)
	 */
	public function getParamsAsArray(
		array $params = [
			'id'				=> 'id',
			'originalName'		=> 'originalName',
			'originalExtension'	=> 'originalExtension',
			'pathTo'			=> 'pathTo',
			'name'				=> 'name',
			'size'				=> 'size',
			'type'				=> 'type',
			'expireTime'		=> 'expireTime' ,
			'lifespanDays'		=> 'lifespanDays',
			'description'		=> 'description',
			'link'				=> 'link',
			'updatedAt'			=> 'updatedAt'
		]
	): array {
		$result = [];

		foreach ($params as $nameParam => $newNameParam) {
			$result[$newNameParam] = $this->$nameParam;
		}

		return $result;
	}

}