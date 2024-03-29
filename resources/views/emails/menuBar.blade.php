<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.mainLibraries')
    @yield('extraLibraries')
    <title>@yield('title')</title>
</head>
<body class="main">
<div class="container" style="height: 100vh; margin-top: 10px">
    <div class="row">
        <div class="col-xs-12 col-md-3 col-md-push-9" style="height: auto">
            <center style="cursor: pointer; margin-top: 10px; margin-bottom: 10px">
                <div style="width: 100%">
                    <img style="width: 100%; height: 150px" src={{URL::asset('images/profile.png')}}>
                </div>
            </center>

            <a style="color: black; float: right" data-toggle="tooltip" title="خانه" href="{{URL('home')}}">
                <button type="submit" style="background-color: transparent; border: none">
                    <span style="margin-left: 10%" class="glyphicon glyphicon-home"></span>
                </button>
            </a>
            <a style="color: black; float: right" data-toggle="tooltip" title="خروج" href="{{URL('logout')}}">
                <button type="submit" style="background-color: transparent; border: none">
                    <span style="margin-left: 10%" class="glyphicon glyphicon-log-out"></span>
                </button>
            </a>

            <?php
                $level = Auth::user()->role;
            ?>

            @if($level == 1)
                <a style="color: black" href="{{URL('createBox')}}"><button type="submit" class="MyBtn">ساخت جعبه ی جدید</button></a>
                <a style="color: black" href="{{URL('seeBoxes')}}"><button type="submit" class="MyBtn">مشاهده ی جعبه های آزمون</button></a>
                <a style="color: black" href="{{URL('quizes')}}"><button type="submit" class="MyBtn">آزمون ها</button></a>
                <a style="color: black" href="{{URL('defineKarname')}}"><button class='MyBtn'>تعریف کارنامه ی آزمون</button></a>
                <a style="color: black" href="{{URL('createTarazTable')}}"><button class='MyBtn'>ساخت جدول ترازآزمون</button></a>
                <a style="color: black" href="{{URL('deleteTarazTable')}}"><button class='MyBtn'>حذف جدول ترازآزمون</button></a>
                <a style="color: black" href="{{URL('QuizStatus')}}"><button class='MyBtn'>وضعیت های آزمون</button></a>
                <a style="color: black" href="{{route('showReport')}}"><button class='MyBtn'>گزارشات</button></a>
            @else
                <a style="color: black" href="{{URL('doQuiz')}}"><button type="submit" class="MyBtn">شرکت در آزمون</button></a>
                <a style="color: black" href="{{URL('seeResult')}}"><button type="submit" class="MyBtn">رویت کارنامه</button></a>
            @endif


        </div>
        <div class="col-xs-12 col-md-9 col-md-pull-3 sideBar">
            @yield('reminder')
        </div>
    </div>
</div>
</body>
</html>