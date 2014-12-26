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

function getPaymentsByEmail() {
	
	// look up payment status for every user
	Stripe::setApiKey(Config::get('app.stripe.api_key'));
	$payments = array();
	$users = User::all();

	$charges = Stripe_Charge::all(array('limit' => 100));

	$numCharges = count($charges->data);
	for ($i = 0; $i < $numCharges; $i++) {

		$charge = $charges->data[$i];

		if ($charge->paid && $charge->card->name) {

			// get total minus refund
			$amount = $charge->amount - $charge->amount_refunded;

			if (!isset($payments[$charge->card->name])) {
				$payments[$charge->card->name] = 0;
			}

			$payments[$charge->card->name] += 
					($amount / 100.0);
		}
	}
	
	return $payments;
}

Route::get('/', function() {

	$upcomingTournament = Tournament::getUpcoming();
	
	if (Auth::check()) { // && Auth::user()->role != 'Admin') {

		if ($upcomingTournament) {
            		$context = array(
                		'tournament' => $upcomingTournament,
                		'myDivision' => Auth::user()->getDivision($upcomingTournament->id),
                		'myTeam' => Auth::user()->getTeam($upcomingTournament->id),
        		);
                }
                else {
            		$context = array(
                		'tournament' => null,
                		'myDivision' => null,
                		'myTeam' => null,
        		);
                }
		
		if (Auth::user()->role == 'Admin') {
			$context['paymentStatus'] = getPaymentsByEmail();
		}
		
		return View::make('home')->with($context);
	} else {

		// get most recent tournament in database
		return View::make('index')->with('tournament', $upcomingTournament);
	}
});

function array2csv(array &$array)
{
   if (count($array) == 0) {
     return null;
   }
   ob_start();
   $df = fopen("php://output", 'w');
   foreach ($array as $row) {
      fputcsv($df, $row);
   }
   fclose($df);
   return ob_get_clean();
}

function download_send_headers($filename) {
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}

Route::get('/export-tournament-csv/{id}', function($tournamentId) {
	
	if (!Auth::check() || Auth::user()->role != 'Admin') {
		return Redirect::to('/');
	}
	
	$tournament = Tournament::find($tournamentId);

	$data = [['First name', 'Last name', 'Email', 'Rating', 'Division', 'Team', 
		'Paid', 'Signature']];
	
	$paymentStatus = getPaymentsByEmail();
	
	foreach ($tournament->getUsers()->sortBy('full_name') as $user) {

		$division = $user->getDivision($tournamentId);
		$divisionName = $division ? $division->name : '';
		
		$team = $user->getTeam($tournament->id);
		$userArray = [$user->first_name, $user->last_name, $user->email,
			$user->rating, $divisionName];
		$userArray[] = $team ? $team->name : '';
		
		$userArray[] = array_key_exists($user->email, $paymentStatus) ? 
					'$' . $paymentStatus[$user->email] : '';
		
		$data[] = $userArray;
	}
	
	download_send_headers(str_replace(' ', '-', $tournament->name) . '-' . 
			date("Y-m-d") . ".csv");
	echo array2csv($data);
	die();
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

	// populate data based on GET parameters or, if this was a failed
	// post, include the params in the original POST to regenerate the same
	// page that failed
	$tournament_id = Input::get('tournament', Input::old('tournament_id'));
	$division_id = Input::get('division', Input::old('division_id'));
	$team_id = Input::get('team', Input::old('team_id'));
	$type = Input::get('type', Input::old('type'));

	if ($tournament_id) {
		$tournament = Tournament::find($tournament_id);
	} else {
		$tournament = null;
	}

	if ($division_id) {
		$division = Division::find($division_id);
	} else {
		$division = null;
	}
	
	if ($team_id) {
		$team = Team::find($division_id);
	} else {
		$team = null;
	}
	
	// if no division, this serves as a "create user" form,
	// but if logged in, just redirect to homepage
	if (!$division_id && Auth::check()) {
		return Redirect::to('/');
	}

	return View::make('register')
			->with('user', $user)
			->with('team', $team)
			->with('tournament', $tournament)
			->with('division', $division)
			->with('type', $type);
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

		// check if email already exists - if so, show prompt
		// to log in first
		$rules = array('email' => 'unique:users,email');
		$validator = Validator::make(array('email' => $email), $rules);
		if ($validator->fails()) {
			
			// try logging in with password
			if (Auth::attempt(array(
					'email' => Input::get('email'),
					'password' => Input::get('password')))) {
			
				$fullName = Auth::user()->full_name;
			}
			else {
				if (Cookie::get('stripeToken')) {
					$errorMessage = 'Invalid email or password';
					
					return Redirect::to(URL::full())
							->withInput()
							->withErrors($errorMessage);
				}
				else {
					$errorMessage = 'You already have an SBVBC account. '
								. 'Please log in to submit your payment';
				
					$minutes = 60 * 24; // last for a day
					return Redirect::to(URL::full())
							->withInput()
							->withCookie(Cookie::make('email', 
									$email, $minutes))
							->withCookie(Cookie::make('stripeToken', 
									Input::get('stripeToken'), $minutes))
							->withErrors($errorMessage);
				}
			}
		}
		
		if (!Auth::check()) {
			
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
				return Redirect::to(URL::full())
						->withInput()
						->withErrors($v->messages());
			}

			$userParams['password'] = Hash::make($password);
			$fullName = $userParams['first_name'] . ' ' . 
					$userParams['last_name'];
		}
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
	$amount = ($type == 'team') ? ($division->team_price * 100) :
			($division->solo_price * 100);

	// Create the charge on Stripe's servers - this will charge the user's card
	try {
		// Note: stripe_id's must start with cus_ - if it starts with
            	// 'ch_' it's a charge which shouldn't be what we save here
		if (Auth::check() && Auth::user()->stripe_id &&
                        strpos(Auth::user()->stripe_id, 'cus_') === 0) {
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
	
		// remove cookie since token is now used
		$cookie = Cookie::forget('stripeToken');
		
		return Redirect::to(URL::full())
				->withInput()
				->withCookie($cookie);
	}
	
	// remove cookie since token is now used
	$cookie = Cookie::forget('stripeToken');

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

	// associate with division
	$user->divisions()->save($division);

	// if this is a group, create new team accordingly
	if ($type == 'team') {

		$teamName = Input::get('team_name');
		$team = Team::create([
			'name' => $teamName ? $teamName : $user->getFullName() . "'s team",
			'division_id' => $division->id
		]);

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
	return Redirect::to('/')->withCookie($cookie);
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
		
	if (!$first_name || !$last_name || !$email) {
		return Redirect::to('/')->withErrors('Name and email are required');
	}
	
	if (!$team_id) {
		return Redirect::to('/')->withErrors('Team ID is required');
	}

	// look up team
	$team = Team::find($team_id);
	$division = $team->division;
	$tournament = $division->tournament;
	
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
		
		// initiate array for sending email to new teammate
		$emailData = [];
		
		// look up if user is existing via email address
		$user = User::where('email', $email)->first();
		//$user = (count($users) == 1) ? $users[0] : null;
		
		if ($user) {
						
			// ensure user isn't already associated with a team
			// in the given tournament, otherwise show error
			if ($user->isRegisteredForTournament($tournament)) {
				
				$existingTeam = $user->getTeam($tournament->id);
				
				return Redirect::to('/')->withErrors($user->full_name .
						' is already playing in this tournament on team "' .
						$existingTeam->name . '"');
			}
			
			$emailData['isNewUser'] = false;
		}
		else {
			
			// create new user
			$user = new User;

			$password = getRandomPassword();

			$userParams = [
				'first_name' => $first_name,
				'last_name' => $last_name,
				'email' => $email,
				'password' => $password
			];

			$v = User::validate($userParams);

			if (!$v->passes()) {
				return Redirect::to('/')->withErrors($v->messages());
			}

			$userParams['password'] = Hash::make($password);

			// create user
			$user = User::create($userParams);

			$user->save();
			
			$emailData['isNewUser'] = true;
			$emailData['password'] = $password;
		}

		// associate user with division and team
		$user->divisions()->save($division);
		$user->teams()->save($team);

		$emailData['email'] = $email;
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

		Session::flash('success', 'You have added ' . 
				$user->full_name . ' to your team! We have sent this person a confirmation email.');
	}
	
	// go back to home on success
	return Redirect::to('/');
});

Route::get('users', function() {
	$users = User::all();

	return View::make('users')->with('users', $users);
});
