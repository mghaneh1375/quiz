@extends('layouts.menuBar')



@section('title')
    تعیین عکس پروفایل
@stop



@section('reminder')

    <div class="col-xs-12" style="margin-top: 100px">

        <form method="post" action="{{route('setProfilePic')}}" enctype="multipart/form-data">

            {{csrf_field()}}

            <center>
                <h3>تصویر فعلی</h3>
                <img src="{{$pic}}" width="200px">
                <input name="pic" type="file" style="margin-top: 20px">

                <input class="btn btn-success" type="submit" value="تایید" style="margin-top: 10px">
            </center>
        </form>

    </div>

@stop