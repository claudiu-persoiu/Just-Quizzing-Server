<?php

class AdminCategories extends AbstractAdminController {

    public function indexAction()
    {
        $this->renderLayout('categories');
    }

    protected function getEntity()
    {
        return DatabaseEntity::getEntity('categories');
    }


    public function updateAction() {
        if(count($_POST) == 0 || !isset($_POST['name'])) {
            $this->redirect();
        }

        $entity = $this->getEntity();

        $key = (int) $_POST['key'];

        try {
            if($_POST['key']) {
                $entity->update(array('name' => $_POST['name'], 'ord' => (int) $_POST['ord']), array('id' => $key));
                MessageHelper::set('Category modified!');
            } else {
                $entity->insert(array('name' => $_POST['name'], 'ord' => (int) $_POST['ord']));
                MessageHelper::set('Category added!');
            }
        } catch (Exception $e) {
            MessageHelper::set($e->getMessage());
        }


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