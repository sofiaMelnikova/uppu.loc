<?php

namespace Application\Models;

use Engine\DbQuery;

class RegistrationModel {

    private $dataBase = null;

    /**
     * RegistrationController constructor.
     * @param DbQuery $dataBase
     */
    public function __construct (DbQuery $dataBase) {
        $this->dataBase = $dataBase;
    }

    /**
     * @param string $login
     * @param string $passwordHash
     * @return bool
     */
    public function saveNewUser (string $login, string $passwordHash) {
        $query = "INSERT INTO `Ishop`.`users` (`login`, `password_hash`) VALUES (:login, :passwordHash)";
        $forExecute = ['login' => $login,
                        'passwordHash' => $passwordHash];
        $result = $this->dataBase->changeData($query, $forExecute);
        return $result;
    }


}