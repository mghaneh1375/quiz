@extends('layouts.menuBar')

@section('title')
    ساخت جدول تراز آزمون
@stop

@section('extraLibraries')
    <script>

        var quiz_id = "{{$quizId}}";
        var qEntryIds = {!! json_encode($qEntryIds) !!};
        var percent = 0;
        var unit;

        $(document).ready(function () {
		
            unit = qEntryIds.length;

            for (i = 0; i < unit; i++) {
                calcTaraz(qEntryIds[i]);
            }
        });
        
        function showPercent() {
            $("#progressPercent").text(percent + "%");
            $("#progressBar").width(((percent / 100) * 400) + "px");
        }
        
        function calcTaraz(qEntryId) {
            $.ajax({
                type: 'post',
                url: '{{route('calcTaraz')}}',
                data: {
                    qEntryId: qEntryId
                },
                success: function (response) {
                    if(response == "ok") {
                        percent += 100 / unit;
                        if(percent == 100) {
                            $("#progressText").text("جدول تراز برای آزمون مورد نظر ساخته شد");
                        }
                        showPercent();
                    }
                }
            });
        }

    </script>
@stop

@section('reminder')
    <center style="margin-top: 130px" id="main">
        <div id="progressText">
            <p>در حال گرفتن اطلاعات</p>
        </div>
        <div style="border: 3px solid black; width: 406px; height: 20px; background-color: transparent">
            <div id="progressBar" style="background-color: #00aa00; width: 0; height: 14px; float: left"></div>
        </div>
        <p id="progressPercent" style="margin-top: 5px">0%</p>
    </center>
@stop
