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
<form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?controller=' . $this->getControllerName(); ?>" enctype="multipart/form-data">
    <table class="question-form">
        <thead>
        <tr>
            <th colspan="2">Import</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="label input-label">Questions file <br /><span style="font-size: 12px;">(Max file size: <?php echo ini_get('post_max_size'); ?>)</span></td>
            <td>
                <input type="file" name="questions" />
            </td>
        </tr>
        <tr>
            <td class="label">Replace questions</td>
            <td>
                <input type="checkbox" style="width: inherit;" name="replace" />
            </td>
        </tr>
        <tr>
            <td colspan="2" class="align-right"><button class="submit">import questions</button></td>
        </tr>
        </tbody>
    </table>
    <input type="hidden" name="action" value="import">
</form>

<table class="question-form">
    <thead>
    <tr>
        <th colspan="2">
            Self processing
            <div style="float: right;" id="show-processing">
                <button class="submit" onclick="window.location='<?php echo $_SERVER['PHP_SELF']; ?>?controller=<?php echo $this->getControllerName(); ?>&action=export'">export questions</button>
            </div>
        </th>
    </tr>
    </thead>
</table>