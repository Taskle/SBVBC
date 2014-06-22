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
		'division' => array(
			'type' => 'relationship',
			'title' => 'Division',
			'relationship' => 'division',
			'select' => '(:table).name',
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
		'users' => array(
			'title' => 'Members',
			'type' => 'relationship',
			'name_field' => 'full_name',
			'options_sort_field' => "CONCAT(first_name, ' ' , last_name)",
		),
		'division' => array(
			'title' => 'Division',
			'type' => 'relationship',
			'name_field' => 'long_name',
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
		'division' => array(
			'title' => 'Division',
			'type' => 'relationship',
			'name_field' => 'long_name',
		),
		'users' => array(
			'title' => 'Members',
			'type' => 'relationship',
			'name_field' => 'full_name',
			'options_sort_field' => "CONCAT(first_name, ' ' , last_name)",
		),
	),

);
