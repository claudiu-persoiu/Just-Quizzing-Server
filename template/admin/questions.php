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
<table class="question-form">
    <thead>
    <tr>
        <th colspan="2"><?php echo $key ? 'Edit':'Add'; ?> question</th>
    </tr>
    </thead>
    <tbody>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return checkLeastAnswer();" enctype="multipart/form-data">
        <tr>
            <td class="label" style="width: 120px;">Question</td>
            <td>
                <textarea name="question" id="question" cols="70" rows="10"><?php echo $data->question; ?></textarea>
            </td>
        </tr>
        <tr>

            <td class="label">Answers</td>
            <td>
                <table>
                    <tbody>
                    <?php
                    $a = range('A', 'Z');

                    for($i = 0; $i < 6; $i++) { ?>
                        <tr>
                            <td style="width: 16px;"><?php echo $a[$i]; ?></td>
                            <td style="width: 25px;">
                                <input type="checkbox" name="a<?php echo $i; ?>" id="a<?php echo $i; ?>" value="true" <?php if($data->ans[$i]->corect) echo 'checked'; ?> />
                            </td>
                            <td>
                                <input type="text" name="q<?php echo $i; ?>" size="100" id="q<?php echo $i; ?>" value="<?php echo $data->ans[$i]->text; ?>" />
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td class="label">Image</td>
            <td><input type="file" name="image" value="<?php echo $data->img; ?>" /></td>
        </tr>
        <input type="hidden" name="key" value="<?php echo $key; ?>" />
        <tr>
            <td colspan="2" style="text-align: right"><button class="submit"><?php echo $key ? "edit":"add"; ?> question</button></td>
        </tr>
    </form>
    </tbody>
</table>

<table class="question-form">
    <thead>
    <tr>
        <th colspan="2">
            Self processing
            <div style="float: right;" id="show-processing">
                <button class="submit" onclick="return showProcessing();">open</button>
            </div>
        </th>
    </tr>
    </thead>
    <tbody  id="processing-container">
    <tr>
        <td class="label" style="width: 50%">Input data</td>
        <td class="label">Input script (content - string, questions - array, answers - array)</td>
    </tr>
    <tr>
        <td>
            <textarea id="unprocessed" cols="70" rows="10"></textarea>
        </td>
        <td>
            <textarea id="process-function" cols="70" rows="10"></textarea>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: right">
            <button class="submit" onclick="return process();">process data</button>
            <button class="cancel" onclick="return hideProcessing();">hide</button>
        </td>
    </tr>
    </tbody>
</table>


<script type="text/javascript">
    function process() {
        var content = document.getElementById('unprocessed').value;

        var questions, answers;

        eval(document.getElementById('process-function').value);

        if(typeof questions != 'undefined' && questions.length) {
            for(var i = 0; i<questions.length; i++) {
                document.getElementById('q' + i).value = questions[i];
            }
        }

        if(typeof answers != 'undefined' && answers.length) {
            for(var i = 0; i<answers.length; i++) {
                if(answers[i] == true) {
                    document.getElementById('a' + i).checked = true;
                }
            }
        }

        return false;
    }

    if(localStorage.hideProcessing) {
        hideProcessing();
    } else {
        showProcessing();
    }

    function hideProcessing() {
        document.getElementById('processing-container').style.display = 'none';
        document.getElementById('show-processing').style.display = '';
        localStorage.hideProcessing = 'hide';
        return false;
    }

    function showProcessing() {
        document.getElementById('processing-container').style.display = '';
        document.getElementById('show-processing').style.display = 'none';
        localStorage.hideProcessing = '';
        return false;
    }

    function checkLeastAnswer() {

        if(document.getElementById('question').value == '') {
            alert('Add a question!');
            return false;
        }

        for(var i = 0; i <6; i++) {
            if(document.getElementById('a' + i).checked == true) {
                return true;
            }
        }
        alert('Check at least one answer!');
        return false;
    }
</script>

<table class="question-form">
    <thead>
    <th colspan="3">Questions</th>
    </thead>
    <tbody>
    <?php

    $stmt = $db->prepare('SELECT * FROM questions');

    $result = $stmt->execute();

    $i = 0;
    while($question = $result->fetchArray(SQLITE3_ASSOC)) {

        $data = json_decode($question['question']);
        $key = $question['id'];

        $i++;
        ?>
        <tr <?php if($i % 2) { echo 'class="alternate"'; } ?>>
            <td style="width: 20px;"><strong><?php echo $i; ?></strong></td>
            <td>
                <div><?php echo $data->question; ?></div>
                <div style="padding-top: 12px;">
                    <?php
                    $j = 0;
                    foreach($data->ans as $answer) {
                        echo $a[$j++] . ' - ';
                        if($answer->corect) {
                            echo '<b>' . $answer->text . '</b>';
                        } else {
                            echo $answer->text;
                        }
                        echo '<br />';
                    } ?>
                </div>
            </td>
            <td style="width: 90px;">
                <a href="?edit=<?php echo $key; ?>" class="submit">edit</a>
                <a href="?del=<?php echo $key; ?>" class="cancel">delete</a>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>