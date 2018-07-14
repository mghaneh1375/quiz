<?phpuse App\models\City;use App\models\Lesson;use App\models\QOQ;use App\models\Question;use App\models\State;use App\models\StudentPanel;use App\models\Subject;use Illuminate\Support\Facades\DB;function getQuestionLevel($qId) { // strategies should be taken    $question = Question::whereId($qId);    if($question == null)        return "متوسط";    switch ($question->grade) {        case 1:            return "ساده"; // easy            break;        case 2:        default:            return "متوسط"; // average            break;        case 3:            return "دشوار"; // hard            break;    }}function getStdCityAndState($uId) {    $tmp = StudentPanel::whereId($uId);    if($tmp == null) {        return ["city" => City::first()->name, "state" => State::whereId(City::first()->state_id)->name,            'cityId' => City::first()->id, 'stateId' => State::whereId(City::first()->state_id)->id];    }    $cityId = $tmp->city_id;    $city = City::whereId($cityId);    if($city == null)        return ["city" => City::first()->name, "state" => State::whereId(City::first()->state_id)->name,            'cityId' => City::first()->id, 'stateId' => State::whereId(City::first()->state_id)->id];    return ["city" => $city->name, "state" => State::whereId(City::whereId($cityId)->state_id)->name,        'cityId' => $cityId, 'stateId' => State::whereId(City::whereId($cityId)->state_id)->id];}function getSubjectQuiz($quizId) {    $sIds = DB::select('SELECT DISTINCT S.id, S.nameSubject as name, S.id_l as lessonId from questions Q, subjects S, qoq QO WHERE QO.quiz_id = ' . $quizId . ' and QO.question_id = Q.id and Q.subject_id = S.id order by S.id ASC');    if(count($sIds) > 0)        return $sIds;    return [];}function calcRankInCity($quizId, $uId, $cityId) {    $ranks = DB::select('SELECT qR.u_id, sum(taraz.taraz * (SELECT lesson.coherence FROM medal_db.lessons as lesson WHERE lesson.id = taraz.l_id))as weighted_avg '. 'from medal_db.qentry as qR, medal_db.taraz as taraz, azmoon.students as rd WHERE qR.id = taraz.q_entry_id and qR.u_id = rd.id AND rd.city_id = ' . $cityId . ' and qR.q_id = ' . $quizId . ' GROUP by (qR.u_id) ORDER by weighted_avg DESC');    for($i = 0; $i < count($ranks); $i++) {        if($ranks[$i]->u_id == $uId) {            $r = $i + 1;            $currTaraz = $ranks[$i]->weighted_avg;            $k = $i - 1;            while ($k >= 0 && $ranks[$k]->weighted_avg == $currTaraz) {                $k--;                $r--;            }            return $r;        }    }    return count($ranks);}function calcRankInState($quizId, $uId, $stateId) {    $ranks = DB::select('SELECT qR.u_id, sum(taraz.taraz * (SELECT lesson.coherence FROM medal_db.lessons as lesson WHERE lesson.id = taraz.l_id)) as weighted_avg '.        'from medal_db.qentry as qR, medal_db.taraz as taraz, azmoon.students as rd, azmoon.cities ci WHERE qR.id = taraz.q_entry_id and qR.u_id = rd.id AND rd.city_id = ci.id and ci.state_id = ' . $stateId . ' and qR.q_id = ' . $quizId . ' GROUP by (qR.u_id) ORDER by weighted_avg DESC');    for($i = 0; $i < count($ranks); $i++) {        if($ranks[$i]->u_id == $uId) {            $r = $i + 1;            $currTaraz = $ranks[$i]->weighted_avg;            $k = $i - 1;            while ($k >= 0 && $ranks[$k]->weighted_avg == $currTaraz) {                $k--;                $r--;            }            return $r;        }    }    return count($ranks);}function getLessonQuiz($quizId) {    $lIds = DB::select('SELECT DISTINCT L.id, L.nameL, L.coherence from lessons L, questions Q, subjects S, qoq QO WHERE QO.quiz_id = ' . $quizId . ' and QO.question_id = Q.id and Q.subject_id = S.id and S.id_l = L.id order by L.id ASC');    if(count($lIds) > 0)        return $lIds;    return [];}function makeValidInput($input) {    $input = addslashes($input);    $input = trim($input);    if(get_magic_quotes_gpc())        $input = stripslashes($input);    $input = htmlspecialchars($input);    return $input;}function getDegreeOfSubject($sId) {    return Lesson::whereId(Subject::whereId($sId)->id_l)->degree;}function getQuestionInfo($questionId, $mode) {    $question = Question::whereId($questionId);    $out[0] = $questionId;    $out[1] = $question->kind_bq;    $out[2] = $question->kind_q;    switch($question->kind_bq) {        case 0:            $out[3] = Question::whereId($questionId)->mqf->question;            if($mode) {                $out[4] = Question::whereId($questionId)->msf->solution;            }            break;        case 1:            $out[3] = Question::whereId($questionId)->mqt->question;            if($mode) {                $out[4] = Question::whereId($questionId)->mst->solution;            }            break;        default:            return null;    }    if ($question->kindQ == 0 && $question->kind_bq == 1) {        $choices = Question::whereId($questionId)->mct;        $idx = ($mode) ? 5 : 4;        $out[$idx] = $choices->ch1;        $out[$idx + 1] = $choices->ch2;        $out[$idx + 2] = $choices->ch3;        $out[$idx + 3] = $choices->ch4;    }    return $out;}function getQOQ($quizId, $mode) {    $qoqs = QOQ::whereQuizId($quizId)->select('question_id', 'id')->orderBy('qNo', 'ASC')->get();    $out = array();    $counter = 0;    while ($counter < count($qoqs)) {        $out[$counter] = getQuestionInfo($qoqs[$counter]->question_id, $mode);        $out[$counter][count($out[$counter])] = $qoqs[$counter]->id;        $counter++;    }    return $out;}