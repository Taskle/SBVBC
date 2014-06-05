<?php

/**
 * Teams model config
 */

return array(
	'title' => 'Teams',
	'single' => 'team',
	'model' => 'Team',

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
			'title' => '# members',
			'relationship' => 'users',
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
		'divisions' => array(
			'title' => 'Divisions',
			'type' => 'relationship',
			'name_field' => 'name',
		),
		'tournaments' => array(
			'title' => 'Tournaments',
			'type' => 'relationship',
			'name_field' => 'name',
		),
		'users' => array(
			'title' => 'Members',
			'type' => 'relationship',
			'name_field' => 'full_name',
			'options_sort_field' => "CONCAT(first_name, ' ' , last_name)",
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
		'divisions' => array(
			'title' => 'Divisions',
			'type' => 'relationship',
			'name_field' => 'name',
		),
		'tournaments' => array(
			'title' => 'Tournaments',
			'type' => 'relationship',
			'name_field' => 'name',
		),
		'users' => array(
			'title' => 'Members',
			'type' => 'relationship',
			'name_field' => 'full_name',
			'options_sort_field' => "CONCAT(first_name, ' ' , last_name)",
		),
	),

);
