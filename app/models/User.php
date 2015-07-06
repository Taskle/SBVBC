<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	protected $table = 'users';
	protected $hidden = array('password');
	protected $guarded = array('id', 'stripe_id');
	public static $rules = array(
		'first_name' => 'Required|Min:1',
		'last_name' => 'Required|Min:1',
		'email' => 'Required|Between:3,64|Email|Unique:users',
		'password' => 'AlphaNum|Between:4,32',
	);

	public static function validate($input) {
		return Validator::make($input, self::$rules);
	}

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier() {
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword() {
		return $this->password;
	}

	/**
	 * Get the token value for the "remember me" session.
	 *
	 * @return string
	 */
	public function getRememberToken() {
		return $this->remember_token;
	}

	/**
	 * Set the token value for the "remember me" session.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setRememberToken($value) {
		$this->remember_token = $value;
	}

	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName() {
		return 'remember_token';
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail() {
		return $this->email;
	}

	/** accessor for using in admin views, etc.
	 *
	 * @return string
	 */
	public function getFullNameAttribute() {
		return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
	}

	/**
	 * Gets team user is on for the given tournament
	 */
	public function getTeam($tournamentId) {
		return $this->teams->filter(function($team) use ($tournamentId) {
			return ($team->division->tournament->id == $tournamentId);
		})->first();
	}

	/**
	 * Gets team user is on for the given tournament
	 */
	public function getDivision($tournamentId) {

		return Division::join('division_user', 'division_id', '=',
				'divisions.id')
			->where('division_user.user_id', '=', $this->id)
			->where('divisions.tournament_id', '=', $tournamentId)
			->first();
	}

	/**
	 * Get the full name of the user
	 *
	 * @return string
	 */
	public function getFullName() {
		return $this->getFullNameAttribute();
	}

	public function divisions() {
		return $this->belongsToMany('Division')->withTimestamps();
	}

	public function teams() {
		return $this->belongsToMany('Team')->withTimestamps();
	}

	/**
	 * Returns true if user is registered for the given tournament,
	 * else false
	 *
	 * @param type $tournament
	 */
	public function isRegisteredForTournament($tournament) {

		$users = $tournament->getUsers();

		foreach ($users as $user) {
			if ($user->id == $this->id) {
				return true;
			}
		}

		return false;
	}

}
