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

class Quiz extends AbstractFrontendController
{

    public function preDispatch()
    {
        $this->getMenu()->addItem('Start', 'startQuiz();', 10);
        $this->getMenu()->addItem('QR for APP', 'displayQr();', 30);
        parent::preDispatch();
    }

    public function indexAction()
    {
        $json = $this->getQuestions();

        $this->addCategoriesToMenu($this->getCategories());

        // cache headers
        $expires = 60 * 10;
        header("Pragma: public");
        header("Cache-Control: maxage=" . $expires);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

        $this->renderLayout('quiz', array('json' => $json));
    }

    protected function getQuestions()
    {
        $questions = array();

        $entityQuestions = DatabaseEntity::getEntity('questions');

        foreach ($entityQuestions->getAll() as $question) {
            $questionArray = array();
            $questionArray['data'] = json_decode($question['question']);
            $questionArray['relations'] = $this->getQuestionRelations($question['id']);
            $questions[] = $questionArray;
        }

        return $questions;
    }

    protected function getQuestionRelations($questionId)
    {
        $relationsResult = DatabaseEntity::getEntity('category_question')
            ->getAll(array('category_id'), array('question_id' => $questionId));

        $relationsIds = array();

        foreach ($relationsResult as $relation) {
            $relationsIds[] = $relation['category_id'];
        }

        return $relationsIds;
    }

    protected function getCategories()
    {
        return DatabaseEntity::getEntity('categories')->getAll(array(), array(), 'ord');
    }

    protected function addCategoriesToMenu($categories)
    {
        $subMenu = new Menu();

        foreach ($categories as $category) {
            $subMenu->addItem($category['name'], 'startQuiz(' . $category['id'] . ', \'' . $category['name'] . '\');');
        }

        $this->getMenu()->addItem($subMenu, false, 20);
    }

}