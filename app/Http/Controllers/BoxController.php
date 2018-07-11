<?phpnamespace App\Http\Controllers;use App\models\Box;use App\models\BoxItems;use App\models\Degree;use App\models\Subject;class BoxController extends Controller {    public function createBox() {        $degrees = Degree::all();        if(count($degrees) == 0) {            $msg = 'پایه ی تحصیلی ای برای ایجاد جعبه ی جدید وجود ندارد';            return view('home', array('msg' => $msg));        }        return view('createBox', array('degrees' => $degrees));    }        public function seeBoxes() {        if(isset($_POST["editSelectedBox"])) {            $boxId = makeValidInput($_POST["editSelectedBox"]);            try {                $degrees = Degree::all();                $box = Box::whereId($boxId);                $sId = BoxItems::whereBoxId($box->id)->first()->subject_id;                $dId = getDegreeOfSubject($sId);                $lId = Subject::whereId($sId)->select("id_l")->first()->id_l;                return view('showBoxes', array('mode' => 'edit', 'selectedDegree' => $dId, 'selectedLesson' => $lId, 'degrees' => $degrees, 'box' => $box, 'msg' => ''));            }            catch (\Exception $e) {}        }        $boxes = Box::all();        return view('showBoxes', array('mode' => 'see', 'boxes' => $boxes));    }}