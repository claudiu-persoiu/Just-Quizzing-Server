<?php

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
        if ($this->getAuthenticator()->checkIsAuthenticated()) {
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