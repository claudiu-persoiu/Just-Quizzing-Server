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

require_once('includes' . DIRECTORY_SEPARATOR . 'config.php');
require_once('includes' . DIRECTORY_SEPARATOR . 'functions.php');

// beginning authentication
if(FRONTEND_USER_RESTRICTION) {

    $authentication = new FrontendAuthentication();

    $user = $authentication->getUserData($_POST['user'], $_POST['pass']);

    if(!$user) {
        echo json_encode(array('error' => 'Invalid user or pass'));
        exit();
    }


}

// end authentication

$entityQuestions = DatabaseEntity::getEntity('questions');

$json = array();

$i = 0;
foreach($entityQuestions->getAll() as $question) {

    $questionData = json_decode($question['question'], true);

    $key = 'q' . ++$i;

    $json[$key] = array();

    if($questionData['img']) {
        $imgPath = 'data' . DIRECTORY_SEPARATOR . QUESTION_IMAGE . DIRECTORY_SEPARATOR . $questionData['img'];

        if(is_file($imgPath)) {
            $json[$key]['image'] = array(
                'mime' => mime_content_type($imgPath),
                'data' => base64_encode(file_get_contents($imgPath))
            );
        }

        unset($questionData['img']);

    }

    $json[$key]['question'] = json_encode($questionData);

}
echo json_encode($json);
exit();

