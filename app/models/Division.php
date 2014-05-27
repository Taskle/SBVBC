<?php

class Division extends Eloquent {

	protected $table = 'divisions';
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

	public function teams() {
		return $this->belongsToMany('Team');
	}

	public function tournaments() {
		return $this->belongsToMany('Tournament');
	}

}
