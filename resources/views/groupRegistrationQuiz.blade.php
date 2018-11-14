@extends('layouts.menuBar')

@section('extraLibraries')

    <style>

        .blink {
            color: black;
            -webkit-animation: blink 3s step-end infinite;
            animation: blink 3s step-end infinite
        }
        @-webkit-keyframes blink {
            80% { opacity: 0 }
        }

        @keyframes blink {
            80% { opacity: 0 }
        }

        .uploadBtn {
            width: 30%;
            margin: 0;
        }

        @media only screen and (max-width: 767px) {

            .uploadBtn {
                width: 45%;
                margin: 0;
            }
        }
    </style>

    <script>
        function displayName() {
            $("#addFile").empty().append($("#group").val());
        }
    </script>
@stop

@section('reminder')

    <div class="col-xs-12" style="margin-top: 20px">
        <center>
            <h4 class="blink">لطفا فایل اکسل زیر را دانلود کرده و آن را به دقت پر نمایید و سپس آن را آپلود نمایید.</h4>

            <div style="margin-top: 40px">
                <div style="width: 200px" class="btn btn-warning"><a target="_blank" href="{{URL::asset('nemone_form2.xlsx')}}" download>دریافت نمونه فایل اکسل</a></div>
            </div>

            <form method="post" action="{{route('doGroupRegistryQuiz')}}" style="margin-top: 10px" enctype="multipart/form-data">
                {{csrf_field()}}
                <input id="group" onchange="displayName()" name="group" type="file" style="display: none">

                <div style="margin: 20px">
                    <label for="qId">آزمون مورد نظر</label>
                    <select name="qId" id="qId">
                        @foreach($quizes as $itr)
                            <option value="{{$itr->id}}">{{$itr->QN}}</option>
                        @endforeach
                    </select>
                </div>

                <label for="group" class="uploadBtn" style="width: 200px">
                    <div id="addFile" class="btn btn-primary" style="width: 100%;">آپلود فایل اکسل</div>
                </label>

                <div style="margin-top: 10px">
                    <input style="width: 200px" type="submit" value="ارسال فایل" class="btn btn-success">
                </div>
            </form>

            <div style="margin-top: 10px">
                @if($err != "")
                    <p class="warning-color">
                        <span>بجز موارد زیر بقیه به درستی به آزمون افزوده شدند </span><br/>
                        {!! html_entity_decode($err) !!}
                    </p>
                @endif
            </div>

        </center>
    </div>
@stop