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
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
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

        margin:0;
        padding:0;
        font-family: 'Lucida Grande', 'Lucida Sans Unicode', Helvetica, Arial, sans-serif;
    }

    #answers p {

        background-color: #efefef;
        border: 1px solid #cccccc;
        cursor: pointer;
        background-position: left center;
        background-size: 17px 17px;
        -webkit-background-size: 17px 17px;
        -o-background-size: 17px 17px;
        -webkit-border-radius: 5px;
        border-radius: 5px;
        background-clip: padding-box;

    }

    #answers p span {
        margin-left: 20px;
        display: block;
        padding: 10px 0;
    }

    #controls button {
        width: 50%;
        font-size: large;
        text-align: center;
        -webkit-border-radius: 18px;
        border-radius: 18px;
        background-clip: padding-box;
        border: 1px solid #427d93;
        padding: 8px 0;
        color: white;
        float: left;
        margin-right: 1%;

        background-color: #6cbcda;
        background-image: -webkit-gradient(linear, left top, left bottom, from(#6cbcda), to(#519cb8));
        background-image: -webkit-linear-gradient(top, #6cbcda, #519cb8);
        background-image:    -moz-linear-gradient(top, #6cbcda, #519cb8);
        background-image:      -o-linear-gradient(top, #6cbcda, #519cb8);
        background-image:         linear-gradient(to bottom, #6cbcda, #519cb8);
        outline: none;
    }

    #controls button:active, #controls button:active:focus {
        background-color: #519cb8;
        background-image: -webkit-gradient(linear, left top, left bottom, from(#519cb8), to(#6cbcda));
        background-image: -webkit-linear-gradient(top, #519cb8, #6cbcda);
        background-image:    -moz-linear-gradient(top, #519cb8, #6cbcda);
        background-image:      -o-linear-gradient(top, #519cb8, #6cbcda);
        background-image:         linear-gradient(to bottom, #519cb8, #6cbcda);
    }

    #controls button:hover {
        background-color: #519cb8;
        background-image: -webkit-gradient(linear, left top, left bottom, from(#306f86), to(#4d91ab));
        background-image: -webkit-linear-gradient(top, #306f86, #4d91ab);
        background-image:    -moz-linear-gradient(top, #306f86, #4d91ab);
        background-image:      -o-linear-gradient(top, #306f86, #4d91ab);
        background-image:         linear-gradient(to bottom, #306f86, #4d91ab);
    }

    #controls #skip {
        width: 49%;
        margin-right: 0;
    }

    #controls #next {
        display:none;
    }

    #answers p.selected {
        background-color: #666357 !important;
        color: white;
    }

    #answers p.selected_correct {
        background-color: #70BF00 !important;
        background-image: url('images/ok.png');
        background-repeat: no-repeat;
        color: white;

    }

    #answers p.error {
        background-color: #E54B17 !important;
        background-image: url('images/error.png');
        background-repeat: no-repeat;
        color: white;
    }

    #answers p.correct {
        background-color: #70BF00 !important;
        background-image: url('images/correct.png');
        background-repeat: no-repeat;
        color: white;
    }

    #results-stats {
        width: 100%;
        display: none;
        padding-top: 15px;
        border-spacing:0;
        clear: both;
    }

    #results-stats span {
        text-align: center;
        float: left;
        font-size: 12px;
        color: white;
        padding: 1px 0px;
    }

    #results-stats #good-result-no {
        background-color: #70BF00;
        width: 7%;
        -moz-border-radius-bottomleft:5px;
        -webkit-border-bottom-left-radius:5px;
        border-bottom-left-radius:5px;
        -moz-border-radius-topleft:5px;
        -webkit-border-top-left-radius:5px;
        border-top-left-radius:5px;
    }

    #results-stats #good-result {
        background-color: #70BF00;
        display: inline-block;
    }

    #results-stats #bad-result {
        background-color: #E54B17;
        display: inline-block;
    }

    #results-stats #bad-result-no {
        background-color: #E54B17;
        width: 7%;
        -moz-border-radius-bottomright:5px;
        -webkit-border-bottom-right-radius:5px;
        border-bottom-right-radius:5px;
        -moz-border-radius-topright:5px;
        -webkit-border-top-right-radius:5px;
        border-top-right-radius:5px;
    }

    #timer, #questions {
        background: repeat-x scroll 0 0 #5a5a5a;
        border-radius: 5px 5px 5px 5px;
        color: white;
        float: left;
        letter-spacing: 1px;
        padding: 10px;

    }

    #question {
        font-size: larger;
        word-wrap:break-word;
    }

    #questions {
        float: right;
    }

    h1 {
        text-align: center; margin: 0 auto; font-size: 26px;
    }

    @media all and (max-width: 500px) {
        h1 {
            font-size: 18px;
        }
    }

    h1 img {
        display: inline; position: relative; vertical-align: middle; right: 5px;
    }

    #content {
        min-height: 100%; position: relative; margin: 0px;
    }

    #header {
        padding: 33px 5px 10px;
        background: url('images/top.gif') repeat-x top left;
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

    #final-results #results {
        padding-bottom: 10px;
    }

    #final-results .overlay {
        opacity: 0.6;width: 100%; height: 100%; background-color: #fff; position: absolute; top: 0; left:0; margin-left: 0px;
    }

    #final-results #result-board {
        position: absolute; top: 20%; width: 80%; left: 10%; text-align: center;
    }

    #result-board #container {
        background: none repeat scroll 0px 0px rgb(241, 241, 241);
        width: 100%; max-width: 500px;
        margin: 0px auto; height: 100%;
        border: 1px solid rgb(204, 204, 204);
        padding: 1.5%;

        -webkit-border-radius: 8px;
        border-radius: 8px;
        background-clip: padding-box;
    }

    #final-results #restart {
        width: 100%; font-size: large;
        background-color: #6cbcda;
        background-image: -webkit-gradient(linear, left top, left bottom, from(#6cbcda), to(#519cb8));
        background-image: -webkit-linear-gradient(top, #6cbcda, #519cb8);
        background-image:    -moz-linear-gradient(top, #6cbcda, #519cb8);
        background-image:      -o-linear-gradient(top, #6cbcda, #519cb8);
        background-image:         linear-gradient(to bottom, #6cbcda, #519cb8);
        -webkit-border-radius: 18px;
        border-radius: 18px;
        background-clip: padding-box;
        max-width: 300px;
        border: 1px solid #427d93;
        padding: 8px 0;
        color: white;
    }

    .font-larder {
        font-size: larger;
    }

    #content #body {
        padding: 5px 5px 110px 5px;
        opacity: 0.9;
        max-width: 900px;
        margin: 0 auto;
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

    function skippQuestion() {
        skipped_questions++;
        getQuestion();
    }

    var percent = function () {

        document.getElementById('results-stats').style.display = 'block';

        document.getElementById('good-result-no').innerHTML = correct_answers; // + ' ok';
        document.getElementById('bad-result-no').innerHTML = incorrect_answers; // + ' bad';

        var proc = Math.round((86 / (correct_answers + incorrect_answers)) * correct_answers);
        document.getElementById('good-result').style.width = proc + '%';
        document.getElementById('bad-result').style.width = (86 - proc) + '%';
    }

    window.onload = getQuestion;

</script>
</head>
<body>
<div id="content">
    <div id="header">
        <div style="max-width: 900px; margin: 0 auto;">
            <div id="timer">0:00:00</div>

            <div id="questions"></div>
            <h1><img src="images/header-image.png"><?php echo TITLE; ?></h1>
        </div>
    </div>
    <div id="body">

        <div id="controls">

        </div>
        <p id="question"></p>

        <div id="answers" style="width: 100%;"></div>

        <div id="controls">
            <button type="submit" id="verifica" onclick="checkAnswers();this.blur();">check</button>
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
                    <div style="margin-bottom: 20px;">Time: <span id="timer-result"></span></div>
                    <div><button onclick="document.location = document.location;" id="restart">Restart</button></div>
                </div>

            </div>
        </div>

    </div>
    <div id="footer">
        Copyright 2013 <a href="http://claudiupersoiu.ro" target="_blank">ClaudiuPersoiu.ro</a>
    </div>
</div>


</body>
</html>
