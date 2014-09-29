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
class AdminQuestions extends AbstractAdminController
{


    public function indexAction()
    {
        $this->renderLayout('questions', array('key' => false));
    }

    protected function getEntity()
    {
        return DatabaseEntity::getEntity('questions');
    }

    public function updateAction()
    {

        if (count($_POST) == 0 || !isset($_POST['question'])) {
            $this->redirect();
        }

        $key = (int)$_POST['key'];

        $elem = new stdClass();
        $elem->question = nl2br(htmlspecialchars($_POST['question']));

        $this->setAnswers($elem);

        $questionsEntity = $this->getEntity();

        try {

            if ($key) {
                $questionsEntity->update(array('question' => json_encode($elem)), array('id' => $key));
                MessageHelper::set('Question modified!');
            } else {
                $questionsEntity->insert(array('question' => json_encode($elem)));
                $key = $questionsEntity->lastInsertRowid();
                MessageHelper::set('Question added!');
            }

            $this->setImage($questionsEntity, $key, $elem);

            $this->setCategories($key);

        } catch (Exception $e) {
            MessageHelper::set($e->getMessage());
        }

        $this->redirect();

    }

    protected function setCategories($key) {

        $entity = DatabaseEntity::getEntity('category_question');

        $entity->delete(array('question_id' => $key));

        foreach($_POST['categories'] as $categoryId) {
            $entity->insert(array('category_id' => $categoryId, 'question_id' => $key));
        }

    }

    protected function setAnswers(stdClass $element) {
        $element->ans = array();

        for ($i = 0; $i < 6; $i++) {
            if ($_POST['q' . $i]) {
                $ans = new stdClass();
                $ans->text = htmlspecialchars($_POST['q' . $i]);
                $ans->corect = isset($_POST['a' . $i]) ? $_POST['a' . $i] : null;

                $element->ans[] = $ans;
            }
        }
    }

    protected function setImage(DatabaseEntity $questionsEntity, $key, stdClass $element)
    {
        if (!isset($_FILES["file"]) || !($_FILES["file"]["error"] == 0 && $_FILES["image"]["tmp_name"])) {
            return;
        }

        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (in_array($extension, array('jpg', 'jpeg', 'gif', 'png'))) {
            $fileTarget = getQuestionImageFolder() . $key . '.' . $extension;
            move_uploaded_file($_FILES['image']['tmp_name'], $fileTarget);

            $element->img = $key . '.' . $extension;

            $questionsEntity->update(array('question' => json_encode($element)), array('id' => $key));
        }
    }

    public function delAction()
    {

        if (!isset ($_GET['key'])) {
            $this->redirect();
        }

        $key = (int)$_GET['key'];

        $questionEntity = $this->getEntity();

        $oldQuestion = $questionEntity->getOne(array('question'), array('id' => $key));

        $result = json_decode($oldQuestion['question']);

        if ($result && $result->img) {
            @unlink(getQuestionImageFolder() . $result->img);
        }

        try {
            $questionEntity->delete(array('id' => $key));
            MessageHelper::set('Question deleted!');
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

        $result = $this->getEntity()->getOne(array(), array('id' => $key));

        $this->renderLayout('questions', array('key' => $key, 'data' => json_decode($result['question'])));
    }

    protected function getSelectedCategoriesArray($key) {

        $entity = DatabaseEntity::getEntity('category_question');

        $categoryIds = $entity->getAll(array('category_id'), array('question_id' => $key));

        $result = array();

        foreach($categoryIds as $categoryId) {
            $result[] = $categoryId['category_id'];
        }

        return $result;
    }

}