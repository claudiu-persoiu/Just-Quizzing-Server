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

class ImportHelper
{

    public static function import($content)
    {
        if (isset($_POST) && isset($_POST['replace'])) {
            self::cleanOldData();
        }

        $jsonContent = @json_decode($content);

        $categories = self::getCategories($jsonContent->categories);

        $categoriesRelations = self::saveCategoriesAndGetRelation($categories);

        self::saveQuestions($jsonContent->questions, $categoriesRelations);
    }

    protected static function cleanOldData()
    {

        $questionsEntity = DatabaseEntity::getEntity('questions');
        $questionsEntity->delete();

        $categoriesEntity = DatabaseEntity::getEntity('categories');
        $categoriesEntity->delete();

        $files = glob(getQuestionImageFolder() . '*'); // get all file names

        foreach ($files as $file) { // iterate files
            if (is_file($file)
                && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), array('jpg', 'jpeg', 'gif', 'png'))
            ) {
                unlink($file); // delete file
            }
        }
    }

    protected static function saveQuestions($jsonQuestions, $categoriesRelations)
    {

        $questionsEntity = DatabaseEntity::getEntity('questions');
        $categoryQuestionEntity = DatabaseEntity::getEntity('category_question');

        foreach ($jsonQuestions as $item) {

            $question = $item->question;

            $questionsEntity->insert(array('question' => $question));

            $questionId = $questionsEntity->lastInsertRowid();

            if (isset($item->image) && $item->image) {

                $imgName = $questionsEntity->lastInsertRowid() . mimeTypeToExtension($item->image->mime);

                $imgPath = getQuestionImageFolder() . $imgName;

                file_put_contents($imgPath, base64_decode($item->image->data));

                $questionData = json_decode($question);

                $questionData->img = $imgName;

                $questionsEntity->update(array('question' => json_encode($questionData)),
                    array('id' => $questionId));
            }

            foreach ($item->categories as $categoryId) {
                $categoryQuestionEntity->insert(array(
                    'category_id' => $categoriesRelations[$categoryId],
                    'question_id' => $questionId
                ));
            }
        }
    }

    protected static function getCategories($jsonCategories)
    {

        $categories = array();
        $i = 0;
        while (true) {
            $key = 'c' . $i;

            if (!isset($jsonCategories->{$key})) {
                break;
            }

            $categories[$i] = $jsonCategories->{$key};
            $i++;
        }

        return $categories;
    }

    protected static function saveCategoriesAndGetRelation($categories)
    {

        $relations = array();
        $categoriesEntity = DatabaseEntity::getEntity('categories');

        foreach ($categories as $key => $category) {
            $categoriesEntity->insert(array('name' => $category->name, 'ord' => $category->ord));
            $relations[$key] = $categoriesEntity->lastInsertRowid();
        }

        return $relations;
    }

}