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
        <h3>گزارش تجزیه و تحلیل نمرات دروس به تفکیک جنسیت</h3>
        <a href="{{route('reports', ['quizId' => $qId])}}"><button>بازگشت به مرحله ی قبل</button></a>
        <div class="line"></div>
        <div style="overflow-x: auto">

            <table style="margin-top: 10px; width: 100%">
                <caption><center style="color: #8a0000">جنسیت : پسرانه</center></caption>
                <tr>
                    <td rowspan="2"><center>نام درس</center></td>
                    <td rowspan="2"><center>حاضرین</center></td>
                    <td colspan="4"><center>تعداد دانش آموزان</center></td>
                    <td colspan="2"><center>درصد دانش آموزان</center></td>
                </tr>
                <tr>
                    <td><center>بین 0 تا 5</center></td>
                    <td><center>بین 5 تا 10</center></td>
                    <td><center>بیت 10 تا 15</center></td>
                    <td><center>بین 15 تا 20</center></td>
                    <td><center>زیر نمره ی 10</center></td>
                    <td><center>بالای نمره ی 10</center></td>
                </tr>
                @foreach($lessons as $lesson)
                    <tr>
                        <td><center>{{$lesson->nameL}}</center></td>
                        <td><center>{{$boys}}</center></td>
                        <td><center>{{$lesson->boyArr[0]}}</center></td>
                        <td><center>{{$lesson->boyArr[1]}}</center></td>
                        <td><center>{{$lesson->boyArr[2]}}</center></td>
                        <td><center>{{$lesson->boyArr[3]}}</center></td>
                        <td><center>{{round(($lesson->boyArr[0] + $lesson->boyArr[1]) * 100 / $boys, 0)}}</center></td>
                        <td><center>{{round(($lesson->boyArr[2] + $lesson->boyArr[3]) * 100 / $boys, 0)}}</center></td>
                    </tr>
                @endforeach
                <tr>
                    <td><center>مجموع</center></td>
                    <td><center>{{$boys}}</center></td>
                    <td><center>{{$boyTotal[0]}}</center></td>
                    <td><center>{{$boyTotal[1]}}</center></td>
                    <td><center>{{$boyTotal[2]}}</center></td>
                    <td><center>{{$boyTotal[3]}}</center></td>
                    <td><center>{{round(($boyTotal[0] + $boyTotal[1]) * 100 / $boys, 0)}}</center></td>
                    <td><center>{{round(($boyTotal[2] + $boyTotal[3]) * 100 / $boys, 0)}}</center></td>
                </tr>
            </table>

            <table style="margin-top: 50px; width: 100%">
                <caption><center style="color: #8a0000">جنسیت : دخترانه</center></caption>
                <tr>
                    <td rowspan="2"><center>نام درس</center></td>
                    <td rowspan="2"><center>حاضرین</center></td>
                    <td colspan="4"><center>تعداد دانش آموزان</center></td>
                    <td colspan="2"><center>درصد دانش آموزان</center></td>
                </tr>
                <tr>
                    <td><center>بین 0 تا 5</center></td>
                    <td><center>بین 5 تا 10</center></td>
                    <td><center>بیت 10 تا 15</center></td>
                    <td><center>بین 15 تا 20</center></td>
                    <td><center>زیر نمره ی 10</center></td>
                    <td><center>بالای نمره ی 10</center></td>
                </tr>
                @foreach($lessons as $lesson)
                    <tr>
                        <td><center>{{$lesson->nameL}}</center></td>
                        <td><center>{{$girls}}</center></td>
                        <td><center>{{$lesson->girlArr[0]}}</center></td>
                        <td><center>{{$lesson->girlArr[1]}}</center></td>
                        <td><center>{{$lesson->girlArr[2]}}</center></td>
                        <td><center>{{$lesson->girlArr[3]}}</center></td>
                        <td><center>{{round(($lesson->girlArr[0] + $lesson->girlArr[1]) * 100 / $girls, 0)}}</center></td>
                        <td><center>{{round(($lesson->girlArr[2] + $lesson->girlArr[3]) * 100 / $girls, 0)}}</center></td>
                    </tr>
                @endforeach
                <tr>
                    <td><center>مجموع</center></td>
                    <td><center>{{$girls}}</center></td>
                    <td><center>{{$girlTotal[0]}}</center></td>
                    <td><center>{{$girlTotal[1]}}</center></td>
                    <td><center>{{$girlTotal[2]}}</center></td>
                    <td><center>{{$girlTotal[3]}}</center></td>
                    <td><center>{{round(($girlTotal[0] + $girlTotal[1]) * 100 / $girls, 0)}}</center></td>
                    <td><center>{{round(($girlTotal[2] + $girlTotal[3]) * 100 / $girls, 0)}}</center></td>
                </tr>
            </table>
        </div>
    </center>

@stop