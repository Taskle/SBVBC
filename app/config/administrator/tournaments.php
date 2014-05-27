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
		'num_members' => array(
			'title' => '# participants',
			'relationship' => 'users',
			'select' => 'COUNT((:table).id)',
		),
		'num_teams' => array(
			'title' => '# teams',
			'relationship' => 'teams',
			'select' => 'COUNT((:table).id)',
		),
		'num_members' => array(
			'title' => '# divisions',
			'relationship' => 'divisions',
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
		'description' => array(
			'title' => 'Description',
			'type' => 'markdown',
			'limit' => 4096,
			'height' => 350
		),
	),

);
