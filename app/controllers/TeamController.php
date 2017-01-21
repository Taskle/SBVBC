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
			if (Auth::user() != null && $teammate != null &&
				$teammate->id == Auth::user()->id) {
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

			$team->registerUserByProxy($tournament, $division, $first_name, $last_name, $email,
					$this->getRandomPassword());
		}

		// go back to home on success
		return Redirect::to('/');
	}

}
