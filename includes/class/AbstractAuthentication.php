<?php

/**
 * Copyright (c) 2013 Claudiu Persoiu (http://www.claudiupersoiu.ro/)
 *
 * This file is part of "Just quizzing".
 *
 * Official project page: http://blog.claudiupersoiu.ro/just-quizzing/
 *
 * You can download the latest version from https://github.com/claudiu-persoiu/Just-Quizzing
 *
 * "Just quizzing" is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * Just quizzing is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

abstract class AbstractAuthentication {

    protected $_sessionKey;

    protected $_sessionFields;

    public function logout($redirect = true) {

        $_SESSION[$this->_sessionKey] = null;

        foreach($this->_sessionFields as $field) {
            $_SESSION[$field] = null;
        }

        if($redirect) {
            $_SESSION['logout_redirect'] = true;

            // get rid of the logout parameter
            header( 'Location: ' . $_SERVER['PHP_SELF']);
            exit();
        }

    }

    public function getUserData($user, $pass) {

        try {
            $row = $this->getEntity()->getOne(array(), array('name' => $user));
        } catch (Exception $e) {
            return false;
        }

        if($row) {
            $seed = self::getSeed($row['pass']);

            if($row['pass'] == self::hashPass($pass, $seed)) {
                return $row;
            }

        }

        return false;

    }

    public function authenticate($data) {

        $_SESSION[$this->_sessionKey] = $data['name'];

        foreach($this->_sessionFields as $key => $field) {
            if(isset($data[$key])) {
                $_SESSION[$field] = $data[$key];
            }
        }

        return $this;
    }

    public function checkIsAuthenticated() {
        if (isset($_SESSION[$this->_sessionKey]) && $_SESSION[$this->_sessionKey]) {
            return true;
        }

        return false;
    }

    public static function encryptPass($raw) {
        $passHash = substr(md5(microtime()), 0, 5);
        return self::hashPass($raw, $passHash);
    }

    public static function hashPass($raw, $passHash) {
        return md5($raw . ':' . $passHash) . ':' . $passHash;
    }

    public static function getSeed($pass) {
        return substr($pass, strpos($pass, ':') + 1);
    }
}