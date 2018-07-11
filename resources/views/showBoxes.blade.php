@extends('layouts.menuBar')

@section('title')
    نمایش جعبه ها
@stop

@section('extraLibraries')
    <script src="{{URL::asset('js/ajaxHandler.js')}}"></script>

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

    @if($mode == 'see')
        <center style="margin-top: 130px">
            <div class="row">
                <h3>جعبه های موجود</h3>
                @foreach($boxes as $box)
                    <div class="col-xs-12" style="margin-top: 10px">

                        <label style="float: right; margin-right: 5px">
                            <span>نام جعبه</span>
                            <input type="text" disabled value="{{$box->name}}">
                        </label>

                        <label style="margin-right: 5px; float: right">
                            <span>از: </span>
                            <input type="text" disabled value="{{$box->from_}}">
                        </label>

                        <label style="float: right; margin-right: 5px">
                            <span>تا: </span>
                            <input type="text" disabled value="{{$box->to_}}">
                        </label>

                        <button style="margin-right: 15px; float: right" class="btn btn-success" onclick="showBoxItems('{{$box->id}}', 'items')" data-toggle="tooltip" title="نمایش آیتم ها">
                            <span style="margin-left: 10%" class="glyphicon glyphicon-th-list"></span>
                        </button>

                        <button style="float: right; margin-right: 5px" class="btn btn-danger" onclick="deleteSelectedBox('{{$box->id}}')" data-toggle="tooltip" title="حذف جعبه">
                            <span style="margin-left: 10%" class="glyphicon glyphicon-remove"></span>
                        </button>

                        <form style="float: right" method="post" action="{{URL('seeBoxes')}}">
                            {{csrf_field()}}
                            <button style="margin-right: 5px" class="btn btn-info" name="editSelectedBox" value="{{$box->id}}" data-toggle="tooltip" title="ویرایش آیتم ها">
                                <span style="margin-left: 10%" class="glyphicon glyphicon-edit"></span>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            <div class="line"></div>

            <div class="row">
                <h3>آیتم ها</h3>
                <div id="items"></div>
            </div>
        </center>

    @else

        <script>

            var numQuestions = 0;
            var from;
            var to;
            var selectedLesson = "{{$selectedLesson}}";
            var box_id = "{{$box->id}}";

            $(document).ready(function () {

                from = parseInt($("#from").val(), 10);
                to = parseInt($("#to").val(), 10);
                numQuestions = to - from + 1;

                subjectIds = [];
                compassIds = [];
                grades = [];

                showIndividualQuestions();

                for (i = 0; i < numQuestions; i++) {
                    subjectIds[i] = 'subjectId_' + (i + from);
                    compassIds[i] = 'compassId_' + (i + from);
                    grades[i] = 'grade_' + (i + from);
                }

                getCompasses(compassIds);
                changeDegreeWithSelectedLesson($("#degreeId").find(":selected").val(), 'lessonId', subjectIds, compassIds, grades, selectedLesson, box_id);
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

                updateBox(from, to, subjectIds, grades, compassIds, boxName, box_id);

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

                getCompasses(compassIds);
                changeLesson($("#lessonId :selected").val(), subjectIds);
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
        <h3>ویرایش جعبه</h3>
        <div class="line"></div>
        <div class="myRegister">
            <div class="row data" style="margin-top: 10px">

                <div class="col-xs-12">
                    <label>
                        <span>نام جعبه</span>
                        <input type="text" id="boxName" value="{{$box->name}}">
                    </label>
                </div>

                <div class="col-xs-12">
                    <label>
                        <span>پایه ی تحصیلی</span>
                        <select id="degreeId" onchange="changeDegree(this.value, 'lessonId', subjectIds)">
                            @foreach($degrees as $degree)
                                @if($degree->id == $selectedDegree)
                                    <option selected value="{{$degree->id}}">{{$degree->dN}}</option>
                                @else
                                    <option value="{{$degree->id}}">{{$degree->dN}}</option>
                                @endif
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
                        <input type="number" onchange="fromOrToChanged()" value="{{$box->from_}}" id="from" min="1">
                    </label>
                </div>

                <div class="col-xs-12">
                    <label>
                        <span>تا سوال</span>
                        <input type="number" onchange="fromOrToChanged()" id="to" value="{{$box->to_}}" min="2">
                    </label>
                </div>

            </div>
        </div>

        <div id="individualQuestions">
        </div>

        <div>
            <button class="MyBtn" style="width: auto; margin-top: 10px" onclick="submitNewBox()"><span>اصلاح جعبه</span></button>
        </div>
        </center>
    @endif

@stop