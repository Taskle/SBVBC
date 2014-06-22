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

}
