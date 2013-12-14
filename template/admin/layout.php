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

?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo TITLE; ?></title>
    <link rel="shortcut icon" href="images/favicon.ico"/>

    <link rel="stylesheet" type="text/css" href="css/admin.css">
</head>

<body>
<div id="content">
    <?php
    $menu_selection = 'admin_questions';
    include( 'template' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'menu.php'); ?>

    <?php if ($_SESSION['message']) : ?>
        <div id="message"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <div id="body">
    <?php include($contentFile); ?>
    </div>
    <div id="footer">
        A project by by <a href="http://claudiupersoiu.ro" target="_blank">Claudiu Persoiu</a>
    </div>
</div>
</body>
</html>