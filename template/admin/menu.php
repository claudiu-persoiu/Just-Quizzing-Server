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
<div id="header">
    <h1><img src="images/header-image.png"><?php echo TITLE; ?></h1>

    <div class="menu-container">

        <a <?php if($section == 'questions') echo 'class="selected"';?> href="?controller=admin_questions">Questions</a>
        <a <?php if($section == 'users_backend') echo 'class="selected"';?> href="?controller=admin_users_backend">Admin users</a>
        <a <?php if($section == 'users_frontend') echo 'class="selected"';?> href="?controller=admin_users_frontend">Site users</a>
        <a <?php if($section == 'import_export') echo 'class="selected"';?> href="?controller=admin_import_export">Import/Export</a>

        <a href="?logout=1" style="float:right;">logout</a>
    </div>
</div>
