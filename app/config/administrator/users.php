<?php

/**
 * Users model config
 */

return array(
	'title' => 'Users',
	'single' => 'user',
	'model' => 'User',

	/**
	 * The display columns
	 */
	'columns' => array(
		'id',
		'name' => array(
			'title' => 'Name',
			'select' => "CONCAT((:table).first_name, ' ', (:table).last_name)",
		),
		'email' => array(
			'title' => 'Email',
			'sort_field' => 'email',
		),
		'rating' => array(
			'title' => 'Rating',
			'sort_field' => 'rating',
		),
		'num_teams' => array(
			'title' => '# teams',
			'relationship' => 'teams',
			'select' => 'COUNT((:table).id)',
		),
		'num_tournaments' => array(
			'title' => '# tourneys',
			'relationship' => 'tournaments',
			'select' => 'COUNT((:table).id)',
		),
	),

	/**
	 * The filter set
	 */
	'filters' => array(
		'id',
		'first_name' => array(
			'title' => 'First Name',
		),
		'last_name' => array(
			'title' => 'Last Name',
		),
		'rating' => array(
			'title' => 'Rating',
		),
		'teams' => array(
			'title' => 'Teams',
			'type' => 'relationship',
			'name_field' => 'name',
		),
		'tournaments' => array(
			'title' => 'Tournaments',
			'type' => 'relationship',
			'name_field' => 'name',
		),
	),

	/**
	 * The editable fields
	 */
	'edit_fields' => array(
		'first_name' => array(
			'title' => 'First Name',
			'type' => 'text',
		),
		'last_name' => array(
			'title' => 'Last Name',
			'type' => 'text',
		),
		'rating' => array(
			'title' => 'Rating',
			'type' => 'enum',
			'options' => array('Unrated', 'AA', 'A', 'BB', 'B'),
		),
		'teams' => array(
			'title' => 'Teams',
			'type' => 'relationship',
			'name_field' => 'name',
		),
		'tournaments' => array(
			'title' => 'Tournaments',
			'type' => 'relationship',
			'name_field' => 'name',
		),
		'role' => array(
			'title' => 'Website Role',
			'type' => 'enum',
			'options' => array('Normal', 'Admin'),
		),
	),

);
