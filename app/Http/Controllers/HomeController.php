<?php

namespace App\Http\Controllers;

use App\models\City;
use App\models\Degree;
use App\models\QEntry;
use App\models\QOQ;
use App\models\Quiz;
use App\models\ROQ;
use App\models\ROQ2;
use App\models\State;
use App\models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
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
    
	public function showHome() {
//        $this->createTestForQuizes();
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
            $user->phone_num = '09' . $phone_num;
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