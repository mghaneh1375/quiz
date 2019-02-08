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
 * An Eloquent Model: 'User'
 *
 * @property integer $id
 * @property integer $city_id
 * @property integer $grade_id
 * @property integer $subscription
 * @property boolean $role
 * @property boolean $sex_id
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string $phone_num
 * @property string $home_phone
 * @property string $father_name
 * @property string $password
 * @method static \Illuminate\Database\Query\Builder|\App\models\User whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\User wherePhoneNum($value)
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @mixin \Eloquent
 */

class User extends Authenticatable{

	use Notifiable;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */


	protected $table = 'users_azmoon';

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
		return User::find($value);
	}
}
