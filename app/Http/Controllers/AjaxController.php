<?php

include_once 'Common.php';

class AjaxController extends BaseController {

    public function getLessons() {

        $degreeId = makeValidInput($_POST["degreeId"]);
        $lessons = Degree::find($degreeId)->lessons()->get();
        foreach ($lessons as $lesson)
            echo "<option value='".$lesson->id."'>".$lesson->nameL."</option>";
    }

    public function getLessonsWithSelected() {

        $degreeId = makeValidInput($_POST["degreeId"]);
        $selectedLesson = makeValidInput($_POST["selectedLesson"]);
        $lessons = Degree::find($degreeId)->lessons()->get();
        foreach ($lessons as $lesson) {
            if($lesson->id == $selectedLesson)
                echo "<option selected value='" . $lesson->id . "'>" . $lesson->nameL . "</option>";
            else
                echo "<option value='" . $lesson->id . "'>" . $lesson->nameL . "</option>";
        }
    }

    public function getSubjects() {

        $lessonId = makeValidInput($_POST["lessonId"]);
        $subjects = Lesson::find($lessonId)->subjects()->get();
        foreach ($subjects as $subject)
            echo "<option value='".$subject->id."'>".$subject->nameSubject."</option>";
    }

    public function addNewBox() {

        $from = makeValidInput($_POST["from"]);
        $to = makeValidInput($_POST["to"]);
        $subjectIds = $_POST["subjectIds"];
        $grades = $_POST["grades"];
        $compassIds = $_POST["compassIds"];
        $boxName = makeValidInput($_POST["boxName"]);

        $box = Box::where('name', '=', $boxName)->count();
        if($box > 0) {
            echo -1;
            return;
        }

        $box = new Box();
        $box->from_ = $from;
        $box->to_ = $to;
        $box->name = $boxName;
        $box->save();

        for($i = 0; $i < count($subjectIds); $i++) {
            $item = new BoxItems();
            $item->boxId = $box->id;
            $item->subject_id = makeValidInput($subjectIds[$i]);
            $item->grade = makeValidInput($grades[$i]);
            $item->compassId = makeValidInput($compassIds[$i]);
            $item->save();
        }
        echo 1;
    }

    public function updateBox() {

        $from = makeValidInput($_POST["from"]);
        $to = makeValidInput($_POST["to"]);
        $subjectIds = $_POST["subjectIds"];
        $grades = $_POST["grades"];
        $compassIds = $_POST["compassIds"];
        $boxName = makeValidInput($_POST["boxName"]);
        $boxId = makeValidInput($_POST["boxId"]);

        $box = Box::find($boxId);

        if($box->name != $boxName) {
            $boxTmp = Box::where('name', '=', $boxName)->count();
            if ($boxTmp > 0) {
                echo -1;
                return;
            }
        }

        $box->from_ = $from;
        $box->to_ = $to;
        $box->name = $boxName;
        $box->save();

        $boxItems = BoxItems::where('boxId', '=', $boxId)->get();

        $i = 0;
        $size = (int)$to - $from + 1;

        foreach ($boxItems as $item) {

            if($i >= $size)
                $item->delete();

            else {
                $item->subject_id = makeValidInput($subjectIds[$i]);
                $item->grade = makeValidInput($grades[$i]);
                $item->compassId = makeValidInput($compassIds[$i++]);
                $item->save();
            }
        }

        echo 1;
    }

    public function deleteBox(){
        $boxId = makeValidInput($_POST["boxId"]);
        Box::destroy($boxId);
    }

    public function getBoxItemsByNames() {
        $boxId = makeValidInput($_POST["boxId"]);
        $boxItems = BoxItems::where('boxId', '=', $boxId)->get();
        $out = array();
        $counter = 0;
        foreach ($boxItems as $boxItem) {
            $subject = Subject::where('id', '=', $boxItem->subject_id)->select('subjects.nameSubject')->get();
            if (count($subject) > 0)
                $boxItem->subject_id = $subject[0]->nameSubject;
            if ($boxItem->grade == 1)
                $boxItem->grade = 'آسان';
            else if ($boxItem->grade == 2)
                $boxItem->grade = 'متوسط';
            else
                $boxItem->grade = 'دشوار';

            $boxItem->compassId = Compass::find($boxItem->compassId)->name;

            $out[$counter++] = $boxItem;
        }
        echo json_encode($out);
    }

    public function getBoxItems() {

        $boxId = makeValidInput($_POST["boxId"]);
        $boxItems = BoxItems::where('boxId', '=', $boxId)->get();
        $out = array();
        $counter = 0;

        foreach ($boxItems as $boxItem) {
            $out[$counter++] = $boxItem;
        }

        echo json_encode($out);
    }

    public function changeQuiz() {
        $qId = makeValidInput($_POST["qId"]);
        $quiz = Quiz::find($qId);
        $out = array();
        $out[0] = $quiz;
        $count = QOQ::where('quizId', '=', $qId)->count();
        $out[1] = $count;
        echo json_encode($out);
    }

    public function submitAns() {

        $qoqId = makeValidInput($_POST["qoqId"]);
        $newVal = makeValidInput($_POST["newVal"]);

        $uId = Auth::user()->id;

        if($uId != -1) {

            $condition = ['uId' => $uId, 'qoqId' => $qoqId];
            $roq = ROQ::where($condition)->first();


            if ($roq != null) {
                $roq->result = $newVal;
                $roq->save();
            }
        }
    }

    public function endQuiz() {

        $quizId = makeValidInput($_POST["quizId"]);
        $uId = makeValidInput(Session::get('uId', -1));

        if($uId != -1) {
            $conditions = ['qId' => $quizId, 'uId' => $uId];
            $qEntry = qEntry::where($conditions)->first();
            if($qEntry != null) {
                $qEntry->status = 1;
                $qEntry->save();
                echo "ok";
            }
        }
    }

    public function calcTaraz() {

        if (isset($_POST["qEntryId"])) {

            $qEntryId = makeValidInput($_POST["qEntryId"]);
            $quizId = qEntry::find($qEntryId);

            if ($quizId == null) {
                echo "nok";
                return;
            }

            $quizId = $quizId->qId;
            $enherafMeyars = Enheraf::where('qId', '=', $quizId)->get();
            if ($enherafMeyars == null || count($enherafMeyars) == 0) {
                echo "nok";
                return;
            }

            foreach ($enherafMeyars as $itr) {
                $lId = $itr->lId;
                $enherafMeyar = $itr->val;
                $lessonAVG = $itr->lessonAVG;
                $conditions = ["qEntryId" => $qEntryId, "lId" => $lId];
                $taraz = Taraz::where($conditions)->first();

                if ($enherafMeyar == 0)
                    $enherafMeyar++;

                if ($taraz != null) {
                    if ($enherafMeyar == 0)
                        $taraz->taraz = 5000;
                    else
                        $taraz->taraz = 1000 * (($taraz->percent - $lessonAVG) / $enherafMeyar) + 5000;
                    $taraz->save();
                }
            }
            echo "ok";
            return;
        }
        echo "nok";
    }

    public function getCompasses() {
        $compasses = Compass::all();
        foreach($compasses as $compass) {
            echo '<option value="'.$compass->id.'">'.$compass->name.'</option>';
        }
    }

    public function getQuizLessons() {

        $qId = makeValidInput($_POST["qId"]);

        echo json_encode(getLessonQuiz($qId));

    }

    public function getQuizStates() {

        if(isset($_POST["qId"])) {

            $quizId = makeValidInput($_POST["qId"]);
            echo json_encode(DB::select('select DISTINCT(state.name) as stateName, state.id as stateId FROM azmoon.states as state, azmoon.cities as city, azmoon.students as std_, medal.qentry WHERE qentry.qId = ' . $quizId . ' and std_.id = qentry.uId and std_.city_id = city.id and city.state_id = state.id'));

        }
    }

    public function getQuizCities() {

        if(isset($_POST["qId"])) {

            $quizId = makeValidInput($_POST["qId"]);
            echo json_encode(DB::select('select DISTINCT(city.name) as cityName, city.id as cityId FROM azmoon.cities as city, azmoon.students as std_, medal.qentry WHERE qentry.qId = ' . $quizId . ' and std_.id = qentry.uId and std_.city_id = city.id'));

        }
    }

    public function getTotalQ() {

        $subjectId = makeValidInput($_POST["subject_id"]);
        $compassId = makeValidInput($_POST["compassId"]);
        $level = makeValidInput($_POST["level"]);

        $condition = ['subject_id' => $subjectId, 'compassId' => $compassId, 'grade' => $level];

        echo Question::where($condition)->count();

    }
}