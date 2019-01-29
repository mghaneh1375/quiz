<?php

namespace App\Http\Controllers;

use App\models\Box;
use App\models\BoxItems;
use App\models\Degree;
use App\models\Enheraf;
use App\models\Lesson;
use App\models\QEntry;
use App\models\QOQ;
use App\models\Question;
use App\models\Quiz;
use App\models\ROQ;
use App\models\Subject;
use App\models\Taraz;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

include_once 'Common.php';

class AjaxController extends Controller {

    public function getLessons() {

        $degreeId = makeValidInput($_POST["degree_id"]);
        $lessons = Degree::whereId($degreeId)->lessons()->get();
        foreach ($lessons as $lesson)
            echo "<option value='".$lesson->id."'>".$lesson->nameL."</option>";
    }

    public function getLessonsWithSelected() {

        $degreeId = makeValidInput($_POST["degree_id"]);
        $selectedLesson = makeValidInput($_POST["selectedLesson"]);
        $lessons = Degree::whereId($degreeId)->lessons()->get();
        foreach ($lessons as $lesson) {
            if($lesson->id == $selectedLesson)
                echo "<option selected value='" . $lesson->id . "'>" . $lesson->nameL . "</option>";
            else
                echo "<option value='" . $lesson->id . "'>" . $lesson->nameL . "</option>";
        }
    }

    public function getSubjects() {

        $lessonId = makeValidInput($_POST["lessonId"]);
        $subjects = Lesson::whereId($lessonId)->subjects()->get();
        foreach ($subjects as $subject)
            echo "<option value='".$subject->id."'>".$subject->nameSubject."</option>";
    }

    public function addNewBox() {

        $from = makeValidInput($_POST["from"]);
        $to = makeValidInput($_POST["to"]);
        $subjectIds = $_POST["subjectIds"];
        $grades = $_POST["grades"];
        $boxName = makeValidInput($_POST["boxName"]);

        $box = Box::whereName($boxName)->count();
        if($box > 0) {
            echo -1;
            return;
        }

        $allow = true;
        $sIds = $selectedGrades = $counter = [];

        for($i = 0; $i < count($subjectIds); $i++) {

            for ($j = 0; $j < count($sIds); $j++) {
                if ($sIds[$j] == makeValidInput($subjectIds[$i]) &&
                    $selectedGrades[$j] == makeValidInput($grades[$i])
                ) {
                    $counter[$j] = $counter[$j] + 1;
                    $allow = false;
                    break;
                }
            }

            if($allow) {
                $sIds[count($sIds)] = makeValidInput($subjectIds[$i]);
                $selectedGrades[count($selectedGrades)] = makeValidInput($grades[$i]);
                $counter[count($counter)] = 1;
            }
        }

        for($i = 0; $i < count($sIds); $i++) {

            if($this->getTotalQ2($sIds[$i], $selectedGrades[$i]) < $counter[$i]) {
                echo -2;
                return;
            }

        }

        $box = new Box();
        $box->from_ = $from;
        $box->to_ = $to;
        $box->name = $boxName;
        $box->save();

        for($i = 0; $i < count($subjectIds); $i++) {
            $item = new BoxItems();
            $item->box_id = $box->id;
            $item->subject_id = makeValidInput($subjectIds[$i]);
            $item->grade = makeValidInput($grades[$i]);
            $item->save();
        }
        echo 1;
    }

    public function updateBox() {

        $from = makeValidInput($_POST["from"]);
        $to = makeValidInput($_POST["to"]);
        $subjectIds = $_POST["subjectIds"];
        $grades = $_POST["grades"];
        $boxName = makeValidInput($_POST["boxName"]);
        $boxId = makeValidInput($_POST["box_id"]);

        $box = Box::whereId($boxId);

        if($box->name != $boxName) {
            if(Box::whereName($boxName)->count() > 0) {
                echo -1;
                return;
            }
        }

        $allow = true;
        $sIds = $selectedGrades = $counter = [];

        for($i = 0; $i < count($subjectIds); $i++) {

            for ($j = 0; $j < count($sIds); $j++) {
                if ($sIds[$j] == makeValidInput($subjectIds[$i]) &&
                    $selectedGrades[$j] == makeValidInput($grades[$i])
                ) {
                    $counter[$j] = $counter[$j] + 1;
                    $allow = false;
                    break;
                }
            }

            if($allow) {
                $sIds[count($sIds)] = makeValidInput($subjectIds[$i]);
                $selectedGrades[count($selectedGrades)] = makeValidInput($grades[$i]);
                $counter[count($counter)] = 1;
            }
        }

        for($i = 0; $i < count($sIds); $i++) {

            if($this->getTotalQ2($sIds[$i], $selectedGrades[$i]) < $counter[$i]) {
                echo -2;
                return;
            }

        }

        $box->from_ = $from;
        $box->to_ = $to;
        $box->name = $boxName;
        $box->save();

        BoxItems::whereBoxId($boxId)->delete();

        for($i = 0; $i < count($subjectIds); $i++) {

            $item = new BoxItems();
            $item->box_id = $box->id;
            $item->subject_id = makeValidInput($subjectIds[$i]);
            $item->grade = makeValidInput($grades[$i]);
            $item->save();
        }

        echo 1;
    }

    public function deleteBox(){
        $boxId = makeValidInput($_POST["box_id"]);
        Box::destroy($boxId);
    }

    public function getBoxItemsByNames() {
        $boxId = makeValidInput($_POST["box_id"]);
        $boxItems = BoxItems::whereBoxId($boxId)->get();
        $out = array();
        $counter = 0;
        foreach ($boxItems as $boxItem) {
            $subject = Subject::whereId($boxItem->subject_id);
            if ($subject != null)
                $boxItem->subject_id = $subject->nameSubject;
            if ($boxItem->grade == 1)
                $boxItem->grade = 'آسان';
            else if ($boxItem->grade == 2)
                $boxItem->grade = 'متوسط';
            else
                $boxItem->grade = 'دشوار';

            $out[$counter++] = $boxItem;
        }
        echo json_encode($out);
    }

    public function getBoxItems() {

        if(isset($_POST["box_id"])) {

            $boxId = makeValidInput($_POST["box_id"]);
            $boxItems = BoxItems::whereBoxId($boxId)->get();
            $out = array();
            $counter = 0;

            foreach ($boxItems as $boxItem) {
                $out[$counter++] = $boxItem;
            }

            echo json_encode($out);
        }
    }

    public function changeQuiz() {
        $qId = makeValidInput($_POST["qId"]);
        $quiz = Quiz::whereId($qId);
        $out = array();
        $out[0] = $quiz;
        $count = QOQ::whereQuizId($qId)->count();
        $out[1] = $count;
        echo json_encode($out);
    }

    public function submitAns() {

        if(isset($_POST["qoqId"]) && isset($_POST["newVal"])) {

            $qoqId = makeValidInput($_POST["qoqId"]);
            $newVal = makeValidInput($_POST["newVal"]);

            $uId = Auth::user()->id;

            if ($uId != -1) {

                $condition = ['u_id' => $uId, 'qoq_id' => $qoqId];
                $roq = ROQ::where($condition)->first();

                if ($roq != null) {
                    $roq->result = $newVal;
                    $roq->save();
                    echo "ok";
                    return;
                }

                echo "nok1";
                return;
            }

            echo "nok2";
            return;
        }

        echo 'nok3';
    }

    public function endQuiz() {

        $quizId = makeValidInput($_POST["quiz_id"]);
        $uId = makeValidInput(Session::get('uId', -1));

        if($uId != -1) {
            $conditions = ['q_id' => $quizId, 'u_id' => $uId];
            $qEntry = QEntry::where($conditions)->first();
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
            $quizId = QEntry::whereId($qEntryId);

            if ($quizId == null) {
                echo "nok";
                return;
            }

            $quizId = $quizId->q_id;
            $enherafMeyars = Enheraf::whereQId($quizId)->get();
            if ($enherafMeyars == null || count($enherafMeyars) == 0) {
                echo "nok";
                return;
            }

            foreach ($enherafMeyars as $itr) {
                $lId = $itr->l_id;
                $enherafMeyar = $itr->val;
                $lessonAVG = $itr->lessonAVG;
                $conditions = ["q_entry_id" => $qEntryId, "l_id" => $lId];
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

    public function getQuizLessons() {

        $qId = makeValidInput($_POST["qId"]);

        echo json_encode(getLessonQuiz($qId));

    }

    public function getQuizStates() {

        if(isset($_POST["qId"])) {

            $quizId = makeValidInput($_POST["qId"]);
            echo json_encode(DB::select('select DISTINCT(state.name) as stateName, state.id as stateId FROM states as state, cities as city, 
users_azmoon as std_, qentry WHERE qentry.q_id = ' . $quizId . ' and std_.id = qentry.u_id and std_.city_id = 
city.id and city.state_id = state.id'));

        }
    }

    public function getQuizCities() {

        if(isset($_POST["qId"])) {

            $quizId = makeValidInput($_POST["qId"]);
            echo json_encode(DB::select('select DISTINCT(city.name) as cityName, city.id as cityId FROM cities as city, users_azmoon as std_, quizayan_quiz
.qentry WHERE qentry.q_id = ' . $quizId . ' and std_.id = qentry.u_id and std_.city_id = city.id'));

        }
    }

    public function getTotalQ() {
        
        $subjectId = makeValidInput($_POST["subject_id"]);
        $level = makeValidInput($_POST["level"]);

        $condition = ['subject_id' => $subjectId, 'grade' => $level];

        echo Question::where($condition)->count();

    }

    private function getTotalQ2($subjectId, $level) {

        $condition = ['subject_id' => $subjectId, 'grade' => $level];
        return Question::where($condition)->count();

    }
}