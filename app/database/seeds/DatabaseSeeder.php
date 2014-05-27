<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->call('UserTableSeeder');
        $this->command->info('User table seeded!');
		$this->call('TournamentTableSeeder');
        $this->command->info('Tournament table seeded!');
		$this->call('DivisionTableSeeder');
        $this->command->info('Divison table seeded!');
		$this->call('TeamTableSeeder');
        $this->command->info('Team table seeded!');
	}

}

class UserTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
        DB::table('users')->delete();
		
		// Create admin users
        $user = User::create(array(
			'first_name' => 'David',
			'last_name' => 'Murray',
			'email' => 'david@sbvbc.org',
			'password' => Hash::make('testtest'),
		));
		$user->role = 'Admin';
		$user->save();
		
        $user = User::create(array(
			'first_name' => 'John',
			'last_name' => 'Lam',
			'email' => 'john@sbvbc.org',
			'password' => Hash::make('testtest'),
		));
		$user->role = 'Admin';
		$user->save();
		
        $user = User::create(array(
			'first_name' => 'Jim',
			'last_name' => 'Moore',
			'email' => 'jim@sbvbc.org',
			'password' => Hash::make('testtest'),
		));
		$user->role = 'Admin';
		$user->save();
		
        $user = User::create(array(
			'first_name' => 'Mark',
			'last_name' => 'Ware',
			'email' => 'mark@sbvbc.org',
			'password' => Hash::make('testtest'),
		));
		$user->role = 'Admin';
		$user->save();
		
        $user = User::create(array(
			'first_name' => 'Roland',
			'last_name' => 'Tabaday',
			'email' => 'roland@sbvbc.org',
			'password' => Hash::make('testtest'),
		));
		$user->role = 'Admin';
		$user->save();
		
        $user = User::create(array(
			'first_name' => 'Kevin',
			'last_name' => 'Yamashiro',
			'email' => 'kevin@sbvbc.org',
			'password' => Hash::make('testtest'),
		));
		$user->role = 'Admin';
		$user->save();
		
		// Create 10 test normal users
		for ($i = 1; $i <= 10; $i++) {
			$user = User::create(array(
				'first_name' => 'Test',
				'last_name' => $i,
				'email' => 'david+' . $i . '@sbvbc.org',
				'password' => Hash::make('testtest'),
			));
			$user->save();
		}
	}

}

class TournamentTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
        DB::table('tournaments')->delete();
        Tournament::create(array(
			'name' => '6/21 Open Grass Tournament',
			'description' => 'Get ready for our upcoming tournament coming up very soon! Details TBD',
			'date' => '2014-06-21T12:00:00'
		));
	}

}

class DivisionTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
        DB::table('divisions')->delete();
        Division::create(array(
			'name' => 'B/BB (6v6)',
			'team_size' => 6,
			'ratings_allowed' => 'B,BB',
			'description' => 'Individuals only (no pre-made teams)',
			'cost_description' => '$30 per individual',
			'solo_price' => 3000,
			'allow_team_registration' => false,
			'allow_solo_registration' => true
		));
        Division::create(array(
			'name' => 'BB/A (4v4)',
			'team_size' => 4,
			'ratings_allowed' => 'A,BB,B',
			'description' => 'Pre-made teams only',
			'cost_description' => '$200 for up to 6 people, $50 for each additional',
			'team_price' => 20000,
			'additional_team_member_price' => 5000,
			'allow_team_registration' => true,
			'allow_solo_registration' => false
		));
        Division::create(array(
			'name' => 'A/AA (2v2)',
			'team_size' => 2,
			'ratings_allowed' => 'A,AA,BB,B',
			'description' => 'Pre-made teams only',
			'cost_description' => '$100 for 2 people, $50 for each additional',
			'team_price' => 10000,
			'additional_team_member_price' => 5000,
			'allow_team_registration' => true,
			'allow_solo_registration' => false
		));
	}

}

class TeamTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
        DB::table('teams')->delete();
        Team::create(array(
			'name' => 'The first team to register',
		));
	}

}
