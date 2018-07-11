<?php

class StudentPanel extends Eloquent {

    protected $connection = 'mysql2';
    protected $table = 'students';
    public $timestamps = false;
}