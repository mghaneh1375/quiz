<?phpnamespace App\models;use Illuminate\Database\Eloquent\Model;use Illuminate\Support\Facades\DB;/** * An Eloquent Model: 'MQT' * * @property integer $id * @property integer $q_id * @property string $question * @method static \Illuminate\Database\Query\Builder|\App\models\MQT whereQId($value) */class MQT extends Model {    protected $table = 'mqt';    public $timestamps = false;    public static function whereId($target) {        return MQT::find($target);    }}