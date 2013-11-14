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

session_start();

define('JSON_FILE', 'data' . DIRECTORY_SEPARATOR .'default.json');

//define('TITLE', 'Test some? - Just quizzing');

$db = new SQLite3('data/db.info');
$db->exec("PRAGMA journal_mode = MEMORY;
PRAGMA temp_store   = MEMORY;
PRAGMA encoding     = 'UTF-8';");

$stmt = $db->prepare('SELECT config, value FROM config');

$result = $stmt->execute();

while($row = $result->fetchArray(SQLITE3_ASSOC)) {
    define(strtoupper($row['config']), $row['value']);
}

function classAutoloader($class) {
    require_once 'includes' . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . $class . '.php';
}

spl_autoload_register('classAutoloader');