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
<div id="header">
    <h1><?php echo TITLE; ?></h1>

    <div class="menu-container">

        <a <?php if($section == 'questions') echo 'class="selected"';?> href="admin_questions.php">Questions</a>
        <a <?php if($section == 'users_backend') echo 'class="selected"';?> href="admin_users_backend.php">Admin users</a>
        <a <?php if($section == 'users_frontend') echo 'class="selected"';?> href="admin_users_frontend.php">Site users</a>

        <a href="?logout=1" style="float:right;">logout</a>
    </div>
</div>
