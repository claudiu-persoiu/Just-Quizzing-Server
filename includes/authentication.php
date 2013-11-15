<?php

/**
 * Copyright (c) 2013 Claudiu Persoiu (http://www.claudiupersoiu.ro/)
 *
 * This file is part of "Just quizzing".

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

function authenticationForm() {
    header('WWW-Authenticate: Basic realm="Enter username and password!"');
    header('HTTP/1.0 401 Unauthorized');
    echo "No credentials? What a bummer...\n";
    exit();
}

function authenticateAbstract($sessionKey, $sessionFields = array(), $usersTable) {

    if($_GET['logout'] && $_SESSION[$sessionKey]) {

        $_SESSION[$sessionKey] = null;

        foreach($sessionFields as $field) {
            $_SESSION[$field] = null;
        }

        $_SESSION['logout'] = true;

        // get rid of the logout parameter
        header( 'Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    if($_SESSION['logout']) {
        $_SESSION['logout'] = false;
        authenticationForm();
    }

    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && !$_SESSION[$sessionKey]) {

        $row = DatabaseEntity::getEntity($usersTable)->getOne(array(), array('name' => $_SERVER['PHP_AUTH_USER']));

        if($row) {
            $seed = getSeed($row['pass']);

            if($row['pass'] == hashPass($_SERVER['PHP_AUTH_PW'], $seed)) {
                $_SESSION[$sessionKey] = $row['name'];

                foreach($sessionFields as $key => $field) {
                    $_SESSION[$field] = $row[$key];
                }

            }

        }
    }

    if(!$_SESSION[$sessionKey]) {
        authenticationForm();
    }
}