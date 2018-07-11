<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;


/**
 * An Eloquent Model: 'Field'
 *
 * @property integer $id
 * @property integer $grade_id
 * @property string $name
 * @method static \Illuminate\Database\Query\Builder|\App\models\Field whereGradeId($value)
 */


class Field extends Model {

    protected $connection = 'mysql2';
    protected $table = 'fields';
    public $timestamps = false;

    public static function whereId($target) {
        return Field::find($target);
    }

}