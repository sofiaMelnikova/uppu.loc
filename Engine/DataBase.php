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

	/**
	 * @param string $query
	 * @param array $queryParams
	 * @param bool $fetchAll
	 * @return array|bool
	 */
	public function select (string $query, array $queryParams = [], bool $fetchAll = true) {
		$preparedQuery = $this->prepareAndExecute($query, $queryParams);
		return $fetchAll ? $preparedQuery->fetchAll(\PDO::FETCH_ASSOC) : $preparedQuery->fetch(\PDO::FETCH_ASSOC);
	}

	/**
	 * @param string $query
	 * @param array $queryParams
	 * @return int
	 */
	public function update (string $query, array $queryParams = []): int {
		$preparedQuery = $this->prepareAndExecute($query, $queryParams);
		return $preparedQuery->rowCount();
	}

	/**
	 * @param string $query
	 * @param array $queryParams
	 * @param bool $getLastInsertId
	 * @return int|string
	 */
	public function insert (string $query, array $queryParams = [], bool $getLastInsertId = false) {
		$preparedQuery = $this->prepareAndExecute($query, $queryParams);
		return $getLastInsertId ? $this->connection->lastInsertId() : $preparedQuery->rowCount();
	}

	/**
	 * @param string $query
	 * @param array $queryParams
	 * @return int
	 */
	public function delete (string $query, array $queryParams = []): int {
		$preparedQuery = $this->prepareAndExecute($query, $queryParams);
		return $preparedQuery->rowCount();
	}

	/**
	 * @param string $query
	 * @param array $queryParams
	 * @return \PDOStatement
	 */
	private function prepareAndExecute (string $query, array $queryParams = []): \PDOStatement {
		$preparedQuery = $this->connection->prepare($query);
		$preparedQuery->execute($queryParams);
		return $preparedQuery;
	}

}