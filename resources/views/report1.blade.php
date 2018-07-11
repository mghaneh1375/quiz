@extends('layouts.menuBar')

@section('title')
    گزارشات
@stop

@section('extraLibraries')

    <style>

        td > center{
            padding: 5px;
            min-width: 100px;
        }
    </style>


@stop

@section('reminder')

    <center style="margin-top: 130px">
        <h3>گزارش رتبه بندی به تفکیک جنسیت در استان</h3>
        <a href="{{route('reports', ['quizId' => $qId])}}"><button>بازگشت به مرحله ی قبل</button></a>
        <div class="line"></div>
        <div style="overflow-x: auto">

            <table style="margin-top: 10px; width: 100%">
                <tr>
                    <td><center>جنسیت</center></td>
                    <td><center>حاضرین</center></td>
                    <td><center>نمره کل</center></td>
                    @foreach($lessons as $lesson)
                        <td><center>{{$lesson->nameL}}</center></td>
                    @endforeach
                </tr>

                <tr>
                    <td><center>دخترانه</center></td>
                    <td><center>{{$girls}}</center></td>
                    <td><center>{{$totalMark[0]}}</center></td>
                    @foreach($lessons as $lesson)
                        <td><center>{{round($lesson->girlAvg, 0)}}</center></td>
                    @endforeach
                </tr>

                <tr>
                    <td><center>پسرانه</center></td>
                    <td><center>{{$boys}}</center></td>
                    <td><center>{{$totalMark[1]}}</center></td>
                    @foreach($lessons as $lesson)
                        <td><center>{{round($lesson->boyAvg, 0)}}</center></td>
                    @endforeach
                </tr>
            </table>
        </div>
    </center>

@stop