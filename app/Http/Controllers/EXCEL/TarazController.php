<?php

class TarazController extends BaseController {

    function fillSubjectsPercentTable($quizId) {

        SubjectsPercent::where('qId', '=', $quizId)->delete();

        $minusMark = Quiz::find($quizId)->minusMark;

        $uIds = qentry::where('qId', '=', $quizId)->select('uId')->get();
        $sIds = DB::select('select DISTINCT questions.subject_id as sId FROM questions, qoq WHERE qoq.quizId = ' . $quizId . ' AND qoq.questionId = questions.id');
        $cIds = DB::select('select DISTINCT questions.compassId as cId FROM questions, qoq WHERE qoq.quizId = ' . $quizId . ' AND qoq.questionId = questions.id');

        $inCorrectsInSubjects = $correctsInSubjects = $inCorrectsInCompasses = $correctsInCompasses = array();

        foreach($uIds as $uId) {
            foreach($sIds as $sId) {
                $inCorrectsInSubjects[$uId->uId][$sId->sId] = 0;
                $correctsInSubjects[$uId->uId][$sId->sId] = 0;
            }
            foreach ($cIds as $cId) {
                $inCorrectsInCompasses[$uId->uId][$cId->cId] = 0;
                $correctsInCompasses[$uId->uId][$cId->cId] = 0;
            }
        }

        $qoqs = DB::select('select qoq.id as qoqId, questions.ans as ans, questions.subject_id as sId, questions.compassId as cId from qoq, questions WHERE qoq.quizId = ' . $quizId . ' AND qoq.questionId = questions.id');
        $totals = array();

        foreach ($sIds as $sId)
            $totals[$sId->sId] = 0;

        $totalsC = array();

        foreach ($cIds as $cId)
            $totalsC[$cId->cId] = 0;


        foreach ($qoqs as $qoq) {
            $roqs = ROQ::where('qoqId', '=', $qoq->qoqId)->select('result', 'uId')->get();
            $totals[$qoq->sId]++;
            $totalsC[$qoq->cId]++;
            foreach ($roqs as $roq) {
                if($qoq->ans == $roq->result) {
                    $correctsInSubjects[$roq->uId][$qoq->sId]++;
                    $correctsInCompasses[$roq->uId][$qoq->cId]++;
                }
                else if($roq->result != 0) {
                    $inCorrectsInSubjects[$roq->uId][$qoq->sId]++;
                    $inCorrectsInCompasses[$roq->uId][$qoq->cId]++;
                }
            }
        }


        foreach ($uIds as $uId) {
            foreach ($sIds as $sId) {
                $subjectsPercent = new SubjectsPercent();
                $subjectsPercent->qId = $quizId;
                $subjectsPercent->sId = $sId->sId;
                $subjectsPercent->uId = $uId->uId;

                if($minusMark)
                    $subjectsPercent->percent =
                        round((3.0 * $correctsInSubjects[$uId->uId][$sId->sId] - $inCorrectsInSubjects[$uId->uId][$sId->sId]) / (3.0 * $totals[$sId->sId]), 4) * 100;
                else
                    $subjectsPercent->percent =
                        round($correctsInSubjects[$uId->uId][$sId->sId] / $totals[$sId->sId], 4) * 100;

                try {
                    $subjectsPercent->save();
                }
                catch (Exception $x) {
                    return -1;
                }

            }
            foreach ($cIds as $cId) {
                $compassesPercent = new CompassesPercent();
                $compassesPercent->qId = $quizId;
                $compassesPercent->cId = $cId->cId;
                $compassesPercent->uId = $uId->uId;

                if($minusMark)
                    $compassesPercent->percent =
                        round((3.0 * $correctsInCompasses[$uId->uId][$cId->cId] - $inCorrectsInCompasses[$uId->uId][$cId->cId]) / (3.0 * $totalsC[$cId->cId]), 4) * 100;
                else
                    $compassesPercent->percent =
                        round($correctsInCompasses[$uId->uId][$cId->cId] / $totalsC[$cId->cId], 4) * 100;
                try {
                    $compassesPercent->save();
                }
                catch (Exception $x) {
                    return -1;
                }
            }
        }

        return 1;
    }

    public function getEnherafMeyar($lId, $lessonAvg, $quizId) {

        $percents = DB::select('select percent from taraz, qentry qe WHERE taraz.lId = ' . $lId .' and taraz.qentryId = qe.id AND qe.qId = '. $quizId);
        $sum = 0.0;
        for($i = 0; $i < count($percents); $i++)
            $sum += pow($percents[$i]->percent - $lessonAvg, 2);
        $sum /= count($percents);
        $sum = sqrt($sum);

        $tmp = new Enheraf();
        $tmp->lId = $lId;
        $tmp->lessonAVG = $lessonAvg;
        $tmp->qId = $quizId;
        $tmp->val = $sum;
        $tmp->save();
    }

    public function createTarazTable() {

        $msg = "";

        if(isset($_POST["createTaraz"]) && isset($_POST["quizId"])) {

            $quizId = makeValidInput($_POST["quizId"]);
			
            try {

                if($this->fillSubjectsPercentTable($quizId) == -1) {
                    $msg = "مشکلی در ایجاد جدول تراز آزمون ایجاد شده است";
                }
                else {
				
                    $this->checkDataQ($quizId);
                    $qentryIds = qentry::where('qId', '=', $quizId)->select('uId', 'id')->get();
					
                    $avgs = $this->getAverageLessons($quizId, $qentryIds);

                    for ($i = 0; $i < count($avgs); $i++) {
                        $this->getEnherafMeyar($avgs[$i][0], $avgs[$i][1], $quizId);
                    }

                    $tmp = array();
                    for ($i = 0; $i < count($qentryIds); $i++) {
                        $tmp[$i] = $qentryIds[$i]->id;
                    }
					
                    return View::make('createTaraz', array('quizId' => $quizId, 'qentryIds' => $tmp));
                }
            }
            catch (Exception $e){
                if($e->getMessage() == 'quiz_end_time_error')
                    $msg = "زمان آزمون مورد نظر هنوز به اتمام نرسیده است";
                else if($e->getMessage() == 'duplicate_error')
                    $msg = "جدول تراز برای این آزمون قبلا ساخته شده است";
            }
        }

        $quizes = Quiz::select('id', 'QN')->get();
        return View::make('createTarazTable', array('msg' => $msg, 'mode' => 'create', 'quizes' => $quizes));
    }

    public function deleteTarazTable() {

        $msg = "";

        if(isset($_POST["createTaraz"])) {
            $quizId = makeValidInput($_POST["quizId"]);

            DB::select('delete from taraz where qentryId IN (SELECT id from qentry where qId = ' . $quizId . ')');
            SubjectsPercent::where('qId', '=', $quizId)->delete();
            Enheraf::where('qId', '=', $quizId)->delete();
            CompassesPercent::where('qId', '=', $quizId)->delete();
            
            $msg = "جدول تراز آزمون مورد نظر با موفقیت حذف گردید";

        }

        $quizes = Quiz::select('id', 'QN')->get();
        return View::make('createTarazTable', array('msg' => $msg, 'mode' => 'delete', 'quizes' => $quizes));
    }

    private function checkDataQ($qId) {
        
        include_once 'Date.php';

        $date = getToday();
        $quiz = Quiz::find($qId);

        if($date["date"] < $quiz->eDate || $date["date"] == $quiz->eDate && $date["time"] < $quiz->eTime)
            throw new Exception('quiz_end_time_error');
        
        $tmp = DB::select('SELECT id FROM taraz WHERE qentryId IN (SELECT id FROM qentry WHERE qId = ' . $qId . ')');
        if($tmp != null && count($tmp) > 0)
            throw new Exception('duplicate_error');
    }

    private function getAverageLesson($lId, $qId, $qentryIds) {

        $minusMark = Quiz::find($qId)->minusMark;

        $questionIds = DB::select('SELECT qoq.id, qoq.questionId, questions.ans FROM qoq, questions WHERE qoq.quizId = ' . $qId . ' and questions.id = qoq.questionId and (SELECT subjects.id_l FROM subjects WHERE subjects.id = questions.subject_id) = ' . $lId);
        $corrects = $inCorrects = $total = 0;
        for($i = 0; $i < count($qentryIds); $i++) {
            $tmpTotal = $tmpCorrects = $tmpInCorrects = 0;
            for($j = 0; $j < count($questionIds); $j++) {
                $conditions = ['qoqId' => $questionIds[$j]->id, 'uId' => $qentryIds[$i]->uId];
                $stdAns = ROQ::where($conditions)->select('result')->get();
                if(count($stdAns) > 0) {
                    $tmpTotal++;
                    if($questionIds[$j]->ans == $stdAns[0]->result)
                        $tmpCorrects++;
                    else if($questionIds[$j]->ans != $stdAns[0]->result && $stdAns[0]->result != 0)
                        $tmpInCorrects++;
                }
            }
            $conditions = ["qentryId" => $qentryIds[$i]->id, 'lId' => $lId];
            $taraz = Taraz::where($conditions)->first();
            if($taraz != null) {
                if($minusMark)
                    $taraz->percent = round((3.0 * $tmpCorrects - $tmpInCorrects) / (3.0 * $tmpTotal) * 100, 2);
                else
                    $taraz->percent = round($tmpCorrects / $tmpTotal * 100, 2);
                $taraz->save();
            }
            else {
                $taraz = new Taraz();
                $taraz->qentryId = $qentryIds[$i]->id;
                $taraz->lId = $lId;
                if($minusMark)
                    $taraz->percent = round((3.0 * $tmpCorrects - $tmpInCorrects) / (3.0 * $tmpTotal) * 100, 2);
                else
                    $taraz->percent = round($tmpCorrects / $tmpTotal * 100, 2);
                $taraz->save();
            }
            $total += $tmpTotal;
            $corrects += $tmpCorrects;
            $inCorrects += $tmpInCorrects;
        }
        if($total == 0)
            $total = 1;

        if($minusMark)
            return round(((((3.0 * $corrects - $inCorrects) / (3.0 * $total)) * 100) / count($qentryIds)), 2);
        else
            return round(((($corrects / $total) * 100) / count($qentryIds)), 2);
    }

    private function getAverageLessons($qId, $qentryIds) {

        $lIds = getLessonQuiz($qId);
        $avgs = array();
        for($i = 0; $i < count($lIds); $i++) {
            $avgs[$i][0] = $lIds[$i]->id;
            $avgs[$i][1] = $this->getAverageLesson($lIds[$i]->id, $qId, $qentryIds);
        }
        return $avgs;
    }
}