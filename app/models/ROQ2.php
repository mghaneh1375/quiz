<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'ROQ2'
 *
 * @property integer $id
 * @property integer $u_id
 * @property integer $quiz_id
 * @property integer $result
 * @method static \Illuminate\Database\Query\Builder|\App\models\ROQ2 whereUId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\ROQ2 whereQuizId($value)
 */

class ROQ2 extends Model {

    protected $table = 'roq2';

    public static function whereId($target) {
        return ROQ2::find($target);
    }

}