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

class AdminUsersFrontend extends UsersAbstractController {

    protected function getEntity() {
        return DatabaseEntity::getEntity('users');
    }

    public function getTemplate() {
        return 'users_frontend';
    }

    public function restrictionAction() {

        $config = 'frontend_user_restriction';

        if(count($_POST) == 0 || !isset($_POST[$config])) {
            $this->redirect();
        }

        $value = (int)$_POST[$config];

        DatabaseEntity::getEntity('config')->update(array('value' => $value), array('config' => $config));

        $_SESSION['message'] = 'Configuration updated';
        $this->redirect();

    }

    public function getEncryptedPassword($pass) {
        return FrontendAuthentication::encryptPass($pass);
    }
}