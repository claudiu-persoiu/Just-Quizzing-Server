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
class ExportHelper
{

    public static function export()
    {
        $categories = DatabaseEntity::getEntity('categories')->getAll();

        $categoriesRelations = array();

        foreach ($categories as $key => $category) {
            $categoriesRelations[$category['id']] = $key;
        }

        $json = array(
            'questions' => self::exportQuestions($categoriesRelations),
            'categories' => self::exportCategories($categories)
        );

        return json_encode($json);
    }

    protected static function exportCategories($categories)
    {

        $categoriesExport = array();

        foreach ($categories as $i => $category) {
            $categoriesExport['c' . $i] = array(
                'name' => $category['name'],
                'ord' => $category['ord']
            );
        }

        return count($categoriesExport) == 0 ? new stdClass() : $categoriesExport;
    }

    protected static function exportQuestions($categoriesRelations)
    {

        $entityQuestions = DatabaseEntity::getEntity('questions');

        $questions = array();

        $i = 0;
        foreach ($entityQuestions->getAll() as $question) {
            $questions['q' . $i++] = self::exportElement($question, $categoriesRelations);
        }

        return $questions;
    }

    protected static function exportElement($question, $categoriesRelations)
    {

        $questionData = json_decode($question['question'], true);

        $element = array();

        if (isset($questionData['img']) && $questionData['img']) {
            $imgPath = getQuestionImageFolder() . $questionData['img'];

            if (is_file($imgPath)) {
                $element['image'] = array(
                    'mime' => mime_content_type($imgPath),
                    'data' => base64_encode(file_get_contents($imgPath))
                );
            }

            unset($questionData['img']);
        }

        $element['question'] = json_encode($questionData);
        $element['categories'] = self::findQuestionCategories($question['id'], $categoriesRelations);

        return $element;
    }

    protected static function findQuestionCategories($questionId, $categories)
    {

        $relationIds = DatabaseEntity::getEntity('category_question')->getAll(array('category_id'), array('question_id' => $questionId));

        $result = array();

        foreach ($relationIds as $relation) {
            $result[] = $categories[$relation['category_id']];
        }

        return $result;
    }

    public static function cacheHeaders()
    {

        header('Content-Description: File Transfer');
        header('Content-type: application/json');
        header('Content-Disposition: attachment; filename="JustQuizzing_' . @date('Y-m-d-H-i-s') . '.json"');
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
    }
}