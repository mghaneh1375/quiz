@extends('layouts.menuBar')



@section('title')

    گزارشات

@stop


@section('reminder')

    <center class="col-xs-12" style="margin-top: 50px">

        @if(!empty($err))
            <p>{{$err}}</p>
        @endif

        <div class="col-xs-12">
            <button style="margin-top: 10px; min-width: 300px" class="btn btn-danger" onclick="document.location.href = '{{route('reports', ['quiz_id' => $quizId])}}'">بازگشت</button>
        </div>

        @if(count($uIds) == 0)
            <p>دانش آموزی وجود ندارد</p>
        @else
            @foreach($uIds as $uId)
                <div class="col-xs-12">
                    <button style="margin-top: 10px; min-width: 300px" class="btn btn-primary" onclick="document.location.href = '{{route('A3', ['quiz_id' => $quizId, 'uId' => $uId->id])}}'">{{$uId->firstName . " " . $uId->lastName}}</button>
                </div>
            @endforeach
        @endif

    </center>

@stop