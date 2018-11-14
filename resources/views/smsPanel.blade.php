@extends('layouts.menuBar')

@section('reminder')

    <div class="col-xs-12" style="margin-top: 20px">

        <center class="col-xs-12">

            <div class="col-xs-12" style="margin: 20px">
                <label for="qId">آزمون مورد نظر</label>
                <select id="qId">
                    @foreach($quizes as $itr)
                        <option value="{{$itr->id}}">{{$itr->QN}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-xs-12">
                <label>متن مورد نظر</label>
                <textarea style="width: 400px; height: 300px" maxlength="1000" placeholder="حداکثر 1000 کاراکتر" id="msg"></textarea>
            </div>

            <div style="margin-top: 10px">
                <input style="width: 200px" type="submit" value="ارسال پیامک" onclick="sendSMS()" class="btn btn-success">
                <p style="margin: 7px" class="warning_color" id="error"></p>
            </div>

        </center>
    </div>
    
    <script>
        
        function sendSMS() {

            $("#error").empty().append('درخواست شما ثبت گردید');

            $.ajax({
                type: 'post',
                url: '{{route('sendSMSToUsers')}}',
                data: {
                    'qId': $("#qId").val(),
                    'msg': $("#msg").val()
                },
                success: function (response) {
                    alert(response);
                }
            });

        }
        
    </script>
@stop