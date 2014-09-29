<?php

class ExportHelper {

    public static function export()
    {
        $categories = DatabaseEntity::getEntity('categories')->getAll();

        $categoriesRelations = array();

        foreach($categories as $key => $category) {
            $categoriesRelations[$category['id']] = $key + 1;
        }

        $json = array(
            'questions' => self::exportQuestions($categoriesRelations),
            'categories' => self::exportCategories($categories)
        );

        return json_encode($json);
    }

    protected static function exportCategories($categories) {

        $categoriesExport = array();

        foreach($categories as $i => $category) {
            $categoriesExport['c' . ($i + 1)] = array(
                'name' => $category['name'],
                'ord'  => $category['ord']
            );
        }

        return $categoriesExport;
    }

    protected static function exportQuestions($categoriesRelations) {

        $entityQuestions = DatabaseEntity::getEntity('questions');

        $questions = array();

        $i = 0;
        foreach ($entityQuestions->getAll() as $question) {
            $questions['q' . ++$i] = self::exportElement($question, $categoriesRelations);
        }

        return $questions;
    }

    protected static function exportElement($question, $categoriesRelations) {

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

        foreach($relationIds as $relation) {
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