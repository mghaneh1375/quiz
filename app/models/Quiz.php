<?php

class Quiz extends Eloquent {

    protected $table = "quiz";
    public $timestamps = false;

    public function degree() {
        return $this->belongsToMany('Degree', 'degreeOfQuiz', 'quizId', 'degreeId');
    }

    public function boxes() {
        return $this->belongsToMany('Box', 'boxesOfQuiz', 'quizId', 'boxId');
    }
}