@extends('layouts.menuBar')

@section('title')
    گزارشات
@stop

@section('reminder')

    <center style="margin-top: 130px">
        <h3>کارنامه ی کلی</h3>
        <a href="{{route('reports', ['quizId' => $qId])}}"><button>بازگشت به مرحله ی قبل</button></a>
        <div class="line"></div>
        <div style="overflow-x: auto">
            <table style="margin-top: 10px; width: 100%">
                <tr>
                    <td><center>گزینه ی صحیح</center></td>
                    <td><center>درصد پاسخ نادرست</center></td>
                    <td><center>درصد بدون جواب</center></td>
                    <td><center>درصد گزینه ی 1</center></td>
                    <td><center>درصد گزینه ی 2</center></td>
                    <td><center>درصد گزینه ی 3</center></td>
                    <td><center>درصد گزینه ی 4</center></td>
                    <td><center>وضعیت سوال</center></td>
                    <td><center>وضعیت دشواری</center></td>
                    <td><center>ضریب همبستگی دو رشته ای</center></td>
                   </tr>

                @foreach($questions as $question)
                    <tr>
                        <td><center>{{$question->ans}}</center></td>
                        <td><center>{{$question->inCorrectPercent}}</center></td>
                        <td><center>{{$question->result0}}</center></td>
                        <td><center>{{$question->result1}}</center></td>
                        <td><center>{{$question->result2}}</center></td>
                        <td><center>{{$question->result3}}</center></td>
                        <td><center>{{$question->result4}}</center></td>
                        <td><center>{{$question->status}}</center></td>
                        <td><center>{{$question->level}}</center></td>
                        <td><center>{{$question->corel}}</center></td>
                    </tr>
                @endforeach
            </table>
        </div>
    </center>

@stop