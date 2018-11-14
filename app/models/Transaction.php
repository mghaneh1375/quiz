<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'Transaction'
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $price
 * @property integer $additional_id
 * @property integer $ref_id
 * @method static \Illuminate\Database\Query\Builder|\App\models\Transaction whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\Transaction whereRefId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\Transaction whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\Transaction whereAdditionalId($value)
 */

class Transaction extends Model {

    protected $table = 'gateway_transactions';
    public $timestamps = false;

    public static function whereId($target) {
        return Transaction::find($target);
    }
}
