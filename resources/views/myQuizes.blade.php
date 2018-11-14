@extends('layouts.menuBar')



@section('reminder')

    <center class="col-xs-12">

        @if(count($quizes) == 0)
            <p class="single-error warning_color">آزمونی جهت ورود به آن موجود نیست</p>
        @else
            <table>

                <tr>
                    <td><center>نام آزمون</center></td>
                    <td><center>مدت آزمون</center></td>
                    <td><center>زمان آغاز آزمون</center></td>
                    <td><center>زمان پایان آزمون</center></td>
                    <td><center>عملیات</center></td>
                </tr>

                @foreach($quizes as $itr)

                    <tr>
                        <td><center>{{$itr->QN}}</center></td>
                        <td><center>{{$itr->tL}}</center></td>
                        <td><center>{{$itr->sDate}} ساعت {{$itr->sTime}}</center></td>
                        <td><center>{{$itr->eDate}} ساعت {{$itr->eTime}}</center></td>
                        @if($itr->quizEntry == 1)
                            <td><center><button class="MyBtn MyBtn-yellow" onclick="redirect('{{route('doQuiz', ['quizId' => $itr->id, 'mode' => true])}}')">ورود به آزمون</button></center></td>
                        @elseif($itr->quizEntry == -2)
                            <td><center><button class="MyBtn MyBtn-yellow" onclick="redirect('{{route('buySelectedQuiz', ['quizId' => $itr->id])}}')">مرور آزمون</button></center></td>
                        @else
                            <td><center>زمان آزمون هنوز فرا نرسیده است</center></td>
                        @endif
                    </tr>

                @endforeach

            </table>
        @endif

    </center>


    <span id="notice" class="ui_overlay item hidden" style="position: fixed; left: 30%; right: 30%; top: 60px; bottom: auto">
        <p style="padding-top: 20px;"><strong>آیا از ورود به آزمون مورد نظر اطمینان دارید؟</strong></p>
        <center>
            <a id="redirector" class="MyBtn MyBtn-green" >بله</a>
            <span style="cursor: pointer" onclick="$('.dark').addClass('hidden'); $('#notice').addClass('hidden')" class="MyBtn MyBtn-yellow">انصراف</span>
        </center>
    </span>



    <script>

        function redirect(url) {
            $("#redirector").attr('href', url);
            $('.dark').removeClass('hidden');
            $('#notice').removeClass('hidden');
        }

    </script>


@stop



@section('title')

    خانه

@stop