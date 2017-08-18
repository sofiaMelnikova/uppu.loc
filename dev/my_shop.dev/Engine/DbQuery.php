<?php
/**
 * Created by PhpStorm.
 * User: smelnikova
 * Date: 18.08.17
 * Time: 14:05
 */

namespace Engine;


class DbQuery
{
    private $dbConnection = null;
    private $dsn = "mysql:dbname=Ishop;host=127.0.0.1";
    private $user = "root";
    private $password = "qwerty133";

    /**
     * @return null|\PDO
     */
    private function getConnection() {
        if (empty($dbConnections)) {
            return $this->dbConnection = new \PDO($this->dsn, $this->user, $this->password);
        }
        return $this->dbConnection;
    }

    /**
     * @param string $query
     * @param array $forExecute
     * @param bool $fetchAll
     * @return array|mixed
     */
    public function getData (string $query, array $forExecute = [], $fetchAll = true) {
        $connection = $this->getConnection();
        $request = $connection->prepare($query);
        $request->execute($forExecute);
        if ($fetchAll) {
            return $request->fetchAll(\PDO::FETCH_ASSOC);
        }
        return $request->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $query
     * @param array $forExecute
     * @return bool
     */
    public function changeData (string $query, array $forExecute = []) {
        $connection = $this->getConnection();
        $request = $connection->prepare($query);
        return $request->execute($forExecute);
    }

}