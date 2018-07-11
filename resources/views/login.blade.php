<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.mainLibraries')
    <title>ورود</title>
</head>

<body class="main">
    <div class="container" style="margin-top: 130px">
        <center>
            <h3>
                ورود
            </h3>
        </center>
        <div class="line"></div>
        <div class="myRegister">
            <center class="data">
                <form method="post" action="{{URL('login')}}">
                    <div class="row">
                        <div class="col-xs-12">
                            <label>
                                <span>نام کاربری</span>
                                <input type="text" name="username" required autofocus maxlength="40">
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>پسورد</span>
                                <input type="password" required name="password">
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <center class="warning_color">{{$msg}}</center>
                            <input type="submit" class="MyBtn" style="margin-top: 10px; width: auto" name="login" value="ورود">
                        </div>
                    </div>
                </form>
            </center>
        </div>
    </div>
</body>