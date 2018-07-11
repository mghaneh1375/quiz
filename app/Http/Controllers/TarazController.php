<?php

namespace App\Http\Controllers;

use App\models\CompassesPercent;
use App\models\Enheraf;
use App\models\QEntry;
use App\models\QOQ;
use App\models\Quiz;
use App\models\ROQ;
use App\models\SubjectsPercent;
use App\models\Taraz;
use Illuminate\Support\Facades\DB;

class TarazController extends Controller {

    function fillSubjectsPercentTable($quizId) {

        SubjectsPercent::whereQId($quizId)->delete();

        $minusMark = Quiz::whereId($quizId)->minusMark;

        $uIds = QEntry::whereQId($quizId)->orderBy('u_id', 'ASC')->select('u_id')->get();

        $sIds = DB::select('select DISTINCT questions.subject_id as sId FROM questions, qoq WHERE qoq.quiz_id = ' . $quizId . ' AND qoq.question_id = questions.id');
        $cIds = DB::select('select DISTINCT questions.compass_id as cId FROM questions, qoq WHERE qoq.quiz_id = ' . $quizId . ' AND qoq.question_id = questions.id');

        $inCorrectsInSubjects = $correctsInSubjects = $inCorrectsInCompasses = $correctsInCompasses = array();
        $counter = 0;

        foreach($uIds as $uId) {
            foreach($sIds as $sId) {
                $inCorrectsInSubjects[$counter][$sId->sId] = 0;
                $correctsInSubjects[$counter][$sId->sId] = 0;
            }
            foreach ($cIds as $cId) {
                $inCorrectsInCompasses[$counter][$cId->cId] = 0;
                $correctsInCompasses[$counter][$cId->cId] = 0;
            }
            $counter++;
        }

        $qoqs = DB::select('select qoq.id as qoqId, questions.ans as ans, questions.subject_id as sId, questions.compass_id as cId from qoq, questions WHERE qoq.quiz_id = ' . $quizId . ' AND qoq.question_id = questions.id');
        $totals = array();

        foreach ($sIds as $sId)
            $totals[$sId->sId] = 0;

        $totalsC = array();

        foreach ($cIds as $cId)
            $totalsC[$cId->cId] = 0;


        foreach ($qoqs as $qoq) {
            $roqs = ROQ::whereQOQId($qoq->qoqId)->orderBy('u_id', 'ASC')->select('result', 'uId')->get();

            $totals[$qoq->sId]++;
            $totalsC[$qoq->cId]++;
            $counter = 0;

            foreach ($roqs as $roq) {

                if($qoq->ans == $roq->result) {
                    $correctsInSubjects[$counter][$qoq->sId]++;
                    $correctsInCompasses[$counter][$qoq->cId]++;
                }
                else if($roq->result != 0) {
                    $inCorrectsInSubjects[$counter][$qoq->sId]++;
                    $inCorrectsInCompasses[$counter][$qoq->cId]++;
                }

                $counter++;
            }

        }

        $counter = 0;

        foreach ($uIds as $uId) {
            foreach ($sIds as $sId) {
                $subjectsPercent = new SubjectsPercent();
                $subjectsPercent->q_id = $quizId;
                $subjectsPercent->s_id = $sId->sId;
                $subjectsPercent->u_id = $uId->uId;

                if($minusMark)
                    $subjectsPercent->percent =
                        round((3.0 * $correctsInSubjects[$counter][$sId->sId] - $inCorrectsInSubjects[$counter][$sId->sId]) / (3.0 * $totals[$sId->sId]), 4) * 100;
                else
                    $subjectsPercent->percent =
                        round($correctsInSubjects[$counter][$sId->sId] / $totals[$sId->sId], 4) * 100;

                try {
                    $subjectsPercent->save();
                }
                catch (\Exception $x) {
                    return -1;
                }

            }
            foreach ($cIds as $cId) {
                $compassesPercent = new CompassesPercent();
                $compassesPercent->q_id = $quizId;
                $compassesPercent->c_id = $cId->cId;
                $compassesPercent->u_id = $uId->uId;

                if($minusMark)
                    $compassesPercent->percent =
                        round((3.0 * $correctsInCompasses[$counter][$cId->cId] - $inCorrectsInCompasses[$counter][$cId->cId]) / (3.0 * $totalsC[$cId->cId]), 4) * 100;
                else
                    $compassesPercent->percent =
                        round($correctsInCompasses[$counter][$cId->cId] / $totalsC[$cId->cId], 4) * 100;
                try {
                    $compassesPercent->save();
                }
                catch (\Exception $x) {
                    return -1;
                }
            }

            $counter++;
        }

        return 1;
    }

    public function getEnherafMeyar($lId, $lessonAvg, $quizId) {

        $percents = DB::select('select percent from taraz, qEntry qe WHERE taraz.l_id = ' . $lId .' and taraz.q_entry_id = qe.id AND qe.q_id = '. $quizId);
        $sum = 0.0;
        for($i = 0; $i < count($percents); $i++)
            $sum += pow($percents[$i]->percent - $lessonAvg, 2);
        $sum /= count($percents);
        $sum = sqrt($sum);

        $tmp = new Enheraf();
        $tmp->l_id = $lId;
        $tmp->lessonAVG = $lessonAvg;
        $tmp->q_id = $quizId;
        $tmp->val = $sum;
        $tmp->save();
    }

    public function createTarazTable() {

        $msg = "";

        if(isset($_POST["createTaraz"]) && isset($_POST["quiz_id"])) {

            $quizId = makeValidInput($_POST["quiz_id"]);
			
            try {

                $notFounded = DB::select('SELECT DISTINCT(roq.u_id) FROM `roq`, qoq WHERE uId not in (SELECT uId from qentry WHERE qId = ' . $quizId . ') and qoq_id = qoq.id and qoq.quiz_id = ' . $quizId);


                include_once 'Date.php';

                foreach ($notFounded as $itr) {

                    try {
                        $tmp = new QEntry();
                        $tmp->q_id = $quizId;
                        $tmp->timeEntry = time();
                        $tmp->dateEntry = getToday()["date"];
                        $tmp->status = 1;
                        $tmp->u_id = $itr->uId;
                        $tmp->save();
                    }
                    catch (\Exception $x) {
                        dd($x->getMessage());
                    }

                }

                $this->checkDataQ($quizId);
                $qEntryIds = QEntry::whereQId($quizId)->select('uId', 'id')->get();

                $avgs = $this->getAverageLessons($quizId, $qEntryIds);


                for ($i = 0; $i < count($avgs); $i++) {
                    $this->getEnherafMeyar($avgs[$i][0], $avgs[$i][1], $quizId);
                }


                if($this->fillSubjectsPercentTable($quizId) == -1) {
                    $msg = "مشکلی در ایجاد جدول تراز آزمون ایجاد شده است";
                }

                $qoqs = QOQ::whereQuizId($quizId)->get();

                $tmp = array();
                for ($i = 0; $i < count($qEntryIds); $i++) {
                    foreach ($qoqs as $qoq) {
                        $condition = ['u_id' => $qEntryIds[$i]->uId, 'qoq_id' => $qoq->id];
                        if(ROQ::where($condition)->count() == 0) {
                            $roq = new ROQ();
                            $roq->qoq_id = $qoq->id;
                            $roq->result = 0;
                            $roq->u_id = $qEntryIds[$i]->uId;
                            $roq->save();
                        }
                    }
                    $tmp[$i] = $qEntryIds[$i]->id;
                }

                return view('createTaraz', array('quiz_id' => $quizId, 'qEntryIds' => $tmp));

            }
            catch (\Exception $e){
                if($e->getMessage() == 'quiz_end_time_error')
                    $msg = "زمان آزمون مورد نظر هنوز به اتمام نرسیده است";
                else if($e->getMessage() == 'duplicate_error')
                    $msg = "جدول تراز برای این آزمون قبلا ساخته شده است";
            }
        }

        $quizes = Quiz::select('id', 'QN')->get();
        return view('createTarazTable', array('msg' => $msg, 'mode' => 'create', 'quizes' => $quizes));
    }

    public function deleteTarazTable() {

        $msg = "";

        if(isset($_POST["createTaraz"])) {
            $quizId = makeValidInput($_POST["quiz_id"]);

            DB::select('delete from taraz where q_entry_id IN (SELECT id from qentry where q_id = ' . $quizId . ')');
            SubjectsPercent::whereQId($quizId)->delete();
            Enheraf::whereQId($quizId)->delete();
            CompassesPercent::whereQId( $quizId)->delete();
            
            $msg = "جدول تراز آزمون مورد نظر با موفقیت حذف گردید";

        }

        $quizes = Quiz::select('id', 'QN')->get();
        return view('createTarazTable', array('msg' => $msg, 'mode' => 'delete', 'quizes' => $quizes));
    }

    private function checkDataQ($qId) {
        
        include_once 'Date.php';

        $date = getToday();
        $quiz = Quiz::whereId($qId);

        if($date["date"] < $quiz->eDate || $date["date"] == $quiz->eDate && $date["time"] < $quiz->eTime)
            throw new \Exception('quiz_end_time_error');
        
        $tmp = DB::select('SELECT id FROM taraz WHERE q_entry_id IN (SELECT id FROM qentry WHERE q_id = ' . $qId . ')');
        if($tmp != null && count($tmp) > 0)
            throw new \Exception('duplicate_error');
    }

    private function getAverageLesson($lId, $qId, $qEntryIds) {

        $minusMark = Quiz::whereId($qId)->minusMark;

        $questionIds = DB::select('SELECT qoq.id, qoq.question_id, questions.ans FROM qoq, questions WHERE qoq.quiz_id = ' . $qId . ' and questions.id = qoq.question_id and (SELECT subjects.id_l FROM subjects WHERE subjects.id = questions.subject_id) = ' . $lId);
        $corrects = $inCorrects = $total = 0;
        for($i = 0; $i < count($qEntryIds); $i++) {
            $tmpTotal = $tmpCorrects = $tmpInCorrects = 0;

            for($j = 0; $j < count($questionIds); $j++) {
                $conditions = ['qoq_id' => $questionIds[$j]->id, 'u_id' => $qEntryIds[$i]->uId];
                $stdAns = ROQ::where($conditions)->select('result')->get();

                $tmpTotal++;

                if(count($stdAns) > 0) {
                    if($questionIds[$j]->ans == $stdAns[0]->result)
                        $tmpCorrects++;
                    else if($questionIds[$j]->ans != $stdAns[0]->result && $stdAns[0]->result != 0)
                        $tmpInCorrects++;
                }
            }

            $conditions = ["q_entry_id" => $qEntryIds[$i]->id, 'l_id' => $lId];
            $taraz = Taraz::where($conditions)->first();
            if($taraz != null) {

                if($minusMark)
                    $taraz->percent = round((3.0 * $tmpCorrects - $tmpInCorrects) / (3.0 * $tmpTotal) * 100, 2);
                else
                    $taraz->percent = round($tmpCorrects / $tmpTotal * 100, 2);
                $taraz->save();
            }
            else {

                try {
                    $taraz = new Taraz();
                    $taraz->qEntryId = $qEntryIds[$i]->id;
                    $taraz->lId = $lId;
                    if ($minusMark)
                        $taraz->percent = round((3.0 * $tmpCorrects - $tmpInCorrects) / (3.0 * $tmpTotal) * 100, 2);
                    else
                        $taraz->percent = round($tmpCorrects / $tmpTotal * 100, 2);
                    $taraz->save();
                }
                catch (\Exception $x){
                    dd($x->getMessage());
                }

            }

            $total += $tmpTotal;
            $corrects += $tmpCorrects;
            $inCorrects += $tmpInCorrects;
        }
        if($total == 0)
            $total = 1;

        if($minusMark)
            return round(((((3.0 * $corrects - $inCorrects) / (3.0 * $total)) * 100) / count($qEntryIds)), 2);

        return round(((($corrects / $total) * 100) / count($qEntryIds)), 2);
    }

    private function getAverageLessons($qId, $qEntryIds) {

        $lIds = getLessonQuiz($qId);

        $avgs = array();
        for($i = 0; $i < count($lIds); $i++) {
            $avgs[$i][0] = $lIds[$i]->id;
            $avgs[$i][1] = $this->getAverageLesson($lIds[$i]->id, $qId, $qEntryIds);
        }
        return $avgs;
    }
}