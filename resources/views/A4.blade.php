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
            <button class="btn btn-success" onclick="document.location.href = '{{route('A4Excel', ['quizId' => $quizId])}}'">دریافت فایل اکسل</button>
        </div>

        <div class="col-xs-12" style="overflow-x: auto">
            <table style="margin-top: 10px">
                <tr>
                    <td><center>شهر</center></td>
                    <td><center>بین -33 تا 10</center></td>
                    <td><center>بین 11 تا 30</center></td>
                    <td><center>بین 31 تا 50</center></td>
                    <td><center>بین 51 تا 75</center></td>
                    <td><center>بین 76 تا 100</center></td>
                </tr>

                @foreach($cities as $city)
                    <tr>
                        <td><center>{{$city->name}}</center></td>
                        <td><center>{{$city->group_0}}</center></td>
                        <td><center>{{$city->group_1}}</center></td>
                        <td><center>{{$city->group_2}}</center></td>
                        <td><center>{{$city->group_3}}</center></td>
                        <td><center>{{$city->group_4}}</center></td>
                    </tr>
                @endforeach
            </table>
        </div>
    </center>
@stop