@extends('layouts.menuBar')


@section('title')
    گزارشات
@stop

@section('extraLibraries')
    <script>

        function getReport() {
            document.location.href = '{{route('home')}}' + '/reports/' + $("#quiz_id").val();
        }
    </script>
@stop

@section('reminder')

    <center style="margin-top: 130px">
        <div>
            <h3>آزمون مورد نظر خود را انتخاب نمایید</h3>
            <div class="line"></div>
        </div>
        <div class="myRegister">
            <div class="data row">

                <div class="col-xs-12">
                    <button class="btn btn-primary" onclick="document.location.href = '{{route('surveyReport')}}'">نتایج نظرسنجی</button>
                </div>

                <div class="col-xs-12">

                    <label>
                        <span>آزمون مورد نظر</span>
                        @if(count($quizes) == 0)
                            <p class="warning_color" style="margin-top: 10px">آزمونی جهت نمایش وجود ندارد</p>
                        @else
                            <select id="quiz_id">
                                @foreach($quizes as $quiz)
                                    <option value="{{$quiz->id}}">{{$quiz->QN}}</option>
                                @endforeach
                            </select>
                        @endif
                    </label>
                </div>
                <div class="col-xs-12">
                    <input type="submit" class="MyBtn" style="width: auto" onclick="getReport()" value="تایید">
                </div>
            </div>
        </div>
    </center>
@stop