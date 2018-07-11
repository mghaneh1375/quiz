<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

//Route::get('alaki', 'HomeController@filterQuestions');


use Illuminate\Support\Facades\Route;

Route::post('getQuizLessons', array('as' => 'getQuizLessons', 'uses' => 'ajaxController@getQuizLessons'));

Route::group(array('before' => 'auth|levelController:1'), function () {

    Route::any('createTarazTable', 'TarazController@createTarazTable');

    Route::any('deleteTarazTable', 'TarazController@deleteTarazTable');
});

Route::group(array('before' => 'auth|levelController:1'), function () {

    Route::any('defineKarname', 'KarnameController@defineKarname');
});

Route::group(array('before' => 'auth|levelController:1'), function () {

    Route::post('getLessonsByChangingDegree', 'ajaxController@getLessons');

    Route::post('getLessonsByChangingDegreeWithSelectedLesson', 'ajaxController@getLessonsWithSelected');

    Route::post('getSubjectsByChangingLesson', 'ajaxController@getSubjects');

    Route::post('addNewBox', 'ajaxController@addNewBox');

    Route::post('updateBox', 'ajaxController@updateBox');

    Route::post('changeQuiz', 'ajaxController@changeQuiz');

    Route::post('getBoxItems', 'ajaxController@getBoxItems');

    Route::post('getBoxItemsByNames', 'ajaxController@getBoxItemsByNames');

    Route::post('deleteBox', 'ajaxController@deleteBox');

});

Route::group(array('before' => 'auth|levelController:1'), function () {

    Route::get('createBox', 'BoxController@createBox');

    Route::any('seeBoxes', 'BoxController@seeBoxes');
});

Route::group(array('before' => 'auth|levelController:1'), function (){

    Route::get('logout', 'HomeController@logout');

    Route::any('createQuiz', 'QuizController@createQuiz');

    Route::any('quizStatus', 'QuizController@quizStatus');

    Route::any('addDegreeToQuiz={quizId}', 'QuizController@addDegreeToQuiz');

    Route::any('addQuestionToQuiz={quizId}', array('as' => 'addQuestionToQuiz', 'uses' => 'QuizController@addQuestionToQuiz'));

    Route::any('quizes', 'QuizController@showQuizes');

    Route::any('editQuiz={quizId}', 'QuizController@editQuiz');
});

Route::group(array('before' => 'auth'), function (){

    Route::get('/', array('as' => 'home', 'uses' => 'HomeController@showHome'));

    Route::get('home','HomeController@showHome');

    Route::any('seeResult','KarnameController@seeResult');

    Route::get('seeResult/{quizId}', array('as' => 'seeResult', 'uses' => 'KarnameController@seeResult'));

    Route::any('doQuiz', 'QuizController@doQuiz');

    Route::post('submitAns', 'ajaxController@submitAns');

    Route::post('endQuiz', 'ajaxController@endQuiz');

    Route::post('getCompasses', 'ajaxController@getCompasses');

    Route::post('getTotalQ', 'ajaxController@getTotalQ');

    Route::post('getEnherafMeyar', 'ajaxController@getEnherafMeyar');

    Route::post('calcTaraz', 'ajaxController@calcTaraz');

    Route::post('fillSubjectsPercentTable', 'ajaxController@fillSubjectsPercentTable');

    Route::post('getQuizStates', array('as' => 'getQuizStates', 'uses' => 'ajaxController@getQuizStates'));

    Route::post('getQuizCities', array('as' => 'getQuizCities', 'uses' => 'ajaxController@getQuizCities'));
});

Route::group(array('before' => 'auth'), function (){

    Route::get('reports/{quizId}', array('as' => 'reports', 'uses' => 'ReportController@reports'));

    Route::get('A5/{quizId}', array('as' => 'A5', 'uses' => 'ReportController@A5'));

    Route::get('A5Excel/{quizId}', array('as' => 'A5Excel', 'uses' => 'ReportController@A5Excel'));

    Route::get('A1/{quizId}', array('as' => 'A1', 'uses' => 'ReportController@A1'));

    Route::get('A1Excel/{quizId}', array('as' => 'A1Excel', 'uses' => 'ReportController@A1Excel'));

    Route::get('A2/{quizId}', array('as' => 'A2', 'uses' => 'ReportController@A2'));

    Route::get('A2Excel/{quizId}', array('as' => 'A2Excel', 'uses' => 'ReportController@A2Excel'));

    Route::get('A3/{quizId}', array('as' => 'preA3', 'uses' => 'ReportController@preA3'));

    Route::get('A3/{quizId}/{uId}/{backURL?}', array('as' => 'A3', 'uses' => 'ReportController@A3'));

    Route::get('A7/{quizId}', array('as' => 'A7', 'uses' => 'ReportController@A7'));

    Route::get('A7Excel/{quizId}', array('as' => 'A7Excel', 'uses' => 'ReportController@A7Excel'));

    Route::get('A4/{quizId}', array('as' => 'A4', 'uses' => 'ReportController@A4'));

    Route::get('A4Excel/{quizId}', array('as' => 'A4Excel', 'uses' => 'ReportController@A4Excel'));

    Route::get('A6/{quizId}', array('as' => 'A6', 'uses' => 'ReportController@A6'));

    Route::get('A6Excel/{quizId}', array('as' => 'A6Excel', 'uses' => 'ReportController@A6Excel'));



    Route::get('showReport', array('as' => 'showReport', 'uses' => 'ReportController@showReport'));

    Route::get('questionAnalysis/{quizId}/{lessonId}', array('as' => 'questionAnalysis', 'uses' => 'ReportController@questionAnalysis'));

    Route::get('questionDiagramAnalysis/{quizId}/{lessonId}', array('as' => 'questionDiagramAnalysis', 'uses' => 'ReportController@questionDiagramAnalysis'));

    Route::get('report1/{quizId}/{stateId}', array('as' => 'report1', 'uses' => 'ReportController@report1'));

    Route::get('report2/{quizId}/{stateId}', array('as' => 'report2', 'uses' => 'ReportController@report2'));

    Route::get('report3/{quizId}/{stateId}', array('as' => 'report3', 'uses' => 'ReportController@report3'));

});

Route::get('login','HomeController@login');

Route::post('login', 'HomeController@doLogin');

Route::post('deleteQuiz', 'HomeController@deleteQuiz');