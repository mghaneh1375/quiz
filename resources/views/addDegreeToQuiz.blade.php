@extends('layouts.menuBar')

@section('title')
    ساخت آزمون
@stop



@section('reminder')
    <center>
        @if(count($selectedDegrees) == 0)
            <h3 style="margin-top: 130px">افزودن پایه ی تحصیلی به آزمون</h3>
        @else
            <h3 style="margin-top: 130px">ویرایش پایه های تحصیلی آزمون</h3>
        @endif
        <div class="line"></div>

        <div class="myRegister">
            <div class="row data">

                <form method="post" action="{{ $url }}">
                    @foreach($degrees as $degree)
                        <?php $allow = true; ?>
                        <div class="col-xs-12">
                            <label>
                                <span>{{$degree->dN}}</span>
                        @foreach($selectedDegrees as $selectedDegree)
                            @if($selectedDegree->degreeId == $degree->id)
                                <?php $allow = false; ?>
                                <input type="checkbox" checked name="degrees[]" value="{{$degree->id}}">
                            @endif
                        @endforeach

                        @if($allow)
                            <input type="checkbox" name="degrees[]" value="{{$degree->id}}">
                        @endif

                            </label>
                        </div>
                    @endforeach
                    @if(isset($error))
                        <p style="margin-top: 10px; color: red">{{$error}}</p>
                    @endif
                    @if(count($selectedDegrees) == 0)
                        <input type="submit" class="MyBtn" style="width: auto" name="submitD" value="مرحله ی بعد">
                    @else
                        <p class="warning_color" style="margin-top: 10px">{{$msg}}</p>
                        <input type="submit" class="MyBtn" style="width: auto" name="editD" value="ویرایش">
                    @endif
                </form>
            </div>
        </div>
    </center>
@stop