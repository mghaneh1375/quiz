<?php

namespace App\Http\Controllers;

use App\models\City;
use App\models\Degree;
use App\models\DegreeOfQuiz;
use App\models\QEntry;
use App\models\QOQ;
use App\models\Quiz;
use App\models\ROQ;
use App\models\ROQ2;
use App\models\State;
use App\models\Survey;
use App\models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use PHPExcel;
use PHPExcel_IOFactory;

class HomeController extends Controller {

    public function login() {
        return view('login', array('msg' => ''));
    }

    public function doLogin() {

        $username = convert(makeValidInput(Input::get('username')));
        $password = convert(makeValidInput(Input::get('password')));

        if(Auth::attempt(array('username' => $username, 'password' => $password)))
            return Redirect::intended('/');
        else {
            $msg = 'نام کاربری و یا پسورد اشتباه است';
            return view('login', array('msg' => $msg));
        }
    }

    public function logout() {

        Auth::logout();
        return Redirect::to("login");
    }

    private function createTestForQuizes() {
        $qoqIds = QOQ::whereQuizId(12)->take(10)->get();
        $users = User::where('role', '=', 0)->select('id')->get();
        for($i = 0; $i < count($qoqIds); $i++) {
            for($j = 0; $j < count($users); $j++) {
                $rand = rand(0, 4);
                $roq = new ROQ();
                $roq->u_id = $users[$j]->id;
                $roq->qoq_id = $qoqIds[$i]->id;
                $roq->result = $rand;
                $roq->save();
            }
        }

        for($j = 0; $j < count($users); $j++) {
            $qentry = new QEntry();
            $qentry->u_id = $users[$j]->id;
            $qentry->q_id = 12;
            $qentry->status = 1;
            $qentry->save();
        }
    }

//    private function fillStudentTable() {
//        $qEntries = QEntry::whereQId(8)->get();
//        $cities = City::all();
//        for($i = 0; $i < count($qEntries); $i++) {
//            $std = new StudentPanel();
//            $std->id = $qEntries[$i]->uId;
//            $rand = rand(0, count($cities) - 1);
//            $std->city_id = $cities[$rand]->id;
//            $std->save();
//        }
//    }

    public function survey() {
        return view('survey');
    }

    public function doSurvey() {

        if(isset($_POST["ans"])) {
            $tmp = new Survey();
            $tmp->result = makeValidInput($_POST["ans"]);
            $tmp->u_id = Auth::user()->id;
            $tmp->save();
        }
    }

    public function getPic() {

        $pic = Auth::user()->pic;

        if($pic == null)
            $pic = URL::asset('images/profile.png');
        else
            $pic = URL::asset('profileImages/' . $pic);

        return view('getPic', ['pic' => $pic]);
    }

    public function setProfilePic() {

        if(isset($_FILES["pic"])) {

            $file = $_FILES["pic"]["name"];

            if(!empty($file)) {

                $path = __DIR__ . '/../../../public/profileImages/' . $file;

                $err = uploadCheck($path, "pic", "تعیین عکس پروفایل", 20000000, -1);

                if (empty($err)) {
                    upload($path, "pic", "تعیین عکس پروفایل");
                    $user = Auth::user();
                    $user->pic = $file;
                    $user->save();

                    return Redirect::route('getPic');
                }
            }
        }
    }

	public function showHome() {
//        $this->createTestForQuizes();

        $degree = Auth::user()->grade_id;
        $quiz_id = DegreeOfQuiz::whereDegreeId($degree)->select('quiz_id')->first();

        if($quiz_id != null) {

            $quizId = $quiz_id->quiz_id;

            $users = DB::select('SELECT qR.id, qR.u_id, sum(taraz.taraz * (SELECT lesson.coherence FROM lessons as lesson WHERE lesson.id = taraz.l_id)) as weighted_avg ' .
                'from qentry qR, taraz WHERE qR.id = taraz.q_entry_id and qR.q_id = ' . $quizId .
                " and (select count(*) from roq r, qoq Q where r.u_id = qR.u_id and r.qoq_id = Q.id and Q.quiz_id = qR.q_id) > 0 " .
                'GROUP by (qR.id) ORDER by weighted_avg DESC limit 0, 10');
//            $users = [];

            $tmp = DB::select('SELECT DISTINCT L.id, L.nameL, L.coherence from lessons L, questions Q, subjects S, qoq QO WHERE QO.quiz_id = ' . $quizId . ' and QO.question_id = Q.id and Q.subject_id = S.id and S.id_l = L.id order by L.id ASC');
            $sum = 0;

            if ($tmp == null || count($tmp) == 0)
                $sum = 1;

            else {
                foreach ($tmp as $itr) {
                    $sum += $itr->coherence;
                }
            }

            for ($i = 0; $i < count($users); $i++)
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

                $target = User::whereId($user->u_id);

                if ($target == null) {
                    array_splice($users, $i);
                    continue;
                }

                $i++;
                $user->name = $target->first_name . " " . $target->last_name;
                $user->uId = $target->id;

                $cityAndState = getStdCityAndState($target->id);
                $user->city = $cityAndState['city'];
                $user->state = $cityAndState['state'];
            }

            usort($users, function ($a, $b) {
                return $a->rank - $b->rank;
            });

            return view('home2', ['users' => $users]);
        }

        return view('home');
	}
    
    public function registration() {
        return view('registration', ['states' => State::orderBy('name', 'ASC')->get(), 'degrees' => Degree::orderBy('id', 'ASC')->get()]);
    }

    public function getCities() {

        if(isset($_POST["stateId"])) {

            echo json_encode(City::whereStateId(makeValidInput($_POST["stateId"]))->get());

        }

    }

    public function doRegistration() {

        if(isset($_POST["first_name"]) && isset($_POST["last_name"]) && isset($_POST["city_id"]) &&
            isset($_POST["sex_id"]) && isset($_POST["degree"]) && isset($_POST["phone_num"]) &&
            isset($_POST["nid"]) && isset($_POST["father_name"]) && isset($_POST["home_phone"]) &&
            isset($_POST["subscription"])
        ) {

            $sex_id = makeValidInput($_POST["sex_id"]);

            if($sex_id == "none") {
                echo "nok1";
                return;
            }

            $subscription = makeValidInput($_POST["subscription"]);

            if($subscription == "none") {
                echo "nok1";
                return;
            }

            $first_name = makeValidInput($_POST["first_name"]);
            $last_name = makeValidInput($_POST["last_name"]);
            $father_name = makeValidInput($_POST["father_name"]);
            $home_phone = makeValidInput($_POST["home_phone"]);
            $phone_num = convert(makeValidInput($_POST["phone_num"]));
            $nid = convert(makeValidInput($_POST["nid"]));

            if(empty($first_name) || empty($last_name) || empty($father_name) || empty($home_phone)
                || empty($phone_num) || empty($nid)) {
                echo "nok2";
                return;
            }


            if(strlen($home_phone) > 12) {
                echo "nok3";
                return;
            }

            if(strlen($phone_num) != 9) {
                echo "nok4";
                return;
            }

            $phone_num = '09' . $phone_num;

            $user = new User();
            $user->city_id = makeValidInput($_POST["city_id"]);
            $user->first_name = $first_name;
            $user->last_name = $last_name;
            $user->father_name = $father_name;
            $user->home_phone = $home_phone;
            $user->password = Hash::make($phone_num);
            $user->username = $nid;
            $user->sex_id = $sex_id;
            $user->grade_id = makeValidInput($_POST["degree"]);
            $user->phone_num = $phone_num;
            $user->subscription = $subscription;

            try {
                $user->save();
                sms('کاربر عزیز!
ثبت نام شما در سایت آینده سازان با موفقیت انجام گرفت.
نام کاربری شما کد ملی شما و رمزعبورتان شماره تلفن همراهتان می باشد.', $user->phone_num);
                echo "ok";
            }
            catch (\Exception $x) {
                echo "nok";
            }

            return;
        }
    }

    public function checkNID() {

        if(isset($_POST["NID"])) {

            $NID = convert(makeValidInput($_POST["NID"]));

            if(!_custom_check_national_code($NID))
                echo "nok1";
            else if(User::whereUsername($NID)->count() > 0)
                echo "nok2";
            else
                echo "ok";
        }

    }

    public function checkPhoneNum() {

        if(isset($_POST["phone_num"])) {

            if(User::wherePhoneNum('09' . convert(makeValidInput($_POST["phone_num"])))->count() > 0)
                echo "nok";
            else
                echo "ok";
        }
    }

    public function doGroupRegistry() {

        $err = "";

        if(isset($_FILES["group"])) {

            $file = $_FILES["group"]["name"];

            if(!empty($file)) {

                $path = __DIR__ . '/../../../public/tmp/' . $file;

                $err = uploadCheck($path, "group", "اکسل ثبت نام گروهی", 20000000, "xlsx");

                if (empty($err)) {
                    upload($path, "group", "اکسل ثبت نام گروهی");
                    $excelReader = PHPExcel_IOFactory::createReaderForFile($path);
                    $excelObj = $excelReader->load($path);
                    $workSheet = $excelObj->getSheet(0);
                    $users = array();
                    $lastRow = $workSheet->getHighestRow();
                    $cols = $workSheet->getHighestColumn();

                    if ($cols < 'G') {
                        unlink($path);
                        $err = "تعداد ستون های فایل شما معتبر نمی باشد";
                    } else {
                        for ($row = 2; $row <= $lastRow; $row++) {

                            if($workSheet->getCell('A' . $row)->getValue() == "")
                                break;
                            $users[$row - 2][0] = $workSheet->getCell('A' . $row)->getValue();
                            $users[$row - 2][1] = $workSheet->getCell('B' . $row)->getValue();
                            $users[$row - 2][2] = convert($workSheet->getCell('C' . $row)->getValue());
                            $users[$row - 2][3] = convert($workSheet->getCell('D' . $row)->getValue());
                            $users[$row - 2][4] = $workSheet->getCell('E' . $row)->getValue();
                            $users[$row - 2][5] = $workSheet->getCell('F' . $row)->getValue();
                            $users[$row - 2][6] = $workSheet->getCell('G' . $row)->getValue();
                            $users[$row - 2][7] = $workSheet->getCell('H' . $row)->getValue();
                            $users[$row - 2][8] = $workSheet->getCell('I' . $row)->getValue();
                            $users[$row - 2][9] = $workSheet->getCell('J' . $row)->getValue();
                        }
                        unlink($path);
                        $err = $this->addUsers($users);
                    }
                }
            }
        }

        if(empty($err))
            $err = "لطفا فایل اکسل مورد نیاز را آپلود نمایید";

        return $this->groupRegistration($err);
    }

    private function addUsers($users) {

        $errs = '';
        $counter = 2;

        foreach ($users as $user) {

            if(count($user) < 7) {
                $errs .= '(تعداد ستون نامعتبر) ردیف ' . ($counter++) . '</br/>';
                continue;
            }

            if(User::whereUsername($user[2])->count() > 0 || !_custom_check_national_code($user[2])) {
                $errs .= '(کد ملی تکراری و یا نامعتبر) ردیف ' . ($counter++) . '</br/>';
                continue;
            }

            $tmp = new User();
            $tmp->first_name = $user[0];
            $tmp->last_name = $user[1];
            $tmp->username = $user[2];
            $tmp->password = Hash::make($user[3]);
            $tmp->phone_num = $user[3];
            $tmp->sex_id = $user[4];
            $tmp->grade_id = $user[5];
            $tmp->city_id = $user[6];
            if($user[7] != null && !empty($user[7]))
                $tmp->father_name = $user[7];

            if($user[8] != null && !empty($user[8]))
                $tmp->home_phone = $user[8];

            if($user[9] != null && !empty($user[9]))
                $tmp->subscription = $user[9];

            try {
                $tmp->save();
            }
            catch (\Exception $x) {
                $errs .= '(خطای 102) ردیف ' . $counter . ' ' . $x->getMessage() . '</br/>';
            }

            $counter++;
        }

        return $errs;
    }
    
    public function groupRegistration($err = "") {
        return view('groupRegistration', array('err' => $err));
    }

}