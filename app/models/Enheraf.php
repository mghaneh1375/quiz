<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;


/**
 * An Eloquent Model: 'Enheraf'
 *
 * @property integer $id
 * @property integer $l_id
 * @property integer $q_id
 * @property integer $val
 * @property float $lessonAVG
 * @method static \Illuminate\Database\Query\Builder|\App\models\Enheraf whereLId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\Enheraf whereQId($value)
 */


class Enheraf extends Model {

    protected $table = 'enheraf';
    public $timestamps = false;
    
}