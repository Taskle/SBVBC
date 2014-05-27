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
	return View::make('index');
});

Route::get('/coming-soon', function() {
	return View::make('coming-soon');
});

Route::get('login', function() {
	$user = new User;
	return View::make('login')->with('user', $user);
});

Route::post('login', function() {
	
	$user = new User;
	$user->fill($_POST);

	$rules = array(
		'email' => User::$rules['email'],
		'password' => User::$rules['password'],
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

Route::get('register', function() {
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
