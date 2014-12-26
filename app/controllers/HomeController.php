<?php

class HomeController extends BaseController {

	/**
	 * Loads homepage
	 *
	 * @return Response
	 */
	public function getHome() {

		$upcomingTournament = Tournament::getUpcoming();

		if (Auth::check()) { // && Auth::user()->role != 'Admin') {
			if ($upcomingTournament) {
				$context = array(
					'tournament' => $upcomingTournament,
					'myDivision' => Auth::user()->getDivision($upcomingTournament->id),
					'myTeam' => Auth::user()->getTeam($upcomingTournament->id),
				);
			} else {
				$context = array(
					'tournament' => null,
					'myDivision' => null,
					'myTeam' => null,
				);
			}

			if (Auth::user()->role == 'Admin') {
				$context['paymentStatus'] = $this->getPaymentsByEmail($upcomingTournament);
			}

			return View::make('home')->with($context);
		} else {

			// get most recent tournament in database
			return View::make('index')->with('tournament', $upcomingTournament);
		}
	}

}
