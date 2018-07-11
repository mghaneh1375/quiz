<?php

namespace App\models;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Auth\UserTrait;
//use Illuminate\Auth\UserInterface;
//use Illuminate\Auth\Reminders\RemindableTrait;
//use Illuminate\Auth\Reminders\RemindableInterface;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

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

class UserPanel extends Authenticatable{

    use Notifiable;

    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */


    protected $table = 'users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    protected $fillable = [
        'name', 'password'
    ];

    protected $hidden = array('password', 'remember_token');

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function getAuthIdentifier() {
        return $this->getKey();
    }
    public function getAuthPassword() {
        return $this->password;
    }

    public static function whereId($value) {
        return UserPanel::find($value);
    }
}
