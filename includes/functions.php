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

function encryptPass($raw) {

    $passHash = substr(md5(microtime()), 0, 5);

    return hashPass($raw, $passHash);
}

function hashPass($raw, $passHash) {
    return md5($raw . ':' . $passHash) . ':' . $passHash;
}

function getSeed($pass) {
    return substr($pass, strpos($pass, ':') + 1);
}

function mimeTypeToExtension($mime) {
    return str_replace('image/', '.', $mime);
}

function classAutoloader($class) {
    require_once 'includes' . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . $class . '.php';
}

spl_autoload_register('classAutoloader');