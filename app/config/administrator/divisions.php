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
		'num_teams' => array(
			'title' => '# teams',
			'relationship' => 'teams',
			'select' => "COUNT((:table).id)",
		),
		'max_teams' => array(
			'title' => 'Maximum number of teams',
			'type' => 'number',
		),
		'allow_team_registration' => array(
			'title' => 'Team reg?',
			'sort_field' => 'allow_team_registration'
		),
		'allow_solo_registration' => array(
			'title' => 'Solo reg?',
			'sort_field' => 'allow_solo_registration'
		)
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
		'tournaments' => array(
			'title' => 'Tournaments',
			'type' => 'relationship',
			'name_field' => 'name',
		),
		'ratings_allowed' => array(
			'title' => 'Ratings allowed (comma-separated)',
			'type' => 'text',
		),
		'team_size' => array(
			'title' => 'On-court team size',
			'type' => 'number',
		),
		'min_teams' => array(
			'title' => 'Minimum number of teams',
			'type' => 'number',
		),
		'max_teams' => array(
			'title' => 'Maximum number of teams',
			'type' => 'number',
		),
		'min_team_members' => array(
			'title' => 'Minimum team size',
			'type' => 'number',
		),
		'max_team_members' => array(
			'title' => 'Maximum team size',
			'type' => 'number',
		),
		'solo_price' => array(
			'title' => 'Price per individual',
                        'symbol' => '$',
                        'decimals' => 2,
			'type' => 'number',
		),
		'team_price' => array(
			'title' => 'Price per team',
                        'symbol' => '$',
                        'decimals' => 2,
			'type' => 'number',
		),
		'additional_team_member_price' => array(
			'title' => 'Team price for each additional person',
                        'symbol' => '$',
                        'decimals' => 2,
			'type' => 'number',
		),
		'allow_team_registration' => array(
			'title' => 'Allow team registration?',
			'type' => 'bool',
		),
		'allow_solo_registration' => array(
			'title' => 'Allow solo registration?',
			'type' => 'bool',
		),		
		'description' => array(
			'title' => 'General description',
			'type' => 'markdown',
			'limit' => 512,
			'height' => 350
		),
	),

);
