<?php

require_once('includes' . DIRECTORY_SEPARATOR . 'config.php');
require_once('includes' . DIRECTORY_SEPARATOR . 'functions.php');

ini_set('display_errors', '1');

$dbResource = DatabaseEntity::getEntity('questions')->getResource();

$dbResource->exec(
    'CREATE TABLE IF NOT EXISTS categories ('
    .' id INTEGER PRIMARY KEY,'
    .' name TEXT, ord INTEGER);'
);

$dbResource->exec(
    'CREATE TABLE IF NOT EXISTS "category_question" ('
    .' "id" INTEGER PRIMARY KEY ,'
    .' "category_id" INTEGER REFERENCES categories(id) ON DELETE CASCADE,'
    .' "question_id"  INTEGER REFERENCES questions(id) ON DELETE CASCADE);'
);

echo 'Update complete';
