<?phpnamespace App\models;use Illuminate\Database\Eloquent\Model;/** * An Eloquent Model: 'Quiz' * * @property integer $id * @property string $QN * @property string $sDate * @property string $eDate * @property string $sTime * @property string $eTime * @property integer $tL * @property integer $mark * @property boolean $minusMark * @property string $author * @property integer $kindQ */class Quiz extends Model {    protected $table = "quiz";    public $timestamps = false;    public function degree() {        return $this->belongsToMany('Degree', 'degreeOfQuiz', 'quiz_id', 'degree_id');    }    public function boxes() {        return $this->belongsToMany('Box', 'boxesOfQuiz', 'quiz_id', 'box_id');    }    public static function whereId($target) {        return Quiz::whereId($target);    }}