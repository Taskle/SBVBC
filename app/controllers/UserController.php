<?php

class UserController extends BaseController {

	/**
	 * Shows the user login form
	 *
	 * @return Response
	 */
	public function getLogin() {
		if (Auth::check()) {
			return Redirect::to('/');
		}

		$user = new User;
		return View::make('login')->with('user', $user);
	}

	/**
	 * Logs the user in
	 *
	 * @return Response
	 */
	public function postLogin() {

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
	}

	/**
	 * Logs the user out
	 *
	 * @return Response
	 */
	public function getLogout() {
		Auth::logout();
		return Redirect::intended('/');
	}

	/**
	 * Shows the user registration form
	 *
	 * @return Response
	 */
	public function getRegister() {
		if (Auth::check()) {
			$user = Auth::user();
		} else {
			$user = new User;
		}

		// populate data based on GET parameters or, if this was a failed
		// post, include the params in the original POST to regenerate the same
		// page that failed
		$tournament_id = Input::get('tournament', Input::old('tournament_id'));
		$division_id = Input::get('division', Input::old('division_id'));
		$team_id = Input::get('team', Input::old('team_id'));
		$type = Input::get('type', Input::old('type'));
		$proxy = Input::get('proxy', Input::old('proxy'));

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
			$team = Team::find($team_id);
		} else {
			$team = null;
		}

		// if no division, this serves as a "create user" form,
		// but if logged in, just redirect to homepage
		if (!$division_id && Auth::check()) {
			return Redirect::to('/');
		}

		// if this is an "additional player" request, there
		// must be teams, so if no teams, show error
		if ($type == 'additional' && ($division == null ||
				$division->teams == null || $division->teams->count() == 0)) {
			$errorMessage = 'Please register your team first before adding additional players.';
			return Redirect::to('/')
				->withErrors($errorMessage);
		}

		return View::make('register')
						->with('user', $user)
						->with('team', $team)
						->with('tournament', $tournament)
						->with('division', $division)
						->with('type', $type)
						->with('proxy', $proxy);
	}

	/**
	 * Registers the user
	 *
	 * @return Response
	 */
	public function postRegister() {

		// set params passed in
		$tournament_id = Input::get('tournament_id');
		$division_id = Input::get('division_id');
		$team_id = Input::get('team_id');
		$proxy = Input::get('proxy'); // registering additional player who is NOT you (the current user)
		$type = Input::get('type') ? Input::get('type') : 'solo'; // default solo
		$firstName = Input::get('first_name');
		$lastName = Input::get('last_name');
		// fetch email from stripe form if provided there instead
		$email = Input::get('email') ? Input::get('email') : Input::get('stripeEmail');
		$teammateFirstName = Input::get('teammate_first_name');
		$teammateLastName = Input::get('teammate_last_name');
		$teammateEmail = Input::get('teammate_email');

		// generate original URL with all GET params in case there is a failure
		// and we need to redirect back to it
		$originalUrl = URL::action('UserController@getRegister', array(
			'type' => $type,
			'tournament' => $tournament_id,
			'division' => $division_id,
			'team' => $team_id,
			'proxy' => ($proxy == 1),
			'first_name' => $firstName,
			'last_name' => $lastName,
			'email' => $email,
			'teammate_first_name' => $teammateFirstName,
			'teammate_last_name' => $teammateLastName,
			'teammate_email_name' => $teammateEmail,
		));

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
			$team = Team::find($team_id);
		} else {
			$team = null;
		}

		// if email and teammate email are the same,
		// this isn't a proxy so disable that flag
		if ($proxy && $email == $teammateEmail) {
			$proxy = false;
		}

		if ($proxy) {

			// set first name and email for user themselves
			// in case this isn't provided above
			if (!$firstName) {
				$firstName = Input::get('teammate_first_name');
			}
			if (!$lastName) {
				$lastName = Input::get('teammate_last_name');
			}
		}

		$forceLogoutAfterCompletion = false;

		// if logged in, this is a new team or an individual registration;
		// if additional, this is someone paying "8th player fee" for their current team
		if (Auth::check()) {

			$email = Auth::user()->email;

			if (!$proxy) {
				$teammateFirstName = Auth::user()->first_name;
				$teammateLastName = Auth::user()->last_name;
				$teammateEmail = Auth::user()->email;
			}

		} else {

			$password = Input::get('password');

			if (!$proxy) {
				$teammateEmail = $email;
			}

			// check if email already exists - if so, accept
			// payment on behalf of the email inputted w/o pwd (we
			// can trust the user as there is no benefit to someone
			// putting in the wrong email) but DO NOT log
			// them in (for security)
			$rules = array('email' => 'unique:users,email');
			$validator = Validator::make(array('email' => $email), $rules);
			if ($validator->fails()) {

				// try logging in with password; if that doesn't work,
				// log in without just to accept payment and associate
				// team info, but then log them out immediately after
				// for security
				if (!Auth::attempt(array(
						'email' => $email,
						'password' => $password))) {

					$user = User::where('email', $email) -> first();

					// log in without password but force logout
					// upon completion for security
					Auth::login($user);
					$forceLogoutAfterCompletion = true;
				}

				if (!$proxy) {
					$teammateFirstName = Auth::user()->first_name;
					$teammateLastName = Auth::user()->last_name;
				}
			}

			// if this is a new user
			if (!Auth::check()) {

				// set random password if none provided
				if (!$password) {
					$password = $this->getRandomPassword();
				}

				$userParams = [
					'first_name' => $firstName,
					'last_name' => $lastName,
					'email' => $email,
					'password' => $password
				];

				$v = User::validate($userParams);

				if (!$v->passes()) {
					return Redirect::to($originalUrl)
									->withInput()
									->withErrors($v->messages());
				}

				$userParams['password'] = Hash::make($password);

				if (!$proxy) {
					$teammateFirstName = $firstName;
					$teammateLastName = $lastName;
				}
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
		if (isset($teammateFirstName) && isset($teammateLastName)) {
			$description .= ' - ' . $teammateFirstName . ' ' . $teammateLastName;
		}
		if (isset($team) && isset($team->name)) {
			$description .= ' - ' . $team->name;
		}

		// get price in cents for sending to stripe
		$amountInDollars = 0;

		if ($type == 'team') {
			$amountInDollars = $division->team_price;
		}
		elseif ($type == 'additional') {
			$amountInDollars = $division->additional_team_member_price;
		}
		else {
			$amountInDollars = $division->solo_price;
		}

		$amountInCents = $amountInDollars * 100;

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
					"description" => $teammateFirstName . ' ' . $teammateLastName
				));
			}

			// Charge the Customer instead of the card
			$charge = Stripe_Charge::create(array(
				"amount" => $amountInCents,
				"currency" => "usd",
				"customer" => $customer->id,
				"description" => $description
			));

		} catch (Stripe_Error $e) {

			$e_json = $e->getJsonBody();
			$error = $e_json['error'];

			// The card has been declined
			// redirect back to checkout page
			Session::flash('error', $error['message']);

			// remove cookie since token is now used
			$cookie = Cookie::forget('stripeToken');

			if ($forceLogoutAfterCompletion) {
				Auth::logout();
			}

			return Redirect::to($originalUrl)
							->withInput()
							->withCookie($cookie);
		}

		// remove cookie since token is now used
		$cookie = Cookie::forget('stripeToken');

		// create and log in user if not logged in
		if (Auth::check()) {
			$isNewUser = false;
			$user = Auth::user();

		} else {
			$isNewUser = true;
			$user = User::create($userParams);
			Auth::attempt([
				'email' => $email,
				'password' => $password
			]);
		}

		// save user's stripe info if legit login or
		// no stripe id set before
		if ($user->stripe_id == null ||
			!$forceLogoutAfterCompletion) {
			$user->stripe_id = $customer->id;
		}
		$user->save();

		// if this is you
		if (!$proxy) {

			// ensure not already playing in this tournament
			if ($user->isRegisteredForTournament($tournament)) {

				$existingTeam = $user->getTeam($tournament->id);

				if ($forceLogoutAfterCompletion) {
					Auth::logout();
				}

				// if registered as individual they may not be on a team
				if ($existingTeam) {
					return Redirect::to('/')->withErrors(
						'You are already playing in this tournament on team "' .
						$existingTeam->name . '"; please email contact@sbvbc.org to make any changes.');
				}
				else {
					return Redirect::to('/')->withErrors(
						"You are already registered for this tournament. " .
						"Please email contact@sbvbc.org to change your team or make other changes.");
				}
			}
			else {

				// associate user with division if not proxy
				$user->divisions()->save($division);
			}
		}

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

		// if this is an additional player, associate to the given team
		if ($type == 'additional') {

			if ($proxy) {
				$team->registerUserByProxy($tournament, $division, $teammateFirstName, $teammateLastName,
						$teammateEmail, $this->getRandomPassword(), $forceLogoutAfterCompletion);
			}
			else {
				// associate team to user
				$user->teams()->save($team);
			}
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

		$emailData['proxy'] = $proxy;
		$emailData['playerName'] = $teammateFirstName . ' ' . $teammateLastName;

		if (isset($team)) {
			$emailData['team'] = $team;
		}

		$subject = 'SBVBC registration confirmed';
		if (isset($tournament) && isset($tournament->name)) {
			$subject .= ' for ' . $tournament->name;
		}

		// send email confirmation to user
		Mail::send(array('emails.welcome-html', 'emails.welcome-text'), $emailData,
				function($message) use ($email, $user, $subject) {

			$message->from('contact@sbvbc.org', 'SBVBC');
			$message->to($email, $user->getFullName())->subject($subject);
		});

		// add tournament and division to stripe description if they're set

		if ($proxy) {
			$output = $emailData['playerName'] . ' is now registered';
		}
		else {
			$output = 'You are now registered';
		}

		if ($tournament && isset($tournament->name)) {
			$output .= ' for ' . $tournament->name;
		}
		if ($division && isset($division->name)) {
			$output .= ' (' . $division->name . ')';
		}
		$output .= '!';

		if ($forceLogoutAfterCompletion) {
			Auth::logout();
		}

		Session::flash('success', $output);
		return Redirect::to('/')->withCookie($cookie);
	}

}
