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

Route::post('getQuizLessons', array('as' => 'getQuizLessons', 'uses' => 'AjaxController@getQuizLessons'));

Route::group(array('before' => 'auth|levelController:1'), function () {

    Route::any('createTarazTable', 'TarazController@createTarazTable');

    Route::any('deleteTarazTable', 'TarazController@deleteTarazTable');
});

Route::group(array('before' => 'auth|levelController:1'), function () {

    Route::any('defineKarname', 'KarnameController@defineKarname');
});

Route::group(array('before' => 'auth|levelController:1'), function () {

    Route::post('getLessonsByChangingDegree', 'AjaxController@getLessons');

    Route::post('getLessonsByChangingDegreeWithSelectedLesson', 'AjaxController@getLessonsWithSelected');

    Route::post('getSubjectsByChangingLesson', 'AjaxController@getSubjects');

    Route::post('addNewBox', 'AjaxController@addNewBox');

    Route::post('updateBox', 'AjaxController@updateBox');

    Route::post('changeQuiz', 'AjaxController@changeQuiz');

    Route::post('getBoxItems', 'AjaxController@getBoxItems');

    Route::post('getBoxItemsByNames', 'AjaxController@getBoxItemsByNames');

    Route::post('deleteBox', 'AjaxController@deleteBox');

});

Route::group(array('before' => 'auth|levelController:1'), function () {

    Route::get('createBox', 'BoxController@createBox');

    Route::any('seeBoxes', 'BoxController@seeBoxes');
});

Route::group(array('before' => 'auth|levelController:1'), function (){

    Route::get('logout', 'HomeController@logout');

    Route::any('createQuiz', 'QuizController@createQuiz');

    Route::any('quizStatus', 'QuizController@quizStatus');

    Route::any('addDegreeToQuiz={quiz_id}', 'QuizController@addDegreeToQuiz');

    Route::any('addQuestionToQuiz={quiz_id}', array('as' => 'addQuestionToQuiz', 'uses' => 'QuizController@addQuestionToQuiz'));

    Route::any('quizes', 'QuizController@showQuizes');

    Route::any('editQuiz={quiz_id}', 'QuizController@editQuiz');
});

Route::group(array('before' => 'auth'), function (){

    Route::get('/', array('as' => 'home', 'uses' => 'HomeController@showHome'));

    Route::get('home','HomeController@showHome');

    Route::any('seeResult','KarnameController@seeResult');

    Route::get('seeResult/{quiz_id}', array('as' => 'seeResult', 'uses' => 'KarnameController@seeResult'));

    Route::any('doQuiz', 'QuizController@doQuiz');

    Route::post('submitAns', 'AjaxController@submitAns');

    Route::post('endQuiz', 'AjaxController@endQuiz');

    Route::post('getCompasses', 'AjaxController@getCompasses');

    Route::post('getTotalQ', 'AjaxController@getTotalQ');

    Route::post('getEnherafMeyar', 'AjaxController@getEnherafMeyar');

    Route::post('calcTaraz', 'AjaxController@calcTaraz');

    Route::post('fillSubjectsPercentTable', 'AjaxController@fillSubjectsPercentTable');

    Route::post('getQuizStates', array('as' => 'getQuizStates', 'uses' => 'AjaxController@getQuizStates'));

    Route::post('getQuizCities', array('as' => 'getQuizCities', 'uses' => 'AjaxController@getQuizCities'));
});

Route::group(array('before' => 'auth'), function (){

    Route::get('reports/{quiz_id}', array('as' => 'reports', 'uses' => 'ReportController@reports'));

    Route::get('A5/{quiz_id}', array('as' => 'A5', 'uses' => 'ReportController@A5'));

    Route::get('A5Excel/{quiz_id}', array('as' => 'A5Excel', 'uses' => 'ReportController@A5Excel'));

    Route::get('A1/{quiz_id}', array('as' => 'A1', 'uses' => 'ReportController@A1'));

    Route::get('A1Excel/{quiz_id}', array('as' => 'A1Excel', 'uses' => 'ReportController@A1Excel'));

    Route::get('A2/{quiz_id}', array('as' => 'A2', 'uses' => 'ReportController@A2'));

    Route::get('A2Excel/{quiz_id}', array('as' => 'A2Excel', 'uses' => 'ReportController@A2Excel'));

    Route::get('A3/{quiz_id}', array('as' => 'preA3', 'uses' => 'ReportController@preA3'));

    Route::get('A3/{quiz_id}/{uId}/{backURL?}', array('as' => 'A3', 'uses' => 'ReportController@A3'));

    Route::get('A7/{quiz_id}', array('as' => 'A7', 'uses' => 'ReportController@A7'));

    Route::get('A7Excel/{quiz_id}', array('as' => 'A7Excel', 'uses' => 'ReportController@A7Excel'));

    Route::get('A4/{quiz_id}', array('as' => 'A4', 'uses' => 'ReportController@A4'));

    Route::get('A4Excel/{quiz_id}', array('as' => 'A4Excel', 'uses' => 'ReportController@A4Excel'));

    Route::get('A6/{quiz_id}', array('as' => 'A6', 'uses' => 'ReportController@A6'));

    Route::get('A6Excel/{quiz_id}', array('as' => 'A6Excel', 'uses' => 'ReportController@A6Excel'));



    Route::get('showReport', array('as' => 'showReport', 'uses' => 'ReportController@showReport'));

    Route::get('questionAnalysis/{quiz_id}/{lessonId}', array('as' => 'questionAnalysis', 'uses' => 'ReportController@questionAnalysis'));

    Route::get('questionDiagramAnalysis/{quiz_id}/{lessonId}', array('as' => 'questionDiagramAnalysis', 'uses' => 'ReportController@questionDiagramAnalysis'));

    Route::get('report1/{quiz_id}/{stateId}', array('as' => 'report1', 'uses' => 'ReportController@report1'));

    Route::get('report2/{quiz_id}/{stateId}', array('as' => 'report2', 'uses' => 'ReportController@report2'));

    Route::get('report3/{quiz_id}/{stateId}', array('as' => 'report3', 'uses' => 'ReportController@report3'));

});

Route::get('login','HomeController@login');

Route::post('login', 'HomeController@doLogin');

Route::post('deleteQuiz', 'HomeController@deleteQuiz');