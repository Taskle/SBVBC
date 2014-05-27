<?php

class Tournament extends Eloquent {

	protected $table = 'tournaments';
    protected $fillable = array('name');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier() {
		return $this->getKey();
	}

	public function divisions() {
		return $this->belongsToMany('Division');
	}

	public function users() {
		return $this->belongsToMany('User');
	}

	public function teams() {
		return $this->belongsToMany('Team');
	}
}
