@extends('layouts.menuBar')

@section('title')
    گزارشات
@stop

@section('extraLibraries')

    <style>

        td > center {
            padding: 5px;
            min-width: 100px;
        }

        table, p {
            margin-top: 20px;
        }

    </style>

@stop

@section('reminder')

    <div class="col-xs-12" style="margin-top: 100px">

        <center>

            <p>سطح میزان کیفیت سوالات آزمون</p>

            <table>
                <tr>
                    <td>گزینه 1</td>
                    <td>گزینه 2</td>
                    <td>گزینه 3</td>
                    <td>گزینه 4</td>
                </tr>
                <tr>
                    <td>{{$answers[0][0]}}</td>
                    <td>{{$answers[0][1]}}</td>
                    <td>{{$answers[0][2]}}</td>
                    <td>{{$answers[0][3]}}</td>
                </tr>
            </table>

        </center>


        <center>

            <p>میزان رضایت شما از نحوه پشتیبانی</p>

            <table>
                <tr>
                    <td>گزینه 1</td>
                    <td>گزینه 2</td>
                    <td>گزینه 3</td>
                    <td>گزینه 4</td>
                </tr>
                <tr>
                    <td>{{$answers[1][0]}}</td>
                    <td>{{$answers[1][1]}}</td>
                    <td>{{$answers[1][2]}}</td>
                    <td>{{$answers[1][3]}}</td>
                </tr>
            </table>

        </center>


        <center>

            <p>نحوه شرکت در آزمون</p>

            <table>
                <tr>
                    <td>گزینه 1</td>
                    <td>گزینه 2</td>
                    <td>گزینه 3</td>
                </tr>
                <tr>
                    <td>{{$answers[2][0]}}</td>
                    <td>{{$answers[2][1]}}</td>
                    <td>{{$answers[2][2]}}</td>
                </tr>
            </table>

        </center>


        <center>

            <p>میزان رضایت مندی شما از مجموع برگزاری این آزمون</p>

            <table>
                <tr>
                    <td>گزینه 1</td>
                    <td>گزینه 2</td>
                    <td>گزینه 3</td>
                    <td>گزینه 4</td>
                </tr>
                <tr>
                    <td>{{$answers[3][0]}}</td>
                    <td>{{$answers[3][1]}}</td>
                    <td>{{$answers[3][2]}}</td>
                    <td>{{$answers[3][3]}}</td>
                </tr>
            </table>

        </center>


        <center>

            <p>میزان رضایت مندی شما از نحوه اطلاع رسانی</p>

            <table>
                <tr>
                    <td>گزینه 1</td>
                    <td>گزینه 2</td>
                    <td>گزینه 3</td>
                    <td>گزینه 4</td>
                </tr>
                <tr>
                    <td>{{$answers[4][0]}}</td>
                    <td>{{$answers[4][1]}}</td>
                    <td>{{$answers[4][2]}}</td>
                    <td>{{$answers[4][3]}}</td>
                </tr>
            </table>

        </center>

    </div>
@stop