<?php

class Quiz extends AbstractFrontendController
{

    public function preDispatch()
    {
        $this->getMenu()->addItem('Start', 'startQuiz();', 10);
        parent::preDispatch();
    }

    public function indexAction()
    {
        $json = array(
            'questions' => $this->getQuestions(),
            'categories' => $this->getCategories()
        );

        $this->addCategoriesToMenu($json['categories']);

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
        $entityRelations = DatabaseEntity::getEntity('category_question');

        foreach ($entityQuestions->getAll() as $question) {
            $questionArray = array();
            $questionArray['data'] = json_decode($question['question']);
            $questionArray['relations'] = $entityRelations->getAll(array('category_id'), array('question_id' => $question['id']));
            $questions[] = $questionArray;
        }

        return $questions;
    }

    protected function getCategories()
    {
        return DatabaseEntity::getEntity('categories')->getAll(array(), array(), 'ord');
    }

    protected function addCategoriesToMenu($categories)
    {
        $subMenu = new Menu();

        foreach ($categories as $category) {
            $subMenu->addItem($category['name'], 'startQuiz(' . $category['id'] . ');');
        }

        $this->getMenu()->addItem($subMenu, false, 20);
    }

}