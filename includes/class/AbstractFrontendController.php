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

abstract class AbstractFrontendController extends AbstractController
{
    protected $_menu = false;

    protected function isRestricted()
    {
        return (boolean)FRONTEND_USER_RESTRICTION;
    }

    protected function getAuthenticator()
    {
        if (!$this->_authenticator) {
            $this->_authenticator = new FrontendAuthentication();
        }

        return $this->_authenticator;
    }

    public function preDispatch()
    {
        if ($this->isRestricted() && $this->getAuthenticator()->checkIsAuthenticated()) {
            $this->getMenu()->addItem('Logout', 'redirect(\'?logout=1\')', 100);
        }
    }

    public function displayAuthenticationForm()
    {
        $this->renderLayout('login');
    }

    public function renderLayout($section, $context = array())
    {
        extract($context, EXTR_SKIP);

        $contentFile = 'template' . DIRECTORY_SEPARATOR . $section . '.php';

        require_once('template' . DIRECTORY_SEPARATOR . 'layout.php');
    }

    public function getMenu() {
        if(!$this->_menu) {
            $this->_menu = new Menu();
        }

        return $this->_menu;
    }
}