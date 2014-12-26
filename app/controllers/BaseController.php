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
		
		// NOTE: we can't get more than 100 charges at a time
		// so we have to page through them
		$chargeParams = array('limit' => 100);
		
		// if tournament param is provided, only count
		// charges associated with this tournament, as determined
		// by the payment date occurred after the prev tourney but 
		// before this one
		if ($tournament) {
			
			$tourneyDate = strtotime($tournament->date);
			if ($tourneyDate < time()) {
				$chargeParams['created[lte]'] = $tourneyDate;
			}
			
			$prevTournament = $tournament->getPreviousTournament();
			if ($prevTournament) {
				$chargeParams['created[gte]'] = strtotime($prevTournament->date);
			}
		}
		
		// get charges from stripe based on params above (note:
		// if no param provided, all charges are pulled, capped at the limit
		// provided
		$charges = Stripe_Charge::all($chargeParams);

		$payments = array();
		$numCharges = count($charges->data);
		
		// go through all pages of data from stripe
		while ($numCharges > 0) {
			
			$charge = null;
			for ($i = 0; $i < $numCharges; $i++) {

				$charge = $charges->data[$i];

				// note: $charge->card->name is actually the person's email address
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
			
			// go through next set of charges
			$chargeParams['starting_after'] = $charge->id;
			$charges = Stripe_Charge::all($chargeParams);
			$numCharges = count($charges->data);
		}

		return $payments;
	}

}
