<?phpnamespace App\models;use Illuminate\Database\Eloquent\Model;/** * An Eloquent Model: 'CompassesPercent' * * @property integer $id * @property integer $q_id * @property integer $u_id * @property integer $c_id * @property float $percent * @method static \Illuminate\Database\Query\Builder|\App\models\CompassesPercent whereQId($value) * @method static \Illuminate\Database\Query\Builder|\App\models\CompassesPercent whereUId($value) * @method static \Illuminate\Database\Query\Builder|\App\models\CompassesPercent whereCId($value) */class CompassesPercent extends Model {    protected $table = 'compasses_percent';    public $timestamps = false;}