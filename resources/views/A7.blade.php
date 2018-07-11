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
        <a href="{{route('reports', ['quiz_id' => $quizId])}}"><button class="btn btn-primary">بازگشت به مرحله ی قبل</button></a>
        <div class="line"></div>

        <div class="col-xs-12" style="margin-top: 10px">
            <button class="btn btn-success" onclick="document.location.href = '{{route('A7Excel', ['quiz_id' => $quizId])}}'">دریافت فایل اکسل</button>
        </div>

        <div class="col-xs-12" style="overflow-x: auto">
            <table style="margin-top: 10px">
                <tr>
                    <td><center>نام درس</center></td>
                    <td><center>بین -33 تا 10</center></td>
                    <td><center>بین 11 تا 30</center></td>
                    <td><center>بین 31 تا 50</center></td>
                    <td><center>بین 51 تا 75</center></td>
                    <td><center>بین 76 تا 100</center></td>
                </tr>

                <?php $i = 0; ?>
                @foreach($lessons as $lesson)
                    <tr>
                        <td><center>{{$lesson->nameL}}</center></td>
                        <td><center>{{$lesson->group_0}}</center></td>
                        <td><center>{{$lesson->group_1}}</center></td>
                        <td><center>{{$lesson->group_2}}</center></td>
                        <td><center>{{$lesson->group_3}}</center></td>
                        <td><center>{{$lesson->group_4}}</center></td>
                    </tr>
                    <?php $i++; ?>
                @endforeach
            </table>
        </div>
    </center>
@stop