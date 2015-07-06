<?php

class TeamController extends BaseController {

	/**
	 * Updates teammate information
	 *
	 * @return Response
	 */
	public function postUpdateTeammate() {

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
		} else {

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

					return Redirect::to('/')->withErrors($user->getFullName() .
									' is already playing in this tournament on team "' .
									$existingTeam->name . '"');
				}

				$emailData['isNewUser'] = false;
			} else {

				// create new user
				$user = new User;

				$password = $this->getRandomPassword();

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
			Mail::send(array('emails.welcome-html', 'emails.welcome-text'), $emailData, function($message) use ($email, $user, $subject) {

				$message->from('contact@sbvbc.org', 'SBVBC');
				$message->to($email, $user->getFullName())->subject($subject);
			});

			Session::flash('success', 'You have added ' .
					$user->full_name . ' to your team! We have sent this person a confirmation email.');
		}

		// go back to home on success
		return Redirect::to('/');
	}

}
