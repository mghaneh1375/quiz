<?phpnamespace App\models;use Illuminate\Database\Eloquent\Model;use Illuminate\Support\Facades\DB;/** * An Eloquent Model: 'MQT' * * @property integer $id * @property integer $q_id * @property string $solution * @method static \Illuminate\Database\Query\Builder|\App\models\MST whereQId($value) */class MST extends Model {    protected $table = 'mst';    public $timestamps = false;    public static function whereId($target) {        return MST::find($target);    }}