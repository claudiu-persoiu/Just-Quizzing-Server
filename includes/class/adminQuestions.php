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

class adminQuestions extends abstractController {


    public function indexAction() {
        $this->renderLayout('questions');
    }

    public function updateAction() {

        if(count($_POST) == 0 || !isset($_POST['question'])) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        global $db;

        $key = (int)$_POST['key'];

        $elem = new stdClass();
        $elem->question = nl2br(htmlspecialchars($_POST['question']));
        $elem->type = $_POST['type'];
        //$elem->img = $_POST['image'];
        $elem->ans = array();

        for($i = 0; $i < 6; $i++) {
            if($_POST['q'.$i]) {
                $ans = new stdClass();
                $ans->text = htmlspecialchars($_POST['q'.$i]);
                $ans->corect = $_POST['a'.$i];

                $elem->ans[] = $ans;
            }
        }

        if($key) {
            $stmt = $db->prepare('UPDATE questions SET question = :question WHERE id = :id');
            $stmt->bindParam(':id', $key, SQLITE3_INTEGER);

            $message = 'Question modified!';
        } else {
            $stmt = $db->prepare('INSERT INTO questions (question) VALUES (:question)');

            $message = 'Question added!';
        }

        $stmt->bindParam(':question', json_encode($elem), SQLITE3_TEXT);
        $stmt->execute();

        if ($_FILES["file"]["error"] == 0 && $_FILES["image"]["tmp_name"]) {
            if(!$key) {
                $key = $db->lastInsertRowid();
            }

            $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if(in_array($extension, array('jpg', 'jpeg', 'gif', 'png'))) {
                $fileTarget = 'data' . DIRECTORY_SEPARATOR . QUESTION_IMAGE . DIRECTORY_SEPARATOR . $key . '.' . $extension;
                move_uploaded_file($_FILES['image']['tmp_name'], $fileTarget);

                $stmt = $db->prepare('UPDATE questions SET question = :question WHERE id = :id');
                $stmt->bindParam(':id', $key, SQLITE3_INTEGER);

                $elem->img = $key . '.' . $extension;

                $stmt->bindParam(':question', json_encode($elem), SQLITE3_TEXT);
                $stmt->execute();

            }
        }

        $_SESSION['message'] = $message;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;

    }

    public function delAction() {

        if(!isset ($_GET['key'])) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        global $db;

        $key = (int)$_GET['key'];

        $stmt = $db->prepare('SELECT question FROM questions WHERE id = :id');
        $stmt->bindParam(':id', $key, SQLITE3_INTEGER);
        $resultSet = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        $result = json_decode($resultSet['question']);

        if($result && $result->img) {
            @unlink('data' . DIRECTORY_SEPARATOR . QUESTION_IMAGE . DIRECTORY_SEPARATOR . $key . '.' . $result->img);
        }

        $stmt = $db->prepare('DELETE FROM questions WHERE id = :id');
        $stmt->bindParam(':id', $key, SQLITE3_INTEGER);
        $stmt->execute();

        $_SESSION['message'] = 'Question deleted!';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    public function editAction() {

        if(!isset ($_GET['key'])) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        global $db;

        $key = (int)$_GET['key'];

        $stmt = $db->prepare('SELECT * FROM questions WHERE id = :id');
        $stmt->bindParam(':id', $key, SQLITE3_INTEGER);
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $key = $row['id'];
            $data = json_decode($row['question']);
        }

        $this->renderLayout('questions', array('key' => $key, 'data' => $data));
    }

}