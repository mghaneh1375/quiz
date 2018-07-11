<?phpnamespace App\models;use Illuminate\Database\Eloquent\Model;/** * An Eloquent Model: 'BoxesOfQuiz' * * @property integer $id * @property integer $state_id * @property string $name * @method static \Illuminate\Database\Query\Builder|\App\models\City whereBoxId($value) */class City extends Model {    protected $connection = 'mysql2';    protected $table = 'cities';    public $timestamps = false;    public static function whereId($target) {        return City::find($target);    }    public function state() {        return $this->belongsTo('App\models\State', 'state_id', 'id');    }}