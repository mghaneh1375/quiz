<?php

namespace App\Http\Controllers;

use App\models\City;
use App\models\Etehadiye;
use App\models\Field;
use App\models\QEntry;
use App\models\QOQ;
use App\models\ROQ;
use App\models\Student;
use App\models\StudentPanel;
use App\models\User;
use App\models\UserPanel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller {

    public function login() {
        return view('login', array('msg' => ''));
    }

    public function doLogin() {

        $username = makeValidInput(Input::get('username'));
        $password = makeValidInput(Input::get('password'));

        $tmp = User::whereUsername($username)->first();

        if($tmp != null > 0 && $tmp->role == 0)
            $tmp->delete();

        if(User::whereUsername($username)->count() == 0) {

            $tmp = UserPanel::whereUsername($username)->first();

            if ($tmp != null && Hash::check($password, $tmp->password)) {
                $stdTmp = StudentPanel::whereId($tmp->id);
                if ($stdTmp != null && Etehadiye::where('NationalID', '=', $stdTmp->IDNumber)->count() > 0) {

                    $field = Field::whereId($stdTmp->field_id);

                    if ($field != null) {

                        $user = new User();
						$user->id = $tmp->id;
                        $user->username = $username;
                        $user->password = Hash::make($password);
                        $user->role = 0;
                        $user->cId = $stdTmp->id;
                        $user->displayN = $stdTmp->first_name . $stdTmp->last_name;
                        $user->save();

                        if(Student::whereId($stdTmp->id) == null) {
                            switch ($field->id) {
                                case 32:
                                    $degree = 34;
                                    break;
                                case 33:
                                    $degree = 37;
                                    break;
                                case 34:
                                    $degree = 38;
                                    break;

                                default:
                                    $degree = 0;
                                    break;
                            }
                            $std = new Student();
                            $std->id = $stdTmp->id;
                            $std->degree = $degree;
                            $std->save();
                        }
                    }
                }
            }
        }


        if(Auth::attempt(array('username' => $username, 'password' => $password))) {
            return Redirect::intended('/');
        }
        else {
            $msg = 'نام کاربری و یا پسورد اشتباه است';
            return view('login', array('msg' => $msg));
        }
    }

    public function logout() {

        Auth::logout();
        return Redirect::to("login");
    }

//    public function filterQuestions() {
//
//        $questions = Question::all();
//
//        $target1 = __DIR__ . '/../../public/upload/';
//        $target2 = __DIR__ . '/../../public/upload1/';
//
//        foreach ($questions as $itr) {
//            $file = MQF::where('qId', '=', $itr->id)->first()->question . '.jpg';
////            copy(URL::asset('upload/' . $file), URL::asset('upload1/' . $file));
//            copy($target1 . $file, $target2 . $file);
//            $file = MSF::where('qId', '=', $itr->id)->first()->solution . '.jpg';
//            copy($target1 . $file, $target2 . $file);
////            copy(URL::asset('upload/' . $file), URL::asset('upload1/' . $file));
//        }
//
//    }

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

    private function fillStudentTable() {
        $qEntries = QEntry::whereQId(8)->get();
        $cities = City::all();
        for($i = 0; $i < count($qEntries); $i++) {
            $std = new StudentPanel();
            $std->id = $qEntries[$i]->uId;
            $rand = rand(0, count($cities) - 1);
            $std->city_id = $cities[$rand]->id;
            $std->save();
        }
    }
    
	public function showHome() {
//        $this->createTestForQuizes();
        return view('home');
	}
}