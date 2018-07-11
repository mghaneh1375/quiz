@extends('layouts.menuBar')

@section('title')
    ویرایش آزمون
@stop

<?php $url = URL('editQuiz') . "=" . $quizId ?>

@section('reminder')
    <center style="margin-top: 130px">
        <h3>ویرایش آزمون</h3>
        <div class="line"></div>
        <div class="myRegister">
            <div class="row data">
                <form method="post" action="{{ $url }}">

                    <div class="col-xs-12">
                        <input type="submit" class="MyBtn" style="width: auto" name="editInfo" value="ویرایش اطلاعات آزمون">
                    </div>
                    <div class="col-xs-12">
                        <input type="submit" class="MyBtn" style="width: auto" name="editDegree" value="ویرایش پایه های تحصیلی آزمون">
                    </div>
                    <div class="col-xs-12">
                        <input type="submit" class="MyBtn" style="width: auto" name="editQuestion" value="ویرایش سوالات آزمون">
                    </div>
                </form>
                <div class="col-xs-12">
                    <center>
                        <p class="warning_color">{{$msg}}</p>
                    </center>
                </div>
            </div>
        </div>
    </center>
@stop