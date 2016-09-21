<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use Hash;
//Temporary Disable it
Route::get('/', function () {
  	return "";
});

Route::auth();

//Temporary Disabled
	//Route::get('/home', 'HomeController@index');

//Evaluations Routes
Route::get('/evals/{company}/{uid}', 'EvalController@index');
Route::get('/eval/{company}/{sid}', 'EvalController@getEval');

  //Plan Routes
Route::get('/plan/{company}', 'PlanController@index');
Route::get('/plan/user-plan/{company}/{uid}', 'PlanController@plan');

//Insights Routes
Route::get('/insights/accumulative-details/{company}/{uid}', 'InsightsController@getAccumlativeDetails');

//Support Routes
Route::get('/support/{company}', 'SupportController@index');
Route::post('/support/{company}', 'SupportController@postRequest');

Route::get('/support/request/{company}/{id}', 'SupportController@getRequest');
Route::post('/support/request/{company}/{id}', 'SupportController@postMessage');

//List Routes
Route::get('/list/{company}', 'ListController@index');
Route::post('/list/{company}', 'ListController@postList');

Route::get('/support/api/{company}/{type}', 'SupportController@api');

Route::get('/home/tell-friend', function(){
  return view('others.tell_friend');
});

Route::post('/home/tell-friend', function(){
  return view('others.tell_friend');
});
Route::get('/hash', function(){
  return Hash::make("cs123");
});
