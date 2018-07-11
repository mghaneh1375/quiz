<?phpnamespace App\models;use Illuminate\Database\Eloquent\Model;use Illuminate\Support\Facades\DB;/** * An Eloquent Model: 'QuizStatus' * * @property integer $id * @property string $status * @property string $color * @property integer $floor * @property integer $ceil * @property integer $level * @property boolean $type * @property boolean $pic * @method static \Illuminate\Database\Query\Builder|\App\models\QuizStatus whereLevel($value) */class QuizStatus extends Model {        protected $table = "quiz_status";    public $timestamps = false;    public static function whereId($target) {        return QuizStatus::find($target);    }}