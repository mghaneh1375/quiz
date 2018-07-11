<?php

class Box extends Eloquent {

    protected $table = 'box';
    public $timestamps = false;

    public function items() {
        return $this->hasMany('BoxItems', 'boxId', 'id');
    }

}