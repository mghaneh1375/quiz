<?php

class KarnameController extends BaseController {

    public function defineKarname() {
        if(isset($_POST["submitKindKarname"]) && isset($_POST["quiz_id"])) {
            $quizId = makeValidInput($_POST["quiz_id"]);
            $kindKarname = KindKarname::find($quizId);
            return view('defineKarname', array('quiz_id' => $quizId, 'kindKarname' => $kindKarname));
        }
        if(isset($_POST["doDefine"])) {
            $quizId = makeValidInput($_POST["quiz_id"]);
            $kindKarname = KindKarname::find($quizId);
            $kindKarname->lessonAvg = (isset($_POST["lessonAvg"]));
            $kindKarname->subjectAvg = (isset($_POST["subjectAvg"]));
            $kindKarname->compassAvg = (isset($_POST["compassAvg"]));
            $kindKarname->lessonStatus = (isset($_POST["lessonStatus"]));
            $kindKarname->subjectStatus = (isset($_POST["subjectStatus"]));
            $kindKarname->compassStatus = (isset($_POST["compassStatus"]));
            $kindKarname->lessonMaxPercent = (isset($_POST["lessonMaxPercent"]));
            $kindKarname->subjectMaxPercent = (isset($_POST["subjectMaxPercent"]));
            $kindKarname->compassMaxPercent = (isset($_POST["compassMaxPercent"]));
            $kindKarname->partialTaraz = (isset($_POST["partialTaraz"]));
            $kindKarname->generalTaraz = (isset($_POST["generalTaraz"]));
            $kindKarname->lessonCityRank = (isset($_POST["lessonCityRank"]));
            $kindKarname->subjectCityRank = (isset($_POST["subjectCityRank"]));
            $kindKarname->compassCityRank = (isset($_POST["compassCityRank"]));
            $kindKarname->lessonStateRank = (isset($_POST["lessonStateRank"]));
            $kindKarname->subjectStateRank = (isset($_POST["subjectStateRank"]));
            $kindKarname->compassStateRank = (isset($_POST["compassStateRank"]));
            $kindKarname->lessonCountryRank = (isset($_POST["lessonCountryRank"]));
            $kindKarname->subjectCountryRank = (isset($_POST["subjectCountryRank"]));
            $kindKarname->compassCountryRank = (isset($_POST["compassCountryRank"]));
            $kindKarname->generalCityRank = (isset($_POST["generalCityRank"]));
            $kindKarname->generalStateRank = (isset($_POST["generalStateRank"]));
            $kindKarname->generalCountryRank = (isset($_POST["generalCountryRank"]));
            $kindKarname->coherences = (isset($_POST["coherences"]));
            $kindKarname->lessonBarChart = (isset($_POST["lessonBarChart"]));
            $kindKarname->subjectBarChart = (isset($_POST["subjectBarChart"]));
            $kindKarname->compassBarChart = (isset($_POST["compassBarChart"]));
            $kindKarname->lessonMark = (isset($_POST["lessonMark"]));
            $kindKarname->subjectMark = (isset($_POST["subjectMark"]));
            $kindKarname->compassMark = (isset($_POST["compassMark"]));
            $kindKarname->save();
            return Redirect::to('home');
        }
        $quiz = Quiz::select('id', 'QN')->get();
        return view('defineKarname', array('quizes' => $quiz));
    }

    public function seeResult($quizId = "") {

        $uId = Auth::user()->id;
        $msg = "";

        if(isset($_POST["getKarname"])) {

            $quizId = makeValidInput($_POST["quiz_id"]);
            $karname = makeValidInput($_POST["kindKarname"]);

            $conditions = ['uId' => $uId, 'qId' => $quizId];
            $qentryId = qentry::where($conditions)->select('id')->first();

            $tmp = Taraz::where('qentryId', '=', $qentryId->id)->count();

            if($tmp == null || $tmp == 0)
                $msg = "صفحه ی نمایش کارنامه برای این آزمون هنوز باز نشده است";

            else {
                $kindKarname = KindKarname::find($quizId);
                if ($kindKarname == null || count($kindKarname) == 0)
                    $msg = "مشکلی در نمایش کارنامه به وجود آمده است";
                else {
                    switch ($karname) {
                        case 1:
                        default:
                            return $this->showGeneralKarname($uId, $quizId, $qentryId, $kindKarname);
                        case 2:
                            return $this->showSubjectKarname($uId, $quizId, $kindKarname, makeValidInput($_POST["lId"]));
                        case 3:
                            $roqs = DB::select('select roq.result, qoq.question_id, questions.ans, questions.attempt, questions.solved from roq, qoq, questions WHERE questions.id = qoq.question_id and qoq.quiz_id = ' . $quizId . ' and roq.qoqId = qoq.id and roq.uId = ' . $uId . ' order by qoq.qNo ASC');
                            $qInfo = getQOQ($quizId, true);
                            return view('questionKarname', array('quiz_id' => $quizId, 'questions' => $qInfo, 'roqs' => $roqs));
                        case 4:
                            return $this->showCompassKarname($uId, $quizId, $kindKarname);
                    }
                }
            }
        }
        $quizes = array();
        if($uId != -1) {
            $myQuizes = qentry::where('uId', '=', $uId)->select('qId')->get();
            $quizes = array();
            for($i = 0; $i < count($myQuizes); $i++)
                $quizes[$i] = Quiz::where('id', '=', $myQuizes[$i]->qId)->select('id', 'QN')->first();
        }
        return view('karname', array('quizes' => $quizes, 'msg' => $msg, 'selectedQuiz' => $quizId));
    }

    public function showGeneralKarname($uId, $quizId, $qentryId, $kindKarname) {

        $status = array();
        if($kindKarname->lessonStatus)
            $status = quizStatus::where('level', '=', 1)->get();

        $rank = $this->calcRank($quizId, $uId);

        $rankInLesson = array();
        $cityRank = -1;
        $stateRank = -1;
        $stateId = -1;
        $rankInLessonCity = array();
        $rankInLessonState = array();

        $cityId = StudentPanel::whereId($uId)->city_id;

        if($kindKarname->lessonCityRank) {
            $cityRank = $this->calcRankInCity($quizId, $uId, $cityId);
        }

        if($kindKarname->lessonStateRank) {
            $stateId = City::whereId($cityId)->state->id;
            $stateRank = $this->calcRankInState($quizId, $uId, $stateId);
        }

        if($kindKarname->lessonAvg &&  $kindKarname->lessonMaxPercent)
            $avgs = DB::select('select SUM(percent) / count(*) as avg, MAX(percent) as maxPercent FROM taraz, qentry WHERE qentry.qId = ' . $quizId . ' and qentry.id  = taraz.qentryId GROUP by(taraz.lId)');
        else if($kindKarname->lessonAvg)
            $avgs = DB::select('select SUM(percent) / count(*) as avg FROM taraz, qentry WHERE qentry.qId = ' . $quizId . ' and qentry.id  = taraz.qentryId GROUP by(taraz.lId)');



        $inCorrects =  DB::select('SELECT count(*) as inCorrects, subjects.id_l as target FROM roq, qoq, questions, subjects WHERE roq.qoqId = qoq.id and qoq.quiz_id = ' . $quizId . ' and qoq.question_id = questions.id and questions.ans <> roq.result and roq.result <> 0 and roq.uId = ' . $uId . ' and subjects.id = questions.subject_id group by(subjects.id_l)');
        $corrects =  DB::select('SELECT count(*) as corrects, subjects.id_l as target FROM roq, qoq, questions, subjects WHERE roq.qoqId = qoq.id and qoq.quiz_id = ' . $quizId . ' and qoq.question_id = questions.id and questions.ans = roq.result and roq.uId = ' . $uId . ' and subjects.id = questions.subject_id group by(subjects.id_l)');
        $total =  DB::select('SELECT count(*) as total, subjects.id_l as target FROM roq, qoq, questions, subjects WHERE roq.qoqId = qoq.id and qoq.quiz_id = ' . $quizId . ' and qoq.question_id = questions.id and roq.uId = ' . $uId . ' and subjects.id = questions.subject_id group by(subjects.id_l)');

        $roq = $this->getResultOfSpecificContainer($total, $corrects, $inCorrects);

        $lessons = getLessonQuiz($quizId);

        $taraz = Taraz::where('qentryId', '=', $qentryId->id)->get();

        if($kindKarname->lessonCountryRank) {
            $counter = 0;
            foreach ($lessons as $lesson) {
                $tmp = DB::select('SELECT qentry.uId, taraz.taraz from qentry, taraz WHERE qentry.id = taraz.qentryId and qentry.qId = ' . $quizId . ' and taraz.lId = ' . $lesson->id . ' ORDER by taraz.taraz DESC');
                $rankInLesson[$counter++] = $this->getRank($tmp, $uId);
            }
        }

        if($kindKarname->lessonStateRank) {
            $counter = 0;
            foreach ($lessons as $lesson) {
                $tmp = DB::select('SELECT qentry.uId, taraz.taraz from azmoon.students std, azmoon.cities ci, qentry, taraz WHERE std.id = qentry.uId and std.city_id = ci.id and ci.state_id = ' . $stateId . ' and qentry.id = taraz.qentryId and qentry.qId = ' . $quizId . ' and taraz.lId = ' . $lesson->id . ' ORDER by taraz.taraz DESC');
                $rankInLessonState[$counter++] = $this->getRank($tmp, $uId);
            }
        }
		
        if($kindKarname->lessonCityRank) {
            $counter = 0;
            foreach ($lessons as $lesson) {
                $tmp = DB::select('SELECT qentry.uId, taraz.taraz from azmoon.students std, qentry, taraz WHERE std.id = qentry.uId and std.city_id = ' . $cityId . ' and qentry.id = taraz.qentryId and qentry.qId = ' . $quizId . ' and taraz.lId = ' . $lesson->id . ' ORDER by taraz.taraz DESC');
                $rankInLessonCity[$counter++] = $this->getRank($tmp, $uId);
            }
        }

        $totalMark = 0;
        if($kindKarname->lessonMark)
            $totalMark = Quiz::whereId($quizId)->mark;

        return view('generalKarname', array('quiz_id' => $quizId, 'status' => $status, 'kindKarname' => $kindKarname,
            'rank' => $rank, 'rankInLessonCity' => $rankInLessonCity, 'rankInLesson' => $rankInLesson,
            'lessons' => $lessons, 'taraz' => $taraz, 'rankInLessonState' => $rankInLessonState, 'stateRank' => $stateRank,
            'avgs' => $avgs, 'roq' => $roq, 'cityRank' => $cityRank, "totalMark" => $totalMark));
    }

    private function showSubjectKarname($uId, $quizId, $kindKarname, $lId) {

        $status = array();
        $avgs = array();
        $cityId = StudentPanel::whereId($uId)->city_id;

        if($kindKarname->subjectStatus)
            $status = quizStatus::where('level', '=', 2)->get();

        if($kindKarname->subjectAvg &&  $kindKarname->subjectMaxPercent)
            $avgs = DB::select('select SUM(percent) / count(*) as avg, MAX(percent) as maxPercent FROM subjectsPercent, subjects WHERE qId = ' . $quizId . ' and subjects.id = sId and subjects.id_l = ' . $lId . ' GROUP by(sId)');
        else if($kindKarname->subjectAvg)
            $avgs = DB::select('select SUM(percent) / count(*) as avg FROM subjectsPercent WHERE qId = ' . $quizId . ' and subjects.id = sId and subjects.id_l = ' . $lId . ' GROUP by(sId)');

        $cityRank = array();
        $stateRank = array();
        $countryRank = array();

        $subjects = $this->getSubjectsQuiz($quizId, $lId);

        if($kindKarname->subjectCityRank) {
            $counter = 0;
            foreach ($subjects as $subject) {
                $tmp = DB::select('SELECT subjectsPercent.uId, subjectsPercent.percent as taraz from azmoon.students std, azmoon.cities ci, subjectsPercent WHERE std.id = subjectsPercent.uId and std.city_id = ' . $cityId . ' and subjectsPercent.qId = ' . $quizId . ' and subjectsPercent.sId = ' . $subject->id . ' ORDER by subjectsPercent.percent DESC');
                $cityRank[$counter++] = $this->getRank($tmp, $uId);
            }
        }

        if($kindKarname->subjectStateRank) {
            $counter = 0;
            $stateId = City::whereId($cityId)->state->id;
            foreach ($subjects as $subject) {
                $tmp = DB::select('SELECT subjectsPercent.uId, subjectsPercent.percent as taraz from azmoon.students std, azmoon.cities ci, subjectsPercent WHERE std.id = subjectsPercent.uId and std.city_id = ci.id and ci.state_id = ' . $stateId . ' and subjectsPercent.qId = ' . $quizId . ' and subjectsPercent.sId = ' . $subject->id . ' ORDER by subjectsPercent.percent DESC');
                $stateRank[$counter++] = $this->getRank($tmp, $uId);
            }
        }

        if($kindKarname->subjectCountryRank) {
            $counter = 0;
            foreach ($subjects as $subject) {
                $tmp = DB::select('SELECT subjectsPercent.uId, subjectsPercent.percent as taraz from subjectsPercent WHERE subjectsPercent.qId = ' . $quizId . ' and subjectsPercent.sId = ' . $subject->id . ' ORDER by subjectsPercent.percent DESC');
                $countryRank[$counter++] = $this->getRank($tmp, $uId);
            }
        }


        $inCorrects =  DB::select('SELECT count(*) as inCorrects, questions.subject_id as target FROM roq, qoq, questions, subjects WHERE roq.qoqId = qoq.id and qoq.quiz_id = ' . $quizId . ' and qoq.question_id = questions.id and questions.ans <> roq.result and roq.result <> 0 and roq.uId = ' . $uId . ' and questions.subject_id = subjects.id and subjects.id_l = ' . $lId . ' group by(subjects.id)');
        $corrects =  DB::select('SELECT count(*) as corrects, questions.subject_id as target FROM roq, qoq, questions, subjects WHERE roq.qoqId = qoq.id and qoq.quiz_id = ' . $quizId . ' and qoq.question_id = questions.id and questions.ans = roq.result and roq.uId = ' . $uId . ' and questions.subject_id = subjects.id and subjects.id_l = ' . $lId . ' group by(subjects.id)');
        $total =  DB::select('SELECT count(*) as total, questions.subject_id as target FROM roq, qoq, questions, subjects WHERE roq.qoqId = qoq.id and qoq.quiz_id = ' . $quizId . ' and qoq.question_id = questions.id and roq.uId = ' . $uId . ' and questions.subject_id = subjects.id and subjects.id_l = ' . $lId . ' group by(subjects.id)');
        
        $roq = $this->getResultOfSpecificContainer($total, $corrects, $inCorrects);

        $totalMark = 0;
        if($kindKarname->subjectMark)
            $totalMark = Quiz::whereId($quizId)->mark;

        $minusMark = Quiz::whereId($quizId)->minusMark;

        return view('subjectKarname', array('quiz_id' => $quizId, 'status' => $status, 'roq' => $roq, 'subjects' => $subjects,
            'kindKarname' => $kindKarname, 'avgs' => $avgs, 'cityRank' => $cityRank, 'stateRank' => $stateRank,
            'countryRank' => $countryRank, 'totalMark' => $totalMark, 'minusMark' => $minusMark));
    }

    private function showCompassKarname($uId, $quizId, $kindKarname) {

        $status = array();
        $avgs = array();
        $cityId = StudentPanel::whereId($uId)->city_id;

        if($kindKarname->compassStatus)
            $status = quizStatus::where('level', '=', 3)->get();

        if($kindKarname->compassAvg &&  $kindKarname->compassMaxPercent)
            $avgs = DB::select('select SUM(percent) / count(*) as avg, MAX(percent) as maxPercent FROM compassesPercent WHERE qId = ' . $quizId . ' GROUP by(cId)');
        else if($kindKarname->subjectAvg)
            $avgs = DB::select('select SUM(percent) / count(*) as avg FROM compassesPercent WHERE qId = ' . $quizId . ' GROUP by(cId)');

        $cityRank = array();
        $stateRank = array();
        $countryRank = array();

        $compasses = $this->getCompassesQuiz($quizId);

        if($kindKarname->compassCityRank) {
            $counter = 0;
            foreach ($compasses as $compass) {
                $tmp = DB::select('SELECT compassesPercent.uId, compassesPercent.percent as taraz from azmoon.students std, azmoon.cities ci, compassesPercent WHERE std.id = compassesPercent.uId and std.city_id = ' . $cityId . ' and compassesPercent.qId = ' . $quizId . ' and compassesPercent.cId = ' . $compass->id . ' ORDER by compassesPercent.percent DESC');
                $cityRank[$counter++] = $this->getRank($tmp, $uId);
            }
        }

        if($kindKarname->compassStateRank) {
            $counter = 0;
            $stateId = City::whereId($cityId)->state->id;
            foreach ($compasses as $compass) {
                $tmp = DB::select('SELECT compassesPercent.uId, compassesPercent.percent as taraz from azmoon.students std, azmoon.cities ci, compassesPercent WHERE std.id = compassesPercent.uId and std.city_id = ci.id and ci.state_id = ' . $stateId . ' and compassesPercent.qId = ' . $quizId . ' and compassesPercent.cId = ' . $compass->id . ' ORDER by compassesPercent.percent DESC');
                $stateRank[$counter++] = $this->getRank($tmp, $uId);
            }
        }

        if($kindKarname->compassCountryRank) {
            $counter = 0;
            foreach ($compasses as $compass) {
                $tmp = DB::select('SELECT compassesPercent.uId, compassesPercent.percent as taraz from compassesPercent WHERE compassesPercent.qId = ' . $quizId . ' and compassesPercent.cId = ' . $compass->id . ' ORDER by compassesPercent.percent DESC');
                $countryRank[$counter++] = $this->getRank($tmp, $uId);
            }
        }


        $inCorrects =  DB::select('SELECT count(*) as inCorrects, questions.compass_id as target FROM roq, qoq, questions WHERE roq.qoqId = qoq.id and qoq.quiz_id = ' . $quizId . ' and qoq.question_id = questions.id and questions.ans <> roq.result and roq.result <> 0 and roq.uId = ' . $uId . ' group by(questions.compass_id)');
        $corrects =  DB::select('SELECT count(*) as corrects, questions.compass_id as target FROM roq, qoq, questions WHERE roq.qoqId = qoq.id and qoq.quiz_id = ' . $quizId . ' and qoq.question_id = questions.id and questions.ans = roq.result and roq.uId = ' . $uId . ' group by(questions.compass_id)');
        $total =  DB::select('SELECT count(*) as total, questions.compass_id as target FROM roq, qoq, questions WHERE roq.qoqId = qoq.id and qoq.quiz_id = ' . $quizId . ' and qoq.question_id = questions.id and roq.uId = ' . $uId . ' group by(questions.compass_id)');

        $roq = $this->getResultOfSpecificContainer($total, $corrects, $inCorrects);

        $totalMark = 0;
        if($kindKarname->compassMark)
            $totalMark = Quiz::whereId($quizId)->mark;

        $minusMark = Quiz::whereId($quizId)->minusMark;

        return view('compassKarname', array('quiz_id' => $quizId, 'status' => $status, 'roq' => $roq, 'compasses' => $compasses,
            'kindKarname' => $kindKarname, 'avgs' => $avgs, 'cityRank' => $cityRank, 'stateRank' => $stateRank,
            'countryRank' => $countryRank, 'totalMark' => $totalMark, 'minusMark' => $minusMark));
    }

    private function getSubjectsQuiz($quizId, $lId) {
        $sIds = DB::select('SELECT DISTINCT S.nameSubject, S.id as id from subjects S, questions q, qoq QO WHERE QO.quiz_id = ' . $quizId . ' and QO.question_id = q.id and S.id = q.subject_id and S.id_l = ' . $lId);
        if(count($sIds) > 0)
            return $sIds;
        return null;
    }

    private function getCompassesQuiz($quizId) {
        $cIds = DB::select('SELECT DISTINCT C.name, C.id as id from compass C, questions q, qoq QO WHERE QO.quiz_id = ' . $quizId . ' and QO.question_id = q.id and C.id = q.compass_id');
        if(count($cIds) > 0)
            return $cIds;
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
            if($tmp[$j]->uId == $uId) {
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
        $ranks = DB::select('SELECT qentry.uId, sum(taraz.taraz * (SELECT lessons.coherence FROM lessons WHERE lessons.id = taraz.lId)) as weighted_avg from qentry, taraz WHERE qentry.id = taraz.qentryId and qentry.qId = ' . $quizId . ' GROUP by (qentry.uId) ORDER by weighted_avg DESC');
        for($i = 0; $i < count($ranks); $i++) {
            if($ranks[$i]->uId == $uId) {
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
        $ranks = DB::select('SELECT qentry.uId, sum(taraz.taraz * (SELECT lessons.coherence FROM lessons WHERE lessons.id = taraz.lId)) as weighted_avg from qentry, taraz, azmoon.students std WHERE qentry.id = taraz.qentryId and qentry.uId = std.id AND std.city_id = ' . $cityId . ' and qentry.qId = ' . $quizId . ' GROUP by (qentry.uId) ORDER by weighted_avg DESC');
        for($i = 0; $i < count($ranks); $i++) {
            if($ranks[$i]->uId == $uId) {
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
        $ranks = DB::select('SELECT qentry.uId, sum(taraz.taraz * (SELECT lessons.coherence FROM lessons WHERE lessons.id = taraz.lId)) as weighted_avg from qentry, taraz, azmoon.students std, azmoon.cities ci WHERE qentry.id = taraz.qentryId and qentry.uId = std.id AND std.city_id = ci.id and ci.state_id = ' . $stateId . ' and qentry.qId = ' . $quizId . ' GROUP by (qentry.uId) ORDER by weighted_avg DESC');
        for($i = 0; $i < count($ranks); $i++) {
            if($ranks[$i]->uId == $uId) {
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