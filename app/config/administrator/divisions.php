<?php

/**
 * Divisions model config
 */

return array(
	'title' => 'Divisions',
	'single' => 'division',
	'model' => 'Division',

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
			'title' => '# teams',
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
	),

	/**
	 * The editable fields
	 */
	'edit_fields' => array(
		'name' => array(
			'title' => 'Name',
			'type' => 'text',
		),
	),

);
