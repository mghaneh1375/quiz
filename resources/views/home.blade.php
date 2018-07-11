@extends('layouts.menuBar')

@section('reminder')

    @if(isset($msg))
        <center>
            <p class="warning_color" style="margin-top: 50px">{{$msg}}</p>
        </center>
    @endif

@stop

@section('title')
    خانه
@stop