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

class adminUsersBackend extends abstractController {

    public function indexAction() {
        $this->renderLayout('users_backend');
    }

    public function updateAction() {
        if(count($_POST) == 0 || !isset($_POST['username'])) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        global $db;

        $passRaw = $_POST['password'];

        $passHash = substr(md5(microtime()), 0, 5);

        $pass = md5($passRaw . ':' . $passHash) . ':' . $passHash;

        if($_POST['key']) {
            $stmt = $db->prepare('UPDATE admin_users SET name = :name, pass = :pass WHERE id = :id');
            $stmt->bindParam(':id', $_POST['key'], SQLITE3_INTEGER);

            $message = 'User modified!';
        } else {
            $stmt = $db->prepare('INSERT INTO admin_users (name, pass) VALUES (:name, :pass)');

            $message = 'User added!';
        }

        $stmt->bindParam(':name', $_POST['username'], SQLITE3_TEXT);
        $stmt->bindParam(':pass', $pass, SQLITE3_TEXT);
        $stmt->execute();

        $_SESSION['message'] = $message;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    public function delAction() {

        if(!isset ($_GET['key'])) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        global $db;

        $key = (int)$_GET['key'];

        $stmt = $db->prepare('DELETE FROM admin_users WHERE id = :id');
        $stmt->bindParam(':id', $key, SQLITE3_INTEGER);
        $stmt->execute();

        $_SESSION['message'] = 'User deleted!';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;

    }

    public function editAction() {

        if(!isset ($_GET['key'])) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        global $db;

        $key = (int)$_GET['key'];

        $stmt = $db->prepare('SELECT id,name FROM admin_users WHERE id = :id');
        $stmt->bindParam(':id', $key, SQLITE3_INTEGER);
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $key = $row['id'];
            $data = $row;
        }

        $this->renderLayout('users_backend', array('key' => $key, 'data' => $data));

    }

}