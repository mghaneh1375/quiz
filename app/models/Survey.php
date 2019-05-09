<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


/**
 * An Eloquent Model: 'Survey'
 *
 * @property integer $id
 * @property integer $u_id
 * @property integer $quiz_id
 * @property integer $result
 * @method static \Illuminate\Database\Query\Builder|\App\models\Survey whereUId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\Survey whereQuizId($value)
 */

class Survey extends Model {

    protected $table = 'survey';
    public $timestamps = false;

    public static function whereId($target) {
        return Survey::find($target);
    }

}