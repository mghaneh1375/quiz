@extends('layouts.menuBar')

@section('title')
    ساخت آزمون جدید
@stop

@section('extraLibraries')
    <link rel="stylesheet" href = {{URL::asset("src/clockpicker.css") }}>
    <link rel="stylesheet" href = {{URL::asset("src/standalone.css") }}>
    <link rel="stylesheet" href = {{URL::asset("dist/bootstrap-clockpicker.css") }}>
    <link rel="stylesheet" href = {{URL::asset("dist/bootstrap-clockpicker.min.css") }}>
    <link rel="stylesheet" href = {{URL::asset("dist/jquery-clockpicker.css") }}>
    <link rel="stylesheet" href = {{URL::asset("dist/jquery-clockpicker.min.css") }}>
    <link rel="stylesheet" href = {{URL::asset("css/skins/calendar-green.css") }}>
    <script src = {{URL::asset("js/jalali.js") }}></script>
    <script src = {{URL::asset("js/calendar.js") }}></script>
    <script src = {{URL::asset("src/clockpicker.js") }}></script>
    <script src = {{URL::asset("js/calendar-setup.js") }}></script>
    <script src = {{URL::asset("dist/bootstrap-clockpicker.js") }}></script>
    <script src = {{URL::asset("dist/bootstrap-clockpicker.min.js") }}></script>
    <script src = {{URL::asset("dist/jquery-clockpicker.js") }}></script>
    <script src = {{URL::asset("dist/jquery-clockpicker.min.js") }}></script>
    <script src = {{URL::asset("js/lang/calendar-fa.js") }}></script>

    <script>
        $(document).ready(function(){
            $('.clockpicker').clockpicker();
        });
    </script>
@stop

@section('reminder')

    <div class="myRegister">
        <div class="row data">
            <center>
                <h3>اطلاعات آزمون</h3>
            </center>
            <div class="line"></div>

            @if($mode == "edit")
                <form method="post" action="{{URL('editQuiz') . '=' . $quizId}}">
            @else
                <form method="post" action="{{URL('createQuiz')}}">
            @endif
                <center>
                    <div class="col-xs-12">
                        <label>
                            <span>نام آزمون</span>
                            <input type="text" name="name" value="{{$qName}}" maxlength="40" required autofocus>
                        </label>
                    </div>
                    <div class="col-xs-12">
                        <label>
                            <span>مدت آزمون بر حسب دقیقه</span>
                            <input type="number" name="timeLen" value="{{$timeLen}}" min="0" max="999">
                        </label>
                    </div>
                    <div class="col-xs-12">
                        <label>
                            <span>تاریخ شروع آزمون</span>
                            <input type="button" style="border: none; width: 30px; height: 30px; background: url({{ URL::asset('images/calendar-flat.png') }}) repeat 0 0; background-size: 100% 100%;" id="date_btn">
                            <br/>
                            <input type="text" id="date_input" name="sDate" value="{{$sDate}}" required readonly>
                            <script>
                                Calendar.setup({
                                    inputField: "date_input",
                                    button: "date_btn",
                                    ifFormat: "%Y/%m/%d",
                                    dateType: "jalali"
                                });
                            </script>
                        </label>
                    </div>
                    <div class="col-xs-12">
                        <label>
                            <span>ساعت شروع آزمون</span>
                            <div class="clockpicker">
                                <input type="text" style="width: 100%" name="sTime" class="form-control" required value="{{$sTime}}">
                            </div>
                        </label>
                    </div>
                    <div class="col-xs-12">
                        <label>
                            <span>تاریخ اتمام آزمون</span>
                            <input type="button" style="width: 30px; height: 30px; border: none; background: url({{ URL::asset('images/calendar-flat.png') }}) repeat 0 0; background-size: 100% 100%;" id="date_btn2">
                            <br/>
                            <input type="text" id="date_input2" value="{{$eDate}}" name="eDate" required readonly>
                            <script>
                                Calendar.setup({
                                    inputField: "date_input2",
                                    button: "date_btn2",
                                    ifFormat: "%Y/%m/%d",
                                    dateType: "jalali"
                                });
                            </script>
                        </label>
                    </div>
                    <div class="col-xs-12">
                        <label>
                            <span>ساعت اتمام آزمون</span>
                            <div class="clockpicker">
                                <input type="text" name="eTime" style="width: 100%" class="form-control" required value="{{$eTime}}">
                            </div>
                        </label>
                    </div>
                    <div class="col-xs-12">
                        <label>
                            <span>نوع سوالات</span>
                            <select name="kindQ">
                                <option value="1">تستی</option>
                                @if($kindQ == 2)
                                    <option selected value="2">کوتاه پاسخ</option>
                                    <option value="3">تلفیقی</option>
                                @elseif($kindQ == 3)
                                    <option value="2">کوتاه پاسخ</option>
                                    <option selected value="3">تلفیقی</option>
                                @else
                                    <option value="2">کوتاه پاسخ</option>
                                    <option value="3">تلفیقی</option>
                                @endif
                            </select>
                        </label>
                    </div>
                    <div class="col-xs-12">
                        <label>
                            <span>نمره از</span>
                            <input type="number" name="mark" max="100" min="0" value="{{$mark}}">
                        </label>
                    </div>
                    <div class="col-xs-12">
                        <label>
                            <span>آزمون نمره ی منفی داشته باشد</span>
                            @if($minusMark)
                                <input type="checkbox" checked name="minusMark">
                            @else
                                <input type="checkbox" name="minusMark">
                            @endif
                        </label>
                    </div>
                    <p style="margin-top: 5px; color: red">{{$error}}</p>
                    @if($mode == "create")
                        <input type="submit" class="MyBtn" style="width: auto; padding: 5px; margin-top: 30px" name="submitQ" value="مرحله ی بعد">
                    @else
                        <input type="submit" class="MyBtn" style="width: auto; padding: 5px; margin-top: 30px" name="editQ" value="ویرایش">
                    @endif
                </center>
            </form>
        </div>
    </div>
@stop