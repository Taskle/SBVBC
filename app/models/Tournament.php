<?php

class Tournament extends Eloquent {

	protected $table = 'tournaments';
	protected $guarded = array('id');

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
}
