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

abstract class AbstractController
{

    protected $_controllerName = false;

    public function preDispatch()
    {
        return true;
    }

    public function postDispatch()
    {
        return true;
    }

    public function dispatch()
    {

        if (!$this->checkAuthentication()) {
            $this->displayAuthenticationForm();
            return;
        }

        $method = $this->getMethod();

        $this->preDispatch();

        $this->{$method}();

        $this->postDispatch();

    }

    final private function getMethod()
    {
        if (isset($_REQUEST['action'])) {
            $method = $_REQUEST['action'] . 'Action';

            if (!method_exists($this, $method)) {
                header('HTTP/1.0 404 Not Found');
                exit;
            }
        } else {
            $method = 'indexAction';
        }

        return $method;
    }

    protected function isRestricted() {
        return FRONTEND_USER_RESTRICTION;
    }

    protected function getAuthenticator() {
        return new FrontendAuthentication();
    }

    protected function checkAuthentication()
    {
        if (!$this->isRestricted()) {
            return true;
        }

        $authentication = $this->getAuthenticator();

        if (!$authentication->checkIsAuthenticated()) {

            $user = false;

            if (isset($_POST['login_user']) && isset($_POST['login_pass'])) {
                $user = $authentication->getUserData($_POST['login_user'], $_POST['login_pass']);
            }

            if (!$user) {
                return false;
            }

            $authentication->authenticate($user);
        } else if (isset($_GET['logout'])) {

            $authentication->logout(true);

        }

        return true;
    }

    public function displayAuthenticationForm() {
        $this->renderLayout('login');
    }

    public function renderLayout($section, $context = array())
    {

        extract($context, EXTR_SKIP);

        $contentFile = 'template' . DIRECTORY_SEPARATOR . $section . '.php';

        require_once('template' . DIRECTORY_SEPARATOR . 'layout.php');
    }

    public function renderSlice($section, $context = array()) {
        extract($context, EXTR_SKIP);

        include('template' . DIRECTORY_SEPARATOR . 'slice' . DIRECTORY_SEPARATOR . $section . '.php');
    }

    public function redirect($url = null)
    {
        if ($url == null) {
            $url = $_SERVER['PHP_SELF'] . '?controller=' . $this->getControllerName();
        }

        header("Location: " . $url);
        exit;
    }

    public function getControllerName() {

        if(!$this->_controllerName) {
            $this->_controllerName = Dispatcher::classToController(get_class($this));
        }

        return $this->_controllerName;
    }

}