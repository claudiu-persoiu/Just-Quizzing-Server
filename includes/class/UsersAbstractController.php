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

abstract class UsersAbstractController extends AbstractAdminController {

    abstract public function getTemplate();

    public function indexAction() {
        $this->renderLayout($this->getTemplate());
    }

    public function updateAction() {
        if(count($_POST) == 0 || !isset($_POST['username'])) {
            $this->redirect();
        }

        $pass = $this->getEncryptedPassword($_POST['password']);

        $usersEntity = $this->getEntity();

        $key = (int) $_POST['key'];

        if($_POST['key']) {

            $usersEntity->update(array('name' => $_POST['username'], 'pass' => $pass), array('id' => $key));

            $message = 'User modified!';

        } else {

            $usersEntity->insert(array('name' => $_POST['username'], 'pass' => $pass));

            $message = 'User added!';
        }

        $_SESSION['message'] = $message;
        $this->redirect();

    }

    public function editAction() {

        if(!isset ($_GET['key'])) {
            $this->redirect();
        }

        $key = (int)$_GET['key'];

        $data = $this->getEntity()->getOne(array(), array('id' => $key));

        $this->renderLayout($this->getTemplate(), array('key' => $key, 'data' => $data));

    }

    public function delAction() {
        if(!isset ($_GET['key'])) {
            $this->redirect();
        }

        $key = (int)$_GET['key'];

        $this->getEntity()->delete(array('id' => $key));

        $_SESSION['message'] = 'User deleted!';
        $this->redirect();

    }

}