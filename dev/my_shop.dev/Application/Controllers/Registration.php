<?php

namespace Application\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Registration
{
    private $app = null;

    public function __construct(Application $app) {
        if (empty($this->app)) {
            $this->app = $app;
        }
    }

    public function registrationAction ($email, $passwordHash) {
        echo $email . "  " . $passwordHash;
    }
}