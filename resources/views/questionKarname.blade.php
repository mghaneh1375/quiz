@extends('layouts.menuBar')

@section('title')
     مشاهده ی کارنامه سوال به سوال
@stop

<style>
    td > center{
        padding: 5px;
    }
</style>

@section('extraLibraries')

    <script>

        var qIdx = 0;
        var answer = {{json_encode($roqs)}};
        var questionArr = {{json_encode($questions)}};

        $(document).ready(function () {
            SUQ();
        });

        function JMPTOQUIZ(idx) {
            qIdx = idx;
            SUQ();
        }

        function SUQ() {

            $("#returnToQuiz").hide();

            for(i = 0; i < answer.length; i++) {
                if(answer[i].result == 0)
                    document.getElementById("td_" + i).style.backgroundColor = "";
                else if(answer[i].ans == answer[i].result)
                    document.getElementById("td_" + i).style.backgroundColor = "green";
                else
                    document.getElementById("td_" + i).style.backgroundColor = "red";
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
                newNode = "<center style='margin-top: 20px;'><span style='font-size: 20px; color: #ff0000'>پاسخ صحیح : </span><span>گزینه ی " + answer[qIdx].ans + "</span></center>";
                newNode += "<img src='upload/" + questionArr[qIdx][4] + ".jpg'>";
                result = (answer[qIdx].result == "0") ? "سفید" : "گزینه ی " + answer[qIdx].result;
                newNode += "<center style='margin-top: 20px;'><span style='font-size: 20px; color: #ff0000'>پاسخ شما : </span><span>" + result + "</span></center>";
                $("#BQ").append(newNode);
            }
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
    </script>
@stop

@section('reminder')
    <center style="margin-top: 130px">
        <h3>کارنامه ی سوال به سوال</h3>
        <a href="{{route('seeResult', ['quizId' => $quizId])}}"><button>بازگشت به مرحله ی قبل</button></a>
        <div class="line"></div>
        @for($i = 0; $i < count($roqs) / 20; $i++)
            <table style="margin-top: 10px">
                <tr>
                    <td><center>شماره ی سوال</center></td>
                    <td><center>پاسخ صحیح</center></td>
                    <td><center>پاسخ شما</center></td>
                    <td><center>سطح سختی</center></td>
                </tr>
                <?php $limit = (count($roqs) - $i * 20 > 20) ? 20 : count($roqs) - $i * 20; ?>
                @for($j = 0; $j < $limit; $j++)
                    <tr>
                        <td style="cursor: pointer" onclick="window.scrollBy(0, document.body.scrollHeight); JMPTOQUIZ({{$i * 20 + $j}})"><center>{{$i * 20 + $j + 1}}</center></td>
                        <td><center>{{$roqs[$i * 20 + $j]->ans}}</center></td>

                        @if($roqs[$i * 20 + $j]->ans == $roqs[$i * 20 + $j]->result)
                            <td><center style="background: green">{{$roqs[$i * 20 + $j]->result}}</center></td>
                        @elseif(0 == $roqs[$i * 20 + $j]->result)
                            <td><center>سفید</center></td>
                        @else
                            <td><center style="background: red">{{$roqs[$i * 20 + $j]->result}}</center></td>
                        @endif

                        <td>
                            <center>
                                @if($roqs[$i * 20 + $j]->attempt != 0 && $roqs[$i * 20 + $j]->solved / $roqs[$i * 20 + $j]->attempt < 0.3)
                                    <img src='{{URL::asset('images/sad.png')}}' data-toggle='tooltip' title='دشوار' style='width: 20px; height: 20px; margin-top: -5px'>
                                @elseif($roqs[$i * 20 + $j]->attempt != 0 && $roqs[$i * 20 + $j]->solved / $roqs[$i * 20 + $j]->attempt < 0.5)
                                    <img src='{{URL::asset('images/confused.png')}}' data-toggle='tooltip' title='متوسط' style='width: 20px; height: 20px; margin-top: -5px'>
                                @else
                                    <img src='{{URL::asset('images/smile.png')}}' data-toggle='tooltip' title='آسان' style='width: 20px;; height: 20px; margin-top: -5px'>
                                @endif
                            </center>
                        </td>
                    </tr>
                @endfor
            </table>
        @endfor

        <div class="col-xs-12">
            <center style="margin-top: 20px;">
                <table style="min-width: 100px;">
                    <?php
                    $counter = 0;
                    for($i = 0; $i < count($roqs); $i++) {
                        if($counter == 0)
                            echo "<tr>";
                        $counter++;
                        echo "<td id='td_$i' onclick='JMPTOQUIZ($i)' style='cursor: pointer; background-color: white; width: 30px; border: 2px solid black;'><center>".($i + 1)."</center></td>";
                        if($counter == 15 || $i == count($roqs) - 1) {
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
                <span style='margin-right: 5px;'>سوال فعلی</span>
            </div>
            <div class="col-xs-12 col-md-4" style='float: right; margin-top: 5px;'>
                <div style="width: 30px; height: 30px; margin-right: 5px; background-color:green; border: 1px solid black; float: right;"></div>
                <span style='margin-right: 5px;'>درست پاسخ داده شده</span>
            </div>
            <div class="col-xs-12 col-md-4" style='float: right; margin-top: 5px;'>
                <div style="width: 30px; height: 30px; margin-right: 5px; background-color: red; border: 1px solid black; float: right"></div>
                <span style='margin-right: 5px;'>نادرست پاسخ داده شده</span>
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
    </center>
@stop