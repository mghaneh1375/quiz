<?php

class HomeController extends BaseController {

    public function login() {
        return View::make('login', array('msg' => ''));
    }

    public function doLogin() {

        $username = makeValidInput(Input::get('username'));
        $password = makeValidInput(Input::get('password'));

        $tmp = User::where('username', '=', $username)->first();

        if($tmp != null && count($tmp) > 0 && $tmp->role == 0)
            $tmp->delete();

        if(User::where('username', '=', $username)->count() == 0) {

            $tmp = UserPanel::where('username', '=', $username)->first();

            if ($tmp != null && count($tmp) > 0 && Hash::check($password, $tmp->password)) {
                $stdTmp = StudentPanel::find($tmp->id);
                if ($stdTmp != null && Etehadiye::where('NationalID', '=', $stdTmp->IDNumber)->count() > 0) {

                    $field = Field::find($stdTmp->field_id);

                    if ($field != null) {

                        $user = new User();
						$user->id = $tmp->id;
                        $user->username = $username;
                        $user->password = Hash::make($password);
                        $user->role = 0;
                        $user->cId = $stdTmp->id;
                        $user->displayN = $stdTmp->first_name . $stdTmp->last_name;
                        $user->save();

                        if(Student::find($stdTmp->id) == null) {
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
            return View::make('login', array('msg' => $msg));
        }
    }

    public function logout() {

        Auth::logout();
        return Redirect::To("login");
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
        $qoqIds = QOQ::where('quizId', '=', 12)->take(10)->get();
        $users = User::where('role', '=', 0)->select('id')->get();
        for($i = 0; $i < count($qoqIds); $i++) {
            for($j = 0; $j < count($users); $j++) {
                $rand = rand(0, 4);
                $roq = new ROQ();
                $roq->uId = $users[$j]->id;
                $roq->qoqId = $qoqIds[$i]->id;
                $roq->result = $rand;
                $roq->save();
            }
        }

        for($j = 0; $j < count($users); $j++) {
            $qentry = new qentry();
            $qentry->uId = $users[$j]->id;
            $qentry->qId = 12;
            $qentry->status = 1;
            $qentry->save();
        }
    }

    private function fillStudentTable() {
        $qEntries = qentry::where('qId', '=', 8)->get();
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
        return View::make('home');
	}
}