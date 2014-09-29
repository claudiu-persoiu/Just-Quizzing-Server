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
<form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?controller=' . $this->getControllerName(); ?>">
    <table class="question-form">
        <thead>
        <tr>
            <th colspan="2">Frotend restriction</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="label" style="width: 200px;">Enable valid user restriction</td>
            <td>
                <select name="frontend_user_restriction">
                    <option value="1" <?php if (FRONTEND_USER_RESTRICTION == 1) echo 'selected' ?>>Enabled</option>
                    <option value="0" <?php if (FRONTEND_USER_RESTRICTION == 0) echo 'selected' ?>>Disabled</option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="align-right">
                <button class="submit">Update</button>
            </td>
        </tr>
        </tbody>
    </table>
    <input type="hidden" name="action" value="restriction">
</form>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?controller=' . $this->getControllerName(); ?>"
      onsubmit="return validate();">
    <table class="question-form">
        <thead>
        <tr>
            <th colspan="2"><?php echo (isset($key) && $key) ? 'Edit' : 'Add'; ?> user</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $this->renderSlice('row_input', array(
            'label' => 'Username',
            'name' => 'username',
            'data' => isset($data['name']) ? $data['name'] : ''
        ));

        $this->renderSlice('row_input', array(
            'label' => 'Password',
            'name' => 'password',
            'data' => ''
        ));
        ?>
        <input type="hidden" name="key" value="<?php if (isset($key) && $key) echo $key; ?>"/>
        <tr>
            <td colspan="2" class="align-right">
                <button class="submit"><?php echo (isset($key) && $key) ? "edit" : "add"; ?> user</button>
            </td>
        </tr>
        </tbody>
    </table>
    <input type="hidden" name="action" value="update">
</form>

<table class="question-form">
    <thead>
    <th colspan="3">Users</th>
    </thead>
    <tbody>
    <?php

    $i = 0;
    foreach ($this->getEntity()->getAll() as $user) {
        $key = $user['id'];

        $i++;
        ?>
        <tr <?php if ($i % 2) {
            echo 'class="alternate"';
        } ?>>
            <td class="identifier"><strong><?php echo $i; ?></strong></td>
            <td>
                <div>
                    <?php echo $user['name']; ?>
                </div>
            </td>
            <td class="actions-container">
                <a href="?controller=<?php echo $this->getControllerName(); ?>&action=edit&key=<?php echo $key; ?>"
                   class="submit">edit</a>
                <a href="?controller=<?php echo $this->getControllerName(); ?>&action=del&key=<?php echo $key; ?>"
                   class="cancel">delete</a>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<script>

    function validate() {
        return validateInput('username', 'Add a username!') && validateInput('password', 'Add a password!');
    }

</script>