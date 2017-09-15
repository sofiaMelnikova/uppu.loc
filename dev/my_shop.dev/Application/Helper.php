<?php

namespace Application;


class Helper
{
    /**
     * @return string
     */
    public function generateRandomString () {
        $symbols = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $countSymbols = strlen($symbols);
        $result = '';
        for ($i = 0; $i < 12; $i++) {
            $result .= $symbols[rand(0, $countSymbols - 1)];
        }
        return $result;
    }
}