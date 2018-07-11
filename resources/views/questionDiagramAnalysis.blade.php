@extends('layouts.menuBar')

@section('title')
    گزارشات
@stop

@section('extraLibraries')
    <script src="{{URL::asset('js/highcharts.js')}}"></script>
    <script src="{{URL::asset('js/exporting.js')}}"></script>

    <style>

        td > center{
            padding: 5px;
            min-width: 100px;
        }

        .highcharts-background {
            fill: #b6b6b6;
            stroke: #000000;
            stroke-width: 2px;
        }
        .highcharts-color-0 {
            fill: #ecd6a3;
            stroke: #ab4e1e;
        }
        .highcharts-color-1 {
            fill: #65ec38;
            stroke: #270767;
        }

        .highcharts-tooltip-box {
            fill: #6e6e6e;
            stroke-width: 0;
        }
    </style>


@stop

@section('reminder')

    <center style="margin-top: 130px">
        <h3>کارنامه ی کلی</h3>
        <a href="{{route('reports', ['quizId' => $qId])}}"><button>بازگشت به مرحله ی قبل</button></a>
        <div class="line"></div>
        <div style="overflow-x: auto">

            <?php
                $i = 0;
            ?>

            @foreach($questions as $question)

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
                </table>
                    <div id="barChart{{$i}}">
                    </div>
                    <script type="text/javascript">

                        var diagram = {{json_encode($diagram[$i])}};
                        var vertical = [];
                        for(i = 0; i < diagram.length; i++)
                            vertical[i] = i + 1;

                        Highcharts.chart('barChart' + '{{$i}}', {

                            chart: {
                                polar: true,
                                type: 'line'
                            },

                            title: {
                                text: 'نمودار مقایسه ای دورس',
                                x: -80
                            },

                            pane: {
                                size: '90%'
                            },

                            xAxis: {
                                categories: vertical,
                                tickmarkPlacement: 'on',
                                lineWidth: 0
                            },

                            yAxis: {
                                gridLineInterpolation: 'polygon',
                                lineWidth: 0,
                                min: 0
                            },
                            tooltip: {
                                useHTML: true,
                                headerFormat: '<table>',
                                pointFormat: '<tr><td style="direction: ltr"><b>{point.y}</b></td></tr>',
                                footerFormat: '</table>',
                                valueDecimals: 2
                            },

                            legend: {
                                align: 'right',
                                verticalAlign: 'top',
                                y: 70,
                                layout: 'vertical'
                            },

                            series: [{
                                name: 'درصد داوطلب',
                                data: diagram,
                                pointPlacement: 'on'
                            }]

                        });
                    </script>
                    <?php
                        $i++;
                    ?>
            @endforeach
        </div>
    </center>

@stop