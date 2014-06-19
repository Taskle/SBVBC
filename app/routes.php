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

	if (Auth::check()) { // && Auth::user()->role != 'Admin') {

		$context = array(
			'myTournaments' => Auth::user()->tournaments()->get(),
			'myDivisions' => Auth::user()->divisions()->get(),
			'myTeams' => Auth::user()->teams()->get(),
		);
		
		if (Auth::user()->role == 'Admin') {
			$context['tournament'] = Tournament::getUpcoming();
			
			// look up payment status for every user
			Stripe::setApiKey(Config::get('app.stripe.api_key'));
			$context['paymentStatus'] = array();
			$users = User::all();
			
			$charges = Stripe_Charge::all(array('limit' => 100));
			
			$numCharges = count($charges->data);
			for ($i = 0; $i < $numCharges; $i++) {

				$charge = $charges->data[$i];
				
				if ($charge->card->paid && $charge->card->name) {
					
					// get total minus refund
					$amount = $charge->amount - $charge->amount_refunded;
										
					if (!isset($context['paymentStatus'][$charge->card->name])) {
						$context['paymentStatus'][$charge->card->name] = 0;
					}
					
					$context['paymentStatus'][$charge->card->name] += 
							($amount / 100.0);
				}
			}
		}
		
		return View::make('home')->with($context);
	} else {

		// get most recent tournament in database
		$tournament = Tournament::getUpcoming();
		return View::make('index')->with('tournament', $tournament);
	}
});

Route::get('/tournaments/{id}', function($tournamentId) {

	$tournament = Tournament::find($tournamentId);
	return View::make('index')->with('tournament', $tournament);
});

Route::get('login', function() {

	if (Auth::check()) {
		return Redirect::to('/');
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
		} else {
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

Route::controller('password', 'RemindersController');

Route::get('register', function() {

	if (Auth::check()) {
		$user = Auth::user();
	}
	else {
		$user = new User;
	}

	$tournament_id = Input::get('tournament');

	if ($tournament_id) {
		$tournament = Tournament::find($tournament_id);
	} else {
		$tournament = null;
	}

	$division_id = Input::get('division');

	if ($division_id) {
		$division = Division::find($division_id);
	} else {
		$division = null;
	}
	
	$team_id = Input::get('team');

	if ($team_id) {
		$team = Team::find($division_id);
	} else {
		$team = null;
	}

	return View::make('register')
					->with('user', $user)
					->with('team', $team)
					->with('tournament', $tournament)
					->with('division', $division)
					->with('type', Input::get('type'));
});

Route::post('register', function() {
	
	$tournament_id = Input::get('tournament_id');

	if ($tournament_id) {
		$tournament = Tournament::find($tournament_id);
	} else {
		$tournament = null;
	}

	$division_id = Input::get('division_id');

	if ($division_id) {
		$division = Division::find($division_id);
	} else {
		$division = null;
	}
	
	// default type to solo
	$type = Input::get('type') ? Input::get('type') : 'solo';
	
	if (Auth::check()) {
		$fullName = Auth::user()->full_name;
		$email = Auth::user()->email;
	}
	else {
		// fetch email from form provided
		$email = Input::get('email') ? Input::get('email') :
				Input::get('stripeEmail');

		// set random password if none provided
		$password = Input::get('password') ? Input::get('password') :
				$password = getRandomPassword();

		$userParams = [
			'first_name' => Input::get('first_name'),
			'last_name' => Input::get('last_name'),
			'email' => $email,
			'password' => $password
		];

		$v = User::validate($userParams);

		if (!$v->passes()) {
			return Redirect::to('/register')->withErrors($v->messages());
		}

		$userParams['password'] = Hash::make($password);
		$fullName = $userParams['first_name'] . ' ' . $userParams['last_name'];
	}

	Stripe::setApiKey(Config::get('app.stripe.api_key'));

	$stripeToken = Input::get('stripeToken');
	$description = $email;
	
	// add tournament and division to stripe description if they're set
	if ($tournament && isset($tournament->name)) {
		$description .= ' - ' . $tournament->name;
	}
	if ($division && isset($division->name)) {
		$description .= ' - ' . $division->name;
	}

	// get price in cents for sending to stripe
	$amount = $type == 'team' ?  ($division->team_price * 100) :
			($division->solo_price * 100);

	// Create the charge on Stripe's servers - this will charge the user's card
	try {
		
		if (Auth::check() && Auth::user()->stripe_id) {
			$customer = Stripe_Customer::retrieve(Auth::user()->stripe_id);
			
			// update card on customer to this one
			$customer->card = $stripeToken;
			$customer->save();
		}
		
		// if customer doesn't exist or wasn't retrieved, create a new one now
		if (!isset($customer) || !$customer) {
			$customer = Stripe_Customer::create(array(
				"card" => $stripeToken,
				"email" => $email,
				"description" => $fullName
			));
		}

		// Charge the Customer instead of the card
		$charge = Stripe_Charge::create(array(
			"amount" => $amount,
			"currency" => "usd",
			"customer" => $customer->id,
			"description" => $description
		));
	}
	catch (Stripe_Error $e) {

		$e_json = $e->getJsonBody();
		$error = $e_json['error'];
		
		// The card has been declined
		// redirect back to checkout page		
		Session::flash('error', $error['message']);
		
		return Redirect::to(URL::full())->withInput();
	}

	// create and log in user if not logged in
	if (Auth::check()) {
		$isNewUser = false;
		$user = Auth::user();
	}
	else {
		$isNewUser = true;
		$user = User::create($userParams);
		Auth::attempt([
			'email' => $email,
			'password' => $password
		]);
	}

	// save user's stripe info
	$user->stripe_id = $customer->id;
	$user->save();

	// associate with division and tournament
	$user->tournaments()->save($tournament);
	$user->divisions()->save($division);

	// if this is a group, create new team accordingly
	if ($type == 'team') {

		$teamName = Input::get('team_name');
		$team = Team::create([
			'name' => $teamName ? $teamName : $user->getFullName() . "'s team"
		]);

		$team->tournaments()->save($tournament);
		$team->divisions()->save($division);

		// associate team to user
		$user->teams()->save($team);
	}

	$emailData = array(
		'isNewUser' => $isNewUser
	);
	
	if ($isNewUser) {
		$emailData['email'] = $email;
		$emailData['password'] = $password;
	}
	
	$emailData['tournament'] = $tournament;
	$emailData['division'] = $division;

	if (isset($team)) {
		$emailData['team'] = $team;
	}

	$subject = 'SBVBC registration confirmed';
	if (isset($tournament) && isset($tournament->name)) {
		$subject .= ' for ' . $tournament->name;
	}

	// send email confirmation to user
	Mail::send(array('emails.welcome-html', 'emails.welcome-text'), 
		$emailData, function($message) use ($email, $user, $subject) {

		$message->from('contact@sbvbc.org', 'SBVBC');
		$message->to($email, $user->getFullName())->subject($subject);
	});

	// add tournament and division to stripe description if they're set
	$output = 'You are now registered';
	if ($tournament && isset($tournament->name)) {
		$output .= ' for ' . $tournament->name;
	}
	if ($division && isset($division->name)) {
		$output .= ' (' . $division->name . ')';
	}
	$output .= '!';
	
	Session::flash('success', $output);
	return Redirect::to('/');
});

function getRandomPassword() {
	return str_random(16);
}

Route::post('update-teammate', function() {
	
	$first_name = Input::get('first_name');
	$last_name = Input::get('last_name');
	$email = Input::get('email');
	$team_id = Input::get('team_id');
	$user_id = Input::get('user_id');
	
	if (!$first_name || !$last_name || !$email || !$team_id) {
		return Redirect::to('/')->withErrors('Name, email, and team ID required');
	}

	// look up team
	$team = Team::find($team_id);
	
	if (!$team) {
		return Redirect::to('/')->withErrors('Team ' . $team_id . ' not found');
	}
	
	// ensure current user is associated with this team
	$found = false;
	foreach ($team->users as $teammate) {
		if ($teammate->id == Auth::user()->id) {
			$found = true;
			break;
		}
	}
	
	if (!$found) {
		return Redirect::to('/')->withErrors('You are not associated with team #' 
				. $team_id);
	}
	
	if ($user_id) {
		// change name / email of the given user
		$found = false;
		foreach ($team->users as $teammate) {
			if ($teammate->id == $user_id) {
				$found = true;
				
				// update this user's info
				$teammate->first_name = $first_name;
				$teammate->last_name = $last_name;
				$teammate->email = $email;
				$teammate->save();
				break;
			}
		}
	
		if (!$found) {
			return Redirect::to('/')->withErrors(
					'That person was not found on your team');
		}
	}
	else {
		// create new user and associate with team
		$teammate = new User;
		
		$password = getRandomPassword();
		
		$userParams = [
			'first_name' => $first_name,
			'last_name' => $last_name,
			'email' => $email,
			'password' => $password
		];

		$v = User::validate($userParams);

		if ($v->passes()) {

			$userParams['password'] = Hash::make($password);

			// create user
			$user = User::create($userParams);
			
			$user->save();
		
			// associate with division, tournament, and team
			foreach ($team->tournaments as $tournament) {
				$user->tournaments()->save($tournament);
			}
			foreach ($team->divisions as $division) {
				$user->divisions()->save($division);
			}
			$user->teams()->save($team);
		
			$emailData = [];
			$emailData['email'] = $email;
			$emailData['password'] = $password;
			$emailData['tournament'] = isset($tournament) ? 
                                $tournament : '(no tournament assigned yet)';
			$emailData['division'] = isset($division) ? 
                                $division : '(no division assigned yet)';
			$emailData['team'] = $team;
			$emailData['team'] = isset($team) ? 
                                $team : '(no team assigned yet)';
			
			$subject = 'SBVBC registration confirmed';
			if (isset($tournament) && isset($tournament->name)) {
				$subject .= ' for ' . $tournament->name;
			}
			
			// send welcome email to user
			Mail::send(array('emails.welcome-html', 'emails.welcome-text'), 
				$emailData, function($message) use ($email, $user, $subject) {

				$message->from('contact@sbvbc.org', 'SBVBC');
				$message->to($email, $user->getFullName())->subject($subject);
			});
		}
		else {
			return Redirect::to('/')->withErrors($v->messages());
		}
	}
	
	// go back to home on success
	return Redirect::to('/');
});

Route::get('users', function() {
	$users = User::all();

	return View::make('users')->with('users', $users);
});
