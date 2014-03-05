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

ini_set('display_errors', '0');

require_once('includes' . DIRECTORY_SEPARATOR . 'config.php');
require_once('includes' . DIRECTORY_SEPARATOR . 'functions.php');

// beginning authentication
if(FRONTEND_USER_RESTRICTION) {

    $authentication = new FrontendAuthentication();

    if(!$authentication->checkIsAuthenticated()) {

        $user = $authentication->getUserData($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);

        if(!$user) {
            $authentication->authenticationForm();
        }

        $authentication->authenticate($user);

    } else if(isset($_GET['logout'])) {

        $authentication->logout();

    }
}
// end authentication

$entityQuestions = DatabaseEntity::getEntity('questions');

$json = array();

foreach($entityQuestions->getAll() as $question) {
    $json[] = json_decode($question['question']);
}

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
<title><?php echo TITLE; ?></title>
<link rel="shortcut icon" href="images/favicon.ico"/>

<link rel="stylesheet" type="text/css" href="css/frontend.css">

</head>
<body>
<div id="content">
    <div id="header">
        <div id="header-container">
            <div id="timer">0:00:00</div>

            <div id="questions"></div>
            <h1><img src="images/header-image.png"><?php echo TITLE; ?></h1>
        </div>
    </div>
    <div id="body">

        <div id="controls">

        </div>
        <p id="question"></p>

        <div id="answers"></div>

        <div id="controls">
            <button type="submit" id="check" onclick="checkAnswers();this.blur();">check</button>
            <button type="button" id="next" onclick="getQuestion(); this.blur();">continue</button>
            <button type="button" id="skip" onclick="skippQuestion();">skip</button>
        </div>

        <div id="results-stats">
            <span id="good-result-no">ok</span>
            <span id="good-result">&nbsp;</span>
            <span id="bad-result">&nbsp;</span>
            <span id="bad-result-no">bad</span>
        </div>
        <div id="final-results">
            <div class="overlay"></div>
            <div id="result-board" class="font-larder">
                <div id="container">
                    <div class="font-larder" id='results'>Results</div>
                    <div>Correct: <span id="good-final-result"></span></div>
                    <div>Wrong: <span id="bad-final-result"></span></div>
                    <div>Skipped: <span id="skipped-final-result"></span></div>
                    <div id="results-timer">Time: <span id="timer-result"></span></div>
                    <div><button onclick="document.location = document.location;" id="restart">Restart</button></div>
                </div>

            </div>
        </div>

    </div>
    <div id="footer">
       A project by <a href="http://claudiupersoiu.ro" target="_blank">Claudiu Persoiu</a>
    </div>
</div>

<script type="text/javascript">

// Array shuffle implementation
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

var json_arr = <?php echo json_encode($json); ?>;

var initial_length = json_arr.length;

json_arr.shuffle();
json_arr.shuffle();
json_arr.shuffle();

var answer_type = 'simple';

var correct_answers = 0;
var incorrect_answers = 0;
var skipped_questions = 0;

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
        document.getElementById('skipped-final-result').innerHTML = skipped_questions;

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

    document.getElementById('check').style.display = '';
    document.getElementById('next').style.display = 'none';

};

var createAnswerObj = function (obj) {
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

var selectItem = function (id) {

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

var checkAnswers = function () {

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
        document.getElementById('check').style.display = 'none';
        document.getElementById('next').style.display = 'block';
    } else {
        correct_answers++;
        getQuestion();
    }

    updatePercent();
}

var skippQuestion = function () {
    skipped_questions++;
    getQuestion();
}

var updatePercent = function () {

    document.getElementById('results-stats').style.display = 'block';

    document.getElementById('good-result-no').innerHTML = correct_answers;
    document.getElementById('bad-result-no').innerHTML = incorrect_answers;

    var proc = Math.round((86 / (correct_answers + incorrect_answers)) * correct_answers);
    document.getElementById('good-result').style.width = proc + '%';
    document.getElementById('bad-result').style.width = (86 - proc) + '%';
}

window.onload = getQuestion;

</script>

</body>
</html>
