<?php

class TournamentController extends BaseController {

	/**
	 * Exports CSV summary of information about the given tournament
	 *
	 * @return Response
	 */
	public function getExportTournamentCSV($tournamentId) {

		if (!Auth::check() || Auth::user()->role != 'Admin') {
			return Redirect::to('/');
		}

		$tournament = Tournament::find($tournamentId);

		$data = [['First name', 'Last name', 'Email', 'Rating', 'Division', 'Team',
		'Paid', 'Signature']];

		$paymentStatus = $this->getPaymentsByEmail($tournament);

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

		$this->download_send_headers(str_replace(' ', '-', $tournament->name) . '-' .
				date("Y-m-d") . ".csv");
		echo $this->array2csv($data);
		die();
	}

	private function array2csv(array &$array) {
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

	private function download_send_headers($filename) {
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

	/**
	 * Gets information about the given tournament
	 *
	 * @return Response
	 */
	public function getTournament($tournamentId) {

		$tournament = Tournament::find($tournamentId);
		return View::make('index')->with('tournament', $tournament);
	}

}
