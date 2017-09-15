<?php

namespace Application\TableDataGateway;

use Engine\DbQuery;
class Login
{
    public $dataBase = null;

    /**
     * LoginModel constructor.
     * @param DbQuery $dataBase
     */
    public function __construct(DbQuery $dataBase) {
        $this->dataBase = $dataBase;
    }

    /**
     * @param string $login
     * @return array|mixed
     */
    public function isUserExist (string $login) {
        $query = "SELECT * FROM `Ishop`.`users` WHERE `login` = :login AND `is_delete` = 0";
        $forExecute = [':login' => $login];
        return $this->dataBase->getData($query, $forExecute, false);
    }

    /**
     * @param int $userId
     * @return array|mixed
     */
    public function isAdmin (int $userId) {
        $query = "SELECT `users`.`admin` FROM `users` WHERE `users`.`id` = :userId;";
        $forExecute = [':userId' => $userId];
        return $this->dataBase->getData($query, $forExecute, false);
    }

    /**
     * @param int $userId
     * @return array|mixed
     */
    public function getLogin (int $userId) {
        $query = "SELECT `users`.`login` FROM `users` WHERE `users`.`id` = :userId;";
        $forExecute = [':userId' => $userId];
        return $this->dataBase->getData($query, $forExecute, false);
    }

    /**
     * @param string $token
     * @return array|mixed
     */
    public function getUserIdByToken (string $token) {
        $now = date("Y-m-d H:i:s", strtotime('now'));
        $query = "SELECT `users`.`id` FROM `users` WHERE `users`.`token` = :token AND `users`.`token_end` > :now;";
        $forExecute = [':token' => $token, ':now' => $now];
        return $this->dataBase->getData($query, $forExecute, false);
    }

    /**
     * @param string $token
     * @param string $endTimeToken
     * @param int $userId
     */
    public function addTokenForUser (string $token, string $endTimeToken, int $userId) {
        $query = "UPDATE `users` SET `users`.`token` = :token, `users`.`token_end` = :endTime WHERE `users`.`id` = :id;";
        $forExecute = [':token' => $token, ':endTime' => $endTimeToken, ':id' => $userId];
        $this->dataBase->changeData($query, $forExecute);
    }

    /**
     * @param string $token
     * @param string $time
     */
    public function updateTimeForToken (string $token, string $time) {
        $query = "UPDATE `users` SET `users`.`token_end` = :newTime WHERE `users`.`token` = :token;";
        $forExecute = [':newTime' => $time, ':token' => $token];
        $this->dataBase->changeData($query, $forExecute);
    }

}