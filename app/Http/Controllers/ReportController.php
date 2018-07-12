<?php

namespace App\Http\Controllers;

use App\models\Lesson;
use App\models\Quiz;
use App\models\ROQ;
use App\models\StudentPanel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use PHPExcel;
use PHPExcel_Writer_Excel2007;

include_once __DIR__ . '/EXCEL/PHPExcel.php';

class ReportController extends Controller {

    private $girlSex = 0;

    public function showReport() {
        return view('showReport', array('quizes' => Quiz::all()));
    }

    public function reports($qId) {
        return view('reports', array('qId' => $qId));
    }

    public function questionAnalysis($qId, $lId) {


        $percents = DB::select("SELECT taraz.percent, qentry.u_id FROM qentry, taraz WHERE qentry.q_id = " . $qId . " AND taraz.q_entry_id = qentry.id and taraz.l_id = " . $lId);

        $questions = DB::select("SELECT questions.ans, qoq.id as qoqId FROM qoq, questions, subjects WHERE qoq.quiz_id = " . $qId . " AND qoq.question_id = questions.id and subjects.id = questions.subject_id and subjects.id_l = " . $lId);
        foreach($questions as $question) {

            $conditions = ['qoq_id' => $question->qoqId, 'result' => 0];
            $question->result0 = ROQ::where($conditions)->count();

            $conditions = ['qoq_id' => $question->qoqId, 'result' => 1];
            $question->result1 = ROQ::where($conditions)->count();

            $conditions = ['qoq_id' => $question->qoqId, 'result' => 2];
            $question->result2 = ROQ::where($conditions)->count();

            $conditions = ['qoq_id' => $question->qoqId, 'result' => 3];
            $question->result3 = ROQ::where($conditions)->count();

            $conditions = ['qoq_id' => $question->qoqId, 'result' => 4];
            $question->result4 = ROQ::where($conditions)->count();

            $question->total = $question->result0 + $question->result1 + $question->result2 + $question->result3 + $question->result4;
            $question->result0 /= $question->total;
            $question->result1 /= $question->total;
            $question->result2 /= $question->total;
            $question->result3 /= $question->total;
            $question->result4 /= $question->total;

            $question->result0 = round($question->result0, 2) * 100;
            $question->result1 = round($question->result1, 2) * 100;
            $question->result2 = round($question->result2, 2) * 100;
            $question->result3 = round($question->result3, 2) * 100;
            $question->result4 = round($question->result4, 2) * 100;

            switch ($question->ans) {
                case 1:
                    $question->inCorrectPercent = 100 - $question->result1 - $question->result0;
                    $target = $question->result1;
                    break;
                case 2:
                    $question->inCorrectPercent = 100 - $question->result2 - $question->result0;
                    $target = $question->result2;
                    break;
                case 3:
                    $question->inCorrectPercent = 100 - $question->result3 - $question->result0;
                    $target = $question->result3;
                    break;
                case 4:
                default:
                    $question->inCorrectPercent = 100 - $question->result4 - $question->result0;
                    $target = $question->result4;
                    break;
            }

            if($target < 30) {
                $question->level = "سخت";
            }
            else if($target < 70) {
                $question->level = "متوسط";
            }
            else
                $question->level = "ساده";

            $col1 = [];
            $i = 0;
            $sum = 0.0;

            foreach ($percents as  $percent) {

                $sum += $percent->percent;
                $conditions = ['uId' => $percent->uId, 'qoqId' => $question->qoqId];
                $tmp = ROQ::where($conditions)->first()->result;
                if($tmp == $question->ans)
                    $col1[$i] = 1;
                else
                    $col1[$i] = 0;
                $i++;
            }

            $sum /= count($percents);

            $sum2 = 0.0;

            for($i = 0; $i < count($col1); $i++) {
                $sum2 += $col1[$i];
            }
            $sum2 /= count($col1);

            $corel1 = $corel2 = $corel3 = 0;

            for($i = 0; $i < count($percents); $i++) {
                $corel1 += (($percents[$i]->percent - $sum) * ($col1[$i] - $sum2));
            }

            for($i = 0; $i < count($percents); $i++) {
                $corel2 += pow(($percents[$i]->percent - $sum), 2);
            }

            $corel2 = sqrt($corel2);

            for($i = 0; $i < count($percents); $i++) {
                $corel3 += pow(($col1[$i] - $sum2), 2);
            }

            $corel3 = sqrt($corel3);

            if($corel3 != 0 && $corel2 != 0)
                $question->corel = round($corel1 / ($corel2 * $corel3), 2);
            else
                $question->corel = "تعریف نشده";

            if($question->corel == "تعریف نشده")
                $question->status = "خیلی بد";
            else if($question->corel < 0.15)
                $question->status = "بد";
            else if($question->corel < 0.3)
                $question->status = "متوسط";
            else if($question->corel < 0.5)
                $question->status = "خوب";
            else
                $question->status = "خیلی خوب";
        }

        return view('questionAnalysis', array('questions' => $questions, 'qId' => $qId, 'lId' => $lId));

    }

    public function questionDiagramAnalysis($qId, $lId) {


        $percents = DB::select("SELECT taraz.percent, qentry.u_id FROM qentry, taraz WHERE qentry.q_id = " . $qId . " AND taraz.q_entry_id = qentry.id and taraz.l_id = " . $lId);
        $diagram = [];
        $counter = 0;
        $minArr = [0, 5, 15, 25, 35, 45, 55, 65, 75, 85, 95];
        $maxArr = [5, 15, 25, 35, 45, 55, 65, 75, 85, 95, 100];
        $questions = DB::select("SELECT questions.id, questions.ans, qoq.id as qoqId FROM qoq, questions, subjects WHERE qoq.quiz_id = " . $qId . " AND qoq.question_id = questions.id and subjects.id = questions.subject_id and subjects.id_l = " . $lId);

        foreach($questions as $question) {

            $conditions = ['qoq_id' => $question->qoqId, 'result' => 0];
            $question->result0 = ROQ::where($conditions)->count();

            $conditions = ['qoq_id' => $question->qoqId, 'result' => 1];
            $question->result1 = ROQ::where($conditions)->count();

            $conditions = ['qoq_id' => $question->qoqId, 'result' => 2];
            $question->result2 = ROQ::where($conditions)->count();

            $conditions = ['qoq_id' => $question->qoqId, 'result' => 3];
            $question->result3 = ROQ::where($conditions)->count();

            $conditions = ['qoq_id' => $question->qoqId, 'result' => 4];
            $question->result4 = ROQ::where($conditions)->count();

            $question->total = $question->result0 + $question->result1 + $question->result2 + $question->result3 + $question->result4;

            $question->result0P = round($question->result0 / $question->total, 2) * 100;
            $question->result1P = round($question->result1 / $question->total, 2) * 100;
            $question->result2P = round($question->result2 / $question->total, 2) * 100;
            $question->result3P = round($question->result3 / $question->total, 2) * 100;
            $question->result4P = round($question->result4 / $question->total, 2) * 100;

            switch ($question->ans) {
                case 1:
                    $question->inCorrectPercent = $question->total - $question->result1 - $question->result0;
                    $target = $question->result1P;
                    break;
                case 2:
                    $question->inCorrectPercent = $question->total - $question->result2 - $question->result0;
                    $target = $question->result2P;
                    break;
                case 3:
                    $question->inCorrectPercent = $question->total - $question->result3 - $question->result0;
                    $target = $question->result3P;
                    break;
                case 4:
                default:
                    $question->inCorrectPercent = $question->total - $question->result4 - $question->result0;
                    $target = $question->result4P;
                    break;
            }

            if($target < 30) {
                $question->level = "سخت";
            }
            else if($target < 70) {
                $question->level = "متوسط";
            }
            else
                $question->level = "ساده";

            $col1 = [];
            $i = 0;
            $sum = 0.0;

            foreach ($percents as  $percent) {

                $sum += $percent->percent;
                $conditions = ['uId' => $percent->uId, 'qoqId' => $question->qoqId];
                $tmp = ROQ::where($conditions)->first()->result;
                if($tmp == $question->ans)
                    $col1[$i] = 1;
                else
                    $col1[$i] = 0;
                $i++;
            }

            $sum /= count($percents);

            $sum2 = 0.0;

            for($i = 0; $i < count($col1); $i++) {
                $sum2 += $col1[$i];
            }
            $sum2 /= count($col1);

            $corel1 = $corel2 = $corel3 = 0;

            for($i = 0; $i < count($percents); $i++) {
                $corel1 += (($percents[$i]->percent - $sum) * ($col1[$i] - $sum2));
            }

            for($i = 0; $i < count($percents); $i++) {
                $corel2 += pow(($percents[$i]->percent - $sum), 2);
            }

            $corel2 = sqrt($corel2);

            for($i = 0; $i < count($percents); $i++) {
                $corel3 += pow(($col1[$i] - $sum2), 2);
            }

            $corel3 = sqrt($corel3);

            if($corel3 != 0 && $corel2 != 0)
                $question->corel = round($corel1 / ($corel2 * $corel3), 2);
            else
                $question->corel = "not defined";

            if($question->corel == "not defined")
                $question->status = "خیلی بد";
            else if($question->corel < 0.15)
                $question->status = "بد";
            else if($question->corel < 0.3)
                $question->status = "متوسط";
            else if($question->corel < 0.5)
                $question->status = "خوب";
            else
                $question->status = "خیلی خوب";

            $alaki = 0;
            for($i = 0; $i < 10; $i++) {

                $tmp = DB::select("SELECT count(*) as count_ FROM taraz, qentry WHERE taraz.percent <= " . $maxArr[$i] . " and taraz.percent >= " . $minArr[$i] . " and taraz.q_entry_id = qentry.id and qentry.q_id = " . $qId);
                $tmp2 = DB::select("SELECT count(*) as count_ FROM questions, taraz, qentry, roq, qoq WHERE taraz.percent <= " . $maxArr[$i] . " and taraz.percent >= " . $minArr[$i] . " and taraz.q_entry_id = qentry.id and qentry.q_id = " . $qId .  " and roq.result = questions.ans and qoq.question_id = questions.id and roq.qoq_id = qoq.id and qoq.quiz_id = " . $qId . " and questions.id = " . $question->id . " and taraz.l_id = " . $lId . " and roq.u_id = qentry.u_id");

                if($tmp[0]->count_ != 0)
                    $diagram[$counter][$alaki++] = round($tmp2[0]->count_ / $tmp[0]->count_, 2);
            }
            $counter++;
        }

        return view('questionDiagramAnalysis', array('questions' => $questions, 'diagram' => $diagram, 'qId' => $qId, 'lId' => $lId));

    }

    public function report1($qId, $stateId) { // استانی بر به تفکیک جنسیت

        $qEntries = DB::select("select qentry.id, qentry.u_id FROM medal.qentry as qentry, azmoon.students as std_, azmoon.cities as city WHERE qentry.q_id = " . $qId . " and std_.id = qentry.u_id and city.id = std_.city_id and city.state_id = " . $stateId);

        $girls = $boys = 0;

        $lessons = getLessonQuiz($qId);

        foreach ($lessons as $lesson) {
            $lesson->boyAvg = 0;
            $lesson->girlAvg = 0;
        }

        foreach ($qEntries as $qEntry) {

            $sex = DB::select("select std_.school_sex_id as sex FROM azmoon.students as std_ WHERE std_.id = " . $qEntry->uId);

            if($sex[0]->sex == $this->girlSex)
                $girls++;
            else
                $boys++;

            foreach ($lessons as $lesson) {

                $condition = ['qEntryId' => $qEntry->id, 'lId' => $lesson->id];

                if($sex[0]->sex == $this->girlSex) {
                    $lesson->girlAvg += Taraz::where($condition)->first()->percent;
                }
                else {
                    $lesson->boyAvg += Taraz::where($condition)->first()->percent;
                }
            }

        }

        $totalMark = [0, 0];
        if($girls == 0)
            $girls = 1;
        if($boys == 0)
            $boys = 1;

        foreach ($lessons as $lesson) {
            $lesson->girlAvg = $lesson->girlAvg / $girls;
            $lesson->boyAvg = $lesson->boyAvg / $boys;
            $totalMark[0] += $lesson->girlAvg;
            $totalMark[1] += $lesson->boyAvg;
        }

        $totalMark[0] = round($totalMark[0] / (count($lessons) * 5), 0);
        $totalMark[1] = round($totalMark[1] / (count($lessons) * 5), 0);

        return view('report1', array('lessons' => $lessons, 'girls' => $girls, 'boys' => $boys,
            'qId' => $qId, 'totalMark' => $totalMark));
    }

    public function report2($qId, $stateId) { // استانی بر به تفکیک جنسیت

        $qEntries = DB::select("select qentry.id, qentry.u_id, std_.school_sex_id as sex FROM medal.qentry as qentry, azmoon.students as std_, azmoon.cities as city WHERE qentry.q_id = " . $qId . " and std_.id = qentry.u_id and city.id = std_.city_id and city.state_id = " . $stateId);

        $girls = $boys = 0;

        $lessons = getLessonQuiz($qId);

        foreach ($lessons as $lesson) {
            $lesson->boyArr = [0, 0, 0, 0];
            $lesson->girlArr = [0, 0, 0, 0];
        }

        $boyTotal = [0, 0, 0, 0];
        $girlTotal = [0, 0, 0, 0];

        foreach ($qEntries as $qEntry) {

            $boyCount = $girlCount = 0;

            if($qEntry->sex == $this->girlSex) {
                $girls++;
                foreach ($lessons as $lesson) {

                    $condition = ['qEntryId' => $qEntry->id, 'lId' => $lesson->id];
                    $percent = Taraz::where($condition)->first()->percent;
                    $idx = ($percent < 0) ? 0 : floor($percent / 25);
                    $lesson->girlArr[$idx] = $lesson->girlArr[$idx] + 1;
                    $girlCount += $percent;
                }
                $count = ($girlCount < 0) ? 0 : floor($girlCount / (count($lessons) * 25));
                $girlTotal[$count] = $girlTotal[$count] + 1;
            }
            else {
                foreach ($lessons as $lesson) {
                    $condition = ['qEntryId' => $qEntry->id, 'lId' => $lesson->id];
                    $percent = Taraz::where($condition)->first()->percent;
                    $idx = ($percent < 0) ? 0 : floor($percent / 25);
                    $lesson->boyArr[$idx] = $lesson->boyArr[$idx] + 1;
                    $boyCount += $percent;
                }

                $count = ($boyCount < 0) ? 0 : floor($boyCount / (count($lessons) * 25));
                $boyTotal[$count] = $boyTotal[$count] + 1;
                $boys++;
            }
        }
        
        if($boys == 0)
            $boys = 1;
        if($girls == 0)
            $girls = 1;

        return view('report2', array('lessons' => $lessons, 'girls' => $girls, 'boys' => $boys,
            'qId' => $qId, 'girlTotal' => $girlTotal, 'boyTotal' => $boyTotal));
    }

    public function report3($qId, $cityId) {

        $qEntries = DB::select("select qentry.id, qentry.u_id FROM medal.qentry as qentry, azmoon.students as std_ WHERE qentry.q_id = " . $qId . " and std_.id = qentry.u_id and std_.city_id = " . $cityId);

        $qoq = QOQ::whereQuizId($qId)->orderBy('qNo', 'ASC')->get();

        foreach ($qoq as $itr) {
            $itr->ans = Question::whereId($itr->question_id)->ans;
            $itr->ans0 = round(DB::select('select count(*) as countNum from roq, azmoon.students as std_ WHERE std_.id = roq.u_id and std_.city_id = ' . $cityId . ' and qoqId = ' . $itr->id . " and result = 0")[0]->countNum * 100 / count($qEntries));
            $itr->ans1 = round(DB::select('select count(*) as countNum from roq, azmoon.students as std_ WHERE std_.id = roq.u_id and std_.city_id = ' . $cityId . ' and qoqId = ' . $itr->id . " and result = 1")[0]->countNum * 100 / count($qEntries));
            $itr->ans2 = round(DB::select('select count(*) as countNum from roq, azmoon.students as std_ WHERE std_.id = roq.u_id and std_.city_id = ' . $cityId . ' and qoqId = ' . $itr->id . " and result = 2")[0]->countNum * 100 / count($qEntries));
            $itr->ans3 = round(DB::select('select count(*) as countNum from roq, azmoon.students as std_ WHERE std_.id = roq.u_id and std_.city_id = ' . $cityId . ' and qoqId = ' . $itr->id . " and result = 3")[0]->countNum * 100 / count($qEntries));
            $itr->ans4 = round(DB::select('select count(*) as countNum from roq, azmoon.students as std_ WHERE std_.id = roq.u_id and std_.city_id = ' . $cityId . ' and qoqId = ' . $itr->id . " and result = 4")[0]->countNum * 100 / count($qEntries));
        }

        return view('report3', array('qoq' => $qoq, 'qId' => $qId));

    }

    public function getCitiesInQuiz($quizId) {

        return DB::select("select std_.city_id as id, cities.name, count(*) as total FROM medal_db.qentry as qentry, azmoon.students as std_, 
azmoon.cities as cities WHERE qentry.q_id = " . $quizId . " and std_.id = qentry.u_id and  cities.id = std_.city_id group by(std_.city_id)");

    }

    public function A2($quizId) {

        $cities = $this->getCitiesInQuiz($quizId);

        foreach ($cities as $city) {
            $city->lessons = DB::select('select AVG(percent) as avgPercent, lessons.nameL as name from medal_db.qentry qR, azmoon.students as std_, medal.taraz,
 medal.lessons WHERE lessons.id = l_id and qR.u_id = std_.id and ' . 'q_id = ' . $quizId . ' and city_id = ' . $city->id . " and (select
 count(*) from roq r, qoq Q where r.u_id = qR.u_id and r.qoq_id = Q.id and Q.quiz_id = qR.q_id) > 0" . ' group by(l_id)');
        }

        return view('A2', array('cities' => $cities, 'quizId' => $quizId));
    }

    public function A2Excel($quizId) {


        $cities = $this->getCitiesInQuiz($quizId);

        foreach ($cities as $city) {
            $city->lessons = DB::select('select AVG(percent) as avgPercent, lessons.nameL as name from medal.qentry qR, azmoon.students as std_, medal.taraz, medal.lessons WHERE lessons.id = lId and qR.u_id = std_.id and ' .
                'qId = ' . $quizId . ' and city_id = ' . $city->id .
                " and (select count(*) from roq r, qoq Q where r.u_id = qR.u_id and r.qoq_id = Q.id and Q.quiz_id = qR.q_id) > 0" .
                ' group by(lId)');
        }

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Gachesefid");
        $objPHPExcel->getProperties()->setLastModifiedBy("Gachesefid");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'تعداد حاضرین');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'شهر');


        if(count($cities) > 0) {

            $j = 'C';
            foreach ($cities[0]->lessons as $itr)
                $objPHPExcel->getActiveSheet()->setCellValue(($j++) . '1', $itr->name);
        }

        $i = 0;

        foreach($cities as $city) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . ($i + 2), $city->name);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . ($i + 2), $city->total);
            $j = 'C';
            foreach($cities[$i]->lessons as $itr) {
                $objPHPExcel->getActiveSheet()->setCellValue(($j++) . ($i + 2), round($itr->avgPercent, 0));
            }
            $i++;
        }

        $fileName = __DIR__ . "/../../../public/tmp/A2.xlsx";

        $objPHPExcel->getActiveSheet()->setTitle('گزارش گیری آزمون ها');

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($fileName);


        if (file_exists($fileName)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($fileName).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fileName));
            readfile($fileName);
            unlink($fileName);
        }

        return view('A2', array('cities' => $cities, 'quiz_id' => $quizId));
    }

    public function A5($quizId, $msg = "") {

        $users = DB::select('SELECT qR.id, qR.u_id, sum(taraz.taraz * (SELECT lesson.coherence FROM lessons as lesson WHERE lesson.id = taraz.l_id)) as weighted_avg ' .
            'from qentry qR, taraz WHERE qR.id = taraz.q_entry_id and qR.q_id = ' . $quizId .
            " and (select count(*) from roq r, qoq Q where r.u_id = qR.u_id and r.qoq_id = Q.id and Q.quiz_id = qR.q_id) > 0 " .
            'GROUP by (qR.id) ORDER by weighted_avg DESC');

        $tmp = DB::select('SELECT DISTINCT L.id, L.nameL, L.coherence from lessons L, questions Q, subjects S, qoq QO WHERE QO.quiz_id = ' . $quizId . ' and QO.question_id = Q.id and Q.subject_id = S.id and S.id_l = L.id order by L.id ASC');
        $sum = 0;

        if($tmp == null || count($tmp) == 0)
            $sum = 1;

        else {
            foreach ($tmp as $itr) {
                $sum += $itr->coherence;
            }
        }

        for($i = 0; $i < count($users); $i++)
            $users[$i]->rank = ($i + 1);


        $preTaraz = (count($users) > 0) ? round($users[0]->weighted_avg / $sum, 0) : 0;

        for ($i = 1; $i < count($users); $i++) {

            if ($preTaraz == round($users[$i]->weighted_avg / $sum, 0))
                $users[$i]->rank = $users[$i - 1]->rank;
            else
                $preTaraz = $users[$i - 1]->rank;
        }

        $i = 0;
        foreach ($users as $user) {

            $tmp = DB::select('select lesson.nameL as name, lesson.coherence, taraz.percent, taraz.taraz from taraz, lessons as lesson WHERE taraz.q_entry_id = ' . $user->id .
                ' and lesson.id = taraz.l_id');

            $user->lessons = $tmp;

            $target = StudentPanel::whereId($user->uId);

            if($target == null) {
                array_splice($users, $i);
                continue;
            }

            $i++;
            $user->name = $target->first_name . " " . $target->last_name;
            $user->uId = $target->id;

            $cityAndState = getStdCityAndState($target->id);
            $user->city = $cityAndState['city'];

            $user->state = $cityAndState['state'];

            $user->cityRank = calcRankInCity($quizId, $user->uId, $cityAndState['cityId']);
            $user->stateRank = calcRankInState($quizId, $user->uId, $cityAndState['stateId']);

        }

        usort($users, function ($a, $b) {
            return $a->rank - $b->rank;
        });

        return view('reportA5', array('users' => $users, 'quizId' => $quizId, 'msg' => $msg));

    }

    public function A5Excel($quizId) {


        $users = DB::select('SELECT qR.id, qR.u_id, sum(taraz.taraz * (SELECT lesson.coherence FROM lessons as lesson WHERE lesson.id = taraz.l_id)) as weighted_avg ' .
            'from qentry qR, taraz WHERE qR.id = taraz.q_entry_id and qR.q_id = ' . $quizId .
            " and (select count(*) from roq r, qoq Q where r.u_id = qR.u_id and r.qoq_id = Q.id and Q.quiz_id = qR.q_id) > 0 " .
            'GROUP by (qR.id) ORDER by weighted_avg DESC');

        $tmp = DB::select('SELECT DISTINCT L.id, L.nameL, L.coherence from lessons L, questions Q, subjects S, qoq QO WHERE QO.quiz_id = ' . $quizId . ' and QO.question_id = Q.id and Q.subject_id = S.id and S.id_l = L.id order by L.id ASC');
        $sum = 0;

        if($tmp == null || count($tmp) == 0)
            $sum = 1;

        else {
            foreach ($tmp as $itr) {
                $sum += $itr->coherence;
            }
        }

        for($i = 0; $i < count($users); $i++)
            $users[$i]->rank = ($i + 1);


        $preTaraz = (count($users) > 0) ? round($users[0]->weighted_avg / $sum, 0) : 0;

        for ($i = 1; $i < count($users); $i++) {

            if ($preTaraz == round($users[$i]->weighted_avg / $sum, 0))
                $users[$i]->rank = $users[$i - 1]->rank;
            else
                $preTaraz = $users[$i - 1]->rank;
        }

        foreach ($users as $user) {

            $tmp = DB::select('select lesson.nameL as name, lesson.coherence, taraz.percent, taraz.taraz from taraz, lessons as lesson WHERE taraz.q_entry_id = ' . $user->id .
                ' and lesson.id = taraz.l_id');

            $user->lessons = $tmp;

            $target = StudentPanel::whereId($user->uId);
            $user->name = $target->first_name . " " . $target->last_name;
            $user->uId = $target->id;

            $cityAndState = getStdCityAndState($target->id);
            $user->city = $cityAndState['city'];

            $user->state = $cityAndState['state'];

            $user->cityRank = calcRankInCity($quizId, $user->uId, $cityAndState['cityId']);
            $user->stateRank = calcRankInState($quizId, $user->uId, $cityAndState['stateId']);

        }

        usort($users, function ($a, $b) {
            return $a->rank - $b->rank;
        });

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Gachesefid");
        $objPHPExcel->getProperties()->setLastModifiedBy("Gachesefid");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'استان');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'شهر');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'نام و نام خانوادگی');

        $j = 'D';

        if(count($users) > 0) {
            foreach($users[0]->lessons as $itr) {
                $objPHPExcel->getActiveSheet()->setCellValue(($j++) . '1', $itr->name);

            }
        }


        $objPHPExcel->getActiveSheet()->setCellValue(($j++) . '1', 'میانگین');
        $objPHPExcel->getActiveSheet()->setCellValue(($j++) . '1', 'تراز کل');
        $objPHPExcel->getActiveSheet()->setCellValue(($j++) . '1', 'رتبه در شهر');
        $objPHPExcel->getActiveSheet()->setCellValue(($j++) . '1', 'رتبه در استان');
        $objPHPExcel->getActiveSheet()->setCellValue(($j) . '1', 'رتبه در کشور');

        $i = 2;

        foreach($users as $user) {

            $sumTaraz = 0;
            $sumLesson = 0;
            $sumCoherence = 0;

            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $user->name);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $user->city);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $user->state);

            $j = 'D';

            foreach($user->lessons as $itr) {
                if($itr->coherence == 0) {
                    $sumTaraz += $itr->taraz;
                    $sumLesson += $itr->percent;
                    $sumCoherence += 1;
                }
                else {
                    $sumTaraz += $itr->taraz * $itr->coherence;
                    $sumLesson += $itr->percent * $itr->coherence;
                    $sumCoherence += $itr->coherence;
                }
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . $i, $itr->percent);
            }
            if($sumCoherence != 0) {
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . $i, round(($sumLesson / $sumCoherence), 0));
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . $i, round(($sumTaraz / $sumCoherence), 0));
            }
            else {
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . $i, round(($sumLesson), 0));
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . $i, round(($sumTaraz), 0));
            }
            $objPHPExcel->getActiveSheet()->setCellValue($j++ . $i, $user->cityRank);
            $objPHPExcel->getActiveSheet()->setCellValue($j++ . $i, $user->stateRank);
            $objPHPExcel->getActiveSheet()->setCellValue($j . $i, $user->rank);
            $i++;
        }

        $fileName = __DIR__ . "/../../../public/tmp/A5.xlsx";

        $objPHPExcel->getActiveSheet()->setTitle('گزارش گیری آزمون ها');

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($fileName);


        if (file_exists($fileName)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($fileName).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fileName));
            readfile($fileName);
            unlink($fileName);
        }

        return view('reportA5', array('users' => $users, 'quiz_id' => $quizId));

    }

    public function A1($quizId) {

        $qInfos = DB::select("select qoq.id as qoqId, questions.id, questions.ans " .
            "from questions, qoq WHERE qoq.quiz_id = " . $quizId . " and " .
            "qoq.question_id = questions.id order by qoq.id ASC");

        if(count($qInfos) == 0)
            return Redirect::to(route('reports', ['quiz_id' => $quizId]));

        $total = ROQ::whereQoqId($qInfos[0]->qoqId)->count();

        foreach ($qInfos as $qInfo) {
            $condition = ['qoq_id' => $qInfo->qoqId,
                'result' => 0];
            $qInfo->white = ROQ::where($condition)->count();

            $condition = ['qoq_id' => $qInfo->qoqId,
                'result' => $qInfo->ans];
            $qInfo->correct = ROQ::where($condition)->count();
        }

        foreach ($qInfos as $qInfo) {
            $contents = DB::select('select subjects.nameSubject as subjectName, lessons.nameL as lessonName from subjects, lessons, questions WHERE questions.id = ' . $qInfo->id . ' and questions.subject_id = subjects.id and subjects.id_l = lessons.id');
            $subjects = [];
            $lessons = [];
            $i = 0;
            foreach ($contents as $content) {
                $subjects[$i] = $content->subjectName;
                if (!in_array($content->lessonName, $lessons))
                    $lessons[count($lessons)] = $content->lessonName;
                $i++;
            }
            $qInfo->subjects = $subjects;
            $qInfo->lessons = $lessons;
            $qInfo->level = getQuestionLevel($qInfo->id);
        }

        return view('A1', array('qInfos' => $qInfos, 'quizId' => $quizId, 'total' => $total));
    }

    public function A1Excel($quizId) {

        $qInfos = DB::select("select qoq.id as qoqId, questions.id, questions.ans " .
            "from questions, qoq WHERE qoq.quiz_id = " . $quizId . " and " .
            "qoq.question_id = questions.id order by qoq.id ASC");

        if(count($qInfos) == 0)
            return Redirect::to(route('reports', ['quiz_id' => $quizId]));

        $total = ROQ::whereQoqId($qInfos[0]->qoqId)->count();

        foreach ($qInfos as $qInfo) {
            $condition = ['qoqId' => $qInfo->qoqId,
                'result' => 0];
            $qInfo->white = ROQ::where($condition)->count();

            $condition = ['qoqId' => $qInfo->qoqId,
                'result' => $qInfo->ans];
            $qInfo->correct = ROQ::where($condition)->count();
        }

        foreach ($qInfos as $qInfo) {
            $contents = DB::select('select subjects.nameSubject as subjectName, lessons.nameL as lessonName from subjects, lessons, questions WHERE questions.id = ' . $qInfo->id . ' and questions.subject_id = subjects.id and subjects.id_l = lessons.id');
            $subjects = [];
            $lessons = [];
            $i = 0;
            foreach ($contents as $content) {
                $subjects[$i] = $content->subjectName;
                if (!in_array($content->lessonName, $lessons))
                    $lessons[count($lessons)] = $content->lessonName;
                $i++;
            }
            $qInfo->subjects = $subjects;
            $qInfo->lessons = $lessons;
            $qInfo->level = getQuestionLevel($qInfo->id);
        }

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Gachesefid");
        $objPHPExcel->getProperties()->setLastModifiedBy("Gachesefid");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'وضعیت دشواری');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'درصد بدون پاسخ');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'درصد پاسخ نادرست');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'درصد پاسخ درست');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'درس');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'مبحث');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'گزینه صحیح');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'شماره سوال');

        $i = 1;
        foreach($qInfos as $qInfo) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . ($i + 1), $i);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . ($i + 1), $qInfo->ans);
            $j = 'C';
            foreach($qInfo->subjects as $itr)
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . ($i + 1), $itr);

            foreach($qInfo->lessons as $itr)
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . ($i + 1), $itr);

            if($total != 0) {
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . ($i + 1), round($qInfo->correct * 100 / $total, 0));
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . ($i + 1), round((($total - $qInfo->correct - $qInfo->white) * 100 / $total), 0));
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . ($i + 1), round(($qInfo->white * 100 / $total), 0));
            }
            else {
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . ($i + 1), 0);
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . ($i + 1), 0);
                $objPHPExcel->getActiveSheet()->setCellValue($j++ . ($i + 1), 0);
            }
            $objPHPExcel->getActiveSheet()->setCellValue($j . ($i + 1), $qInfo->level);
            $i++;
        }

        $fileName = __DIR__ . "/../../../public/tmp/A1.xlsx";

        $objPHPExcel->getActiveSheet()->setTitle('گزارش گیری آزمون ها');

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($fileName);


        if (file_exists($fileName)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($fileName).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fileName));
            readfile($fileName);
            unlink($fileName);
        }

        return Redirect::to(route('A1', ['quiz_id' => $quizId]));
    }

    public function A7($quizId) {

        $floor = [-34, 10, 30, 50, 75];
        $ceil = [11, 31, 51, 76, 101];

        $lessons = getLessonQuiz($quizId);

        $total = DB::select('SELECT count(*) as total FROM qentry qR WHERE q_id = ' . $quizId .
            " and (select count(*) from roq r, qoq Q where r.u_id = qR
.u_id and r.qoq_id = Q.id and Q.quiz_id = qR.q_id) > 0");

        if($total == null || count($total) == 0 || empty($total->total))
            $total = 0;
        else
            $total = $total[0]->total;

        foreach ($lessons as $lesson) {

            $tmp = DB::select('select count(*) as countNum from qentry qR, taraz WHERE q_id = ' . $quizId .
                " and " . 'percent < ' . $ceil[0] . ' and percent > ' . $floor[0] .
' and l_id = ' . $lesson->id . " and (select count(*) from roq r, qoq Q where r.u_id = qR.u_id and r.qoq_id = Q.id and Q.quiz_id = qR.q_id) > 0");

            if($tmp != null && count($tmp) > 0)
                $lesson->group_0 = $tmp[0]->countNum;
            else
                $lesson->group_0 = 0;

            $tmp = DB::select('select count(*) as countNum from qentry qR, taraz WHERE q_id = ' . $quizId .
                " and " . 'percent < ' . $ceil[1] . ' and percent > ' . $floor[1] . ' and l_id = ' . $lesson->id .
                " and (select count(*) from roq r, qoq Q where r.u_id = qR.u_id and r.qoq_id = Q.id 
and Q.quiz_id = qR.q_id) > 0");

            if($tmp != null && count($tmp) > 0)
                $lesson->group_1 = $tmp[0]->countNum;
            else
                $lesson->group_1 = 0;

            $tmp = DB::select('select count(*) as countNum from qentry qR, taraz WHERE q_id = ' . $quizId .
                " and " . 'percent < ' . $ceil[2] . ' and percent > ' . $floor[2] . ' and l_id = ' . $lesson->id .
                " and (select count(*) from roq r, qoq Q where r.u_id = qR.u_id and r.qoq_id = Q
.id and Q.quiz_id = qR.q_id) > 0");

            if($tmp != null && count($tmp) > 0)
                $lesson->group_2 = $tmp[0]->countNum;
            else
                $lesson->group_2 = 0;

            $tmp = DB::select('select count(*) as countNum from qentry qR, taraz WHERE q_id = ' . $quizId .
                " and " . 'percent < ' . $ceil[3] . ' and percent > ' . $floor[3] . ' and l_id = ' . $lesson->id .
                " and (select count(*) from roq r, qoq Q where r.u_id = qR.u_id and r.qoq_id = Q
.id and Q.quiz_id = qR.q_id) > 0");

            if($tmp != null && count($tmp) > 0)
                $lesson->group_3 = $tmp[0]->countNum;
            else
                $lesson->group_3 = 0;

            $tmp = DB::select('select count(*) as countNum from qentry qR, taraz WHERE q_id = ' . $quizId .
                " and " . 'percent < ' . $ceil[4] . ' and percent > ' . $floor[4] . ' and l_id = ' . $lesson->id .
                " and (select count(*) from roq r, qoq Q where r.u_id = qR.u_id and r.qoq_id = Q
.id and Q.quiz_id = qR.q_id) > 0");

            if($tmp != null && count($tmp) > 0)
                $lesson->group_4 = $tmp[0]->countNum;
            else
                $lesson->group_4 = 0;
        }

        return view('A7', array('lessons' => $lessons, 'total' => $total, 'quizId' => $quizId));
    }

    public function A7Excel($quizId) {
        $floor = [-34, 10, 30, 50, 75];
        $ceil = [11, 31, 51, 76, 101];

        $lessons = getLessonQuiz($quizId);

        $total = DB::select('SELECT count(*) as total FROM qentry qR WHERE qId = ' . $quizId .
            " and (select count(*) from roq r, qoq Q where r.u_id = qR.u_id and r.qoq_id = Q.id and Q.quiz_id = qR.q_id) > 0");

        if($total == null || count($total) == 0 || empty($total->total))
            $total = 0;
        else
            $total = $total[0]->total;

        foreach ($lessons as $lesson) {

            $tmp = DB::select('select count(*) as countNum from qentry qR, taraz WHERE qId = ' . $quizId .
                " and " .
                'percent < ' . $ceil[0] . ' and percent > ' . $floor[0] . ' and lId = ' . $lesson->id .
                " and (select count(*) from roq r, qoq Q where r.u_id = qR.u_id and r.qoq_id = Q.id and Q.quiz_id = qR.q_id) > 0");

            if($tmp != null && count($tmp) > 0)
                $lesson->group_0 = $tmp[0]->countNum;
            else
                $lesson->group_0 = 0;

            $tmp = DB::select('select count(*) as countNum from qentry qR, taraz WHERE qId = ' . $quizId .
                " and " .
                'percent < ' . $ceil[1] . ' and percent > ' . $floor[1] . ' and lId = ' . $lesson->id .
                " and (select count(*) from roq r, qoq Q where r.u_id = qR.u_id and r.qoq_id = Q.id and Q.quiz_id = qR.q_id) > 0");

            if($tmp != null && count($tmp) > 0)
                $lesson->group_1 = $tmp[0]->countNum;
            else
                $lesson->group_1 = 0;

            $tmp = DB::select('select count(*) as countNum from qentry qR, taraz WHERE qId = ' . $quizId .
                " and " .
                'percent < ' . $ceil[2] . ' and percent > ' . $floor[2] . ' and lId = ' . $lesson->id .
                " and (select count(*) from roq r, qoq Q where r.u_id = qR.u_id and r.qoq_id = Q.id and Q.quiz_id = qR.q_id) > 0");

            if($tmp != null && count($tmp) > 0)
                $lesson->group_2 = $tmp[0]->countNum;
            else
                $lesson->group_2 = 0;

            $tmp = DB::select('select count(*) as countNum from qentry qR, taraz WHERE qId = ' . $quizId .
                " and " .
                'percent < ' . $ceil[3] . ' and percent > ' . $floor[3] . ' and lId = ' . $lesson->id .
                " and (select count(*) from roq r, qoq Q where r.u_id = qR.u_id and r.qoq_id = Q.id and Q.quiz_id = qR.q_id) > 0");

            if($tmp != null && count($tmp) > 0)
                $lesson->group_3 = $tmp[0]->countNum;
            else
                $lesson->group_3 = 0;

            $tmp = DB::select('select count(*) as countNum from qentry qR, taraz WHERE qId = ' . $quizId .
                " and " .
                'percent < ' . $ceil[4] . ' and percent > ' . $floor[4] . ' and lId = ' . $lesson->id .
                " and (select count(*) from roq r, qoq Q where r.u_id = qR.u_id and r.qoq_id = Q.id and Q.quiz_id = qR.q_id) > 0");

            if($tmp != null && count($tmp) > 0)
                $lesson->group_4 = $tmp[0]->countNum;
            else
                $lesson->group_4 = 0;
        }

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Gachesefid");
        $objPHPExcel->getProperties()->setLastModifiedBy("Gachesefid");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'بین 76 تا 100');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'بین 51 تا 75');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'بین 31 تا 50');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'بین 11 تا 30');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'بین -33 تا 10');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'نام درس');

        $i = 0;

        foreach($lessons as $lesson) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . ($i + 2), $lesson->nameL);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . ($i + 2), $lesson->group_0);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . ($i + 2), $lesson->group_1);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . ($i + 2), $lesson->group_2);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . ($i + 2), $lesson->group_3);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . ($i + 2), $lesson->group_4);

            $i++;
        }

        $fileName = __DIR__ . "/../../../public/tmp/A7.xlsx";

        $objPHPExcel->getActiveSheet()->setTitle('گزارش گیری آزمون ها');

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($fileName);


        if (file_exists($fileName)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($fileName).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fileName));
            readfile($fileName);
            unlink($fileName);
        }

        return view('A7', array('lessons' => $lessons, 'total' => $total, 'quiz_id' => $quizId));
    }

    public function A6($quizId) {

        $subjects = getSubjectQuiz($quizId);

        foreach ($subjects as $sId) {
            $tmp = DB::select('select count(*) as countNum from roq, questions as question, qoq WHERE qoq.quiz_id = ' . $quizId . " and roq.qoq_id = qoq.id" .
                " and ans = result and qoq.question_id = question.id and question.subject_id = " . $sId->id);

            if ($tmp == null || count($tmp) == 0)
                $sId->correct = 0;
            else
                $sId->correct = $tmp[0]->countNum;

            $tmp = DB::select('select count(*) as countNum from roq, questions as question, qoq WHERE qoq.quiz_id = ' . $quizId . " and roq.qoq_id = qoq.id" .
                " and ans <> result and result <> 0 and qoq.question_id = question.id and question.subject_id = " . $sId->id);
            if ($tmp == null || count($tmp) == 0)
                $sId->inCorrect = 0;
            else
                $sId->inCorrect = $tmp[0]->countNum;

            $tmp = DB::select('select count(*) as countNum from roq, questions as question, qoq WHERE qoq.quiz_id = ' . $quizId . " and roq.qoq_id = qoq.id" .
                " and result = 0 and qoq.question_id = question.id and question.subject_id = " . $sId->id);
            if ($tmp == null || count($tmp) == 0)
                $sId->white = 0;
            else
                $sId->white = $tmp[0]->countNum;

            $sId->lessonName = Lesson::whereId($sId->lessonId)->nameL;
        }

        return view('A6', array('subjects' => $subjects, 'quizId' => $quizId));

    }

    public function A6Excel($quizId) {

        $subjects = getSubjectQuiz($quizId);

        foreach ($subjects as $sId) {
            $tmp = DB::select('select count(*) as countNum from roq, questions as question, qoq WHERE qoq.quiz_id = ' . $quizId . " and roq.qoq_id = qoq.id" .
                " and ans = result and qoq.question_id = question.id and question.subject_id = " . $sId->id);

            if ($tmp == null || count($tmp) == 0)
                $sId->correct = 0;
            else
                $sId->correct = $tmp[0]->countNum;

            $tmp = DB::select('select count(*) as countNum from roq, questions as question, qoq WHERE qoq.quiz_id = ' . $quizId . " and roq.qoq_id = qoq.id" .
                " and ans <> result and result <> 0 and qoq.question_id = question.id and question.subject_id = " . $sId->id);
            if ($tmp == null || count($tmp) == 0)
                $sId->inCorrect = 0;
            else
                $sId->inCorrect = $tmp[0]->countNum;

            $tmp = DB::select('select count(*) as countNum from roq, questions as question, qoq WHERE qoq.quiz_id = ' . $quizId . " and roq.qoq_id = qoq.id" .
                " and result = 0 and qoq.question_id = question.id and question.subject_id = " . $sId->id);
            if ($tmp == null || count($tmp) == 0)
                $sId->white = 0;
            else
                $sId->white = $tmp[0]->countNum;

            $sId->lessonName = Lesson::whereId($sId->lessonId)->nameL;
        }

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Gachesefid");
        $objPHPExcel->getProperties()->setLastModifiedBy("Gachesefid");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'درصد');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'بدون پاسخ');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'نادرست');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'درست');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'نام مبحث');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'نام درس');

        $i = 0;
        foreach($subjects as $subject) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . ($i + 2), $subject->lessonName);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . ($i + 2), $subject->name);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . ($i + 2), $subject->correct);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . ($i + 2), $subject->inCorrect);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . ($i + 2), $subject->white);
            if($subject->correct + $subject->inCorrect + $subject->white != 0)
                $objPHPExcel->getActiveSheet()->setCellValue('F' . ($i + 2), round($subject->correct * 100 / ($subject->correct + $subject->inCorrect + $subject->white), 0));
            else
                $objPHPExcel->getActiveSheet()->setCellValue('F' . 0);
            $i++;
        }

        $fileName = __DIR__ . "/../../../public/tmp/A6.xlsx";

        $objPHPExcel->getActiveSheet()->setTitle('گزارش گیری آزمون ها');

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($fileName);


        if (file_exists($fileName)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($fileName).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fileName));
            readfile($fileName);
            unlink($fileName);
        }

        return view('A6', array('subjects' => $subjects, 'quiz_id' => $quizId));

    }

    public function A4($quizId) {

        $lessonsNo = count(getLessonQuiz($quizId));

        $cities = $this->getCitiesInQuiz($quizId);

        foreach ($cities as $city) {

            $lessons = DB::select('select coherence, percent from azmoon.students as rd, medal.taraz as taraz, medal.qentry qR, medal.lessons as lesson WHERE lesson.id = lId and rd.city_id = ' . $city->id .
                ' and qR.q_id = ' . $quizId . ' and rd.id = qR.u_id' .
                " and (select count(*) from roq r, qoq Q where r.u_id = qR.u_id and r.qoq_id = Q.id and Q.quiz_id = qR.q_id) > 0");

            $count = 0;
            $sum = 0;
            $sumCoherence = 0;

            $city->group_0 = 0;
            $city->group_1 = 0;
            $city->group_2 = 0;
            $city->group_3 = 0;
            $city->group_4 = 0;

            foreach ($lessons as $lesson) {

                if($lesson->coherence != 0) {
                    $sum += $lesson->coherence * $lesson->percent;
                    $sumCoherence += $lesson->coherence;
                }
                else {
                    $sum += $lesson->percent;
                    $sumCoherence += 1;
                }
                $count++;

                if($count % $lessonsNo == 0) {
                    $sum /= $sumCoherence;

                    if($sum < 11)
                        $city->group_0 = $city->group_0 + 1;
                    else if($sum < 31)
                        $city->group_1 = $city->group_1 + 1;
                    else if($sum < 51)
                        $city->group_2 = $city->group_2 + 1;
                    else if($sum < 76)
                        $city->group_3 = $city->group_3 + 1;
                    else
                        $city->group_4 = $city->group_4 + 1;

                    $sum = 0;
                    $sumCoherence = 0;
                }
            }
        }
        return view('A4', array('cities' => $cities, 'quizId' => $quizId));
    }

    public function A4Excel($quizId) {

        $lessonsNo = count(getLessonQuiz($quizId));

        $cities = $this->getCitiesInQuiz($quizId);

        foreach ($cities as $city) {

            $lessons = DB::select('select coherence, percent from azmoon.students as rd, medal.taraz as taraz, medal.qentry qR, medal.lessons as lesson WHERE lesson.id = lId and rd.city_id = ' . $city->id .
                ' and qR.q_id = ' . $quizId . ' and rd.id = qR.u_id' .
                " and (select count(*) from roq r, qoq Q where r.u_id = qR.u_id and r.qoq_id = Q.id and Q.quiz_id = qR.q_id) > 0");

            $count = 0;
            $sum = 0;
            $sumCoherence = 0;

            $city->group_0 = 0;
            $city->group_1 = 0;
            $city->group_2 = 0;
            $city->group_3 = 0;
            $city->group_4 = 0;

            foreach ($lessons as $lesson) {

                if($lesson->coherence != 0) {
                    $sum += $lesson->coherence * $lesson->percent;
                    $sumCoherence += $lesson->coherence;
                }
                else {
                    $sum += $lesson->percent;
                    $sumCoherence += 1;
                }
                $count++;

                if($count % $lessonsNo == 0) {
                    $sum /= $sumCoherence;

                    if($sum < 11)
                        $city->group_0 = $city->group_0 + 1;
                    else if($sum < 31)
                        $city->group_1 = $city->group_1 + 1;
                    else if($sum < 51)
                        $city->group_2 = $city->group_2 + 1;
                    else if($sum < 76)
                        $city->group_3 = $city->group_3 + 1;
                    else
                        $city->group_4 = $city->group_4 + 1;

                    $sum = 0;
                    $sumCoherence = 0;
                }
            }
        }

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Gachesefid");
        $objPHPExcel->getProperties()->setLastModifiedBy("Gachesefid");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'بین 76 تا 100');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'بین 51 تا 75');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'بین 31 تا 50');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'بین 11 تا 30');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'بین -33 تا 10');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'شهر');


        $i = 2;
        foreach($cities as $city) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $city->name);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $city->group_0);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $city->group_1);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $city->group_2);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $city->group_3);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $city->group_4);
            $i++;
        }

        $fileName = __DIR__ . "/../../../public/tmp/A4.xlsx";

        $objPHPExcel->getActiveSheet()->setTitle('گزارش گیری آزمون ها');

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($fileName);


        if (file_exists($fileName)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($fileName).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fileName));
            readfile($fileName);
            unlink($fileName);
        }

        return view('A4', array('cities' => $cities, 'quiz_id' => $quizId));
    }

    public function preA3($quizId, $err = "") {

        $uIds = DB::select('select users.id, users.first_name as firstName, users.last_name as lastName from medal_db.qentry qR, azmoon.students 
as users WHERE ' . 'q_id = ' . $quizId . ' and users.id = qR.u_id ' .
            " and (select count(*) from roq r, qoq Q where r.u_id = qR.u_id 
 and r.qoq_id = Q.id and Q.quiz_id = qR.q_id) > 0"
        );

        return view('chooseStudent', array('uIds' => $uIds, 'quizId' => $quizId, 'err' => $err));

    }

    public function A3($quizId, $uId, $backURL = "") {

        $condition = ['qId' => $quizId, 'uId' => $uId];
        $qEntryId = QEntry::where($condition)->first();
        $tmp = QOQ::whereQuizId($quizId)->first();

        if($tmp == null || count($tmp) == 0 || $qEntryId == null || count($qEntryId) == 0  ||
            ($qEntryId->online == 1 && empty($qEntryId->timeEntry))) {

            if(empty($backURL))
                return $this->preA3($quizId, 'فرد مورد نظر در آزمون شرکت نکرده است');
            else
                return $this->A5($quizId, 'فرد مورد نظر در آزمون شرکت نکرده است');
        }

        $condition = ['qoqId' => $tmp->id, 'uId' => $uId];

        if(ROQ::where($condition)->count() == 0) {
            if(empty($backURL))
                return $this->preA3($quizId, 'فرد مورد نظر در آزمون شرکت نکرده است');
            else
                return $this->A5($quizId, 'فرد مورد نظر در آزمون شرکت نکرده است');
        }

        $class = new KarnameController();

        return $class->showGeneralKarname($uId, $quizId, $qEntryId, KindKarname::where('quiz_id', '=', $quizId)->first());
    }
}