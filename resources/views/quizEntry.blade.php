@extends('layouts.menuBar')

@section('title')
    شرکت در آزمون
@stop

@section('extraLibraries')

    <script src="{{URL::asset('js/ajaxHandler.js')}}"></script>

    <script>
        $(document).ready(function () {
            selected = $("#quiz").find(":selected").val();
            if(selected != null)
                changeQuizLocal(selected);
        });

        function changeQuizLocal(qId) {
            changeQuiz(qId, 'quizName', 'quizAuthor', 'quizNo', 'quizStartTime', 'quizStartDate', 'quizEndTime', 'quizEndDate', 'timeLen');
        }

        $(function() {
            $("#submit_btn").click(function(){
                if (confirm('آیا مطمئن هستید می خواهید آزمون را شروع کنید؟')){
                    $('form#submit').submit();
                }
            });
        });

    </script>
@stop

@section('reminder')

    <center style="margin-top: 130px">
        <h3>آزمون مورد نظر خود را انتخاب نمایید</h3>
        <div class="line"></div>

        <form method="post" action="{{URL('doQuiz')}}" id="submit">
            <div class="row" style="margin-top: 10px">
                <div class="col-xs-12">
                    <label>
                        <span style="margin-left: 5px">آزمون مورد نظر</span>
                        <select id="quiz" name="quizId" onchange="changeQuizLocal(this.value)">
                            @foreach($validQuizes as $validQuiz)
                                <option value="{{$validQuiz[0]}}">{{$validQuiz[1]}}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <div>
                    <center>
                        <div class='row cycling' style='margin-top: 50px;'>
                            <div class='col-xs-12'>
                                <div style='margin-top: 30px;'>
                                    <p>مشخصات ارزیابی</p>
                                    <p id="quizName"></p>
                                    <p id="quizAuthor"></p>
                                    <p id="quizNo"></p>
                                    <p id="quizStartTime"></p>
                                    <p id="quizStartDate"></p>
                                    <p id="quizEndTime"></p>
                                    <p id="quizEndDate"></p>
                                    <p id="timeLen"></p>
                                </div>
                            </div>
                        </div>
                    </center>
                    <center>
                        <p class="warning_color" style="margin-top: 10px">{{$msg}}</p>
                        <input type='button' class='MyBtn' id="submit_btn" style='margin-top: 30px; margin-bottom: 30px; width: auto; border: solid 2px #a4712b;' value='ورود به ارزیابی'>

                        <p style='font-weight: 600; color: red;'>
                            تذکر های مهم
                        </p>
                        <p>
                            ۱- قبل از شروع ارزیابی از اتصال دستگاه خود به اینترنت اطمینان حاصل نمایید
                        </p>
                        <p>
                            ۲- توجه نمایید چنانچه در حین پاسخگویی به سوالات ارزیابی ارتباط دستگاه شما با اینترنت قطع گردد زمان ارزیابی شما متوقف نخواهد شد
                        </p>
                        <p>
                            ۳- پس از پاسخگویی به کلیه ی سوالات ارزیابی می بایست بر روی آیکون اتمام ارزیابی کلیک نمایید
                        </p>
                    </center>
        </div>
            </div>
        </form>
    </center>
@stop