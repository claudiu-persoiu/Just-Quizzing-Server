<?php

class Quiz extends AbstractController
{

    public function indexAction()
    {

        $entityQuestions = DatabaseEntity::getEntity('questions');

        $json = array();

        foreach ($entityQuestions->getAll() as $question) {
            $json[] = json_decode($question['question']);
        }

        // cache headers
        $expires = 60 * 10;
        header("Pragma: public");
        header("Cache-Control: maxage=" . $expires);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

        $this->renderLayout('quiz', array('json' => $json));
    }

}