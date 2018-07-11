@extends('layouts.menuBar')

@section('title')
    آزمون
@stop

<?php
    $numQ = count($questions);
    if ($questions == null || $numQ == 0) {
        echo "<div style='margin-top: 140px;'><center>سوالی در این آزمون وجود ندارد</center></div>";
        return;
    }
?>

@section('extraLibraries')
    <script>

        var mode = "{{$mode}}";

        if(mode == "normal") {
            var total_time = "{{$tL - time() + $startTime}}";
            var c_minutes = parseInt(total_time / 60);
            var c_seconds = parseInt(total_time % 60);
        }

        var answer = {{json_encode($roqs)}};
        var qIdx = 0;
        var questionArr = {{json_encode($questions)}};
        var quiz_id = "{{$quizId}}";

        $(document).ready(function () {
            SUQ();
            if(mode == "normal") {
                if (total_time > 0)
                    setTimeout("checkTime()", 1);
                else
                    endQ();
            }
        });

        function checkTime() {
            document.getElementById("quiz_time").innerHTML = "زمان باقی مانده : " + c_seconds + " : " + c_minutes;
            if (total_time <= 0)
                setTimeout("endQ()", 1);
            else {
                total_time--;
                c_minutes = parseInt(total_time / 60);
                c_seconds = parseInt(total_time % 60);
                setTimeout("checkTime()", 1000);
            }
        }

        function endQuiz() {
            whiteList = [];
            counter = 0;
            for(i = 0; i < questionArr.length; i++) {
                if(answer[i] == 0)
                    whiteList[counter++] = i + 1;
            }
            if(counter != 0) {
                msg = "سوالات زیر را هنوز جواب نداده اید آیا مطمئن هستید که می خواهید از ارزیابی خارج شوید" + "\n";
                for(i = 0; i < counter - 1; i++)
                    msg = msg + whiteList[i] + " - ";
                msg = msg + whiteList[counter - 1];
            }
            else
                msg = "آیا مطمئن هستید می خواهید از ارزیابی خارج شوید؟";
            msg = msg + "\n" + "اگر می خواهید به سوالات برگردید دکمه ی cancel را بزنید" + "\n";
            response = confirm(msg);
            if(response == true)
                endQ();
        }

        function endQ() {
            if(mode == "normal") {
                $.ajax({
                    type: 'post',
                    url: 'endQuiz',
                    data: {
                        quiz_id: quiz_id
                    },
                    success: function (response) {
                        document.getElementById("percent").innerHTML = "برای نمایش کارنامه می توانید بعد از اتمام زمان آزمون به قسمت مربوطه مراجعه فرمایید";
                        $("#popUpMenu2").show();
                        $("#reminder").hide();
                    }
                });
            }

            else {
                document.location.href = "{{URL('home')}}";
            }
        }

        function submitC(val) {

            if(questionArr[qIdx][2] == 1 && val == answer[qIdx]) {
                $(":radio").attr("checked", false);
                answer[qIdx] = 0;
            }
            else
                answer[qIdx] = val;

            if(questionArr[qIdx].length == 9)
                qoqId = questionArr[qIdx][8];
            else if(questionArr[qIdx].length == 5)
                qoqId = questionArr[qIdx][4];

            if(mode == "special")
                return;

            $.ajax({
                type: 'post',
                url: 'submitAns',
                data: {
                    qoqId: qoqId,
                    newVal: answer[qIdx]
                },
                error: function (response) {
                    alert('Something went wrong' + response.responseText);
                }
            });
        }

        function incQ() {
            if(qIdx + 1 < questionArr.length) {
                qIdx++;
                SUQ();
            }
        }

        function decQ() {
            if(qIdx - 1 >= 0) {
                qIdx--;
                SUQ();
            }
        }

        function JMPTOQUIZ(idx) {
            qIdx = idx;
            SUQ();
        }

        function SUQ() {

            $("#returnToQuiz").hide();

            for(i = 0; i < answer.length; i++) {
                if(answer[i] == 0)
                    document.getElementById("td_" + i).style.backgroundColor = "white";
                else
                    document.getElementById("td_" + i).style.backgroundColor = "gray";
            }

            document.getElementById("td_" + qIdx).style.backgroundColor = "yellow";

            if(qIdx == 0)
                $("#backQ").hide();
            else
                $("#backQ").show();
            if(qIdx == questionArr.length - 1)
                $("#nxtQ").hide();
            else
                $("#nxtQ").show();

            var newNode;
            if(questionArr[qIdx][2] == 0)
                newNode = "<span><img alt='در حال بارگذاری تصویر' style='max-width: 100%' src='upload/" + questionArr[qIdx][3] + ".jpg'></span><br/>";
            else
                newNode = (qIdx + 1) +  " - <span style='background-color: transparent;'>" + questionArr[qIdx][3] +  "</span><br/>";
            $("#BQ").empty();
            $("#BQ").append(newNode);
            if(questionArr[qIdx][1] != 0 && questionArr[qIdx][2] == 0) {
                for(i = 0; i < 4; i++) {
                    newNode = "<input type='radio' name='correctChoice' value='" + (i + 1) +  "' onclick='submitC(this.value)' style='margin-top: 10px;'"; if(answer[qIdx] == (i + 1)) newNode += "checked >  "; else newNode += ">  "; newNode += (i + 1) + ") " + questionArr[qIdx][4 + i] + "<br/>";
                    $("#BQ").append(newNode);
                }
            }
            else if(questionArr[qIdx][1] == 0 && questionArr[qIdx][2] == 0) {
                newNode = "<center style='margin-top: 20px;'><span style='font-size: 20px; color: #ff0000'>پاسخ : </span><select style='width: 60px; font-size: 14px' id='choices' onchange='submitC(this.value)'>";
                if(answer[qIdx] == 0)
                    newNode = newNode + "<option value='0' selected>سفید</option>";
                else
                    newNode = newNode + "<option value='0'>سفید</option>";
                if(answer[qIdx] == 1)
                    newNode = newNode + "<option value='1' selected>1</option>";
                else
                    newNode = newNode + "<option value='1'>1</option>";
                if(answer[qIdx] == 2)
                    newNode = newNode + "<option value='2' selected>2</option>";
                else
                    newNode = newNode + "<option value='2'>2</option>";
                if(answer[qIdx] == 3)
                    newNode = newNode + "<option value='3' selected>3</option>";
                else
                    newNode = newNode + "<option value='3'>3</option>";
                if(answer[qIdx] == 4)
                    newNode = newNode + "<option value='4' selected>4</option>";
                else
                    newNode = newNode + "<option value='4'>4</option>";
                newNode = newNode + "</select></center>";
                $("#BQ").append(newNode);
            }
        }
    </script>
@stop

@section('reminder')

    <div id="popUpMenu2" style="margin-top: 300px;" hidden>
        <center id="percent"></center>
        <center style='margin-top: 20px;'><a href="{{URL('home')}}"><input type='submit' value='تایید'></a></center>
    </div>

    <div class="row" id="reminder" style="margin-top: 130px">

        <div class="row" id="reminder">
            <div class="col-xs-12">
                <center style="margin-top: 20px">
                    <div id="quiz_time" style='font-size: 14px;'></div>
                </center>
            </div>
        </div>
        <div class="col-xs-12">
            <center style="margin-top: 20px;">
                <table style="min-width: 100px;">
                    <?php
                    $counter = 0;
                    for($i = 0; $i < $numQ; $i++) {
                        if($counter == 0)
                            echo "<tr>";
                        $counter++;
                        echo "<td id='td_$i' onclick='JMPTOQUIZ($i)' style='cursor: pointer; background-color: white; width: 30px; border: 2px solid black;'><center>".($i + 1)."</center></td>";
                        if($counter == 15 || $i == $numQ - 1) {
                            echo "</tr>";
                            $counter = 0;
                        }
                    }
                    ?>
                </table>
            </center>
        </div>

        <div class="col-xs-12" style="float: right; margin-top: 10px;">
            <div class="col-xs-12 col-md-4" style='float: right; margin-top: 5px;'>
                <div style="width: 30px; height: 30px; margin-right: 5px; background-color: yellow; border: 1px solid black; float: right;"></div>
                <span style='margin-right: 5px;'>در حال پاسخ گویی</span>
            </div>
            <div class="col-xs-12 col-md-4" style='float: right; margin-top: 5px;'>
                <div style="width: 30px; height: 30px; margin-right: 5px; background-color:gray; border: 1px solid black; float: right;"></div>
                <span style='margin-right: 5px;'>پاسخ داده شده</span>
            </div>
            <div class="col-xs-12 col-md-4" style='float: right; margin-top: 5px;'>
                <div style="width: 30px; height: 30px; margin-right: 5px; background-color: white; border: 1px solid black; float: right"></div>
                <span style='margin-right: 5px;'>هنوز پاسخ داده نشده</span>
            </div>
        </div>

        <div class='col-xs-12 well well-sm' style="margin-top: 10px; border: 3px solid black; background-color: #ffffff">
            <div id="BQ" style='height: auto; width: auto; max-width: 100%'></div>
        </div>
        <div class="col-xs-12">
            <div style='margin-top: 5px;'>
                <center>
                    <button id="nxtQ" class="MyBtn" style="width: auto; margin-right: 5px; border: solid 2px #a4712b;" onclick="incQ()">سوال بعدی</button>
                    <button id="backQ" class="MyBtn" style="width: auto; margin-right: 5px; border: solid 2px #a4712b;" onclick="decQ()">سوال قبلی</button>
                    <button id="returnToQuiz" class="MyBtn" style="width: auto; margin-right: 5px; border: solid 2px #a4712b;" onclick="SUQ()" hidden>بازگشت به سوالات</button>
                </center>
            </div>
        </div>
        <div class="col-xs-12">
            <center>
                @if($mode == "special")
                    <button class="MyBtn" style="width: auto; border: solid 2px #a4712b;" onclick="document.location.href = '{{route('addQuestionToQuiz', ['quiz_id' => $quizId])}}'">بازگشت به مرحله قبل</button>
                @else
                    <button class="MyBtn" style="width: auto; border: solid 2px #a4712b;" onclick="endQuiz()">اتمام ارزیابی</button>
                @endif
            </center>
        </div>
    </div>
@stop