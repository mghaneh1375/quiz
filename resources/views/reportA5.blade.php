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
            <button class="btn btn-success" onclick="document.location.href = '{{route('A5Excel', ['quiz_id' => $quizId])}}'">دریافت فایل اکسل</button>
        </div>

        @if(isset($msg) && !empty($msg))
            <p style="margin-top: 10px; color: #963019 ">{{$msg}}</p>
        @endif


        <p>
            <span>تعداد کل:</span><span>&nbsp;</span><span>{{count($users)}}</span>
        </p>


        <div class="col-xs-12" style="overflow-x: auto">
            <table style="margin-top: 10px">
                <tr>
                    <td><center>نام و نام خانوادگی</center></td>
                    <td><center>شهر</center></td>
                    <td><center>استان</center></td>
                    @if(count($users) > 0)
                        @foreach($users[0]->lessons as $itr)
                            <td><center>{{$itr->name}}</center></td>
                        @endforeach
                    @endif

                    <td><center>میانگین</center></td>
                    <td><center>تراز کل</center></td>
                    <td><center>رتبه در شهر</center></td>
                    <td><center>رتبه در استان</center></td>
                    <td><center>رتبه در کشور</center></td>
                </tr>

                @foreach($users as $user)
                    <?php $sumTaraz = 0; $sumLesson = 0; $sumCoherence = 0; ?>
                    <tr style="cursor: pointer" onclick="document.location.href = '{{route('A3', ['quiz_id' => $quizId, 'uId' => $user->uId, 'backURL' => 'A5'])}}'">
                        <td><center>{{$user->name}}</center></td>
                        <td><center>{{$user->city}}</center></td>
                        <td><center>{{$user->state}}</center></td>
                        @foreach($user->lessons as $itr)
                            <?php
                                if($itr->coherence == 0) {
                                    $sumTaraz += $itr->taraz;
                                    $sumLesson += $itr->percent;
                                    $sumCoherence += 1;
                                }
                                else {
                                    $sumTaraz += $itr->taraz * $itr->coherence;
                                    $sumLesson += $itr->percent * $itr->coherence;
                                    $sumCoherence += $itr->coherence;
                                }
                            ?>
                            <td><center style="direction: ltr">{{$itr->percent}}</center></td>
                        @endforeach
                        @if($sumCoherence != 0)
                            <td><center style="direction: ltr">{{round(($sumLesson / $sumCoherence), 0)}}</center></td>
                            <td><center style="direction: ltr">{{round(($sumTaraz / $sumCoherence), 0)}}</center></td>
                        @else
                            <td><center style="direction: ltr">{{round(($sumLesson), 0)}}</center></td>
                            <td><center style="direction: ltr">{{round(($sumTaraz), 0)}}</center></td>
                        @endif
                        <td><center>{{$user->cityRank}}</center></td>
                        <td><center>{{$user->stateRank}}</center></td>
                        <td><center>{{$user->rank}}</center></td>
                    </tr>
                @endforeach
            </table>
        </div>
    </center>
@stop