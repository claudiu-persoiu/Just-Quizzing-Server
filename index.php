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

require_once('includes' . DIRECTORY_SEPARATOR . 'config.php');

require_once('includes' . DIRECTORY_SEPARATOR . 'authentication_frontend.php');

$entityQuestions = DatabaseEntity::getEntity('questions');

$json = array();

foreach($entityQuestions->getAll() as $question) {
    $json['q' . $question['id']] = json_decode($question['question']);
}

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php echo TITLE; ?></title>
<link rel="shortcut icon" href="images/favicon.ico"/>

<style type="text/css">
    html {
        min-height: 100%;
        height: 100%;
    }

    body {
        min-height: 100%;
        height: 100%;
        font: bold Helvetica;
        background-image: url('images/main.png');
        background-position: right bottom;
        background-repeat: no-repeat;
        margin:0;
        padding:0;
        font-family: 'Lucida Grande', 'Lucida Sans Unicode', Helvetica, Arial, sans-serif;
    }

    #answers p {

        background-color: #fefba0;
        border: 1px solid #cccccc;
        cursor: pointer;
        background-position: left center;
        background-size: 17px 17px;
        -webkit-background-size: 17px 17px;
        -o-background-size: 17px 17px;
    }

    #answers p span {
        margin-left: 20px;
        display: block;
        word-wrap:break-word;
    }

    #controls button {
        width: 100%;
        font-size: large;
        text-align: center;
    }

    #controls #next {
        display:none;
    }

    #answers p.selected {
        background-color: #a4f7ff !important;
    }

    #answers p.selected_correct {
        background-color: #7ef3ff !important;
        background-image: url('images/ok.png');
        background-repeat: no-repeat;

    }

    #answers p.error {
        background-color: #fe909c !important;
        background-image: url('images/error.png');
        background-repeat: no-repeat;
    }

    #answers p.correct {
        background-color: #a0fea6 !important;
        background-image: url('images/correct.png');
        background-repeat: no-repeat;
    }

    #results-stats {
        width: 100%;
    }

    #results-stats #good-result-no {
        background-color: #99fea4; width: 10%;
    }

    #results-stats #good-result {
        background-color: #99fea4;
    }

    #results-stats #bad-result {
        background-color: #ff9396
    }

    #results-stats #bad-result-no {
        background-color: #ff9396;
        text-align: right;
        width: 10%;
    }

    #timer {
        float: left;
    }

    #questions {
        float: right;
    }

    h1 {
        text-align: center; margin: 0 auto; width: 300px; font-size: large;
    }

    #content {
        min-height: 100%; position: relative; margin: 0px;
    }

    #header {
        padding: 33px 5px 10px; background-repeat: repeat-x; background: url('images/top.gif');
    }

    #footer {
        position: absolute; bottom: 0px; height: 45px; vertical-align: bottom; line-height: 45px; text-align: center;
        background: #eee;
        border-top: #bbb 1px solid;
        border-bottom: #222 6px solid;
        width: 100%;
        font-size: smaller;
        opacity: 0.8;
    }

    #footer a {
        color: #000000; text-decoration: none; font-weight: bold;
    }

    #final-results {
        display: none;
    }

    #final-results .overlay {
        opacity: 0.6;width: 100%; height: 100%; background-color: #fff; position: absolute; top: 0; left:0; margin-left: 0px;
    }

    #final-results #result-board {
        padding: 1%; position: absolute; top: 20%; width: 70%; left: 15%; text-align: center; background: #fefba0;border: 1px solid #cccccc;
    }

    #final-results #restart {
        width: 100%; font-size: large;
    }

    .font-larder {
        font-size: larger;
    }


</style>

<script type="text/javascript">

    Array.prototype.shuffle = function () {
        var i = this.length;
        if (i == 0) return false;
        while (--i) {
            var j = Math.floor(Math.random() * ( i + 1 ));
            var tempi = this[i];
            var tempj = this[j];
            this[i] = tempj;
            this[j] = tempi;
        }
        return this;
    };

    var json = <?php echo json_encode($json); ?>;

    var json_arr = [];

    for (var val in json) {
        json_arr.push(json[val]);
    }

    var initial_length = json_arr.length;

    json_arr.shuffle();
    json_arr.shuffle();
    json_arr.shuffle();

    var answer_type = 'simple';

    var correct_answers = 0;
    var incorrect_answers = 0;

    var time = 0;
    var timer_container = null;
    var timer = setInterval(function () {
        time++;

        var hours = Math.floor(time / 3600);
        var minutes = Math.floor(time / 60) - hours * 60;
        var seconds = time - minutes * 60 - hours * 3600;

        if(minutes < 10) {
            minutes = '0' + minutes;
        }

        if(seconds < 10) {
            seconds = '0' + seconds;
        }

        if(!timer_container) {
            timer_container = document.getElementById('timer');
        }

        timer_container.innerHTML = hours + ':' + minutes + ':' + seconds;
    }, 1000);

    var getQuestion = function () {

        current = json_arr.pop();

        document.getElementById('questions').innerHTML = (initial_length - json_arr.length) + '/' + initial_length;

        if(!current) {

            document.getElementById('final-results').style.display = 'block';
            document.getElementById('good-final-result').innerHTML = correct_answers;
            document.getElementById('bad-final-result').innerHTML = incorrect_answers;

            document.getElementById('timer-result').innerHTML = timer_container.innerHTML;
            clearInterval(timer);
            return false;
        }

        var img = '';

        if (current.img) {
            img = '<br /><img src="data/<?php echo QUESTION_IMAGE; ?>/' + current.img + '" width=100% />';
        }

        document.getElementById('question').innerHTML = current.question + img;

        var answers = current.ans.shuffle();

        var i = 0;
        answer_type = 'simple';
        for (var j = 0; j < answers.length; j++) {

            if (answers[j].corect === 'true') {
                i++;
            }

            if (i > 1) {
                answer_type = 'multiple';
                break;
            }
        }

        var answers_html = document.getElementById('answers');

        answers_html.innerHTML = '';

        for (var i = 0; i < answers.length; i++) {
            var p = createAnswerObj({'id': i, 'txt': answers[i]['text']});
            answers_html.appendChild(p);
        }

        document.getElementById('verifica').style.display = '';
        document.getElementById('next').style.display = 'none';

    };

    function createAnswerObj(obj) {
        var p = document.createElement('p');
        p.onclick = function () {
            selectItem(this.id);
        };
        p.id = 'ap' + obj.id;

        var span = document.createElement('span');
        span.innerHTML = obj.txt;
        p.appendChild(span);

        var input = document.createElement('input');
        input.type = 'hidden';
        input.id = 'a' + obj.id;
        p.appendChild(input);

        return p;
    }

    function selectItem(id) {

        var no = id.replace('ap', '');

        if (answer_type == 'simple') {

            for (var i = 0; i < current.ans.length; i++) {
                document.getElementById('ap' + i).className = '';
                document.getElementById('a' + i).value = '';
            }

            document.getElementById('ap' + no).className = 'selected';
            document.getElementById('a' + no).value = 'true';
        } else {
            if (document.getElementById('ap' + no).className == '') {
                document.getElementById('ap' + no).className = 'selected';
                document.getElementById('a' + no).value = 'true';
            } else {
                document.getElementById('ap' + no).className = '';
                document.getElementById('a' + no).value = '';
            }
        }

    }

    function checkAnswers() {

        if(!current) {
            return false;
        }

        var correct = true;
        var answers = current.ans;

        for (var i = 0; i < answers.length; i++) {

            var p = document.getElementById('ap' + i);
            var input = document.getElementById('a' + i);

            if (input.value == 'true' && answers[i]['corect'] == 'true') {
                p.className = 'selected_correct';
            } else if (input.value == 'true' && answers[i]['corect'] !== 'true') {
                corent = false;
                p.className = 'error';
            } else if (input.value == '' && answers[i]['corect'] == 'true') {
                correct = false;
                p.className = 'correct';
            }

            p.onclick = '';
        }


        if (correct == false) {
            incorrect_answers++;
            document.getElementById('verifica').style.display = 'none';
            document.getElementById('next').style.display = 'block';
        } else {
            correct_answers++;
            getQuestion();
        }

        percent();
    }

    var percent = function () {

        document.getElementById('good-result-no').innerHTML = correct_answers + ' ok';
        document.getElementById('bad-result-no').innerHTML = incorrect_answers + ' bad';

        var proc = Math.round((80 / (correct_answers + incorrect_answers)) * correct_answers);
        document.getElementById('good-result').width = proc + '%';
        document.getElementById('bad-result').width = (80 - proc) + '%';
    }

    window.onload = getQuestion;

</script>
</head>
<body>
<div id="content">
    <div id="header">
        <div id="timer">0:00:00</div>

        <div id="questions"></div>
        <h1><?php echo TITLE; ?></h1>
    </div>
    <div id="body" style="padding: 5px 5px 50px 5px; opacity: 0.9;">



        <div id="controls">
            <button type="button" onclick="getQuestion();">next >>></button>
        </div>
        <p id="question"></p>

        <div id="answers" style="width: 100%;"></div>

        <div id="controls">
            <button type="submit" id="verifica" onclick="checkAnswers();this.blur();">check answer</button>
            <button type="button" id="next" onclick="getQuestion(); this.blur();">next >>></button>

        </div>
        <table id="results-stats">
            <tr>
                <td id="good-result-no"></td>
                <td id="good-result"></td>
                <td id="bad-result"></td>
                <td id="bad-result-no"></td>
            </tr>
        </table>
        <div id="final-results">
            <div class="overlay"></div>
            <div id="result-board" class="font-larder">
                <div class="font-larder">Results</div>
                <div>Correct: <span id="good-final-result">x</span></div>
                <div>Wrong: <span id="bad-final-result">x</span></div>
                <div style="margin-bottom: 20px;">Time: <span id="timer-result"></span></div>
                <div><button onclick="document.location = document.location;" id="restart">Restart</button></div>
            </div>
        </div>

    </div>
    <div id="footer">
        Copyright 2013 <a href="http://claudiupersoiu.ro" target="_blank">ClaudiuPersoiu.ro</a>
    </div>
</div>


</body>
</html>
