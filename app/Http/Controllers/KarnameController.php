<?php

namespace App\Http\Controllers;

use App\models\City;
use App\models\KindKarname;
use App\models\QEntry;
use App\models\Quiz;
use App\models\QuizStatus;
use App\models\Survey;
use App\models\Taraz;
use App\models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class KarnameController extends Controller {

    public function defineKarname() {

        if(isset($_POST["submitKindKarname"]) && isset($_POST["quiz_id"])) {
            $quizId = makeValidInput($_POST["quiz_id"]);
            $kindKarname = KindKarname::whereId($quizId);
            return view('defineKarname', array('quizId' => $quizId, 'kindKarname' => $kindKarname));
        }

        if(isset($_POST["doDefine"])) {
            $quizId = makeValidInput($_POST["quiz_id"]);
            $kindKarname = KindKarname::whereId($quizId);
            $kindKarname->lessonAvg = (isset($_POST["lessonAvg"]));
            $kindKarname->subjectAvg = (isset($_POST["subjectAvg"]));
            $kindKarname->lessonStatus = (isset($_POST["lessonStatus"]));
            $kindKarname->subjectStatus = (isset($_POST["subjectStatus"]));
            $kindKarname->lessonMaxPercent = (isset($_POST["lessonMaxPercent"]));
            $kindKarname->subjectMaxPercent = (isset($_POST["subjectMaxPercent"]));
            $kindKarname->partialTaraz = (isset($_POST["partialTaraz"]));
            $kindKarname->generalTaraz = (isset($_POST["generalTaraz"]));
            $kindKarname->lessonCityRank = (isset($_POST["lessonCityRank"]));
            $kindKarname->subjectCityRank = (isset($_POST["subjectCityRank"]));
            $kindKarname->lessonStateRank = (isset($_POST["lessonStateRank"]));
            $kindKarname->subjectStateRank = (isset($_POST["subjectStateRank"]));
            $kindKarname->lessonCountryRank = (isset($_POST["lessonCountryRank"]));
            $kindKarname->subjectCountryRank = (isset($_POST["subjectCountryRank"]));
            $kindKarname->generalCityRank = (isset($_POST["generalCityRank"]));
            $kindKarname->generalStateRank = (isset($_POST["generalStateRank"]));
            $kindKarname->generalCountryRank = (isset($_POST["generalCountryRank"]));
            $kindKarname->coherences = (isset($_POST["coherences"]));
            $kindKarname->lessonBarChart = (isset($_POST["lessonBarChart"]));
            $kindKarname->subjectBarChart = (isset($_POST["subjectBarChart"]));
            $kindKarname->lessonMark = (isset($_POST["lessonMark"]));
            $kindKarname->subjectMark = (isset($_POST["subjectMark"]));
            $kindKarname->save();
            return Redirect::to('home');
        }

        $quiz = Quiz::select('id', 'QN')->get();
        return view('defineKarname', array('quizes' => $quiz));
    }

    public function seeResult($quizId = "") {

        $uId = Auth::user()->id;

        if(Survey::whereUId($uId)->count() == 0)
            return Redirect::route('survey');

        $msg = "";

        if(isset($_POST["getKarname"])) {

            $quizId = makeValidInput($_POST["quiz_id"]);
            $karname = makeValidInput($_POST["kindKarname"]);

            $conditions = ['u_id' => $uId, 'q_id' => $quizId];
            $qentryId = QEntry::where($conditions)->select('id')->first();

            $tmp = Taraz::whereQEntryId($qentryId->id)->count();

            if($tmp == null || $tmp == 0)
                $msg = "صفحه ی نمایش کارنامه برای این آزمون هنوز باز نشده است";

            else {
                $kindKarname = KindKarname::whereId($quizId);
                if ($kindKarname == null)
                    $msg = "مشکلی در نمایش کارنامه به وجود آمده است";
                else {
                    switch ($karname) {
                        case 1:
                        default:
                            return $this->showGeneralKarname($uId, $quizId, $qentryId, $kindKarname);
                        case 2:
                            return $this->showSubjectKarname($uId, $quizId, $kindKarname, makeValidInput($_POST["lId"]));
                        case 3:
                            $roqs = DB::select('select roq.result, qoq.question_id, questions.ans, questions.attempt, questions.solved from roq, qoq, questions WHERE questions.id = qoq.question_id and qoq.quiz_id = ' . $quizId . ' and roq.qoq_id = qoq.id and roq.u_id = ' . $uId . ' order by qoq.qNo ASC');
                            $qInfo = getQOQ($quizId, true);
                            return view('questionKarname', array('quizId' => $quizId, 'questions' => $qInfo, 'roqs'
                            => $roqs));
                    }
                }
            }
        }
        $quizes = array();
        if($uId != -1) {
            $myQuizes = QEntry::whereUId($uId)->select('q_id')->get();
            $quizes = array();
            for($i = 0; $i < count($myQuizes); $i++)
                $quizes[$i] = Quiz::where('id', '=', $myQuizes[$i]->q_id)->select('id', 'QN')->first();
        }

        return view('karname', array('quizes' => $quizes, 'msg' => $msg, 'selectedQuiz' => $quizId));
    }

    public function showGeneralKarname($uId, $quizId, $qentryId, $kindKarname) {

        $status = array();
        if($kindKarname->lessonStatus)
            $status = QuizStatus::whereLevel(1)->get();

        $rank = $this->calcRank($quizId, $uId);

        $rankInLesson = array();
        $cityRank = -1;
        $stateRank = -1;
        $stateId = -1;
        $rankInLessonCity = array();
        $rankInLessonState = array();

        $cityId = User::whereId($uId)->city_id;

        if($kindKarname->lessonCityRank) {
            $cityRank = $this->calcRankInCity($quizId, $uId, $cityId);
        }

        if($kindKarname->lessonStateRank) {
            $stateId = City::whereId($cityId)->state->id;
            $stateRank = $this->calcRankInState($quizId, $uId, $stateId);
        }

        if($kindKarname->lessonAvg &&  $kindKarname->lessonMaxPercent)
            $avgs = DB::select('select SUM(percent) / count(*) as avg, MAX(percent) as maxPercent FROM taraz, qentry WHERE qentry.q_id = ' . $quizId . ' and qentry.id = taraz.q_entry_id GROUP by(taraz.l_id)');
        else if($kindKarname->lessonAvg)
            $avgs = DB::select('select SUM(percent) / count(*) as avg FROM taraz, qentry WHERE qentry.q_id = ' . $quizId . ' and qentry.id  = taraz.q_entry_id GROUP by(taraz.l_id)');
        

        $inCorrects =  DB::select('SELECT count(*) as inCorrects, subjects.id_l as target FROM roq, qoq, questions, subjects WHERE roq.qoq_id = qoq.id and qoq.quiz_id = ' . $quizId . ' and qoq.question_id = questions.id and questions.ans <> roq.result and roq.result <> 0 and roq.u_id = ' . $uId . ' and subjects.id = questions.subject_id group by(subjects.id_l)');
        $corrects =  DB::select('SELECT count(*) as corrects, subjects.id_l as target FROM roq, qoq, questions, subjects WHERE roq.qoq_id = qoq.id and qoq.quiz_id = ' . $quizId . ' and qoq.question_id = questions.id and questions.ans = roq.result and roq.u_id = ' . $uId . ' and subjects.id = questions.subject_id group by(subjects.id_l)');
        $total =  DB::select('SELECT count(*) as total, subjects.id_l as target FROM roq, qoq, questions, subjects WHERE roq.qoq_id = qoq.id and qoq.quiz_id = ' . $quizId . ' and qoq.question_id = questions.id and roq.u_id = ' . $uId . ' and subjects.id = questions.subject_id group by(subjects.id_l)');

        $roq = $this->getResultOfSpecificContainer($total, $corrects, $inCorrects);

        $lessons = getLessonQuiz($quizId);

        $taraz = Taraz::whereQEntryId($qentryId->id)->get();

        if($kindKarname->lessonCountryRank) {
            $counter = 0;
            foreach ($lessons as $lesson) {
                $tmp = DB::select('SELECT qentry.u_id, taraz.taraz from qentry, taraz WHERE qentry.id = taraz.q_entry_id and qentry.q_id = ' . $quizId . ' and taraz.l_id = ' . $lesson->id . ' ORDER by taraz.taraz DESC');
                $rankInLesson[$counter++] = $this->getRank($tmp, $uId);
            }
        }

        if($kindKarname->lessonStateRank) {
            $counter = 0;
            foreach ($lessons as $lesson) {
                $tmp = DB::select('SELECT qentry.u_id, taraz.taraz from users_azmoon std, cities ci, qentry, taraz WHERE std.id = qentry.u_id and std.city_id = ci.id and ci.state_id = ' . $stateId . ' and qentry.id = taraz.q_entry_id and qentry.q_id = ' . $quizId . ' and taraz.l_id = ' . $lesson->id . ' ORDER by taraz.taraz DESC');
                $rankInLessonState[$counter++] = $this->getRank($tmp, $uId);
            }
        }
		
        if($kindKarname->lessonCityRank) {
            $counter = 0;
            foreach ($lessons as $lesson) {
                $tmp = DB::select('SELECT qentry.u_id, taraz.taraz from users_azmoon std, qentry, taraz WHERE std.id = qentry.u_id and std.city_id = ' . $cityId . ' and qentry.id = taraz.q_entry_id and qentry.q_id = ' . $quizId . ' and taraz.l_id = ' . $lesson->id . ' ORDER by taraz.taraz DESC');
                $rankInLessonCity[$counter++] = $this->getRank($tmp, $uId);
            }
        }

        $totalMark = 0;
        if($kindKarname->lessonMark)
            $totalMark = Quiz::whereId($quizId)->mark;

        return view('generalKarname', array('quizId' => $quizId, 'status' => $status, 'kindKarname' => $kindKarname,
            'rank' => $rank, 'rankInLessonCity' => $rankInLessonCity, 'rankInLesson' => $rankInLesson,
            'lessons' => $lessons, 'taraz' => $taraz, 'rankInLessonState' => $rankInLessonState, 'stateRank' => $stateRank,
            'avgs' => $avgs, 'roq' => $roq, 'cityRank' => $cityRank, "totalMark" => $totalMark));
    }

    private function showSubjectKarname($uId, $quizId, $kindKarname, $lId) {

        $status = array();
        $avgs = array();
        $cityId = User::whereId($uId)->city_id;

        if($kindKarname->subjectStatus)
            $status = QuizStatus::whereLevel(2)->get();

        if($kindKarname->subjectAvg &&  $kindKarname->subjectMaxPercent)
            $avgs = DB::select('select SUM(percent) / count(*) as avg, MAX(percent) as maxPercent FROM subjects_percent, subjects WHERE q_id = 
' . $quizId . ' and subjects.id = s_id and subjects.id_l = ' . $lId . ' GROUP by(s_id)');
        else if($kindKarname->subjectAvg)
            $avgs = DB::select('select SUM(percent) / count(*) as avg FROM subjects_percent, subjects WHERE q_id = ' . $quizId . ' and subjects.id = s_id and subjects
.id_l = ' . $lId . ' GROUP by(s_id)');

        $cityRank = array();
        $stateRank = array();
        $countryRank = array();

        $subjects = $this->getSubjectsQuiz($quizId, $lId);

        if($kindKarname->subjectCityRank) {
            $counter = 0;
            foreach ($subjects as $subject) {
                $tmp = DB::select('SELECT subjects_percent.u_id, subjects_percent.percent as taraz from users_azmoon std, cities ci, 
subjects_percent WHERE std.id = subjects_percent.u_id and std.city_id = ' . $cityId . ' and subjects_percent.q_id = ' . $quizId .
                    ' and subjects_percent.s_id = ' . $subject->id . ' ORDER by subjects_percent.percent DESC');
                $cityRank[$counter++] = $this->getRank($tmp, $uId);
            }
        }

        if($kindKarname->subjectStateRank) {
            $counter = 0;
            $stateId = City::whereId($cityId)->state->id;
            foreach ($subjects as $subject) {
                $tmp = DB::select('SELECT subjects_percent.u_id, subjects_percent.percent as taraz from users_azmoon std, cities ci, subjects_percent WHERE std.id = subjects_percent.u_id and std.city_id = ci.id and ci.state_id = ' . $stateId . ' and subjects_percent.q_id = ' . $quizId . ' and subjects_percent.s_id = ' . $subject->id . ' ORDER by subjects_percent.percent DESC');
                $stateRank[$counter++] = $this->getRank($tmp, $uId);
            }
        }

        if($kindKarname->subjectCountryRank) {
            $counter = 0;
            foreach ($subjects as $subject) {
                $tmp = DB::select('SELECT subjects_percent.u_id, subjects_percent.percent as taraz from subjects_percent WHERE subjects_percent.q_id = ' . $quizId .

' and subjects_percent.s_id = ' . $subject->id . ' ORDER by subjects_percent.percent DESC');
                $countryRank[$counter++] = $this->getRank($tmp, $uId);
            }
        }


        $inCorrects =  DB::select('SELECT count(*) as inCorrects, questions.subject_id as target FROM roq, qoq, questions, subjects WHERE roq.qoq_id = qoq.id and qoq.quiz_id = ' . $quizId . ' and qoq.question_id = questions.id and questions.ans <> roq.result and roq.result <> 0 and roq.u_id = ' . $uId . ' and questions.subject_id = subjects.id and subjects.id_l = ' . $lId . ' group by(subjects.id)');
        $corrects =  DB::select('SELECT count(*) as corrects, questions.subject_id as target FROM roq, qoq, questions, subjects WHERE roq.qoq_id = qoq.id and qoq.quiz_id = ' . $quizId . ' and qoq.question_id = questions.id and questions.ans = roq.result and roq.u_id = ' . $uId . ' and questions.subject_id = subjects.id and subjects.id_l = ' . $lId . ' group by(subjects.id)');
        $total =  DB::select('SELECT count(*) as total, questions.subject_id as target FROM roq, qoq, questions, subjects WHERE roq.qoq_id = qoq.id and qoq.quiz_id = ' . $quizId . ' and qoq.question_id = questions.id and roq.u_id = ' . $uId . ' and questions.subject_id = subjects.id and subjects.id_l = ' . $lId . ' group by(subjects.id)');
        
        $roq = $this->getResultOfSpecificContainer($total, $corrects, $inCorrects);

        $totalMark = 0;
        if($kindKarname->subjectMark)
            $totalMark = Quiz::whereId($quizId)->mark;

        $minusMark = Quiz::whereId($quizId)->minusMark;

        return view('subjectKarname', array('quizId' => $quizId, 'status' => $status, 'roq' => $roq, 'subjects' =>
            $subjects,
            'kindKarname' => $kindKarname, 'avgs' => $avgs, 'cityRank' => $cityRank, 'stateRank' => $stateRank,
            'countryRank' => $countryRank, 'totalMark' => $totalMark, 'minusMark' => $minusMark));
    }
    
    private function getSubjectsQuiz($quizId, $lId) {
        $sIds = DB::select('SELECT DISTINCT S.nameSubject, S.id as id from subjects S, questions q, qoq QO WHERE QO.quiz_id = ' . $quizId . ' and QO.question_id = q.id and S.id = q.subject_id and S.id_l = ' . $lId);
        if(count($sIds) > 0)
            return $sIds;
        return null;
    }

    private function getResultOfSpecificContainer($total, $corrects, $inCorrects) {


        $j = $k = 0;
        $correctsArr = $inCorrectsArr = $totalArr = array();

        for($i = 0; $i < count($total); $i++) {

            $totalArr[$i] = $total[$i]->total;

            if($j < count($corrects) && $total[$i]->target == $corrects[$j]->target)
                $correctsArr[$i] = $corrects[$j++]->corrects;
            else
                $correctsArr[$i] = 0;

            if($k < count($inCorrects) && $total[$i]->target == $inCorrects[$k]->target)
                $inCorrectsArr[$i] = $inCorrects[$k++]->inCorrects;
            else
                $inCorrectsArr[$i] = 0;
        }
        return [$inCorrectsArr, $correctsArr, $totalArr];
    }
    
    private function getRank($tmp, $uId) {
        for($j = 0; $j < count($tmp); $j++) {
            if($tmp[$j]->u_id == $uId) {
                $r = $j + 1;
                $currTaraz = $tmp[$j]->taraz;
                $k = $j - 1;
                while ($k >= 0 && $tmp[$k]->taraz == $currTaraz) {
                    $k--;
                    $r--;
                }
                return $r;
            }
        }
        return count($tmp);
    }

    private function calcRank($quizId, $uId) {
        $ranks = DB::select('SELECT qentry.u_id, sum(taraz.taraz * (SELECT lessons.coherence FROM lessons WHERE lessons.id = taraz.l_id)) as weighted_avg from qentry, taraz WHERE qentry.id = taraz.q_entry_id and qentry.q_id = ' . $quizId . ' GROUP by (qentry.u_id) ORDER by weighted_avg DESC');
        for($i = 0; $i < count($ranks); $i++) {
            if($ranks[$i]->u_id == $uId) {
                $r = $i + 1;
                $currTaraz = $ranks[$i]->weighted_avg;
                $k = $i - 1;
                while ($k >= 0 && $ranks[$k]->weighted_avg == $currTaraz) {
                    $k--;
                    $r--;
                }
                return $r;
            }
        }
        return count($ranks);
    }

    private function calcRankInCity($quizId, $uId, $cityId) {
        $ranks = DB::select('SELECT qentry.u_id, sum(taraz.taraz * (SELECT lessons.coherence FROM lessons WHERE lessons.id = taraz.l_id)) as weighted_avg from qentry, taraz, users_azmoon std WHERE qentry.id = taraz.q_entry_id and qentry.u_id = std.id AND std.city_id = ' . $cityId . ' and qentry.q_id = ' . $quizId . ' GROUP by (qentry.u_id) ORDER by weighted_avg DESC');
        for($i = 0; $i < count($ranks); $i++) {
            if($ranks[$i]->u_id == $uId) {
                $r = $i + 1;
                $currTaraz = $ranks[$i]->weighted_avg;
                $k = $i - 1;
                while ($k >= 0 && $ranks[$k]->weighted_avg == $currTaraz) {
                    $k--;
                    $r--;
                }
                return $r;
            }
        }
        return count($ranks);
    }

    private function calcRankInState($quizId, $uId, $stateId) {
        $ranks = DB::select('SELECT qentry.u_id, sum(taraz.taraz * (SELECT lessons.coherence FROM lessons WHERE lessons.id = taraz.l_id)) as weighted_avg from qentry, taraz, users_azmoon std, cities ci WHERE qentry.id = taraz.q_entry_id and qentry.u_id = std.id AND std.city_id = ci.id and ci.state_id = ' . $stateId . ' and qentry.q_id = ' . $quizId . ' GROUP by (qentry.u_id) ORDER by weighted_avg DESC');
        for($i = 0; $i < count($ranks); $i++) {
            if($ranks[$i]->u_id == $uId) {
                $r = $i + 1;
                $currTaraz = $ranks[$i]->weighted_avg;
                $k = $i - 1;
                while ($k >= 0 && $ranks[$k]->weighted_avg == $currTaraz) {
                    $k--;
                    $r--;
                }
                return $r;
            }
        }
        return count($ranks);
    }
}