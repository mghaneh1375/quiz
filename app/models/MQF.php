<?phpnamespace App\models;use Illuminate\Database\Eloquent\Model;use Illuminate\Support\Facades\DB;/** * An Eloquent Model: 'MQF' * * @property integer $id * @property integer $q_id * @property string $question * @method static \Illuminate\Database\Query\Builder|\App\models\MQF whereQId($value) */class MQF extends Model {    protected $table = 'mqf';    public $timestamps = false;    public static function whereId($target) {        return MQF::find($target);    }}