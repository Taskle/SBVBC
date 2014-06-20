<?php

/**
 * Tournaments model config
 */

return array(
	'title' => 'Tournaments',
	'single' => 'tournament',
	'model' => 'Tournament',

	/**
	 * The display columns
	 */
	'columns' => array(
		'id',
		'name' => array(
			'title' => 'Name',
			'sort_field' => 'name',
		),
		'date' => array(
			'title' => 'Date/Time',
		),
		'registration_deadline' => array(
			'title' => 'Registration Deadline',
		),
		'num_members' => array(
			'title' => 'Participants',
			'relationship' => 'users',
			'select' => 'COUNT((:table).id)',
		),
		'num_teams' => array(
			'title' => 'Teams',
			'relationship' => 'teams',
			'select' => 'COUNT((:table).id)',
		),
	),

	/**
	 * The filter set
	 */
	'filters' => array(
		'id',
		'name' => array(
			'title' => 'Name',
		),
		'date' => array(
			'title' => 'Date/Time',
		),
		'registration_deadline' => array(
			'title' => 'Registration Deadline',
		),
	),

	/**
	 * The editable fields
	 */
	'edit_fields' => array(
		'name' => array(
			'title' => 'Name',
			'type' => 'text',
		),
		'date' => array(
			'title' => 'Date/Time',
			'type' => 'datetime',
		),
		'registration_deadline' => array(
			'title' => 'Registration Deadline',
			'type' => 'datetime',
		),
		'description' => array(
			'title' => 'Description',
			'type' => 'markdown',
			'limit' => 4096,
			'height' => 350
		),
		'schedule_url' => array(
			'title' => 'Schedule',
			'type' => 'file',
			'location' => sys_get_temp_dir() . '/',
			'naming' => 'keep',
			'length' => 127,
			'size_limit' => 2,
		),
	),

);
