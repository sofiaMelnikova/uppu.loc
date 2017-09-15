<?php

namespace Application\TableDataGateway;

use Engine\DbQuery;
class Registration
{
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
     * @param string $phone
     * @param string $passwordHash
     * @return bool|string
     */
    public function saveNewUser (string $login, string $phone, string $passwordHash) {
        $query = "INSERT INTO `Ishop`.`users` (`login`, `phone`, `password_hash`) VALUES (:login, :phone, :passwordHash)";
        $forExecute = ['login' => $login,
            ':phone' => $phone,
            'passwordHash' => $passwordHash];
        $result = $this->dataBase->changeData($query, $forExecute);
        return $result;
    }

    /**
     * @param string $login
     * @return array|mixed
     */
    public function isLoginExist (string $login) {
        $query = "SELECT * FROM `Ishop`.`users` WHERE `login` = :login AND `is_delete` = 0";
        $forExecute = [':login' => $login];
        return $this->dataBase->getData($query, $forExecute, false);

    }

    /**
     * @param string $phoneNumber
     * @return array|mixed
     */
    public function isPhoneExist (string $phoneNumber) {
        $query = "SELECT `users`.`id` FROM `users` WHERE `users`.`phone` = :phoneNumber";
        $forExecute = [':phoneNumber' => $phoneNumber];
        return $this->dataBase->getData($query, $forExecute, false);
    }

    /**
     * @param string $phoneNumber
     * @return bool|string
     */
    public function addNewUserByPhone (string $phoneNumber) {
        $query = "INSERT INTO `users` (`phone`) VALUES (:phoneNumber)";
        $forExecute = [':phoneNumber' => $phoneNumber];
        return $this->dataBase->changeData($query, $forExecute);
    }
}