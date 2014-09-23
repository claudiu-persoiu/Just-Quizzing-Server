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

class AdminQuestions extends AbstractAdminController {


    public function indexAction() {
        $this->renderLayout('questions');
    }

    protected function getEntity() {
        return DatabaseEntity::getEntity('questions');
    }

    public function updateAction() {

        if(count($_POST) == 0 || !isset($_POST['question'])) {
            $this->redirect();
        }

        $key = (int)$_POST['key'];

        $elem = new stdClass();
        $elem->question = nl2br(htmlspecialchars($_POST['question']));
        $elem->type = $_POST['type'];

        $elem->ans = array();

        for($i = 0; $i < 6; $i++) {
            if($_POST['q'.$i]) {
                $ans = new stdClass();
                $ans->text = htmlspecialchars($_POST['q'.$i]);
                $ans->corect = $_POST['a'.$i];

                $elem->ans[] = $ans;
            }
        }

        $questionsEntity = $this->getEntity();

        try {

            if($key) {
                $questionsEntity->update(array('question' => json_encode($elem)), array('id' => $key));
                $message = 'Question modified!';
            } else {
                $questionsEntity->insert(array('question' => json_encode($elem)));
                $message = 'Question added!';
            }

            if ($_FILES["file"]["error"] == 0 && $_FILES["image"]["tmp_name"]) {
                if(!$key) {
                    $key = $questionsEntity->lastInsertRowid();
                }

                $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                if(in_array($extension, array('jpg', 'jpeg', 'gif', 'png'))) {
                    $fileTarget = 'data' . DIRECTORY_SEPARATOR . QUESTION_IMAGE . DIRECTORY_SEPARATOR . $key . '.' . $extension;
                    move_uploaded_file($_FILES['image']['tmp_name'], $fileTarget);

                    $elem->img = $key . '.' . $extension;

                    $questionsEntity->update(array('question' => json_encode($elem)), array('id' => $key));

                }
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
        }

        $_SESSION['message'] = $message;
        $this->redirect();

    }

    public function delAction() {

        if(!isset ($_GET['key'])) {
            $this->redirect();
        }

        $key = (int)$_GET['key'];

        $questionEntity = $this->getEntity();

        $oldQuestion = $questionEntity->getOne(array('question'), array('id' => $key));

        $result = json_decode($oldQuestion['question']);

        if($result && $result->img) {
            @unlink('data' . DIRECTORY_SEPARATOR . QUESTION_IMAGE . DIRECTORY_SEPARATOR . $result->img);
        }

        try {
            $questionEntity->delete(array('id' => $key));
            $message = 'Question deleted!';
        } catch (Exception $e) {
            $message = $e->getMessage();
        }

        $_SESSION['message'] = $message;
        $this->redirect();
    }

    public function editAction() {

        if(!isset ($_GET['key'])) {
            $this->redirect();
        }

        $key = (int)$_GET['key'];

        $result = $this->getEntity()->getOne(array(), array('id' => $key));

        $this->renderLayout('questions', array('key' => $key, 'data' => json_decode($result['question'])));
    }

}