<?php

namespace App\Http\Controllers;

use App\models\Box;
use App\models\BoxesOfQuiz;
use App\models\BoxItems;
use App\models\Degree;
use App\models\DegreeOfQuiz;
use App\models\KindKarname;
use App\models\QEntry;
use App\models\QOQ;
use App\models\Quiz;
use App\models\QuizStatus;
use App\models\ROQ;
use App\models\ROQ2;
use App\models\Subject;
use App\models\Transaction;
use App\models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Larabookir\Gateway\Gateway;
use PHPExcel_IOFactory;

include_once 'Date.php';

class QuizController extends Controller {

    public function sendSMSToUsers() {

        if(isset($_POST["qId"]) && isset($_POST["msg"])) {

            $msg = $_POST["msg"];
            $qId = makeValidInput($_POST["qId"]);
            $users = QEntry::whereQId($qId)->get();

            foreach ($users as $itr)
                sms($msg, User::whereId($itr->u_id)->phone_num);
        }

    }

    public function smsPanel() {
        return view('smsPanel', ['quizes' => Quiz::all()]);
    }

    private function uploadCheck($target_file, $name, $section, $limitSize, $ext) {
        $err = "";
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

        $check = getimagesize($_FILES[$name]["tmp_name"]);
        $uploadOk = 1;

        if($check === false) {
            $err .= "فایل ارسالی در قسمت " . $section . " معتبر نمی باشد" .  "<br />";
            $uploadOk = 0;
        }

        if ($uploadOk == 1 && $_FILES[$name]["size"] > $limitSize)
            $err .= "حداکثر حجم مجاز برای آپلود تصویر $limitSize کیلو بایت می باشد" . "<br />";

        $imageFileType = strtolower($imageFileType);

        if($imageFileType != $ext)
            $err .= "شما تنها فایل های $ext. را می توانید در این قسمت آپلود نمایید" . "<br />";
        return $err;
    }

    private function upload($target_file, $name, $section) {
        $err = "";
        try {
            move_uploaded_file($_FILES[$name]["tmp_name"], $target_file);
        }
        catch (\Exception $x) {
            return "اشکالی در آپلود تصویر در قسمت " . $section . " به وجود آمده است" . "<br />";
        }
        return "";
//        $err .= ;
//    return $err;
    }

    function showQuizes() {
        $msg = '';
        if(isset($_POST["editSelectedQuiz"])) {
            $quizId = makeValidInput($_POST["editSelectedQuiz"]);
            return Redirect::to('editQuiz=' . $quizId);
        }
        else if(isset($_POST["deleteSelectedQuiz"])) {
            $quizIds = $_POST["selectedQuiz"];
            for($i = 0; $i < count($quizIds); $i++)
                Quiz::destroy(makeValidInput($quizIds[$i]));
            $msg = 'آزمون (ها) مورد نظر به درستی از سامانه حذف شد' . '<br/>';
        }
        else if(isset($_POST["createQuiz"])) {
            return Redirect::to("createQuiz");
        }
        $quizIds = Quiz::select('id', 'QN')->get();
        if(count($quizIds) == 0) {
            $msg .= 'آزمونی جهت نمایش وجود ندارد' . '<br/>';
        }
        return view('selectQuiz', array('msg' => $msg, 'quizIds' => $quizIds));
    }

    public function QuizStatus() {

        $mode = 'show';
        $msg = "";

        if(isset($_POST["addNewStatus"]))
            $mode = 'addNewStatus';

        else if(isset($_POST["doAddStatus"])) {

            $isPicSet = makeValidInput($_POST["isPicSet"]);

            if($isPicSet == "1" && !isset($_FILES["pic"]))
                $msg = "لطفا فایلی را به عنوان تصویر وضعیت انتخاب کنید";

            else if($isPicSet == "0" && !isset($_POST["statusName"]))
                $msg = "لطفا متنی را به عنوان متن وضعیت انتخاب کنید";

            else {

                $quizStatus = new QuizStatus();
                if($isPicSet == "0")
                    $quizStatus->status = makeValidInput($_POST["statusName"]);
                else
                    $quizStatus->status = $_FILES["pic"]["name"];

                $quizStatus->level = makeValidInput($_POST["level"]);

                if (empty($quizStatus->level))
                    $msg = "لطفا تمامی فیلد های لازم را پر نمایید";

                else {

                    $quizStatus->type = makeValidInput($_POST["type"]);
                    $quizStatus->floor = makeValidInput($_POST["floorStatus"]);
                    $quizStatus->ceil = makeValidInput($_POST["ceilStatus"]);
                    $quizStatus->color = makeValidInput($_POST["color"]);
                    $quizStatus->pic = $isPicSet;

                    $file = $_FILES["pic"];

                    $targetFile = __DIR__ . "/../../../public/status/" . $file["name"];

                    if($isPicSet) {
                        if (!file_exists($targetFile)) {
                            $msg = $this->uploadCheck($targetFile, "pic", "ایجاد وضعیت جدید", 300000, "jpg");
                            if (empty($msg)) {
                                $msg = $this->upload($targetFile, "pic", "ایجاد وضعیت جدید");
                                if (empty($msg)) {
                                    $quizStatus->save();
                                    return Redirect::to('QuizStatus');
                                }
                            }
                        }
                    }
                    else {
                        $quizStatus->save();
                        return Redirect::to('QuizStatus');
                    }
                }
            }
        }

        else if(isset($_POST["removeStatus"])) {

            $quizStatusId = makeValidInput($_POST["removeStatus"]);
            $quizStatus = QuizStatus::whereId($quizStatusId);

            if($quizStatus != null) {

                if(!empty($quizStatus->pic) && file_exists(__DIR__ . "/../../../public/status/" . $quizStatus->status)) {
                    $targetFile = __DIR__ . "/../../../public/status/" . $quizStatus->status;
                    unlink($targetFile);
                }

                $quizStatus->delete();
            }
        }

        $quizStatus = QuizStatus::all();
        return view('QuizStatus', array('quizStatus' => $quizStatus, 'mode' => $mode, 'msg' => $msg));
    }

    public function addQuestionToQuiz($quizId) {

        $msg = "";
        $items = array();
        $questions = array();
        $boxId = "";
        $qoq = "";

        if(isset($_POST["showSelectedBox"])) {
            $boxId = makeValidInput($_POST["showSelectedBox"]);
            $items = BoxItems::whereBoxId($boxId)->get();
            for($i = 0; $i < count($items); $i++) {
                $conditions = ['grade' => $items[$i]->grade];
                $questions[$i] = Subject::whereId($items[$i]->subject_id)->questions()->where($conditions)->select('questions.id', 'questions.organization_id')->get();
                $subject = Subject::whereId($items[$i]->subject_id);
                if($subject != null)
                    $items[$i]->subject_id = $subject->nameSubject;
                if($items[$i]->grade == 1)
                    $items[$i]->grade = 'آسان';
                else if($items[$i]->grade == 2)
                    $items[$i]->grade = 'متوسط';
                else
                    $items[$i]->grade = 'دشوار';
            }

            $qoq = DB::select('select qoq.id, qoq.quiz_id, qoq.question_id, qoq.qNo from qoq, box WHERE qoq.quiz_id = ' . $quizId . ' and box.id = ' . $boxId . ' and qoq.qNo <= box.to_ and qoq.qNo >= box.from_');
        }

        else if(isset($_POST["changeQOQ"])) {
            if(isset($_POST["qoqIds"])) {
                $qoqIds = $_POST["qoqIds"];
                for($i = 0; $i < count($qoqIds); $i++) {
                    $qoqId = makeValidInput($qoqIds[$i]);
                    $tmp = explode('_', $qoqId);
                    $qoqId = $tmp[0];
                    $questionId = $tmp[1];
                    $qoqTmp = QOQ::whereId($qoqId);
                    $qoqTmp->question_id = $questionId;
                    $qoqTmp->save();
                }
            }
        }

        else if(isset($_POST["deleteSelectedBox"])) {
            $boxId = makeValidInput($_POST["deleteSelectedBox"]);
            $conditions = ['box_id' => $boxId, 'quiz_id' => $quizId];
            BoxesOfQuiz::where($conditions)->delete();
            $box = Box::whereId($boxId);
            if($box != null) {
                DB::delete('delete from qoq where quiz_id = ' . $quizId . ' and qNo >= ' . $box->from_
                    . ' and qNo <= ' . $box->to_);
            }
        }

        else if(isset($_POST["addBoxToQuiz"])) {
            $boxId = makeValidInput($_POST["selectedBox"]);
            $conditions = ['quiz_id' => $quizId, 'box_id' => $boxId];
            if(BoxesOfQuiz::where($conditions)->count() == 0) {

                $boxesOfQuizTmp = BoxesOfQuiz::whereQuizId($quizId)->select('box_id')->get();
                $froms = array();
                $toes = array();
                for($i = 0; $i < count($boxesOfQuizTmp); $i++) {
                    $boxTmp = Box::whereId($boxesOfQuizTmp[$i]->box_id);
                    $froms[$i] = $boxTmp->from_;
                    $toes[$i] = $boxTmp->to_;
                }

                $selectedBox = Box::whereId($boxId);
                $from = $selectedBox->from_;
                $to = $selectedBox->to_;

                $allow = true;

                for($i = 0; $i < count($froms); $i++) {
                    if(($froms[$i] <= $from && $toes[$i] >= $to) || ($from <= $froms[$i] && $froms[$i] <= $to) || ($from <= $toes[$i] && $toes[$i] <= $to)) {
                        $allow = false;
                        break;
                    }
                }


                if($allow) {

                    $box = Box::whereId($boxId);
                    $itemsTmp = BoxItems::whereBoxId($box->id)->orderBy('id', 'ASC')->get();
                    $qIds = array();
                    $qNos = array();

                    $skip = 0;
                    $oldSubjectId = -1;

                    for ($i = 0; $i < count($itemsTmp); $i++) {

                        $conditions = ['grade' => $itemsTmp[$i]->grade];
                        if($oldSubjectId == $itemsTmp[$i]->subject_id)
                            $skip++;
                        else
                            $skip = 0;

                        $oldSubjectId = $itemsTmp[$i]->subject_id;

                        $questionId = Subject::whereId($itemsTmp[$i]->subject_id)->questions()->where($conditions)->skip($skip)->take(1)->select('questions.id')->first();
                        if ($questionId == null) {
                            $msg = "سوالی در جعبه ی مورد نظر قرار نمی گیرد";
                            break;
                        }
                        $qIds[$i] = $questionId->id;
                        $qNos[$i] = $box->from_ + $i;
                    }

                    if(empty($msg)) {

                        $boxOfQuiz = new BoxesOfQuiz();
                        $boxOfQuiz->box_id = $boxId;
                        $boxOfQuiz->quiz_id = $quizId;
                        $boxOfQuiz->save();

                        for($i = 0; $i < count($qIds); $i++) {
                            $qoq = new QOQ();
                            $qoq->question_id = $qIds[$i];
                            $qoq->quiz_id = $quizId;
                            $qoq->qNo = $qNos[$i];
                            $qoq->save();
                        }
                    }
                }
                else
                    $msg = "شماره ی سوال جعبه ها با هم تداخل دارند";
            }
            else
                $msg = "جعبه ی انتخابی برای آزمون قبلا انتخاب شده است";
        }

        else if(isset($_POST["showQuiz"])) {
            return $this->doQuiz($quizId);
        }

        $boxesOfQuiz = Quiz::whereId($quizId)->boxes()->select('box.id', 'box.name')->get();
        $allBoxes = Box::select('box.id', 'box.name')->get();

        return view('addQuestionToQuiz', array('boxesOfQuiz' => $boxesOfQuiz, 'quizId' => $quizId, 'qoq' => $qoq,
            'boxId' => $boxId, 'boxes' => $allBoxes, 'items' => $items, 'questions' => $questions, 'msg' => $msg));
    }

    public function addDegreeToQuiz($quizId) {

        if(isset($_POST["submitD"])) {
            if(!isset($_POST["degrees"])) {
                $degrees = Degree::all();
                return view('addDegreeToQuiz', array('quiz_id' => $quizId,
                    'degrees' => $degrees,
                    'error' => 'پایه ی تحصیلی ای برای آزمون خود انتخاب نمایید'));
            }
            $degrees = $_POST["degrees"];
            foreach ($degrees as $degree) {
                $degreeOfQuiz = new DegreeOfQuiz;
                $degreeOfQuiz->quiz_id = $quizId;
                $degreeOfQuiz->degree_id = makeValidInput($degree);
                $degreeOfQuiz->save();
            }
            return Redirect::to('addQuestionToQuiz='.$quizId);
        }

        $degrees = Degree::all();
        if(count($degrees) == 0)
            return view('home', array('msg' => 'پایه ی تحصیلی ای برای آزمون مورد نظر وجود ندارد'));

        $url = "addDegreeToQuiz=" . $quizId;
        $selectedDegrees = array();
        return view('addDegreeToQuiz', array('url' => $url, 'selectedDegrees' => $selectedDegrees, 'quiz_id' => $quizId, 'degrees' => $degrees));
    }

    public function editQuiz($quizId) {

        $msg = "";

        if(isset($_POST["editInfo"])) {
            $quiz = Quiz::whereId($quizId);
            $url = URL('editQuiz') . "=" . $quizId;
            return view('createQuiz', array('qName' => $quiz->QN,
                'timeLen' => $quiz->tL,
                'sDate' => convertStringToDate($quiz->sDate),
                'sTime' => convertStringToTime($quiz->sTime),
                'eDate' => convertStringToDate($quiz->eDate),
                'eTime' => convertStringToTime($quiz->eTime),
                'mark' => $quiz->mark,
                'minusMark' => $quiz->minusMark,
                'price' => $quiz->price,
                'kindQ' => $quiz->kindQ,
                'error' => '',
                'quizId' => $quizId,
                'url' => $url,
                'mode' => 'edit'));
        }

        else if(isset($_POST["editQ"])) {
            $newQuiz = Quiz::whereId($quizId);

            $qName = makeValidInput($_POST["name"]);
            $timeLen = makeValidInput($_POST["timeLen"]);
            $sDate = makeValidInput($_POST["sDate"]);
            $sTime = makeValidInput($_POST["sTime"]);
            $eDate = makeValidInput($_POST["eDate"]);
            $eTime = makeValidInput($_POST["eTime"]);
            $mark = makeValidInput($_POST["mark"]);
            $price = makeValidInput($_POST["price"]);
            $minusMark = (isset($_POST["minusMark"])) ? true : false;
            $kindQ = makeValidInput($_POST["kindQ"]);

            if($qName != $newQuiz->QN) {
                $count = Quiz::where('QN', '=', $qName)->count();
                if ($count > 0) {
                    $err = "آزمونی با همین نام در سیستم موجود است" . "<br/>";
                    return view('editQuiz', array('qName' => $qName,
                        'timeLen' => $timeLen,
                        'sDate' => $sDate,
                        'sTime' => $sTime,
                        'eDate' => $eDate,
                        'eTime' => $eTime,
                        'mark' => $mark,
                        'minusMark' => $minusMark,
                        'kindQ' => $kindQ,
                        'price' => $price,
                        'error' => $err,
                        'quiz_id' => $quizId,
                        'mode' => 'editInfo'));
                }
            }

            $sDateTmp = convertDateToString($sDate);
            $sTimeTmp = convertTimeToString($sTime);
            $eDateTmp = convertDateToString($eDate);
            $eTimeTmp = convertTimeToString($eTime);

            if ($sDateTmp > $eDateTmp || ($sDateTmp == $eDateTmp && $sTimeTmp >= $eTimeTmp)) {
                $err = "زمان شروع آزمون باید قبل از زمان اتمام آن باشد" . "<br/>";
                return view('editQuiz', array('qName' => $qName,
                    'timeLen' => $timeLen,
                    'sDate' => $sDate,
                    'sTime' => $sTime,
                    'eDate' => $eDate,
                    'eTime' => $eTime,
                    'mark' => $mark,
                    'price' => $price,
                    'minusMark' => $minusMark,
                    'kindQ' => $kindQ,
                    'error' => $err,
                    'quiz_id' => $quizId,
                    'mode' => 'editInfo'));
            }

            $newQuiz->QN = $qName;
            $newQuiz->tL = $timeLen;
            $newQuiz->author = "admin";
            $newQuiz->sDate = $sDateTmp;
            $newQuiz->sTime = $sTimeTmp;
            $newQuiz->eDate = $eDateTmp;
            $newQuiz->price = $price;
            $newQuiz->eTime = $eTimeTmp;
            $newQuiz->mark = $mark;
            $newQuiz->minusMark = $minusMark;
            $newQuiz->kindQ = $kindQ;
            $newQuiz->save();

            $msg = 'ویرایش اطلاعات آزمون به درستی انجام پذیرفت';
        }

        else if(isset($_POST["editQuestion"]))
            return Redirect::to("addQuestionToQuiz=" . $quizId);

        else if(isset($_POST["editDegree"])) {
            $url = URL('editQuiz') . "=" . $quizId;
            $degrees = Degree::all();
            $selectedDegree = DegreeOfQuiz::whereQuizId($quizId)->select('degree_id')->get();
            return view('addDegreeToQuiz', array('url' => $url, 'msg' => '', 'quiz_id' => $quizId, 'degrees' => $degrees, 'selectedDegrees' => $selectedDegree));
        }

        else if(isset($_POST["editD"])) {
            if(!isset($_POST["degrees"])) {
                $msg = 'پایه ی تحصیلی ای برای آزمون خود انتخاب نمایید';
                $url = URL('editQuiz') . "=" . $quizId;
                $degrees = Degree::all();
                $selectedDegree = DegreeOfQuiz::whereQuizId($quizId)->select('degree_id')->get();
                return view('addDegreeToQuiz', array('url' => $url, 'msg' => $msg, 'quiz_id' => $quizId, 'degrees' => $degrees, 'selectedDegrees' => $selectedDegree));
            }
            else {
                $degrees = $_POST["degrees"];
                DegreeOfQuiz::whereQuizId($quizId)->delete();
                foreach ($degrees as $degree) {
                    $degreeOfQuiz = new DegreeOfQuiz;
                    $degreeOfQuiz->quiz_id = $quizId;
                    $degreeOfQuiz->degree_id = makeValidInput($degree);
                    $degreeOfQuiz->save();
                }
                $msg = "ویرایش پایه های تحصیلی آزمون به درستی انجام پذیرفت";
            }
        }

        return view('editQuiz', array('quizId' => $quizId, 'msg' => $msg));
    }

    public function createQuiz() {

        if(isset($_POST["submitQ"])) {

            $qName = makeValidInput($_POST["name"]);
            $timeLen = makeValidInput($_POST["timeLen"]);
            $sDate = makeValidInput($_POST["sDate"]);
            $sTime = makeValidInput($_POST["sTime"]);
            $eDate = makeValidInput($_POST["eDate"]);
            $eTime = makeValidInput($_POST["eTime"]);
            $mark = makeValidInput($_POST["mark"]);
            $minusMark = (isset($_POST["minusMark"])) ? true : false;
            $kindQ = makeValidInput($_POST["kindQ"]);
            $price = makeValidInput($_POST["price"]);

            $count = Quiz::where('QN', '=', $qName)->count();
            if ($count > 0) {
                $err = "آزمونی با همین نام در سیستم موجود است" . "<br/>";
                return view('createQuiz', array('qName' => $qName,
                    'timeLen' => $timeLen,
                    'sDate' => $sDate,
                    'sTime' => $sTime,
                    'eDate' => $eDate,
                    'eTime' => $eTime,
                    'mark' => $mark,
                    'price' => $price,
                    'minusMark' => $minusMark,
                    'kindQ' => $kindQ,
                    'error' => $err));
            }

            $sDateTmp = convertDateToString($sDate);
            $sTimeTmp = convertTimeToString($sTime);
            $eDateTmp = convertDateToString($eDate);
            $eTimeTmp = convertTimeToString($eTime);

            if ($sDateTmp > $eDateTmp || ($sDateTmp == $eDateTmp && $sTimeTmp >= $eTimeTmp)) {
                $err = "زمان شروع آزمون باید قبل از زمان اتمام آن باشد" . "<br/>";
                return view('createQuiz', array('qName' => $qName,
                    'timeLen' => $timeLen,
                    'sDate' => $sDate,
                    'price' => $price,
                    'sTime' => $sTime,
                    'eDate' => $eDate,
                    'eTime' => $eTime,
                    'mark' => $mark,
                    'minusMark' => $minusMark,
                    'kindQ' => $kindQ,
                    'error' => $err));
            }

            $newQuiz = new Quiz;

            $newQuiz->QN = $qName;
            $newQuiz->tL = $timeLen;
            $newQuiz->author = "admin";
            $newQuiz->sDate = $sDateTmp;
            $newQuiz->sTime = $sTimeTmp;
            $newQuiz->eDate = $eDateTmp;
            $newQuiz->eTime = $eTimeTmp;
            $newQuiz->mark = $mark;
            $newQuiz->price = $price;
            $newQuiz->minusMark = $minusMark;
            $newQuiz->kindQ = $kindQ;
            $newQuiz->save();

            $qId = $newQuiz->id;

            $kindKarname = new KindKarname();
            $kindKarname->quiz_id = $qId;
            $kindKarname->lessonAvg = 1;
            $kindKarname->subjectAvg = 1;
            $kindKarname->lessonStatus = 1;
            $kindKarname->subjectStatus = 1;
            $kindKarname->lessonMaxPercent = 1;
            $kindKarname->subjectMaxPercent = 1;
            $kindKarname->partialTaraz = 1;
            $kindKarname->generalTaraz = 1;
            $kindKarname->lessonCityRank = 1;
            $kindKarname->subjectCityRank = 1;
            $kindKarname->lessonStateRank = 1;
            $kindKarname->subjectStateRank = 1;
            $kindKarname->lessonCountryRank = 1;
            $kindKarname->subjectCountryRank = 1;
            $kindKarname->generalCityRank = 1;
            $kindKarname->generalStateRank = 1;
            $kindKarname->generalCountryRank = 1;
            $kindKarname->coherences = 1;
            $kindKarname->lessonBarChart = 1;
            $kindKarname->subjectBarChart = 1;
            $kindKarname->lessonMark = 1;
            $kindKarname->subjectMark = 1;
            $kindKarname->save();

            return Redirect::to('addDegreeToQuiz='.$qId);
        }

        return view('createQuiz', array('qName' => '',
            'timeLen' => 0,
            'sDate' => '',
            'sTime' => '09:30',
            'eDate' => '',
            'eTime' => '09:30',
            'mark' => 0,
            'price' => 0,
            'minusMark' => true,
            'kindQ' => 1,
            'mode' => 'create',
            'error' => ''));
    }

    public function checkDate($quizId) {

        $quiz = Quiz::whereId($quizId);
        $sDate = $quiz->sDate;
        $eDate = $quiz->eDate;
        $sTime = $quiz->sTime;
        $eTime = $quiz->eTime;

        $day_time = getToday();

        if($sDate > $day_time['date'] || ($sDate == $day_time['date'] && $day_time['time'] < $sTime))
            return "-1";
        if($eDate < $day_time['date'] || ($eDate == $day_time['date'] && $day_time['time'] > $eTime))
            return "0";

        return $quiz->QN;
    }

    private function goToDoQuizPage($msg = "") {

        $degree = Auth::user()->degree;

        if($degree == -1 && Auth::user()->role == 1)
            $quizes = DegreeOfQuiz::select('quiz_id')->get();
        else
            $quizes = DegreeOfQuiz::whereDegreeId($degree)->select('quiz_id')->get();

        $validQuizes = array();
        $counter = 0;

        for($i = 0; $i < count($quizes); $i++) {
            $tmp = $this->checkDate($quizes[$i]->quiz_id);
            if ($tmp != "0" && $tmp != "-1")
                $validQuizes[$counter++] = array($quizes[$i]->quiz_id, $tmp);
        }

        if($counter == 0)
            $msg = "آزمونی برای ورود وجود ندارد";

        return view('quizEntry', array('msg' => $msg, 'validQuizes' => $validQuizes));
    }

    public function doQuiz($qId = "", $mode = false) {

        $uId = Auth::user()->id;
        $verify = '';
        
        if(isset($_POST["quiz_id"]) || $qId != "") {

            $quizTmp = Quiz::whereId($qId);

            if($qId == "" || $mode) {

                if($qId == "")
                    $qId = makeValidInput($_POST["quiz_id"]);

                $entry = QEntry::whereQId($qId)->whereUId($uId)->first();

                $mode = "normal";
                $tL = $quizTmp->tL;
                
                if($entry == null)
                    return $this->goToDoQuizPage("شما در آزمون مورد نظر ثبت نام نکرده اید");

                if($entry->timeEntry == null || empty($entry->timeEntry)) {

                    $tmp = $this->checkDate($qId);
                    
                    if(($tmp != "0" && $tmp != "-1") || $uId == 2089) {

                        $entry->timeEntry = time();
                        $entry->status = 0;
                        try {
                            $entry->save();
                        }
                        catch (\Exception $x) {
                            dd($x->getMessage());
                        }

                        $tmpROQ2Str = "";
                        $qInfo = getQOQ($qId, false);
                        
                        for ($i = 0; $i < count($qInfo); $i++)
                            $tmpROQ2Str .= "0";

                        $tmpROQ2 = new ROQ2();
                        $tmpROQ2->u_id = $uId;
                        $tmpROQ2->quiz_id = $qId;
                        $tmpROQ2->result = $tmpROQ2Str;
                        $tmpROQ2->save();

                        $verify = Hash::make($tmpROQ2->id);

                        $tmpROQ2 = $tmpROQ2Str;

//                        $this->fillROQ($qId);
                        $startTime = time();
                    }

                    else if($tmp == "0")
                        return $this->goToDoQuizPage("زمان آزمون مورد نظر به اتمام رسیده است");

                    else
                        return $this->goToDoQuizPage("زمان آزمون مورد نظر هنوز فرا نرسیده است");
                }

                else {

                    if($entry->status == 1)
                        return $this->goToDoQuizPage("شما قبلا در این آزمون شرکت کرده اید");

                    $qInfo = getQOQ($qId, false);

                    $tmpROQ2 = ROQ2::whereUId($uId)->whereQuizId($qId)->first();
                    $verify = Hash::make($tmpROQ2->id);

                    $tmpROQ2 = $tmpROQ2->result;

//                        for ($i = 0; $i < count($qInfo); $i++) {
//                            $condition = ['u_id' => $uId, 'qoq_id' => $qInfo[$i][count($qInfo[$i]) - 1]];
//                            $roqs[$i] = ROQ::where($condition)->select('result')->first()->result;
//                        }

                    $startTime = $entry->timeEntry;
                }
            }

            else {
                $startTime = time();
                $mode = "special";
                $qInfo = getQOQ($qId, false);
                $tL = 0;

                $tmpROQ2 = "";

                for ($i = 0; $i < count($qInfo); $i++) {
                    $tmpROQ2 .= "0";
                }
//                for ($i = 0; $i < count($qInfo); $i++) {
//                    $roqs[$i] = 0;
//                }
            }

            $roq = [];
            $counter = 0;

            for($i = 0; $i < strlen($tmpROQ2); $i++) {
                $roq[$counter++] = $tmpROQ2[$i];
            }

            $today = getToday();

            $reminder = ($tL * 60 - time() + $startTime);

            if($quizTmp->eDate == $today["date"]) {

                if($uId != 2089) {

                    $tmp = subTimes($quizTmp->eTime, $today['time']);
                    if ($reminder > $tmp) {
                        $reminder = $tmp;
                    }
                }
            }

            return view('quiz', array('quizId' => $qId, 'roqs' => $roq, 'questions' => $qInfo, 'uId' => $uId,
                'reminder' => $reminder, 'mode' => $mode, 'verify' => $verify));
        }

        return $this->goToDoQuizPage();

    }

    public function submitAllAns() {

        if(isset($_POST["newVals"]) && isset($_POST["quizId"]) && isset($_POST["uId"]) &&
            isset($_POST["verify"])) {

            $roq = ROQ2::whereUId(makeValidInput($_POST["uId"]))->whereQuizId(makeValidInput($_POST["quizId"]))->first();
            
            if(!Hash::check($roq->id, $_POST["verify"]))
                return;

            if($roq != null) {
                $roq->result = makeValidInput($_POST["newVals"]);
                try {
                    $roq->save();
                    echo "ok";
                }
                catch (\Exception $x) {
                    echo $x->getMessage();
                }
                return;
            }
        }

        echo "nok2";
    }
    
    public function seeQuiz($qId) {

        $mode = "special";
        $qInfo = getQOQ($qId, false);
        $counter = 0;
        $roq = [];

        $roq2 = ROQ2::whereUId(Auth::user()->id)->whereQuizId($qId)->first();
        if($roq2 != null) {
            $tmpROQ2 = $roq2->result;
            for ($i = 0; $i < strlen($tmpROQ2); $i++)
                $roq[$counter++] = $tmpROQ2[$i];
        }
        else {
            $roq2 = DB::select('select r.result from roq r, qoq q WHERE r.qoq_id = q.id and r.u_id = ' . Auth::user()->id . ' and q.quiz_id = ' . $qId);
            foreach ($roq2 as $itr) {
                $roq[$counter++] = $itr->result;
            }
        }

        return view('quiz', array('quizId' => $qId, 'roqs' => $roq, 'questions' => $qInfo,
            'mode' => $mode, 'reminder' => 0));
    }

    public function myQuizes() {

        $today = getToday();
        $date = $today["date"];
        $time = $today["time"];

        $uId = Auth::user()->id;
        $quizes = QEntry::whereUId($uId)->get();

        $selectedQuizes = [];
        $counter = 0;

        foreach ($quizes as $itr) {

            $quiz = Quiz::whereId($itr->q_id);

            if($quiz == null)
                continue;

            if(($quiz->sDate < $date && $quiz->eDate > $date) ||
                ($quiz->sDate < $date && $quiz->eDate >= $date && $quiz->eTime > $time) ||
                ($quiz->sDate == $date && $quiz->sTime <= $time && (
                        ($quiz->sDate == $quiz->eDate && $quiz->eTime > $time) ||
                        ($quiz->sDate != $quiz->eDate) ||
                        ($quiz->eDate == $date && $quiz->eTime > $time) ||
                        ($uId == 2089)
                    )
                )) {

                $timeLen = $quiz->tL;

                if($itr->timeEntry == "") {
                    $quiz->quizEntry = 1;
                }
                else {
                    $timeEntry = $itr->timeEntry;
                    $reminder = $timeLen * 60 - time() + $timeEntry;
                    if($reminder <= 0)
                        $quiz->quizEntry = -2;
                    else
                        $quiz->quizEntry = 1;
                }
            }
            else if($quiz->sDate > $date ||
                ($quiz->sDate == $date && $quiz->sTime > $time)) {
                $quiz->quizEntry = -1;
            }
            else {
                $quiz->quizEntry = -2;
            }

            $quiz->sDate = convertStringToDate($quiz->sDate);
            $quiz->eDate = convertStringToDate($quiz->eDate);
            $quiz->sTime = convertStringToTime($quiz->sTime);
            $quiz->eTime = convertStringToTime($quiz->eTime);
            $selectedQuizes[$counter++] = $quiz;

        }

        return view('myQuizes', ['quizes' => $selectedQuizes]);

    }

    public function buyQuiz() {

        $quizes = DB::select('select q.* from quiz q, degree_of_quiz dq WHERE dq.quiz_id = q.id and dq.degree_id = ' . Auth::user()->grade_id);
//        $quizes = Quiz::all();
        $today = getToday();
        $date = $today["date"];
        $time = $today["time"];

        $uId = Auth::user()->id;
        $validQuizes = [];
        $counter = 0;

        foreach ($quizes as $quiz) {

            if (($quiz->sDate > $date || $quiz->sDate == $date && $quiz->sTime > $time)) {
                if(QEntry::whereQId($quiz->id)->whereUId($uId)->count() == 0) {
                    $quiz->sDate = convertStringToDate($quiz->sDate);
                    $quiz->eDate = convertStringToDate($quiz->eDate);
                    $quiz->sTime = convertStringToTime($quiz->sTime);
                    $quiz->eTime = convertStringToTime($quiz->eTime);
                    if($quiz->price == 0)
                        $quiz->price = "رایگان";
                    else
                        $quiz->price .= " تومان";
                    $validQuizes[$counter++] = $quiz;
                }
            }
        }

        return view('buyQuiz', ['quizes' => $validQuizes]);
    }

    public function buySelectedQuiz($quizId) {

        if(Transaction::whereUserId(Auth::user()->id)->whereAdditionalId($quizId)->whereStatus("SUCCEED")->count() > 0) {
            dd("شما قبلا این آزمون را خریداری کرده اید");
        }

        $quiz = Quiz::whereId($quizId);
        if($quiz != null) {

            if($quiz->price == 0) {

                $tmp = new QEntry();
                $tmp->u_id = Auth::user()->id;
                $tmp->q_id = $quizId;

                $tmp->save();
                return Redirect::route('myQuizes');
            }

            return $this->doPayment($quizId);
        }

    }

    private function doPayment($additionalId) {

        try {
            $gateway = \Gateway::zarinpal();
            $gateway->setCallback(url('callback'));
            $price = Quiz::whereId($additionalId)->price * 10;

            $gateway->price($price)->ready($additionalId);
            return Redirect::to($gateway->redirect());

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function groupRegistrationQuiz($err = "") {

        $quizes = Quiz::all();
        $today = getToday();
        $date = $today["date"];
        $time = $today["time"];

        $validQuizes = [];
        $counter = 0;

        foreach ($quizes as $quiz) {
            if (($quiz->sDate > $date || $quiz->sDate == $date && $quiz->sTime > $time))
                $validQuizes[$counter++] = $quiz;
        }

        return view('groupRegistrationQuiz', ['err' => $err,
            'quizes' => $validQuizes]);
    }

    public function doGroupRegistryQuiz() {

        $err = "";

        if(isset($_FILES["group"]) && isset($_POST["qId"])) {

            $file = $_FILES["group"]["name"];
            $qId = makeValidInput($_POST["qId"]);

            if(!empty($file)) {

                $path = __DIR__ . '/../../../public/tmp/' . $file;

                $err = uploadCheck($path, "group", "اکسل ثبت نام گروهی آزمون", 20000000, "xlsx");

                if (empty($err)) {
                    upload($path, "group", "اکسل ثبت نام گروهی آزمون");
                    $excelReader = PHPExcel_IOFactory::createReaderForFile($path);
                    $excelObj = $excelReader->load($path);
                    $workSheet = $excelObj->getSheet(0);
                    $users = array();
                    $lastRow = $workSheet->getHighestRow();
                    $cols = $workSheet->getHighestColumn();

                    if ($cols < 'A') {
                        unlink($path);
                        $err = "تعداد ستون های فایل شما معتبر نمی باشد";
                    } else {
                        for ($row = 2; $row <= $lastRow; $row++) {

                            if($workSheet->getCell('A' . $row)->getValue() == "")
                                break;
                            $users[$row - 2] = $workSheet->getCell('A' . $row)->getValue();
                        }
                        unlink($path);
                        $err = $this->addUsersToQuiz($users, $qId);
                    }
                }
            }
        }

        if(empty($err))
            $err = "لطفا فایل اکسل مورد نیاز را آپلود نمایید";

        return $this->groupRegistrationQuiz($err);
    }

    private function addUsersToQuiz($users, $qId) {

        $errs = '';
        $counter = 2;

        foreach ($users as $user) {

            $tmpUser = User::whereUsername($user)->first();
            if($tmpUser == null) {
                $errs .= "ردیف " . ($counter++) . "<br/>";
                continue;
            }

            $tmp = new QEntry();
            $tmp->q_id = $qId;
            $tmp->u_id = $tmpUser->id;

            try {
                $tmp->save();
            }
            catch (\Exception $x) {
                $errs .= "ردیف " . $counter . " " . $x->getMessage() . "<br/>";
            }

            $counter++;

        }

        return $errs;

    }
}