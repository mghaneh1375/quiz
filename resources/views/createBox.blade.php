@extends('layouts.menuBar')

@section('title')
    ایجاد جعبه ی جدید
@stop

@section('extraLibraries')
    <script src = {{URL::asset("js/ajaxHandler.js") }}></script>

    <script>

        function getTotalQuestion(val) {

            sId = $("#subjectId_" + val).val();
            cId = $("#compassId_" + val).val();
            level = $("#grade_" + val).val();

            getTotalQ(sId, cId, level);
        }
    </script>
@stop

@section('reminder')

    <script>
        var numQuestions = 0;
        var from;
        var to;

        $(document).ready(function () {

            from = parseInt($("#from").val(), 10);
            to = parseInt($("#to").val(), 10);
            numQuestions = to - from + 1;
            subjectIds = [];
            compassIds = [];
            showIndividualQuestions();

            for(i = 0; i < numQuestions; i++) {
                subjectIds[i] = 'subjectId_' + (i + from);
                compassIds[i] = 'compassId_' + (i + from);
            }


            changeDegree($("#degreeId").find(":selected").val(), 'lessonId', subjectIds);
            getCompasses(compassIds);
        });

        function submitNewBox() {

            boxName = $("#boxName").val();

            if(boxName == ""){
                alert('لطفا نام جعبه را مشخص نمایید');
                return;
            }

            from = parseInt($("#from").val(), 10);
            to = parseInt($("#to").val(), 10);
            numQuestions = to - from + 1;
            subjectIds = [];
            grades = [];
            compassIds = [];

            for(i = 0; i < numQuestions; i++) {
                subjectIds[i] = $('#subjectId_' + (from + i) + ' :selected').val();
                grades[i] = $('#grade_' + (from + i) + ' :selected').val();
                compassIds[i] = $('#compassId_' + (from + i) + ' :selected').val();
            }

            addBox(from, to, subjectIds, grades, compassIds, boxName);

        }
        
        function fromOrToChanged() {
            from = parseInt($("#from").val(), 10);
            to = parseInt($("#to").val(), 10);
            numQuestions = to - from + 1;
            showIndividualQuestions();
            subjectIds = [];
            compassIds = [];

            for(i = 0; i < numQuestions; i++) {
                subjectIds[i] = 'subjectId_' + (i + from);
                compassIds[i] = 'compassId_' + (i + from);
            }
            changeLesson($("#lessonId :selected").val(), subjectIds);
            getCompasses(compassIds);


        }

        function showIndividualQuestions() {

            $("#individualQuestions").empty();
            newElement = "";

            for(i = 0; i < numQuestions; i++) {
                newElement += "<div style='width: 100%; margin-top: 10px; float: right'>";
                newElement += "<span>سوال " + (from + i) +  "</span>";
                newElement += "<select style='margin-right: 10px' id='subjectId_" + (from + i) + "'></select>";
                newElement += "<select style='margin-right: 10px' id='compassId_" + (from + i) + "'></select>";
                newElement += "<select style='margin-right: 10px' id ='grade_" + (from + i) + "'><option value='1'>آسان</option><option value='2'>متوسط</option><option value='3'>دشوار</option></select>";
                newElement += "<button onclick='getTotalQuestion(" + (from + i) + ")' style='margin-right: 10px'>مشاهده ی تعداد کل سوالات</button>";
                newElement += "</div>";
            }

            $("#individualQuestions").append(newElement);
            $("#individualQuestions").show();
        }

    </script>
    <center style="margin-top: 100px">
        <h3>ایجاد جعبه ی جدید</h3>
        <div class="line"></div>
        <div class="myRegister">
            <div class="row data" style="margin-top: 10px">

                <div class="col-xs-12">
                    <label>
                        <span>نام جعبه</span>
                        <input type="text" id="boxName">
                    </label>
                </div>

                <div class="col-xs-12">
                    <label>
                        <span>پایه ی تحصیلی</span>
                        <select id="degreeId" onchange="changeDegree(this.value, 'lessonId', subjectIds)">
                            @foreach($degrees as $degree)
                                <option value="{{$degree->id}}">{{$degree->dN}}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <div class="col-xs-12">
                    <label>
                        <span>درس</span>
                        <select id="lessonId" onchange="changeLesson(this.value, subjectIds)">
                        </select>
                    </label>
                </div>
                <div class="col-xs-12">
                    <label>
                        <span>از سوال</span>
                        <input type="number" onchange="fromOrToChanged()" value="1" id="from" min="1">
                    </label>
                </div>
                <div class="col-xs-12">
                    <label>
                        <span>تا سوال</span>
                        <input type="number" onchange="fromOrToChanged()" id="to" value="2" min="2">
                    </label>
                </div>
            </div>
        </div>

        <div id="individualQuestions">
        </div>

        <div>
            <button class="MyBtn" style="width: auto; margin-top: 10px" onclick="submitNewBox()"><span>اضافه کردن جعبه ی جدید</span></button>
        </div>
    </center>

@stop