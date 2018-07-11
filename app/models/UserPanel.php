<?php

class UserPanel extends Eloquent {

    protected $connection = 'mysql2';
    protected $table = 'users';
    public $timestamps = false;
}