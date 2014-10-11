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

class AdminCategories extends AbstractAdminController
{

    protected function getTemplate()
    {
        return 'categories';
    }

    public function indexAction()
    {
        $this->renderLayout($this->getTemplate());
    }

    protected function getEntity()
    {
        return DatabaseEntity::getEntity('categories');
    }


    public function updateAction()
    {
        if (count($_POST) == 0 || !isset($_POST['name'])) {
            $this->redirect();
        }

        $entity = $this->getEntity();

        $key = (int)$_POST['key'];

        try {
            if ($_POST['key']) {
                $entity->update(array('name' => $_POST['name'], 'ord' => (int)$_POST['ord']), array('id' => $key));
                MessageHelper::set('Category modified!');
            } else {
                $entity->insert(array('name' => $_POST['name'], 'ord' => (int)$_POST['ord']));
                MessageHelper::set('Category added!');
            }
        } catch (Exception $e) {
            MessageHelper::set($e->getMessage());
        }


        $this->redirect();
    }

    public function editAction()
    {

        if (!isset ($_GET['key'])) {
            $this->redirect();
        }

        $key = (int)$_GET['key'];

        $data = $this->getEntity()->getOne(array(), array('id' => $key));

        $this->renderLayout($this->getTemplate(), array('key' => $key, 'data' => $data));

    }

    public function delAction()
    {
        if (!isset ($_GET['key'])) {
            $this->redirect();
        }

        $key = (int)$_GET['key'];

        try {
            $this->getEntity()->delete(array('id' => $key));
            $message = 'Category deleted!';
        } catch (Exception $e) {
            $message = $e->getMessage();
        }

        MessageHelper::set($message);
        $this->redirect();
    }
}