<?php

class Field extends Eloquent {

    protected $connection = 'mysql2';
    protected $table = 'fields';
    public $timestamps = false;
}