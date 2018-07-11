@extends('layouts.menuBar')

@section('title')
    مشاهده ی کارنامه مبحثی
@stop

@section('extraLibraries')
    <script src="{{URL::asset('js/highcharts.js')}}"></script>
    <script src="{{URL::asset('js/highcharts-more.js')}}"></script>
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

        .highcharts-tooltip-box {
            fill: #6e6e6e;
            stroke-width: 0;
        }
    </style>


@stop

@section('reminder')
    <center style="margin-top: 130px">
        <h3>کارنامه ی مبحثی</h3>
        <a href="{{route('seeResult', ['quizId' => $quizId])}}"><button>بازگشت به مرحله ی قبل</button></a>
        <div class="line"></div>

        <div style="overflow-x: auto">
            <table style="margin-top: 10px">
                <tr>
                    <td><center>نام مبحث</center></td>
                    <td><center>تعداد کل سوالات</center></td>
                    <td><center>درست</center></td>
                    <td><center>نادرست</center></td>
                    <td><center>نزده</center></td>
                    @if($kindKarname->subjectMark)
                        <td><center><span>نمره از </span><span>{{$totalMark}}</span></center></td>
                    @endif
                    <td><center>درصد پاسخ گویی</center></td>
                    @if($kindKarname->subjectAvg)
                        <td><center>میانگین درصد پاسخ گویی</center></td>
                    @endif
                    @if($kindKarname->subjectMaxPercent)
                        <td><center>بیشترین درصد پاسخ گویی</center></td>
                    @endif
                    @if($kindKarname->subjectCityRank)
                        <td><center>رتبه در شهر</center></td>
                    @endif
                    @if($kindKarname->subjectStateRank)
                        <td><center>رتبه در استان</center></td>
                    @endif
                    @if($kindKarname->subjectCountryRank)
                        <td><center>رتبه در کشور</center></td>
                    @endif
                    @if(count($status) > 0)
                        <td><center>وضعیت</center></td>
                    @endif
                </tr>

                <?php
                $i = 0;
                ?>

                @foreach($subjects as $subject)

                    <?php
                        $percent[$i] = ($minusMark) ?
                                round(($roq[1][$i] * 3.0 - $roq[0][$i]) / (3.0 * $roq[2][$i]), 2) * 100 :
                                round($roq[1][$i] / $roq[2][$i], 2) * 100;

                        $allow = true;
                    ?>

                    @if(count($status) > 0)
                        @foreach($status as $itr)
                                @if($itr->type && $itr->floor <= $percent[$i] && $percent[$i] <= $itr->ceil
                                    || (!$itr->type && $kindKarname->subjectAvg &&
                                    $avgs[$i]->avg - $itr->floor <= $percent[$i] && $avgs[$i]->avg + $itr->ceil >= $percent[$i]))
                                <tr style="background: {{$itr->color}}">
                                <?php $allow = false; ?>
                            @endif
                        @endforeach
                    @endif

                    @if($allow)
                        <tr>
                    @endif

                        <td><center>{{$subject->nameSubject}}</center></td>
                        <td><center>{{$roq[2][$i]}}</center></td>
                        <td><center>{{$roq[1][$i]}}</center></td>
                        <td><center>{{$roq[0][$i]}}</center></td>
                        <td><center>{{$roq[2][$i] - $roq[1][$i] - $roq[0][$i]}}</center></td>

                        @if($kindKarname->subjectMark)
                            <td><center style="direction: ltr">{{($percent[$i] <= 0) ? 0 : round($percent[$i] * $totalMark / 100, 0)}}</center></td>
                        @endif
                        <td><center style="direction: ltr">{{$percent[$i]}}</center></td>
                        @if($kindKarname->subjectAvg)
                            <td><center style="direction: ltr">{{round($avgs[$i]->avg, 0)}}</center></td>
                        @endif
                        @if($kindKarname->subjectMaxPercent)
                            <td><center style="direction: ltr">{{round($avgs[$i]->maxPercent, 0)}}</center></td>
                        @endif
                        @if($kindKarname->subjectCityRank)
                            <td><center>{{$cityRank[$i]}}</center></td>
                        @endif
                        @if($kindKarname->subjectStateRank)
                            <td><center>{{$stateRank[$i]}}</center></td>
                        @endif
                        @if($kindKarname->subjectCountryRank)
                            <td><center>{{$countryRank[$i]}}</center></td>
                        @endif
                        @if(count($status) > 0)
                            <td>
                                <center>
                                    @foreach($status as $itr)
                                        @if($itr->type && $itr->floor <= $percent[$i] && $percent[$i] <= $itr->ceil
                                            || (!$itr->type && $kindKarname->subjectAvg &&
                                            $avgs[$i]->avg - $itr->floor <= $percent[$i] && $avgs[$i]->avg + $itr->ceil >= $percent[$i]))
                                            @if($itr->pic)
                                                <img width="40px" height="40px" src="{{URL('status') . '/' . $itr->status}}">
                                            @else
                                                <p>{{$itr->status}}</p>
                                            @endif
                                        @endif
                                    @endforeach
                                </center>
                            </td>
                        @endif
                    </tr>
                    <?php
                    $i++;
                    ?>
                @endforeach
            </table>
        </div>


        @if($kindKarname->subjectBarChart)
            <div id="barChart1" style="min-width: 310px; height: 400px; margin-top: 10px; direction: ltr"></div>

            <script type="text/javascript">

                var percents = {{json_encode($percent)}};
                var subjects = {{json_encode($subjects)}};

                for(i = 0; i < subjects.length; i++) {
                    subjects[i] = subjects[i].nameSubject;
//                  percents[i] += 33.3;
                }

    //                Highcharts.chart('barChart1', {
    //
    //                    chart: {
    //                        type: 'column'
    //                    },
    //
    //                    title: {
    //                        text: 'نمودار مقایسه ای مباحث'
    //                    },
    //
    //                    xAxis: {
    //                        categories: subjects,
    //                        title: {
    //                            text: 'مباحث امتحان'
    //                        }
    //                    },
    //                    yAxis: {
    //                        className: 'highcharts-color-0',
    //                        title: {
    //                            text: 'درصد'
    //                        }
    //                    },
    //                    series: [{
    //                        type: 'spline',
    //                        name: 'درصد داوطلب',
    //                        data: percents,
    //                        tooltip: {
    //                            useHTML: true,
    //                            headerFormat: '<table>',
    //                            pointFormat: '<tr><td style="direction: ltr"><b>{point.y}</b></td></tr>',
    //                            footerFormat: '</table>',
    //                            valueDecimals: 0
    //                        },
    //                        marker: {
    //                            lineWidth: 2,
    //                            lineColor: Highcharts.getOptions().colors[1],
    //                            fillColor: '#65ec38'
    //                        }
    //                    }]
    //                });
    //
    //                $(document).ready(function () {
    //
    //                    $(".highcharts-title").css({
    //                        "font-size" : "20px"
    //                    });
    //                    $(".highcharts-xaxis-labels").children().css({
    //                        "font-size" : "14px"
    //                    });
    //                    $(".highcharts-axis-title").children().css({
    //                        "font-size" : "18px"
    //                    });
    //                    $(".highcharts-yaxis").children().css({
    //                        "font-size" : "18px"
    //                    });
    //                });
                    Highcharts.chart('barChart1', {

                        chart: {
                            polar: true,
                            type: 'line'
                        },

                        title: {
                            text: 'نمودار مقایسه ای مباحث',
                            x: 20
                        },

                        pane: {
                            size: '90%'
                        },

                        xAxis: {
                            categories: subjects,
                            tickmarkPlacement: 'on',
                            lineWidth: 0
                        },

                        yAxis: {
                            gridLineInterpolation: 'polygon',
                            lineWidth: 0,
                            min: -33
                        },
                        tooltip: {
                                useHTML: true,
                                headerFormat: '<table>',
                                pointFormat: '<tr><td style="direction: ltr"><b>{point.y}</b></td></tr>',
                                footerFormat: '</table>',
                                valueDecimals: 0
                            },

                        legend: {
                            align: 'right',
                            verticalAlign: 'top',
                            y: 70,
                            layout: 'vertical'
                        },

                        series: [{
                            name: 'مباحث',
                            data: percents,
                            pointPlacement: 'on'
                        }]

                    });
            </script>
        @endif
    </center>
@stop