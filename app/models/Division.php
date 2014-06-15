<?php

class Division extends Eloquent {

	protected $table = 'divisions';
	protected $guarded = array('id');

	private function prettyFormatDollarAmount($amt) {
			if (($amt * 100) % 100 == 0) {
				return number_format($amt, 0);
			}
			else {
				return $amt;
			}
	}

	/** accessor for using in admin views, etc.
	 * 
	 * @return string
	 */
	public function getFormattedSoloPriceAttribute() {
                return $this->prettyFormatDollarAmount($this->solo_price);
	}

	/** accessor for using in admin views, etc.
	 * 
	 * @return string
	 */
	public function getFormattedTeamPriceAttribute() {
                return $this->prettyFormatDollarAmount($this->team_price);
	}

	/** accessor for using in admin views, etc.
	 * 
	 * @return string
	 */
	public function getFormattedAdditionalTeamMemberPriceAttribute() {
                return $this->prettyFormatDollarAmount(
                        $this->additional_team_member_price);
	}

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier() {
		return $this->getKey();
	}

	public function users() {
		return $this->belongsToMany('User');
	}

	public function teams() {
		return $this->belongsToMany('Team');
	}

	public function tournaments() {
		return $this->belongsToMany('Tournament');
	}
}
