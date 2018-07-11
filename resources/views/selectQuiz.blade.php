@extends('layouts.menuBar')

@section('title')
    آزمون ها
@stop

@section('reminder')
    <center style="margin-top: 130px">
        <h3>آزمون ها</h3>
        <div class="line"></div>

        <div class="myRegister">
            <div class="row data">
                <form method="post" action="{{ URL('quizes') }}">

                        @foreach($quizIds as $quizId)
                            <div class="col-xs-12">
                                <center>
                                    <span>{{$quizId->QN}}</span>
                                    <button style="margin-right: 15px" class="btn btn-success"  name="editSelectedQuiz" data-toggle="tooltip" title="ویرایش آزمون" value="{{$quizId->id}}">
                                        <span  style="margin-left: 10%" class="glyphicon glyphicon-edit"></span>
                                    </button>

                                    <span style="margin-right: 10px">انتخاب برای حذف</span>
                                    <input type="checkbox" name="selectedQuiz[]" value="{{$quizId->id}}">
                                </center>
                            </div>
                        @endforeach
                    <div class="col-xs-12">
                        <p class="warning_color" style="margin-top: 10px">{{$msg}}</p>
                        <input type="submit" value="حذف" name="deleteSelectedQuiz" class="MyBtn" style="width: auto">
                        <input type="submit" name="createQuiz" value="ساخت آزمون جدید" class="MyBtn" style="width: auto">
                    </div>
                </form>
            </div>
        </div>
    </center>
@stop