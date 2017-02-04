<?php

class Team extends Eloquent {

	protected $table = 'teams';
	protected $guarded = array('id');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier() {
		return $this->getKey();
	}

	public function users() {
		return $this->belongsToMany('User')->withTimestamps();
	}

	public function division() {
		return $this->belongsTo('Division');
	}

	public function tournament() {
		return $this->division()->tournament();
	}

	/**
	 * Attempts to register the user with the given team
	 * when the user is NOT the currently logged in user
	 *
	 * @return boolean
	 */
	public function registerUserByProxy($tournament, $division, $firstName,
			$lastName, $email, $password, $forceLogoutAfterCompletion) {

		$team = $this;

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

				if ($forceLogoutAfterCompletion) {
					Auth::logout();
				}

				// if registered as individual they may not be on a team
				if ($existingTeam) {
					return Redirect::to('/')->withErrors($user->getFullName() .
						' is already playing in this tournament on team "' .
						$existingTeam->name . '"');
				}
				else {
					return Redirect::to('/')->withErrors($user->getFullName() .
						" is already registered for this tournament. " .
						"Please email contact@sbvbc.org to change this person's team.");
				}
			}

			$emailData['isNewUser'] = false;
		} else {

			// create new user
			$user = new User;

			$userParams = [
				'first_name' => $firstName,
				'last_name' => $lastName,
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
		Mail::send(array('emails.welcome-html', 'emails.welcome-text'), $emailData, function($message) use ($email, $user, $subject) {

			$message->from('contact@sbvbc.org', 'SBVBC');
			$message->to($email, $user->getFullName())->subject($subject);
		});

		if ($forceLogoutAfterCompletion) {
			Auth::logout();
		}

		Session::flash('success', 'You have added ' .
				$user->full_name . ' to your team! We have sent this person a confirmation email.');
	}
}
