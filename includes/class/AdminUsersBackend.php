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

class AdminUsersBackend extends AbstractController {

    public function indexAction() {
        $this->renderLayout('users_backend');
    }

    protected function getEntity() {
        return DatabaseEntity::getEntity('admin_users');
    }

    public function updateAction() {
        if(count($_POST) == 0 || !isset($_POST['username'])) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        $pass = encryptPass($_POST['password']);

        $usersEntity = $this->getEntity();

        $key = (int) $_POST['key'];

        if($key) {

            $usersEntity->update(array('name' => $_POST['username'], 'pass' => $pass), array('id' => $key));

            $message = 'User modified!';
        } else {
            $usersEntity->insert(array('name' => $_POST['username'], 'pass' => $pass));

            $message = 'User added!';
        }

        $_SESSION['message'] = $message;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    public function delAction() {

        if(!isset ($_GET['key'])) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        $key = (int)$_GET['key'];

        $this->getEntity()->delete(array('id' => $key));

        $_SESSION['message'] = 'User deleted!';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;

    }

    public function editAction() {

        if(!isset ($_GET['key'])) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        $key = (int)$_GET['key'];

        $data = $this->getEntity()->getOne(array(), array('id' => $key));

        $this->renderLayout('users_backend', array('key' => $key, 'data' => $data));

    }

}
// 109