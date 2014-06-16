<?php

class Tournament extends Eloquent {

	protected $table = 'tournaments';
	protected $guarded = array('id');

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
	
	/** 
	 * Gets the next upcoming tournament based on date
	 */
	public static function getUpcoming() {		
		return Tournament::where('date', '>', time())->get()->sortBy(function($t) {
			return $t->date;
		})->first();
	}

}
