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
// cache headers
$expires = 60 * 10;
header("Pragma: public");
header("Cache-Control: maxage=".$expires);
header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');

?>
<!DOCTYPE html>
<html manifest="cache.appcache">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
    <meta name="HandheldFriendly" content="true"/>
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
            <button type="submit" id="check" onclick="checkAnswers();this.blur();" onmouseup="this.blur();">check</button>
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
                    <div><button onclick="startQuiz();" id="restart">Restart</button></div>
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

/**
 * Clone JS objects or array
 */
function clone(obj) {
    // Handle the 3 simple types, and null or undefined
    if (null == obj || "object" != typeof obj) return obj;

    // Handle Array
    if (obj instanceof Array) {
        var copy = [];
        for (var i = 0, len = obj.length; i < len; i++) {
            copy[i] = clone(obj[i]);
        }
        return copy;
    }

    // Handle Object
    if (obj instanceof Object) {
        var copy = {};
        for (var attr in obj) {
            if (obj.hasOwnProperty(attr)) copy[attr] = clone(obj[attr]);
        }
        return copy;
    }

    throw new Error("Unable to copy obj! Its type isn't supported.");
}

var json_base_arr = <?php echo json_encode($json); ?>;

// number of correct answers
var correct_answers,
    // number of incorrect answers
    incorrect_answers,
    // questions that were skipped
    skipped_questions,
    // number of seconds since the begging
    time,
    // type of current question
    answer_type,
    // initial length of the questions json object
    initial_length,
    // timer html container
    timer_container = document.getElementById('timer'),
    // results overlay container
    results_container = document.getElementById('final-results'),
    // questions container
    questions_container = document.getElementById('questions'),
    // question container
    question_container = document.getElementById('question'),
    // answers container
    answers_container = document.getElementById('answers'),
    // check button container
    check_container = document.getElementById('check'),
    // next button container
    next_container = document.getElementById('next'),
    // result stats container
    stats_container = document.getElementById('results-stats'),
    // stats with correct results
    stats_correct_container = document.getElementById('good-result-no'),
    // stats with wrong results
    stats_wrong_container = document.getElementById('bad-result-no'),
    // stats with correct results bar
    stats_correct_bar_container = document.getElementById('good-result'),
    // stats with wrong results bar
    stats_wrong_bar_container = document.getElementById('bad-result'),
    // interval timer
    timer,
    // questions json object
    json_arr;

/**
 * Start quiz
 *
 * @returns {boolean}
 */
var startQuiz = function () {
    // clone the original json object
    json_arr = clone(json_base_arr);

    // get the length of the json object
    initial_length = json_arr.length;

    // give it a good shuffle
    json_arr.shuffle();
    json_arr.shuffle();
    json_arr.shuffle();

    // reset the answers counter
    correct_answers = 0;
    incorrect_answers = 0;
    skipped_questions = 0;

    // reset the time
    time = 0;

    // set the interval to update the time
    timer = setInterval(function () {
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

        timer_container.innerHTML = hours + ':' + minutes + ':' + seconds;
    }, 1000);

    // in case of a restart hide the result stats
    results_container.style.display = 'none';

    // hide results stats because there isn't any answer at this point
    stats_container.style.display = 'none';

    // get the first question
    getQuestion();

    return true;

}

/**
 * Get a new question
 *
 * @returns {boolean}
 */
var getQuestion = function () {

    current = json_arr.pop();

    questions_container.innerHTML = (initial_length - json_arr.length) + '/' + initial_length;

    if(!current) {
        return stopGame();
    }

    var img = '';

    if (current.img) {
        img = '<br /><img src="data/<?php echo QUESTION_IMAGE; ?>/' + current.img + '" width=100% />';
    }

    question_container.innerHTML = current.question + img;

    var answers = current.ans.shuffle();

    answers_container.innerHTML = '';

    var correct = 0;
    answer_type = 'simple';
    for (var i = 0; i < answers.length; i++) {

        if (answers[i].corect === 'true') {
            correct++;
        }

        if(correct > 1) {
            answer_type = 'multiple';
        }

        var p = createAnswerObj({'id': i, 'txt': answers[i]['text']});
        answers_container.appendChild(p);
    }

    check_container.style.display = '';
    next_container.style.display = 'none';
};

/**
 * Stop game if there aren't any more questions, see getQuestion
 *
 * @returns {boolean}
 */
var stopGame = function () {

    results_container.style.display = 'block';
    document.getElementById('good-final-result').innerHTML = correct_answers;
    document.getElementById('bad-final-result').innerHTML = incorrect_answers;
    document.getElementById('skipped-final-result').innerHTML = skipped_questions;

    document.getElementById('timer-result').innerHTML = timer_container.innerHTML;
    clearInterval(timer);

    return false;
}

/**
 * Create answer HTML object to be inseted in the page
 *
 * @param obj Current question object
 * @returns {HTMLElement}
 */
var createAnswerObj = function (obj) {
    var p = document.createElement('p');
    // assign select answer functionality to the new element
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

/**
 * Select answer
 *
 * @param id Id of the selected answer
 */
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

/**
 * Check if the answers selected are correct
 *
 * @returns {boolean}
 */
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

        p.onclick = function () { return false; };
    }

    // if the answer is correct go to the next question, otherwise display the correct result
    if (correct == false) {
        incorrect_answers++;
        check_container.style.display = 'none';
        next_container.style.display = 'block';
    } else {
        correct_answers++;
        getQuestion();
    }

    updatePercent();
}

/**
 * Skip current question
 */
var skippQuestion = function () {
    skipped_questions++;
    getQuestion();
}

/**
 * Update results stats from the bottom of the screen
 */
var updatePercent = function () {

    // display results stats container if it's not already displayed
    stats_container.style.display = 'block';

    // set number of correct/wrong answers
    stats_correct_container.innerHTML = correct_answers;
    stats_wrong_container.innerHTML = incorrect_answers;

    // display the graph with results percent
    var proc = Math.round((86 / (correct_answers + incorrect_answers)) * correct_answers);
    stats_correct_bar_container.style.width = proc + '%';
    stats_wrong_bar_container.style.width = (86 - proc) + '%';
}

// on window load start the quiz
window.onload = startQuiz;

</script>

</body>
</html>
