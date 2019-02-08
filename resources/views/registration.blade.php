<!DOCTYPE html>

<html lang="en">

<head>

    @include('layouts.topBar')

    @include('layouts.mainLibraries')

    <title>ورود</title>

</head>

<body class="main">

<div class="container">

    <center>

        <h3 class="title">

            ورود

        </h3>

    </center>

    <div class="line"></div>

    <div class="myRegister">

        <center class="data">

            <div class="row">

                <div class="col-xs-12">

                    <label>

                        <span class="label-span">نام</span>

                        <input type="text" id="first_name" autofocus maxlength="100">

                    </label>

                </div>

                <div class="col-xs-12">

                    <label>

                        <span class="label-span">نام خانوادگی</span>

                        <input type="text" id="last_name" maxlength="100">

                    </label>

                </div>

                <div class="col-xs-12">

                    <label>

                        <span class="label-span">نام پدر</span>

                        <input type="text" id="father_name" autofocus maxlength="100">

                    </label>

                </div>

                <div class="col-xs-12">

                    <label>

                        <span class="label-span">کد ملی</span>

                        <input id="nid" type="tel" maxlength="10">

                    </label>

                </div>

                <div class="col-xs-12">

                    <label>

                        <span class="label-span">شماره همراه</span>

                        <input id="phone_num" type="tel" maxlength="9" minlength="9">

                        <span>&nbsp;-&nbsp;</span>

                        <input type="tel" style="max-width: 40px !important;; min-width: 40px !important;" value="09" disabled>

                    </label>

                </div>

                <div class="col-xs-12">

                    <label>

                        <span class="label-span">استان</span>

                        <select id="states" onchange="changeState()">
                            @foreach($states as $state)
                                <option value="{{$state->pre_phone_code . '_' . $state->id}}">{{$state->name}}</option>
                            @endforeach
                        </select>

                    </label>

                </div>

                <div class="col-xs-12">

                    <label>

                        <span class="label-span">شهر</span>

                        <select id="cities"></select>

                    </label>

                </div>

                <div class="col-xs-12">

                    <label>

                        <span class="label-span">تلفن منزل</span>

                        <input type="tel" id="home_phone" maxlength="8" minlength="8">
                        <span>&nbsp;-&nbsp;</span>
                        <input style="max-width: 70px !important; min-width: 40px !important;" type="tel" disabled id="prePhoneCode">

                    </label>

                </div>

                <div class="col-xs-12">

                    <label>

                        <span class="label-span">پایه تحصیلی</span>

                        <select id="degree">
                            @foreach($degrees as $degree)
                                <option value="{{$degree->id}}">{{$degree->dN}}</option>
                            @endforeach
                        </select>

                    </label>

                </div>

                <div class="col-xs-12">

                    <label>

                        <span class="label-span">جنسیت</span>

                        <select id="sex">
                            <option value="none">انتخاب کنید</option>
                            <option value="0">دختر</option>
                            <option value="1">پسر</option>
                        </select>

                    </label>

                </div>

                <div class="col-xs-12">

                    <label>

                        <span class="label-span">نوع عضویت</span>

                        <select id="subscription">
                            <option value="none">انتخاب کنید</option>
                            <option value="1">عضو قرارگاه ملی جدید هستم(سال تحصیلی 98-97)</option>
                            <option value="2">عضو قرارگاه ملی قدیم هستم(سال تحصیلی 97-96)</option>
                            <option value="3">عضو قرارگاه استانی سال تحصیلی 98-97 هستم</option>
                            <option value="4">سایر</option>
                        </select>

                    </label>

                </div>

                <div class="col-xs-12">

                    <center id="err" class="warning_color"></center>

                    <input onclick="checkNID()" type="submit" class="MyBtn MyBtn-green" style="margin-top: 10px; width: auto" name="register" value="ثبت نام">
                </div>

            </div>

        </center>

    </div>


    <form class="hidden" id="loginForm" method="post" action="{{URL('login')}}">
        {{csrf_field()}}
        <input type="text" name="username" id="usernameLogin" required autofocus maxlength="40">
        <input type="password" required id="passwordLogin" name="password">
    </form>

</div>

<script>

    $(document).ready(function () {
        changeState();
    });

    function changeState() {

        var vals = $("#states").val().split('_');

        $.ajax({
            type: 'post',
            url: '{{route('getCities')}}',
            data: {
                'stateId':vals[1]
            },
            success: function (response) {

                newElement = "";
                response = JSON.parse(response);

                for(i = 0; i < response.length; i++) {
                    newElement += "<option value='" + response[i].id + "'>" + response[i].name + "</option>";
                }

                $("#prePhoneCode").val(vals[0]);
                $("#cities").empty().append(newElement);

            }
        });
        
    }

    function checkNID() {

        $.ajax({
            type: 'post',
            url: '{{route('checkNID')}}',
            data: {
                'NID': $("#nid").val()
            },
            success: function (response) {
                if(response == "nok1")
                    $("#err").empty().append('کد ملی وارد شده معتبر نمی باشد');
                else if(response == "nok2")
                    $("#err").empty().append('کد ملی وارد شده در سامانه موحود است');
                else
                    checkPhoneNum();
            }
        });

    }

    function checkPhoneNum() {

        $.ajax({
            type: 'post',
            url: '{{route('checkPhoneNum')}}',
            data: {
                'phone_num': $("#phone_num").val()
            },
            success: function (response) {
                if(response == "nok")
                    $("#err").empty().append('شماره همراه وارد شده در سامانه موجود است');
                else
                    submitForm();
            }
        });
    }
    
    function submitForm() {

        var sex = $("#sex").val();
        if(sex == "none") {
            $("#err").empty().append('لطفا جنسیت خود را مشخص کنید');
            return;
        }

        var subscription = $("#subscription").val();
        if(subscription == "none") {
            $("#err").empty().append('لطفا نوع عضویت خود را مشخص کنید');
            return;
        }

        var homePhone = $("#home_phone").val();
        if(homePhone.length != 8) {
            $("#err").empty().append('تلفن منزل معتبر نمی باشد');
            return;
        }

        $("#err").empty().append('در حال ارسال اطلاعات');

        $.ajax({

            type: 'post',
            url: '{{route('doRegistration')}}',
            data: {
                'first_name': $("#first_name").val(),
                'last_name': $("#last_name").val(),
                'city_id': $("#cities").val(),
                'nid': $("#nid").val(),
                'sex_id': sex,
                'home_phone': ($("#prePhoneCode").val() + "" + homePhone),
                'degree': $("#degree").val(),
                'subscription': subscription,
                'father_name': $("#father_name").val(),
                'phone_num': $("#phone_num").val()
            },
            success: function (response) {

                if(response == "ok") {
                    $("#usernameLogin").val($("#nid").val());
                    $("#passwordLogin").val($("#phone_num").val());
                    $("#loginForm").submit();
                }
                else if(response == "nok1")
                    $("#err").empty().append('لطفا جنسیت خود را مشخص کنید');
                else if(response == "nok2")
                    $("#err").empty().append('لطفا تمام اطلاعات خود را وارد نمایید');
                else if(response == "nok3")
                    $("#err").empty().append('تلفن منزل معتبر نمی باشد');
                else if(response == "nok4")
                    $("#err").empty().append('تلفن همراه معتبر نمی باشد');
                else
                    $("#err").empty().append('اشکالی در ثبت نام به وجود آمده است');
            }

        });
        
    }

</script>

@include('layouts.footer')

</body>