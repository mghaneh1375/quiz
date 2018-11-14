@extends('layouts.menuBar')

@section('title')
    گزارشات
@stop

@section('extraLibraries')
    <style>
        .hidden {
            display: none;
        }
    </style>


    <script>

        var selectedPath;
        var getLessonsDir = '{{route('getQuizLessons')}}';
        var getStatesDir = '{{route('getQuizStates')}}';
        var getCitiesDir = '{{route('getQuizCities')}}';
        var quiz_id = '{{$qId}}';

        function showLessonPopUp(path) {

            selectedPath = path;

            $.ajax({
                type: 'post',
                url: getLessonsDir,
                data: {
                    'qId': quiz_id
                },
                success: function (response) {

                    response = JSON.parse(response);

                    newElement = "<select style='margin: 20px' id='lessonId'>";

                    for(i = 0; i < response.length; i++) {
                        newElement += "<option value='" + response[i].id + "'>" + response[i].nameL + "</option>";
                    }

                    newElement += "</select>";

                    hideElement();
                    $("#lessonsPrompt").append(newElement);
                    $("#lessonContainer").removeClass('hidden');
                }
            });
        }
        
        function showStatePopUp(path) {

            selectedPath = path;

            $.ajax({
                type: 'post',
                url: getStatesDir,
                data: {
                    'qId': quiz_id
                },
                success: function (response) {

                    response = JSON.parse(response);

                    newElement = "<select style='margin: 20px' id='stateId'>";

                    for(i = 0; i < response.length; i++) {
                        newElement += "<option value='" + response[i].stateId + "'>" + response[i].stateName + "</option>";
                    }

                    newElement += "</select>";

                    hideElement();
                    $("#statesPrompt").append(newElement);
                    $("#stateContainer").removeClass('hidden');
                }
            });
        }

        function showCityPopUp(path) {

            selectedPath = path;

            $.ajax({
                type: 'post',
                url: getCitiesDir,
                data: {
                    'qId': quiz_id
                },
                success: function (response) {

                    response = JSON.parse(response);

                    newElement = "<select style='margin: 20px' id='cityId'>";

                    for(i = 0; i < response.length; i++) {
                        newElement += "<option value='" + response[i].cityId + "'>" + response[i].cityName + "</option>";
                    }

                    newElement += "</select>";

                    hideElement();
                    $("#citiesPrompt").append(newElement);
                    $("#cityContainer").removeClass('hidden');
                }
            });
        }

        function showReport() {
            if($("#lessonId").val() != null) {
                document.location.href = selectedPath + quiz_id + "/" + $("#lessonId").val();
                return;
            }
            if($("#stateId").val() != null) {
                document.location.href = selectedPath + quiz_id + "/" + $("#stateId").val();
                return;
            }
            if($("#cityId").val() != null) {
                document.location.href = selectedPath + quiz_id + "/" + $("#cityId").val();
                return;
            }
        }

        function hideElement() {
            $("#statesPrompt").empty();
            $("#lessonsPrompt").empty();
            $(".item").addClass('hidden');
        }

    </script>

    <style>
        .ui_overlay {
            z-index: 10004;
            display: inline-block;
            padding: 48px;
            direction: rtl;
            background-color: #fff;
            box-shadow: 0 3px 12px 0 rgba(0, 0, 0, 0.25);
            box-sizing: border-box;
        }

        .ui_close_x:before {
            cursor: pointer;
            margin-right: -30px;
            position: absolute;
            font-size: 28px;
            line-height: 36px;
            color: #00AF87;
            content: "\00d7";
        }
    </style>
@stop

@section('reminder')
    <center style="margin-top: 100px;" class="row">
        <div class="col-xs-12">
            <a onclick="showLessonPopUp('{{route('home')}}' + '/questionAnalysis/')" style="color: #ffffff;"><button style="min-width: 350px; max-width: 350px;" class="MyBtn MyBtn-blue">گزارش تحلیل سوالات آزمون</button></a>
        </div>
        <div class="col-xs-12">
            <a onclick="showLessonPopUp('{{route('home')}}' + '/questionDiagramAnalysis/')" style="color: #ffffff;"><button style="min-width: 350px; max-width: 350px;" class="MyBtn MyBtn-blue">گزارش تحلیل سوالات آزمون با کمک منحنی سوال</button></a>
        </div>
        <div class="col-xs-12">
            <a onclick="showStatePopUp('{{route('home')}}' + '/report1/')" style="color: #ffffff;"><button style="min-width: 350px; max-width: 350px;" class="MyBtn MyBtn-blue">گزارشات به تفکیک جنسیت در استان</button></a>
        </div>
        <div class="col-xs-12">
            <a onclick="showStatePopUp('{{route('home')}}' + '/report2/')" style="color: #ffffff;"><button style="min-width: 350px; max-width: 350px;" class="MyBtn MyBtn-blue">گزارشات تجزیه و تحلیل نمرات دروس به تفکیک جنسیت</button></a>
        </div>
        <div class="col-xs-12">
            <a onclick="showCityPopUp('{{route('home')}}' + '/report3/')" style="color: #ffffff;"><button style="min-width: 350px; max-width: 350px;" class="MyBtn MyBtn-blue">درصد پاسخ به هر گزینه در هر سوال به تفکیک شهر</button></a>
        </div>
        <div class="col-xs-12">
            <a onclick="document.location.href = '{{route('A1', ['quiz_id' => $qId])}}'" style="color: #ffffff;"><button style="min-width: 350px; max-width: 350px;" class="MyBtn MyBtn-blue">نمای کلی آزمون</button></a>
        </div>
        <div class="col-xs-12">
            <a onclick="document.location.href = '{{route('A2', ['quiz_id' => $qId])}}'" style="color: #ffffff;"><button style="min-width: 350px; max-width: 350px;" class="MyBtn MyBtn-blue">وضعیت شهر های شرکت کننده</button></a>
        </div>
        <div class="col-xs-12">
            <a onclick="document.location.href = '{{route('preA3', ['quiz_id' => $qId])}}'" style="color: #ffffff;"><button style="min-width: 350px; max-width: 350px;" class="MyBtn MyBtn-blue">کارنامه تفصیلی دانش آموزان</button></a>
        </div>
        <div class="col-xs-12">
            <a onclick="document.location.href = '{{route('A4', ['quiz_id' => $qId])}}'" style="color: #ffffff;"><button style="min-width: 350px; max-width: 350px;" class="MyBtn MyBtn-blue">پراکندگی درصد شهرها</button></a>
        </div>
        <div class="col-xs-12">
            <a onclick="document.location.href = '{{route('A5', ['quiz_id' => $qId])}}'" style="color: #ffffff;"><button style="min-width: 350px; max-width: 350px;" class="MyBtn MyBtn-blue">کارنامه کلی دانش آموزان</button></a>
        </div>
        <div class="col-xs-12">
            <a onclick="document.location.href = '{{route('A6', ['quiz_id' => $qId])}}'" style="color: #ffffff;"><button style="min-width: 350px; max-width: 350px;" class="MyBtn MyBtn-blue">گزارش درس به درس</button></a>
        </div>
        <div class="col-xs-12">
            <a onclick="document.location.href = '{{route('A7', ['quiz_id' => $qId])}}'" style="color: #ffffff;"><button style="min-width: 350px; max-width: 350px;" class="MyBtn MyBtn-blue">پراکندگی نمرات هر درس</button></a>
        </div>
    </center>

    <span id="lessonContainer" class="ui_overlay item hidden" style="position: fixed; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">درس مورد نظر</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
            <div class="body_text">
                <div id="lessonsPrompt"></div>
                <div class="submitOptions">
                    <button onclick="showReport()" class="btn btn-success">تایید</button>
                    <input type="submit" onclick="hideElement()" value="خیر" class="btn btn-default">
                </div>
            </div>
    </span>

    <span id="stateContainer" class="ui_overlay item hidden" style="position: fixed; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">استان مورد نظر</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
            <div class="body_text">
                <div id="statesPrompt"></div>
                <div class="submitOptions">
                    <button onclick="showReport()" class="btn btn-success">تایید</button>
                    <input type="submit" onclick="hideElement()" value="خیر" class="btn btn-default">
                </div>
            </div>
    </span>

    <span id="cityContainer" class="ui_overlay item hidden" style="position: fixed; left: 40%; right: auto; top: 174px; bottom: auto">
        <div class="header_text">شهر مورد نظر</div>
        <div onclick="hideElement()" class="ui_close_x"></div>
            <div class="body_text">
                <div id="citiesPrompt"></div>
                <div class="submitOptions">
                    <button onclick="showReport()" class="btn btn-success">تایید</button>
                    <input type="submit" onclick="hideElement()" value="خیر" class="btn btn-default">
                </div>
            </div>
    </span>
@stop