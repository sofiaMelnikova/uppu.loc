<?php

namespace Application\Models;

use Engine\DbQuery;
use Application\TableDataGateway\Registration;
class RegistrationModel
{
    /**
     * @param DbQuery $dbQuery
     * @return Registration
     */
    public function newRegistration (DbQuery $dbQuery) {
        return new Registration($dbQuery);
    }

    /**
     * @param string $login
     * @param string $phone
     * @param string $passwordHash
     * @return bool|string
     */
    public function saveNewUser (string $login, string $phone, string $passwordHash) {
        return ($this->newRegistration(new DbQuery()))->saveNewUser($login, $phone, $passwordHash);
    }


    /**
     * @param string $login
     * @return bool
     */
    public function isLoginExist (string $login) {
        $result = ($this->newRegistration(new DbQuery()))->isLoginExist($login);
        if (empty($result)) {
            return false;
        }
        return true;
    }

    /**
     * @param string $phoneNumber
     * @return bool|int
     */
    public function isPhoneExist (string $phoneNumber) {
        $userId = $this->newRegistration(new DbQuery())->isPhoneExist($phoneNumber)['id'];
        if (empty($userId)) {
            return false;
        }
        return intval($userId);
    }

    /**
     * @param string $phoneNumber
     * @return int
     */
    public function addNewUserByPhone (string  $phoneNumber) {
        $userId = $this->newRegistration(new DbQuery())->addNewUserByPhone($phoneNumber);
        return intval($userId);
    }

    /**
     * @param string $phoneNumber
     * @return array|int
     */
    public function registrateNewUserByPhone (string $phoneNumber) {
        if (!is_numeric($phoneNumber) || (strlen($phoneNumber) != 11)) {
            return ['error' => 'Error: phone is not corrected.'];
        }
        $userId = $this->isPhoneExist($phoneNumber);
        if ($userId) {
            return $userId;
        }
        return $this->addNewUserByPhone($phoneNumber);
    }



}