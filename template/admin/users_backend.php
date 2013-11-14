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
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return checkLeastAnswer();">
<table class="question-form">
    <thead>
    <tr>
        <th colspan="2"><?php echo $key ? 'Edit':'Add'; ?> user</th>
    </tr>
    </thead>
    <tbody>
        <tr>
            <td class="label" style="width: 120px;">Username</td>
            <td>
                <input type="text" name="username" id="username" value="<?php echo $data['name']; ?>" />
            </td>
        </tr>
        <tr>
            <td class="label">Password</td>
            <td>
                <input type="password" name="password" id="password" />
            </td>
        </tr>

        <input type="hidden" name="key" value="<?php echo $key; ?>" />
        <tr>
            <td colspan="2" style="text-align: right"><button class="submit"><?php echo $key ? "edit":"add"; ?> user</button></td>
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

    $stmt = $db->prepare('SELECT id, name FROM admin_users');

    $result = $stmt->execute();

    $i = 0;
    while($user = $result->fetchArray(SQLITE3_ASSOC)) {
        $key = $user['id'];

        $i++;
        ?>
        <tr <?php if($i % 2) { echo 'class="alternate"'; } ?>>
            <td style="width: 20px;"><strong><?php echo $i; ?></strong></td>
            <td>
                <div>
                    <?php echo $user['name']; ?>
                </div>
            </td>
            <td style="width: 90px;">
                <a href="?action=edit&key=<?php echo $key; ?>" class="submit">edit</a>
                <?php if($_SESSION['authenticated_admin_id'] != $key) { ?>
                    <a href="?action=del&key=<?php echo $key; ?>" class="cancel">delete</a>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<script>

    function checkLeastAnswer() {

        var username = document.getElementById('username');
        if(username.value == '') {
            alert('Add a username!');
            username.focus();
            return false;
        }

        var password = document.getElementById('password');
        if(password.value == '') {
            alert('Add a password!');
            password.focus();
            return false;
        }

        return true;
    }

</script>