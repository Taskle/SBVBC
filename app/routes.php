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

Route::get('/', function() {
	
	if (Auth::check()) {
		return View::make('home');
	}
	else {
		return View::make('index');
	}
});

Route::get('login', function() {
	
	if (Auth::check()) {
		return Redirect::intended('/');
	}
	
	$user = new User;
	return View::make('login')->with('user', $user);	
});

Route::post('login', function() {

	$rules = array(
		'email' => 'Required',
		'password' => 'Required',
	);

    $v = Validator::make(Input::all(), $rules);

	if ($v->passes()) {

		// look up user via email
		if (Auth::attempt(array(
			'email' => Input::get('email'), 
			'password' => Input::get('password')))) {
			
			return Redirect::intended('/login');
		}
		else {
			return Redirect::to('/login')->withErrors('Invalid email or password');
		}
				
	} else {
		
		return Redirect::to('/login')->withErrors($v->messages());
	}
});

Route::get('logout', function() {
	Auth::logout();
	return Redirect::intended('/');
});

Route::get('register', function() {
	
	if (Auth::check()) {
		return Redirect::intended('/');
	}
	
	$user = new User;
	return View::make('register')->with('user', $user);
});

Route::post('register', function() {
	$user = new User;
	$user->fill($_POST);

	$v = User::validate(Input::all());

	if ($v->passes()) {

		User::create(array(
			'name' => Input::get('name'),
			'email' => Input::get('email'),
			'password' => Hash::make(Input::get('password')),
		));

		return 'Thanks for registering!';
		
	} else {
		return Redirect::to('/register')->withErrors($v->messages());
	}
});

Route::get('users', function() {
	$users = User::all();

	return View::make('users')->with('users', $users);
});
