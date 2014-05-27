<?php

class Division extends Eloquent {

	protected $table = 'divisions';
    protected $fillable = array('name');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier() {
		return $this->getKey();
	}
	
    public function users()
    {
        return $this->hasMany('User');
    }

	public function teams() {
		return $this->belongsToMany('Team');
	}

	public function tournaments() {
		return $this->belongsToMany('Tournament');
	}

}
