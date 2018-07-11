<?php

include_once 'Date.php';

class QuizController extends BaseController {

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
        catch (Exception $x) {
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

                    $targetFile = "status/" . $file["name"];

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
            $quizStatus = QuizStatus::find($quizStatusId);

            if($quizStatus != null) {

                if(!empty($quizStatus->pic) && file_exists(__DIR__ . "/../../public/status/" . $quizStatus->status)) {
                    $targetFile = __DIR__ . "/../../public/status/" . $quizStatus->status;
                    unlink($targetFile);
                }

                $quizStatus->delete();
            }
        }

        $quizStatus = QuizStatus::all();
        return view('QuizStatus', array('QuizStatus' => $quizStatus, 'mode' => $mode, 'msg' => $msg));
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
                $conditions = ["compass_id" => $items[$i]->compass_id, 'grade' => $items[$i]->grade];
                $questions[$i] = Subject::find($items[$i]->subject_id)->questions()->where($conditions)->select('questions.id', 'questions.organizationId')->get();
                $subject = Subject::whereId($items[$i]->subject_id)->select('subjects.nameSubject')->get();
                if(count($subject) > 0)
                    $items[$i]->subject_id = $subject[0]->nameSubject;
                if($items[$i]->grade == 1)
                    $items[$i]->grade = 'آسان';
                else if($items[$i]->grade == 2)
                    $items[$i]->grade = 'متوسط';
                else
                    $items[$i]->grade = 'دشوار';
                $items[$i]->compass_id = Compass::whereId($items[$i]->compass_id)->name;
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
                    $qoqTmp = QOQ::find($qoqId);
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
            if($box != null && count($box) > 0) {
                DB::select('delete from qoq where quiz_id = ' . $quizId . ' and qNo >= ' . $box->from_
                    . ' and qNo <= ' . $box->to_);
            }
        }

        else if(isset($_POST["addBoxToQuiz"])) {
            $boxId = makeValidInput($_POST["selectedBox"]);
            $conditions = ['quiz_id' => $quizId, 'box_id' => $boxId];
            if(BoxesOfQuiz::where($conditions)->count() == 0) {

                $boxesOfQuizTmp = BoxesOfQuiz::where('quiz_id', '=', $quizId)->select('box_id')->get();
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

                    for ($i = 0; $i < count($itemsTmp); $i++) {
                        $conditions = ['compass_id' => $itemsTmp[$i]->compass_id, 'grade' => $itemsTmp[$i]->grade];
                        $questionId = Subject::find($itemsTmp[$i]->subject_id)->questions()->where($conditions)->select('questions.id')->first();
                        if ($questionId == null || count($questionId) == 0) {
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
            else {
                $msg = "جعبه ی انتخابی برای آزمون قبلا انتخاب شده است";
            }
        }

        else if(isset($_POST["showQuiz"])) {
            return $this->doQuiz($quizId);
        }

        $boxesOfQuiz = Quiz::whereId($quizId)->boxes()->select('box.id', 'box.name')->get();
        $allBoxes = Box::select('box.id', 'box.name')->get();

        return view('addQuestionToQuiz', array('boxesOfQuiz' => $boxesOfQuiz, 'quiz_id' => $quizId, 'qoq' => $qoq, 'box_id' => $boxId,
            'boxes' => $allBoxes, 'items' => $items, 'questions' => $questions, 'msg' => $msg));
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
                $degreeOfQuiz->degreeId = makeValidInput($degree);
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
                'kindQ' => $quiz->kindQ,
                'error' => '',
                'quiz_id' => $quizId,
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
                    'minusMark' => $minusMark,
                    'kindQ' => $kindQ,
                    'error' => $err,
                    'quiz_id' => $quizId,
                    'mode' => 'editInfo'));
            }

            $newQuiz->QN = $qName;
            $newQuiz->classId = 1;
            $newQuiz->tL = $timeLen;
            $newQuiz->author = "admin";
            $newQuiz->sDate = $sDateTmp;
            $newQuiz->sTime = $sTimeTmp;
            $newQuiz->eDate = $eDateTmp;
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
            $selectedDegree = DegreeOfQuiz::where('quiz_id', '=', $quizId)->select('degreeId')->get();
            return view('addDegreeToQuiz', array('url' => $url, 'msg' => '', 'quiz_id' => $quizId, 'degrees' => $degrees, 'selectedDegrees' => $selectedDegree));
        }

        else if(isset($_POST["editD"])) {
            if(!isset($_POST["degrees"])) {
                $msg = 'پایه ی تحصیلی ای برای آزمون خود انتخاب نمایید';
                $url = URL('editQuiz') . "=" . $quizId;
                $degrees = Degree::all();
                $selectedDegree = DegreeOfQuiz::where('quiz_id', '=', $quizId)->select('degreeId')->get();
                return view('addDegreeToQuiz', array('url' => $url, 'msg' => $msg, 'quiz_id' => $quizId, 'degrees' => $degrees, 'selectedDegrees' => $selectedDegree));
            }
            else {
                $degrees = $_POST["degrees"];
                DegreeOfQuiz::where('quiz_id', '=', $quizId)->delete();
                foreach ($degrees as $degree) {
                    $degreeOfQuiz = new DegreeOfQuiz;
                    $degreeOfQuiz->quiz_id = $quizId;
                    $degreeOfQuiz->degreeId = makeValidInput($degree);
                    $degreeOfQuiz->save();
                }
                $msg = "ویرایش پایه های تحصیلی آزمون به درستی انجام پذیرفت";
            }
        }

        return view('editQuiz', array('quiz_id' => $quizId, 'msg' => $msg));
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
            $newQuiz->classId = 1;
            $newQuiz->tL = $timeLen;
            $newQuiz->author = "admin";
            $newQuiz->sDate = $sDateTmp;
            $newQuiz->sTime = $sTimeTmp;
            $newQuiz->eDate = $eDateTmp;
            $newQuiz->eTime = $eTimeTmp;
            $newQuiz->mark = $mark;
            $newQuiz->minusMark = $minusMark;
            $newQuiz->kindQ = $kindQ;
            $newQuiz->save();

            $qId = $newQuiz->id;

            $kindKarname = new KindKarname();
            $kindKarname->quiz_id = $qId;
            $kindKarname->lessonAvg = 1;
            $kindKarname->subjectAvg = 1;
            $kindKarname->compassAvg = 1;
            $kindKarname->lessonStatus = 1;
            $kindKarname->subjectStatus = 1;
            $kindKarname->compassStatus = 1;
            $kindKarname->lessonMaxPercent = 1;
            $kindKarname->subjectMaxPercent = 1;
            $kindKarname->compassMaxPercent = 1;
            $kindKarname->partialTaraz = 1;
            $kindKarname->generalTaraz = 1;
            $kindKarname->lessonCityRank = 1;
            $kindKarname->subjectCityRank = 1;
            $kindKarname->compassCityRank = 1;
            $kindKarname->lessonStateRank = 1;
            $kindKarname->subjectStateRank = 1;
            $kindKarname->compassStateRank = 1;
            $kindKarname->lessonCountryRank = 1;
            $kindKarname->subjectCountryRank = 1;
            $kindKarname->compassCountryRank = 1;
            $kindKarname->generalCityRank = 1;
            $kindKarname->generalStateRank = 1;
            $kindKarname->generalCountryRank = 1;
            $kindKarname->coherences = 1;
            $kindKarname->lessonBarChart = 1;
            $kindKarname->subjectBarChart = 1;
            $kindKarname->compassBarChart = 1;
            $kindKarname->lessonMark = 1;
            $kindKarname->subjectMark = 1;
            $kindKarname->compassMark = 1;
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

    private function fillROQ($quizId) {
        $uId = Auth::user()->id;
        if($uId != -1) {
            $qoqIds = QOQ::whereQuizId($quizId)->select('id')->get();
            for($i = 0; $i < count($qoqIds); $i++) {
                $roq = new ROQ();
                $roq->qoqId = $qoqIds[$i]->id;
                $roq->result = 0;
                $roq->uId = $uId;
                $roq->save();
            }
        }
    }

    private function goToDoQuizPage($msg = "") {

        $degree = Auth::user()->student->degree;

        if($degree == -1 && Auth::user()->role == 1)
            $quizes = DegreeOfQuiz::select('quiz_id')->get();
        else
            $quizes = DegreeOfQuiz::where('degreeId', '=', $degree)->select('quiz_id')->get();

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

    public function doQuiz($qId = "") {

        if(isset($_POST["quiz_id"]) || $qId != "") {

            $roqs = array();

            if($qId == "") {
                $qId = makeValidInput($_POST["quiz_id"]);
                $uId = Auth::user()->id;

                $condition = ["qId" => $qId, "uId" => $uId];
                $entry = QEntry::where($condition)->select('status', 'timeEntry', 'dateEntry')->get();

                $mode = "normal";
                $tL = Quiz::whereId($qId)->tL;
                $qInfo = getQOQ($qId, false);

                if($entry == null || count($entry) == 0) {
                    $tmp = $this->checkDate($qId);
                    if($tmp != "0" && $tmp != "-1") {
                        $entry = new qentry();
                        $entry->uId = $uId;
                        $entry->qId = $qId;
                        $date_time = getToday();
                        $entry->timeEntry = time();
                        $entry->dateEntry = $date_time["date"];
                        $entry->status = 0;
                        $entry->save();
                        $this->fillROQ($qId);
                        $startTime = time();
                    }
                    else if($tmp == "0")
                        return $this->goToDoQuizPage("زمان آزمون مورد نظر به اتمام رسیده است");
                    else
                        return $this->goToDoQuizPage("زمان آزمون مورد نظر هنوز فرا نرسیده است");
                }
                else {

                    $uId = Auth::user()->id;
                    if ($uId != -1) {
                        for ($i = 0; $i < count($qInfo); $i++) {
                            $condition = ['uId' => $uId, 'qoqId' => $qInfo[$i][count($qInfo[$i]) - 1]];
                            $roqs[$i] = ROQ::where($condition)->select('result')->first()->result;
                        }
                    }
                    $entry = $entry[0];
                    if($entry->status == 1)
                        return $this->goToDoQuizPage("شما قبلا در این آزمون شرکت کرده اید");
                    $startTime = $entry->timeEntry;
                }
            }

            else {
                $startTime = time();
                $mode = "special";
                $qInfo = getQOQ($qId, false);
                $tL = 0;
                for ($i = 0; $i < count($qInfo); $i++) {
                    $roqs[$i] = 0;
                }
            }

            return view('quiz', array('quiz_id' => $qId, 'roqs' => $roqs, 'questions' => $qInfo,
                'tL' => $tL * 60, 'mode' => $mode, 'startTime' => $startTime));
        }

        return $this->goToDoQuizPage();

    }

}