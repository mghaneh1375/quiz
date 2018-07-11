@extends('layouts.menuBar')

@section('title')
    @if($mode == 'create')
        ساخت جدول تراز آزمون
    @else
        حذف جدول تراز آزمون
    @endif
@stop

@section('reminder')
    <center style="margin-top: 130px">
        <h3>آزمون مورد نظر خود را وارد نمایید</h3>
        <div class="line"></div>
    </center>
    <div class="myRegister">
        <div class="row data">
            <center>
                @if($mode == "create")
                    <form method="post" action="{{URL('createTarazTable')}}">
                @else
                    <form method="post" action="{{URL('deleteTarazTable')}}">
                @endif
                    <div class="col-xs-12">
                        <label>
                            <span>آزمون مورد نظر</span>
                            <select name="quizId">
                                @foreach($quizes as $quiz)
                                    <option value="{{$quiz->id}}">{{$quiz->QN}}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                    <div class="col-xs-12">
                        <p class="warning_color">{{$msg}}</p>
                        <input type="submit" value="تایید" name="createTaraz" class="MyBtn" style="width: auto">
                    </div>
                </form>
            </center>
        </div>
    </div>
@stop