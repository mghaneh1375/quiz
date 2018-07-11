@extends('layouts.menuBar')



@section('title')

    گزارشات

@stop



@section('extraLibraries')



    <style>
        td {
            padding: 10px;
            min-width: 100px;
        }
        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>


@stop



@section('reminder')

    <center style="margin-top: 10px">
        <a href="{{route('reports', ['quizId' => $quizId])}}"><button class="btn btn-primary">بازگشت به مرحله ی قبل</button></a>
        <div class="line"></div>

        <div class="col-xs-12" style="margin-top: 10px">
            <button class="btn btn-success" onclick="document.location.href = '{{route('A6Excel', ['quizId' => $quizId])}}'">دریافت فایل اکسل</button>
        </div>

        <div class="col-xs-12" style="overflow-x: auto">
            <table style="margin-top: 10px">
                <tr>
                    <td><center>نام درس</center></td>
                    <td><center>نام مبحث</center></td>
                    <td><center>درست</center></td>
                    <td><center>نادرست</center></td>
                    <td><center>بدون پاسخ</center></td>
                    <td><center>درصد</center></td>
                </tr>

                <?php $i = 0; ?>
                @foreach($subjects as $subject)
                    <tr>
                        <td><center>{{$subject->lessonName}}</center></td>
                        <td><center>{{$subject->name}}</center></td>
                        <td><center>{{$subject->correct}}</center></td>
                        <td><center>{{$subject->inCorrect}}</center></td>
                        <td><center>{{$subject->white}}</center></td>
                        @if($subject->correct + $subject->inCorrect + $subject->white != 0)
                            <td><center style="direction: ltr">{{round($subject->correct * 100 / ($subject->correct + $subject->inCorrect + $subject->white), 0)}}</center></td>
                        @else
                            <td><center>0</center></td>
                        @endif
                    </tr>
                    <?php $i++; ?>
                @endforeach
            </table>
        </div>
    </center>
@stop