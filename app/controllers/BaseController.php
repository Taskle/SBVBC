<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout() {
		if (!is_null($this->layout)) {
			$this->layout = View::make($this->layout);
		}
	}

	/**
	 * Generates a random 16-digit password
	 * 
	 * @return string
	 */
	protected function getRandomPassword() {
		return str_random(16);
	}

	/**
	 * Gets an array of objects with payment data based on a user's email
	 * address
	 * 
	 * @param Tournament if set, only provide payments matching that tournament
	 * 
	 * @return array
	 */
	protected function getPaymentsByEmail($tournament = null) {

		// look up payment status for every user
		Stripe::setApiKey(Config::get('app.stripe.api_key'));
		$charges = Stripe_Charge::all(array('limit' => 100));

		$prevTournamentDate = 0;
		if ($tournament) {
			$prevTournament = $tournament->getPreviousTournament();
			if ($prevTournament) {
				$prevTournamentDate = strtotime($prevTournament->date);
			}
		}

		$payments = array();
		$numCharges = count($charges->data);
		for ($i = 0; $i < $numCharges; $i++) {

			$charge = $charges->data[$i];

			// note: $charge->card->name is actually the person's email address
			if ($charge->paid && $charge->card->name) {

				// if tournament param is provided, only count
				// charges associated with this tournament, as determined
				// by the payment date occurred after the prev tourney but 
				// before this one
				if ($tournament) {

					// if name not in description and charge was not between
					// last tourney and this one
					if ($charge->created < $prevTournamentDate ||
						$charge->created > strtotime($tournament->date)) {

						// this charge isn't for this tournament, so ignore it
						continue;
					}
				}

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

}
