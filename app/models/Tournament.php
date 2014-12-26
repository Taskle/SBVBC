<?php

class Tournament extends Eloquent {

	protected $table = 'tournaments';
	protected $guarded = array('id');

	public function getYearAttribute() {
		return $this->date ? substr($this->date, 0, 4) : date('Y');
	}
	
	/**
	 * If filename is there, it was just uploaded to temp,
	 * so upload it to S3, the update the name
	 */
	public function save(array $options = array()) {
			
		if ($this->isDirty('schedule_url') && $this->schedule_url) {
			
			$localUri = sys_get_temp_dir() . '/' . $this->schedule_url;
			$newUrl = 'tournaments/' . $this->id . '/' . 
					$this->schedule_url;

			try {

				if (!$this->id) {

					// try saving to get id
					if (!parent::save($options)) {
						return false;
					}
				}

				// upload to S3
				$s3 = AWS::get('s3');
				$s3->putObject(array(
					'Bucket'     => Config::get('app.s3_bucket'),
					'Key'        => $newUrl,
					'SourceFile' => $localUri,
				));

			} catch (S3Exception $e) {

				$this->setErrors($e->getMessage());
				return false;
			}

			// set url to S3 url
			$this->schedule_url = $s3->getObjectUrl(
					Config::get('app.s3_bucket'), $newUrl);
		}
		
		parent::save($options);
	}
	
	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier() {
		return $this->getKey();
	}

	public function divisions() {
		return $this->hasMany('Division');
	}

	public function teams() {
        return $this->hasManyThrough('Team', 'Division', 'tournament_id', 
				'division_id');
	}

	public function getUsers() {
		
		$divisionIds = $this->divisions->map(function($division) {
			return $division->id;
		});
		
		if (count($divisionIds) > 0) {
			return User::join('division_user', 'users.id', '=', 
					'division_user.user_id')
				->whereIn('division_user.division_id', $divisionIds->toArray())
				->get(array('users.*'));
		}
		else {
			return array();
		}
	}
	
	/** 
	 * Gets the next upcoming tournament based on date
	 */
	public static function getUpcoming() {		
		return Tournament::where('date', '>', date('Y-m-d H:i:s'))->
				get()->sortBy('date')->first();
	}

	/**
	 * Checks deadline datetime and returns true or false depending on if
	 * registration is open or not
	 */
	public function isRegistrationOpen() {
		if ($this->registration_deadline) {

			// if current time is before deadline, return true, else aflse
			return (date('Y-m-d H:i:s') < $this->registration_deadline);
		}
		else {
			// no deadline means registration is always open
			return true;
		}
	}
	
	/**
	 * Returns object for a previous tournament, if one exists
	 * 
	 */
	public function getPreviousTournament() {
		
		// get all tournaments, ordered by date
		$tournaments = Tournament::all()->sortBy('date');
		
		// iterate through list until current tourney reached, then
		// get previous one on list
		foreach ($tournaments as $i => $tourney) {
			if ($tourney == $this) {
				
				// if first tournament, previous is null
				if ($i == 0) {
					return null;
				}
				
				return $tournaments[$i - 1];
			}
		}
		
		return null;
	}
}
