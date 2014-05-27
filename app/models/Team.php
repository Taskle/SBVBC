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
		return $this->belongsToMany('User');
	}

	public function divisions() {
		return $this->belongsToMany('Division');
	}

	public function tournaments() {
		return $this->belongsToMany('Tournament');
	}

}
