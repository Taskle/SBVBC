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



// home
Route::get('/', 'HomeController@getHome');

// users
Route::get('login', 'UserController@getLogin');
Route::post('login', 'UserController@postLogin');
Route::get('logout', 'UserController@getLogout');
Route::get('register', 'UserController@getRegister');
Route::post('register', 'UserController@postRegister');
Route::controller('password', 'RemindersController');

// tournaments
Route::get('/export-tournament-csv/{id}', 'TournamentController@getExportTournamentCSV');
Route::get('/tournaments/{id}', 'TournamentController@getTournament');

// teams
Route::post('update-teammate', 'TeamController@postUpdateTeammate');

// verify apply pay
Route::get('/.well-known/apple-developer-merchantid-domain-association',
			'HomeController@getApplePayVerificationFile');
