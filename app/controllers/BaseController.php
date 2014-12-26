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
	 * @return array
	 */
	protected function getPaymentsByEmail() {

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

}
