@extends('layouts.menuBar')



@section('title')

    گزارشات

@stop


@section('reminder')

    <center class="col-xs-12" style="margin-top: 50px">

        @if(count($quizes) == 0)
            <p>دانش آموزی وجود ندارد</p>
        @else
            @foreach($quizes as $quiz)
                <div class="col-xs-12">
                    <button style="margin-top: 10px; min-width: 300px" class="btn btn-primary" onclick="document.location.href = '{{route('showQuizReport', ['quizId' => $quiz->id])}}'">{{$quiz->QN}}</button>
                </div>
            @endforeach
        @endif

    </center>

@stop