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
		$proxy = Input::get('proxy');
		$type = Input::get('type') ? Input::get('type') : 'solo'; // default solo
		// generate original URL with all GET params in case there is a failure
		// and we need to redirect back to it
		$originalUrl = URL::action('UserController@getRegister', array(
			'type' => $type,
			'tournament' => $tournament_id,
			'division' => $division_id,
			'team' => $team_id,
			'proxy' => ($proxy == 1)
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

		// if logged in, this is a new team or an individual registration;
		// if additional, this is someone paying "8th player fee" for their current team
		if (Auth::check()) {

			$email = Auth::user()->email;

			if ($proxy) {
				$teammateFirstName = Input::get('first_name');
				$teammateLastName = Input::get('last_name');
				$teammateEmail = Input::get('email');
			}
			else {
				$teammateFirstName = Auth::user()->first_name;
				$teammateLastName = Auth::user()->last_name;
				$teammateEmail = Auth::user()->email;
			}

		} else {

			// fetch email from form provided
			$email = Input::get('email') ? Input::get('email') :
					Input::get('stripeEmail');
			$teammateEmail = $email;

			// check if email already exists - if so, show prompt
			// to log in first
			$rules = array('email' => 'unique:users,email');
			$validator = Validator::make(array('email' => $email), $rules);
			if ($validator->fails()) {

				// try logging in with password
				if (Auth::attempt(array(
							'email' => Input::get('email'),
							'password' => Input::get('password')))) {

					$teammateFirstName = Auth::user()->first_name;
					$teammateLastName = Auth::user()->last_name;

				} else {
					if (Cookie::get('stripeToken')) {
						$errorMessage = 'Invalid email or password';

						return Redirect::to($originalUrl)
										->withInput()
										->withErrors($errorMessage);
					} else {
						$errorMessage = 'You already have an SBVBC account. '
								. 'Please log in to submit your payment';

						$minutes = 60 * 24; // last for a day
						return Redirect::to($originalUrl)
										->withInput()
										->withCookie(Cookie::make('email', $email, $minutes))
										->withCookie(Cookie::make('stripeToken', Input::get('stripeToken'), $minutes))
										->withErrors($errorMessage);
					}
				}
			}

			if (!Auth::check()) {

				// set random password if none provided
				$password = Input::get('password') ? Input::get('password') :
						$password = $this->getRandomPassword();

				$userParams = [
					'first_name' => Input::get('first_name'),
					'last_name' => Input::get('last_name'),
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

				$teammateFirstName = $userParams['first_name'];
				$teammateLastName = $userParams['last_name'];
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

		// if this is an additional player, associate to the given team
		if ($type == 'additional') {

			if ($proxy) {
				$team->registerUserByProxy($tournament, $division, $teammateFirstName, $teammateLastName,
						$teammateEmail, $this->getRandomPassword());
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

		Session::flash('success', $output);
		return Redirect::to('/')->withCookie($cookie);
	}

}
