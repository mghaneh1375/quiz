<?php

class BoxItems extends Eloquent {

    protected $table = "boxItems";
    public $timestamps = false;

    public function box() {
        return $this->belongsTo('Box', 'boxId', 'id');
    }

}