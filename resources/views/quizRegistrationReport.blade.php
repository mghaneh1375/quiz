@extends('layouts.menuBar')



@section('title')

    گزارش گیری ثبت نام در آزمون

@stop



@section('extraLibraries')

    <style>

        td > center{

            padding: 5px;

            min-width: 100px;

        }

        .filter {
            display: inline-block;
            padding: 10px;
        }

    </style>
@stop



@section('reminder')

    <center style="margin-top: 130px">

        <div class="line"></div>

        <div style="margin: 10px"><span>تعداد رکورد های پیدا شده:</span><span>&nbsp;</span><span id="records"></span></div>

        <div><button onclick="document.location.href = '{{route('registrationReportExcel')}}'" class="btn btn-primary">دانلود فایل اکسل</button></div>

        <div style="overflow-x: auto">

            <div class="filter">
                <label for="subscription">نوع عضویت</label>
                <select onchange="fetchData()" id="subscription">
                    <option value="-1">انتخاب کنید</option>
                    <option value="1">قرارگاه ملی جدید</option>
                    <option value="2">قرارگاه ملی قدیم</option>
                    <option value="3">قرارگاه استانی</option>
                    <option value="4">سایر</option>
                </select>
            </div>

            <div class="filter">
                <label for="state">استان</label>
                <select onchange="fetchData()" id="state">
                    <option value="-1">انتخاب کنید</option>
                    @foreach($states as $state)
                        <option value="{{$state->id}}">{{$state->name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter">
                <label for="sex">جنسیت</label>
                <select onchange="fetchData()" id="sex">
                    <option value="-1">انتخاب کنید</option>
                    <option value="0">دختر</option>
                    <option value="1">پسر</option>
                </select>
            </div>

            <table id="data" style="margin-top: 10px; width: 100%"></table>

        </div>

    </center>

    <script>

        var state, sex, subscription;

        $(document).ready(function () {
            fetchData();
        });

        function fetchData() {

            sex = $("#sex").val();
            subscription = $("#subscription").val();
            state = $("#state").val();

            $.ajax({
                type: 'post',
                url: '{{route('fetchStudentsInQuiz')}}',
                data: {
                    'state': state,
                    'sex': sex,
                    'subscription': subscription,
                    'quizId': '{{$quiz->id}}'
                },
                success: function (response) {

                    if(response.length > 0) {

                        response = JSON.parse(response);

                        $("#records").empty().append(response.length);

                        var newElement = "";

                        newElement += '<tr>';
                        newElement += '<td><center>نام</center></td>';
                        newElement += '<td><center>نام کاربری</center></td>';
                        newElement += '<td><center>شهر</center></td>';
                        newElement += '<td><center>استان</center></td>';
                        newElement += '<td><center>جنسیت</center></td>';
                        newElement += '<td><center>شماره همراه</center></td>';
                        newElement += '<td><center>نوع عضویت</center></td>';
                        newElement += '</tr>';

                        for(var i = 0; i < response.length; i++) {
                            newElement += "<tr>";
                            newElement += "<td><center>" + response[i].name + "</center></td>";
                            newElement += "<td><center>" + response[i].username + "</center></td>";
                            newElement += "<td><center>" + response[i].cityName + "</center></td>";
                            newElement += "<td><center>" + response[i].stateName + "</center></td>";
                            newElement += "<td><center>" + response[i].sex_id + "</center></td>";
                            newElement += "<td><center>" + response[i].phone_num + "</center></td>";
                            newElement += "<td><center>" + response[i].subscription + "</center></td>";
                            newElement += "</tr>";
                        }

                        $("#data").empty().append(newElement);

                    }

                }
            });

        }

    </script>

@stop