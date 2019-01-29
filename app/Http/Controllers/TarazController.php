<?php

namespace App\Http\Controllers;

use App\models\Enheraf;
use App\models\QEntry;
use App\models\QOQ;
use App\models\Quiz;
use App\models\ROQ;
use App\models\ROQ2;
use App\models\SubjectsPercent;
use App\models\Taraz;
use Illuminate\Support\Facades\DB;

class TarazController extends Controller {

    function fillSubjectsPercentTable($quizId) {

        SubjectsPercent::whereQId($quizId)->delete();

        $minusMark = Quiz::whereId($quizId)->minusMark;

        $uIds = QEntry::whereQId($quizId)->orderBy('u_id', 'ASC')->select('u_id')->get();

        $sIds = DB::select('select DISTINCT questions.subject_id as sId FROM questions, qoq WHERE qoq.quiz_id = ' . $quizId . ' AND qoq.question_id = questions.id');

        $inCorrectsInSubjects = $correctsInSubjects = array();
        $counter = 0;

        for($i = 0; $i < count($uIds); $i++) {
            foreach($sIds as $sId) {
                $inCorrectsInSubjects[$counter][$sId->sId] = 0;
                $correctsInSubjects[$counter][$sId->sId] = 0;
            }
            $counter++;
        }

        $qoqs = DB::select('select qoq.id as qoqId, questions.ans as ans, questions.subject_id as sId from qoq, questions WHERE qoq.quiz_id = ' . $quizId . ' AND qoq.question_id = questions.id');
        $totals = array();

        foreach ($sIds as $sId)
            $totals[$sId->sId] = 0;

        foreach ($qoqs as $qoq) {
            $roqs = ROQ::whereQoqId($qoq->qoqId)->orderBy('u_id', 'ASC')->select('result', 'u_id')->get();

            $totals[$qoq->sId]++;
            $counter = 0;

            foreach ($roqs as $roq) {

                if($qoq->ans == $roq->result)
                    $correctsInSubjects[$counter][$qoq->sId]++;

                else if($roq->result != 0)
                    $inCorrectsInSubjects[$counter][$qoq->sId]++;

                $counter++;
            }

        }

        $counter = 0;

        foreach ($uIds as $uId) {
            foreach ($sIds as $sId) {
                $subjectsPercent = new SubjectsPercent();
                $subjectsPercent->q_id = $quizId;
                $subjectsPercent->s_id = $sId->sId;
                $subjectsPercent->u_id = $uId->u_id;

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
            
            $counter++;
        }

        return 1;
    }

    public function getEnherafMeyar($lId, $lessonAvg, $quizId) {

        $percents = DB::select('select percent from taraz, qentry qe WHERE taraz.l_id = ' . $lId .' and taraz.q_entry_id = qe.id AND qe.q_id = '. $quizId);
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

                include_once 'Date.php';

                $this->checkDataQ($quizId);
                $qEntryIds = QEntry::whereQId($quizId)->select('u_id', 'id')->get();

                $avgs = $this->getAverageLessons($quizId, $qEntryIds);


                for ($i = 0; $i < count($avgs); $i++) {
                    $this->getEnherafMeyar($avgs[$i][0], $avgs[$i][1], $quizId);
                }


                if($this->fillSubjectsPercentTable($quizId) == -1) {
                    $msg = "مشکلی در ایجاد جدول تراز آزمون ایجاد شده است";

                    $quizes = Quiz::select('id', 'QN')->get();
                    return view('createTarazTable', array('msg' => $msg, 'mode' => 'create', 'quizes' => $quizes));

                }

                $this->transferFromROQ2ToROQ($quizId);

                $tmp = array();
                for ($i = 0; $i < count($qEntryIds); $i++) {
                    $tmp[$i] = $qEntryIds[$i]->id;
                }

                return view('createTaraz', array('quizId' => $quizId, 'qEntryIds' => $tmp));

            }
            catch (\Exception $e){
                if($e->getMessage() == 'quiz_end_time_error')
                    $msg = "زمان آزمون مورد نظر هنوز به اتمام نرسیده است";
                else if($e->getMessage() == 'duplicate_error')
                    $msg = "جدول تراز برای این آزمون قبلا ساخته شده است";

                dd($e->getMessage());
            }
        }

        $quizes = Quiz::select('id', 'QN')->get();
        return view('createTarazTable', array('msg' => $msg, 'mode' => 'create', 'quizes' => $quizes));
    }

    public function transferFromROQ2ToROQ($quizId) {

        $roq2 = ROQ2::whereQuizId($quizId)->get();

        $condition = ['quiz_id' => $quizId, 'qNo' => 1];

        foreach ($roq2 as $itr) {

            $str = $itr->result;

            for($i = 0; $i < strlen($str); $i++) {
                $tmp = new ROQ();
                $tmp->u_id = $itr->u_id;
                $tmp->result = $str[$i];
                $condition["qNo"] = $i + 1;
                $tmp->qoq_id = QOQ::where($condition)->first()->id;

                $tmp->save();
            }

        }

        DB::delete('DELETE t1 FROM roq t1
        INNER JOIN
    roq t2 
WHERE
    t1.id < t2.id AND t1.u_id = t2.u_id and t1.qoq_id = t2.qoq_id');

        DB::delete('DELETE from roq2 WHERE quiz_id = ' . $quizId);
    }

    public function deleteTarazTable() {

        $msg = "";

        if(isset($_POST["createTaraz"])) {
            $quizId = makeValidInput($_POST["quiz_id"]);

            DB::select('delete from taraz where q_entry_id IN (SELECT id from qentry where q_id = ' . $quizId . ')');
            SubjectsPercent::whereQId($quizId)->delete();
            Enheraf::whereQId($quizId)->delete();
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
                $conditions = ['qoq_id' => $questionIds[$j]->id, 'u_id' => $qEntryIds[$i]->u_id];
                $stdAns = ROQ::where($conditions)->select('result')->first();
                $tmpTotal++;

                if($stdAns != null) {
                    if($questionIds[$j]->ans == $stdAns->result)
                        $tmpCorrects++;
                    else if($questionIds[$j]->ans != $stdAns->result && $stdAns->result != 0)
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
                    $taraz->q_entry_id = $qEntryIds[$i]->id;
                    $taraz->l_id = $lId;
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