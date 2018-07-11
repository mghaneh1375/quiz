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
        <h3>درصد پاسخ به هر گزینه در هر سوال به تفکیک شهر</h3>
        <a href="{{route('reports', ['quizId' => $qId])}}"><button>بازگشت به مرحله ی قبل</button></a>
        <div class="line"></div>
        <div>
            <table style="margin-top: 10px; width: 100%">
                <tr>
                    <td rowspan="2"><center>سوال</center></td>
                    <td rowspan="2"><center>گزینه ی صحیح</center></td>
                    <td><center>گزینه ی 1</center></td>
                    <td><center>گزینه ی 2</center></td>
                    <td><center>گزینه ی 3</center></td>
                    <td><center>گزینه ی 4</center></td>
                    <td><center>بدون پاسخ</center></td>
                </tr>

                <tr>
                    <td><center>درصد</center></td>
                    <td><center>درصد</center></td>
                    <td><center>درصد</center></td>
                    <td><center>درصد</center></td>
                    <td><center>درصد</center></td>
                </tr>
                @for($i = 0; $i < count($qoq); $i++)

                    <tr>
                        <td><center>{{$qoq[$i]->qNo}}</center></td>
                        <td><center>{{$qoq[$i]->ans}}</center></td>
                        <td><center>{{$qoq[$i]->ans1}}</center></td>
                        <td><center>{{$qoq[$i]->ans2}}</center></td>
                        <td><center>{{$qoq[$i]->ans3}}</center></td>
                        <td><center>{{$qoq[$i]->ans4}}</center></td>
                        <td><center>{{$qoq[$i]->ans0}}</center></td>
                    </tr>
                @endfor
            </table>
        </div>
        </div>
    </center>

@stop