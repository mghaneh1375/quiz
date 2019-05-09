@extends('layouts.menuBar')

@section('title')
    نظرسنجی
@stop

@section('reminder')

    <script>
        var q1 = 1, q2 = 1, q3 = 1, q4 = 1, q5 = 1, q6 = 1;
    </script>

    <center class="col-xs-12" style="margin-top: 100px">
        
        <h4 style="line-height: 1.6; border: 2px solid #fd3b13; padding: 10px; border-radius: 7px; text-align: right;">دانش آموز عزیز خواهشمند است به جهت ارتقا سطح کیفی آزمون های برخط موسسه علمی آینده سازان پیش از مشاهده کارنامه پرسشنامه زیر را تکمیل نمایید</h4>
        
            <div class="col-xs-12" style="margin-top: 10px">
                <p>سطح میزان کیفیت سوالات آزمون</p>
                <span onclick="q1 = 1; $('.q1').removeClass('btn-success').addClass('btn-default'); $(this).removeClass('btn-default').addClass('btn-success')" class="btn btn-default q1">عالی</span>
                <span onclick="q1 = 2; $('.q1').removeClass('btn-success').addClass('btn-default'); $(this).removeClass('btn-default').addClass('btn-success')" class="btn btn-default q1">خوب</span>
                <span onclick="q1 = 3; $('.q1').removeClass('btn-success').addClass('btn-default'); $(this).removeClass('btn-default').addClass('btn-success')" class="btn btn-default q1">متوسط</span>
                <span onclick="q1 = 4; $('.q1').removeClass('btn-success').addClass('btn-default'); $(this).removeClass('btn-default').addClass('btn-success')" class="btn btn-default q1">ضعیف</span>
            </div>

            <div class="col-xs-12" style="margin-top: 10px">
                <p>میزان رضایت شما از نحوه پشتیبانی</p>
                <span onclick="q2 = 1; $('.q2').removeClass('btn-success').addClass('btn-default'); $(this).removeClass('btn-default').addClass('btn-success')" class="btn btn-default q2">عالی</span>
                <span onclick="q2 = 2; $('.q2').removeClass('btn-success').addClass('btn-default'); $(this).removeClass('btn-default').addClass('btn-success')" class="btn btn-default q2">خوب</span>
                <span onclick="q2 = 3; $('.q2').removeClass('btn-success').addClass('btn-default'); $(this).removeClass('btn-default').addClass('btn-success')" class="btn btn-default q2">متوسط</span>
                <span onclick="q2 = 4; $('.q2').removeClass('btn-success').addClass('btn-default'); $(this).removeClass('btn-default').addClass('btn-success')" class="btn btn-default q2">ضعیف</span>
            </div>

            <div class="col-xs-12" style="margin-top: 10px">
                <p>نحوه شرکت در آزمون</p>
                <span onclick="q3 = 1; $('.q3').removeClass('btn-success').addClass('btn-default'); $(this).removeClass('btn-default').addClass('btn-success')" class="btn btn-default q3">رایانه</span>
                <span onclick="q3 = 2; $('.q3').removeClass('btn-success').addClass('btn-default'); $(this).removeClass('btn-default').addClass('btn-success')" class="btn btn-default q3">موبایل</span>
                <span onclick="q3 = 3; $('.q3').removeClass('btn-success').addClass('btn-default'); $(this).removeClass('btn-default').addClass('btn-success')" class="btn btn-default q3">تبلت</span>
            </div>

            <div class="col-xs-12" style="margin-top: 10px">
                <p>میزان رضایت مندی شما از مجموع برگزاری این آزمون</p>
                <span onclick="q4 = 1; $('.q4').removeClass('btn-success').addClass('btn-default'); $(this).removeClass('btn-default').addClass('btn-success')" class="btn btn-default q4">عالی</span>
                <span onclick="q4 = 2; $('.q4').removeClass('btn-success').addClass('btn-default'); $(this).removeClass('btn-default').addClass('btn-success')" class="btn btn-default q4">خوب</span>
                <span onclick="q4 = 3; $('.q4').removeClass('btn-success').addClass('btn-default'); $(this).removeClass('btn-default').addClass('btn-success')" class="btn btn-default q4">متوسط</span>
                <span onclick="q4 = 4; $('.q4').removeClass('btn-success').addClass('btn-default'); $(this).removeClass('btn-default').addClass('btn-success')" class="btn btn-default q4">ضعیف</span>
            </div>

            <div class="col-xs-12" style="margin-top: 10px">
                <p>میزان رضایت مندی شما از نحوه اطلاع رسانی</p>
                <span onclick="q5 = 1; $('.q5').removeClass('btn-success').addClass('btn-default'); $(this).removeClass('btn-default').addClass('btn-success')" class="btn btn-default q5">عالی</span>
                <span onclick="q5 = 2; $('.q5').removeClass('btn-success').addClass('btn-default'); $(this).removeClass('btn-default').addClass('btn-success')" class="btn btn-default q5">خوب</span>
                <span onclick="q5 = 3; $('.q5').removeClass('btn-success').addClass('btn-default'); $(this).removeClass('btn-default').addClass('btn-success')" class="btn btn-default q5">متوسط</span>
                <span onclick="q5 = 4; $('.q5').removeClass('btn-success').addClass('btn-default'); $(this).removeClass('btn-default').addClass('btn-success')" class="btn btn-default q5">ضعیف</span>
            </div>

            <div class="col-xs-12" style="margin-top: 10px">
                <p>نحوه آشنایی با زمان برگزاری آزمون های آزمایشی آنلاین(برخط) آینده سازان از چه طریق بوده است؟</p>
                <span onclick="q6 = 1; $('.q6').removeClass('btn-success').addClass('btn-default'); $(this).removeClass('btn-default').addClass('btn-success')" class="btn btn-default q6">اطلاع رسانی مسئول اتحادیه استان</span>
                <span onclick="q6 = 2; $('.q6').removeClass('btn-success').addClass('btn-default'); $(this).removeClass('btn-default').addClass('btn-success')" class="btn btn-default q6">وب سایت موسسه های آینده سازان</span>
                <span onclick="q6 = 3; $('.q6').removeClass('btn-success').addClass('btn-default'); $(this).removeClass('btn-default').addClass('btn-success')" class="btn btn-default q6">وب سایت اتحادیه انجمن های اسلامی دانش آموزان</span>
                <span onclick="q6 = 4; $('.q6').removeClass('btn-success').addClass('btn-default'); $(this).removeClass('btn-default').addClass('btn-success')" class="btn btn-default q6">پیام رسان یا کانال های مجازی (سروش، اینستاگرام و ...) موسسه آینده سازان</span>
            </div>

            <div class="col-xs-12" style="margin-top: 10px">
                <input onclick="submitAns()" type="submit" class="btn btn-success" value="تایید و مشاهده کارنامه">
            </div>

    </center>

    <script>
        
        function submitAns() {
            
            $.ajax({
                type: 'post',
                url: '{{route('doSurvey')}}',
                data: {
                    'ans': q1 + "" + q2 + "" + q3 + "" + q4 + "" + q5 + "" + q6,
                    'quiz_id': '{{$quiz_id}}'
                },
                success: function (response) {
                    document.location.href = '{{URL('seeResult')}}';
                }
            });
            
        }
        
    </script>
    
@stop