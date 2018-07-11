<?php

class TarazController extends BaseController {

    function fillSubjectsPercentTable($quizId) {

        SubjectsPercent::where('qId', '=', $quizId)->delete();

        $minusMark = Quiz::find($quizId)->minusMark;

        $uIds = qentry::where('qId', '=', $quizId)->orderBy('uId', 'ASC')->select('uId')->get();

        $sIds = DB::select('select DISTINCT questions.subject_id as sId FROM questions, qoq WHERE qoq.quizId = ' . $quizId . ' AND qoq.questionId = questions.id');
        $cIds = DB::select('select DISTINCT questions.compassId as cId FROM questions, qoq WHERE qoq.quizId = ' . $quizId . ' AND qoq.questionId = questions.id');

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

        $qoqs = DB::select('select qoq.id as qoqId, questions.ans as ans, questions.subject_id as sId, questions.compassId as cId from qoq, questions WHERE qoq.quizId = ' . $quizId . ' AND qoq.questionId = questions.id');
        $totals = array();

        foreach ($sIds as $sId)
            $totals[$sId->sId] = 0;

        $totalsC = array();

        foreach ($cIds as $cId)
            $totalsC[$cId->cId] = 0;


        foreach ($qoqs as $qoq) {
            $roqs = ROQ::where('qoqId', '=', $qoq->qoqId)->orderBy('uId', 'ASC')->select('result', 'uId')->get();

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
                $subjectsPercent->qId = $quizId;
                $subjectsPercent->sId = $sId->sId;
                $subjectsPercent->uId = $uId->uId;

                if($minusMark)
                    $subjectsPercent->percent =
                        round((3.0 * $correctsInSubjects[$counter][$sId->sId] - $inCorrectsInSubjects[$counter][$sId->sId]) / (3.0 * $totals[$sId->sId]), 4) * 100;
                else
                    $subjectsPercent->percent =
                        round($correctsInSubjects[$counter][$sId->sId] / $totals[$sId->sId], 4) * 100;

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
                        round((3.0 * $correctsInCompasses[$counter][$cId->cId] - $inCorrectsInCompasses[$counter][$cId->cId]) / (3.0 * $totalsC[$cId->cId]), 4) * 100;
                else
                    $compassesPercent->percent =
                        round($correctsInCompasses[$counter][$cId->cId] / $totalsC[$cId->cId], 4) * 100;
                try {
                    $compassesPercent->save();
                }
                catch (Exception $x) {
                    return -1;
                }
            }

            $counter++;
        }

        return 1;
    }

    public function getEnherafMeyar($lId, $lessonAvg, $quizId) {

        $percents = DB::select('select percent from taraz, qEntry qe WHERE taraz.lId = ' . $lId .' and taraz.qEntryId = qe.id AND qe.qId = '. $quizId);
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

                $notFounded = DB::select('SELECT DISTINCT(roq.uId) FROM `roq`, qoq WHERE uId not in (SELECT uId from qentry WHERE qId = ' . $quizId . ') and qoqId = qoq.id and qoq.quizId = ' . $quizId);


                include_once 'Date.php';

                foreach ($notFounded as $itr) {

                    try {
                        $tmp = new qEntry();
                        $tmp->qId = $quizId;
                        $tmp->timeEntry = time();
                        $tmp->dateEntry = getToday()["date"];
                        $tmp->status = 1;
                        $tmp->uId = $itr->uId;
                        $tmp->save();
                    }
                    catch (Exception $x) {
                        dd($x->getMessage());
                    }

                }

                $this->checkDataQ($quizId);
                $qEntryIds = qEntry::where('qId', '=', $quizId)->select('uId', 'id')->get();

                $avgs = $this->getAverageLessons($quizId, $qEntryIds);


                for ($i = 0; $i < count($avgs); $i++) {
                    $this->getEnherafMeyar($avgs[$i][0], $avgs[$i][1], $quizId);
                }


                if($this->fillSubjectsPercentTable($quizId) == -1) {
                    $msg = "مشکلی در ایجاد جدول تراز آزمون ایجاد شده است";
                }

                $qoqs = QOQ::where('quizId', '=', $quizId)->get();

                $tmp = array();
                for ($i = 0; $i < count($qEntryIds); $i++) {
                    foreach ($qoqs as $qoq) {
                        $condition = ['uId' => $qEntryIds[$i]->uId, 'qoqId' => $qoq->id];
                        if(ROQ::where($condition)->count() == 0) {
                            $roq = new ROQ();
                            $roq->qoqId = $qoq->id;
                            $roq->result = 0;
                            $roq->uId = $qEntryIds[$i]->uId;
                            $roq->save();
                        }
                    }
                    $tmp[$i] = $qEntryIds[$i]->id;
                }

                return View::make('createTaraz', array('quizId' => $quizId, 'qEntryIds' => $tmp));

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

            DB::select('delete from taraz where qEntryId IN (SELECT id from qEntry where qId = ' . $quizId . ')');
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
        
        $tmp = DB::select('SELECT id FROM taraz WHERE qEntryId IN (SELECT id FROM qentry WHERE qId = ' . $qId . ')');
        if($tmp != null && count($tmp) > 0)
            throw new Exception('duplicate_error');
    }

    private function getAverageLesson($lId, $qId, $qEntryIds) {

        $minusMark = Quiz::find($qId)->minusMark;

        $questionIds = DB::select('SELECT qoq.id, qoq.questionId, questions.ans FROM qoq, questions WHERE qoq.quizId = ' . $qId . ' and questions.id = qoq.questionId and (SELECT subjects.id_l FROM subjects WHERE subjects.id = questions.subject_id) = ' . $lId);
        $corrects = $inCorrects = $total = 0;
        for($i = 0; $i < count($qEntryIds); $i++) {
            $tmpTotal = $tmpCorrects = $tmpInCorrects = 0;

            for($j = 0; $j < count($questionIds); $j++) {
                $conditions = ['qoqId' => $questionIds[$j]->id, 'uId' => $qEntryIds[$i]->uId];
                $stdAns = ROQ::where($conditions)->select('result')->get();

                $tmpTotal++;

                if(count($stdAns) > 0) {
                    if($questionIds[$j]->ans == $stdAns[0]->result)
                        $tmpCorrects++;
                    else if($questionIds[$j]->ans != $stdAns[0]->result && $stdAns[0]->result != 0)
                        $tmpInCorrects++;
                }
            }

            $conditions = ["qEntryId" => $qEntryIds[$i]->id, 'lId' => $lId];
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
                catch (Exception $x){
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