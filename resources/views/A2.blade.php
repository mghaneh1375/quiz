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
            <button class="btn btn-success" onclick="document.location.href = '{{route('A2Excel', ['quiz_id' => $quizId])}}'">دریافت فایل اکسل</button>
        </div>

        <div class="col-xs-12" style="overflow-x: auto">
            <table style="margin-top: 10px">
                <tr>
                    <td><center>شهر</center></td>
                    <td><center>تعداد حاضرین</center></td>
                    @if(count($cities) > 0)
                        @foreach($cities[0]->lessons as $itr)
                            <td><center>{{$itr->name}}</center></td>
                        @endforeach
                    @endif
                </tr>

                <?php $i = 0;?>

                @foreach($cities as $city)
                    <tr>
                        <td><center>{{$city->name}}</center></td>
                        <td><center>{{$city->total}}</center></td>
                        @foreach($cities[$i]->lessons as $itr)
                            <td><center style="direction: ltr">{{round($itr->avgPercent, 0)}}</center></td>
                        @endforeach
                    </tr>
                    <?php $i++; ?>
                @endforeach
            </table>
        </div>
    </center>
@stop