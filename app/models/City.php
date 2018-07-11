<?php

class City extends Eloquent {

    protected $connection = 'mysql2';
    protected $table = 'cities';
    public $timestamps = false;

    public function state() {
        return $this->belongsTo('State', 'state_id', 'id');
    }
}