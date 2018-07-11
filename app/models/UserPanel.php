<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'UserPanel'
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @method static \Illuminate\Database\Query\Builder|\App\models\UserPanel whereUsername($value)
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @mixin \Eloquent
 */

class UserPanel extends Model {

    protected $connection = 'mysql2';
    protected $table = 'users';

    public function getRememberToken() {
        return $this->remember_token;
    }

    public function setRememberToken($value) {
        $this->remember_token = $value;
    }

    public function getRememberTokenName() {
        return 'remember_token';
    }

    public static function whereId($value) {
        return UserPanel::find($value);
    }
}
