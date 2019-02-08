@extends('layouts.menuBar')



@section('title')

    گزارش گیری ثبت نام

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

        <div style="overflow-x: auto">

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
                <label for="grade">مقطع</label>
                <select onchange="fetchData()" id="grade">
                    <option value="-1">انتخاب کنید</option>
                    @foreach($grades as $grade)
                        <option value="{{$grade->id}}">{{$grade->dN}}</option>
                    @endforeach
                </select>
            </div>

            <table id="data" style="margin-top: 10px; width: 100%"></table>

        </div>

    </center>

    <script>

        var state, grade, subscription;

        $(document).ready(function () {
            fetchData();
        });

        function fetchData() {

            state = $("#state").val();
            grade = $("#grade").val();
            subscription = $("#subscription").val();

            $.ajax({
                type: 'post',
                url: '{{route('fetchStudents')}}',
                data: {
                    'state': state,
                    'grade': grade,
                    'subscription': subscription
                },
                success: function (response) {

                    if(response.length > 0) {

                        response = JSON.parse(response);

                        newElement = "";

                        newElement += '<tr>';
                        newElement += '<td><center>نام</center></td>';
                        newElement += '<td><center>نام کاربری</center></td>';
                        newElement += '<td><center>شهر</center></td>';
                        newElement += '<td><center>پایه تحصیلی</center></td>';
                        newElement += '<td><center>شماره همراه</center></td>';
                        newElement += '<td><center>نوع عضویت</center></td>';
                        newElement += '</tr>';

                        for(i = 0; i < response.length; i++) {
                            newElement += "<tr>";
                            newElement += "<td><center>" + response[i].name + "</center></td>";
                            newElement += "<td><center>" + response[i].username + "</center></td>";
                            newElement += "<td><center>" + response[i].city + "</center></td>";
                            newElement += "<td><center>" + response[i].grade + "</center></td>";
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