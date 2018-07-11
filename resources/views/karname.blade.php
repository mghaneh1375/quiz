@extends('layouts.menuBar')

@section('title')
    مشاهده ی کارنامه
@stop

@section('extraLibraries')

    <script>

        $(document).ready(function () {

            changeKindKarname();

        });

        function getQuizLessons(qId) {

            $("#lessonContainer").empty();

            $.ajax({

                type: 'post',
                url: '{{route('getQuizLessons')}}',
                data: {
                    'qId': qId
                },
                success: function (response) {

                    response = JSON.parse(response);

                    newElement = "";

                    for(i = 0; i < response.length; i++) {
                        newElement += "<option value='" + response[i].id + "'>" + response[i].nameL + "</option>";
                    }

                    $("#lessonContainer").append(newElement);
                    $("#divLessonContainer").css("visibility", "visible");

                    $("#getKarnameBtn").removeAttr('disabled');

                }
            });
        }

        function changeKindKarname() {

            if($("#kindKarname").find(":selected").val() == 2) {
                $("#getKarnameBtn").attr('disabled', 'disabled');
                getQuizLessons($("#quiz_id").find(":selected").val(), 'lessonContainer');
            }
            else {
                $("#divLessonContainer").css("visibility", "hidden");
            }

        }
    </script>
@stop

@section('reminder')

    <center style="margin-top: 130px">
        <h3>لطفا آزمون مورد نظر خود را انتخاب کنید</h3>
        <div class="line"></div>
    </center>

    <div class="myRegister">
        <div class="row data">
            <center>
                <form method="post" action="{{URL('seeResult')}}">
                    {{csrf_field()}}
                    <div class="col-xs-12">
                        <label>
                            <span>آزمون مورد نظر</span>
                            @if(count($quizes) == 0)
                                <p class="warning_color" style="margin-top: 10px">آزمونی جهت نمایش وجود ندارد</p>
                            @else
                                <select id="quiz_id" name="quiz_id" onchange="changeKindKarname()">
                                    @foreach($quizes as $quiz)
                                        @if(isset($selectedQuiz) && !empty($selectedQuiz) && $selectedQuiz == $quiz->id)
                                            <option selected value="{{$quiz->id}}">{{$quiz->QN}}</option>
                                        @else
                                            <option value="{{$quiz->id}}">{{$quiz->QN}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            @endif
                        </label>
                    </div>
                    @if(count($quizes) != 0)
                        <div class="col-xs-12">
                            <label>
                                <span>نوع کارنامه ی مورد نظر</span>
                                <select id="kindKarname" name="kindKarname" onchange="changeKindKarname()">
                                    <option value="1">کارنامه ی کلی</option>
                                    <option value="2">کارنامه ی مبحثی</option>
                                    <option value="4">کارنامه ی حیطه ای</option>
                                    <option value="3">کارنامه ی سوال به سوال</option>
                                </select>
                            </label>
                        </div>

                        <div id="divLessonContainer" class="col-xs-12">
                            <label>
                                <span>درس مورد نظر</span>
                                <select name='lId' id="lessonContainer">
                                </select>
                            </label>
                        </div>

                        <div class="col-xs-12">
                            <center class="warning_color" style="margin-top: 10px">{{$msg}}</center>
                            <input id="getKarnameBtn" type="submit" name="getKarname" class="MyBtn" style="width: auto" value="مشاهده ی کارنامه">
                        </div>
                    @endif
                </form>
            </center>
        </div>
    </div>

@stop