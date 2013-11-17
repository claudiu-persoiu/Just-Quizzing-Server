<?php

/**
 * Copyright (c) 2013 Claudiu Persoiu (http://www.claudiupersoiu.ro/)
 *
 * This file is part of "Just quizzing".

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

class AdminImportExport extends AbstractController {

    public function getTemplate() {
        return 'import_export';
    }

    public function indexAction() {
        $this->renderLayout($this->getTemplate());
    }

    protected function getEntity() {
        return DatabaseEntity::getEntity('questions');
    }

    public function importAction() {

        if ($_FILES['questions']['error'] > 0 || !$_FILES['questions']['tmp_name']) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        $content = file_get_contents($_FILES['questions']['tmp_name']);

        if(!$content) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        if(isset($_POST) && $_POST['replace']) {

            $this->getEntity()->delete();

            $files = glob('data' . DIRECTORY_SEPARATOR . QUESTION_IMAGE . DIRECTORY_SEPARATOR . '*'); // get all file names

            foreach($files as $file){ // iterate files
                if(is_file($file)
                    && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), array('jpg', 'jpeg', 'gif', 'png'))) {
                    unlink($file); // delete file

                }

            }
        }

        $jsonContent = @json_decode($content);

        foreach($jsonContent as $item) {

            $question = $item->question;

            $this->getEntity()->insert(array('question' => $question));

            if($item->image) {

                $imgName = $this->getEntity()->lastInsertRowid() . mimeTypeToExtension($item->image->mime);

                $imgPath = 'data' . DIRECTORY_SEPARATOR . QUESTION_IMAGE . DIRECTORY_SEPARATOR . $imgName;

                file_put_contents($imgPath, base64_decode($item->image->data));

                $questionData = json_decode($question);

                $questionData->img = $imgName;

                $this->getEntity()->update(array('question' => json_encode($questionData)),
                    array('id' => $this->getEntity()->lastInsertRowid()));
            }

        }

        $_SESSION['message'] = 'Questions inserted!';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;

    }

    public function exportAction() {

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


        header('Content-Description: File Transfer');
        header('Content-type: application/json');
        header('Content-Disposition: attachment; filename="JustQuizzing_'. @date('Y-m-d-H-i-s') .'.data"');
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        echo json_encode($json);
        exit();
    }

}