<?php

namespace Engine;

class DataBase {

	/**
	 * @var null|\PDO
	 */
	private $connection = null;

	/**
	 * DataBase constructor.
	 * @param string $dbName
	 * @param string $userName
	 * @param string $password
	 * @param string $host
	 * @param string $port
	 */
	public function __construct(string $dbName, string $userName, string $password, string $host = 'localhost', string $port = '3306') {
		$this->connection = new \PDO("mysql:host=$host;dbname=$dbName;port=$port", $userName, $password);
	}

	/**
	 * @return null|\PDO
	 */
	public function getConnection() {
		return $this->connection;
	}

}