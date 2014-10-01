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

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
    <meta name="HandheldFriendly" content="true"/>
    <title><?php echo TITLE; ?></title>
    <link rel="shortcut icon" href="images/favicon.ico"/>
    <link rel="stylesheet" type="text/css" href="css/frontend.css"/>
    <link rel="stylesheet" type="text/css" href="css/common.css"/>
    <script type="text/javascript" src="js/frontend.js"></script>
</head>
<body>
<div id="content">
    <div id="header">
        <?php if($this->getMenu()->count()) : ?>
            <div id="menu-icon" class="menu-icon-closed" onclick="Menu.toggleMenu();">&nbsp;</div>
        <?php endif; ?>
        <div id="header-container" style="vertical-align: middle;">

            <img src="images/header-image.png" style="width: 30px;"><span><?php echo TITLE; ?></span>
        </div>
    </div>
    <?php if($this->getMenu()->count()) : ?>
        <div id="side-menu">
            <?php
            foreach ($this->getMenu()->getItems() as $item) :
                if (is_object($item['name'])) :
                    echo '<ul>';
                    foreach ($item['name']->getItems() as $subItem) :
                        echo '<li' . (isset($subItem['callback']) ? ' onclick="' . $subItem['callback'] . '"' : '') . '>';
                        echo $subItem['name'];
                        echo '</li>';
                    endforeach;
                    echo '</ul>';
                else :
                    echo '<div' . (isset($item['callback']) ? ' onclick="' . $item['callback'] . '"' : '') . '>';
                    echo $item['name'];
                    echo '</div>';
                endif;
            endforeach;
            ?>
        </div>
    <?php endif; ?>
    <div id="body">
        <?php include($contentFile); ?>
    </div>
    <?php include('footer.php'); ?>
</div>
</body>
</html>