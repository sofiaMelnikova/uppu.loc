<?php

namespace Engine;

use \Symfony\Component\HttpFoundation\Session\Session as SymfonySession;

class Session
{
    private $session;

    public function __construct(SymfonySession $session) {
        if (empty($this->session)) {
            $this->session = $session;
        }
    }

    public function lifetime ($time) {
        session_set_cookie_params($time);
        $now = new \DateTime('now');
        $now->modify('+' . $time . ' seconds');
        return $now->format('Y-m-d H:i:s');
    }

}