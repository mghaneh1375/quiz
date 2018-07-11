<?php

class QOQ extends Eloquent {

    protected $table = 'qoq';
    public $timestamps = false;

    public function question() {
        return $this->belongsTo('Question', 'questionId', 'id');
    }

    public function box() {
        return $this->belongsTo('BoxesOfQuiz', 'boxesOfQuizId', 'id');
    }
}