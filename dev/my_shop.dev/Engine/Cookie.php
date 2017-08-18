<?php
/**
 * Created by PhpStorm.
 * User: smelnikova
 * Date: 18.08.17
 * Time: 19:29
 */

namespace Engine;


class Cookie
{
    private $cookie;

    public function __construct () {
        $this->cookie = $_COOKIE;
    }

    public function get () {

    }
}