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
		
        $user = User::create(array(
			'first_name' => 'David',
			'last_name' => 'Murray',
			'email' => 'david@sbvbc.org',
			'password' => Hash::make('testtest'),
		));
		$user->role = 'Admin';
		$user->save();
		
        $user2 = User::create(array(
			'first_name' => 'John',
			'last_name' => 'Lam',
			'email' => 'john@sbvbc.org',
			'password' => Hash::make('testtest'),
		));
		$user2->role = 'Admin';
		$user2->save();
		
        $user3 = User::create(array(
			'first_name' => 'Jim',
			'last_name' => 'Moore',
			'email' => 'jim@sbvbc.org',
			'password' => Hash::make('testtest'),
		));
		$user3->role = 'Admin';
		$user3->save();
		
        $user4 = User::create(array(
			'first_name' => 'Mark',
			'last_name' => 'Ware',
			'email' => 'mark@sbvbc.org',
			'password' => Hash::make('testtest'),
		));
		$user4->role = 'Admin';
		$user4->save();
		
        $user5 = User::create(array(
			'first_name' => 'Roland',
			'last_name' => 'Tabaday',
			'email' => 'roland@sbvbc.org',
			'password' => Hash::make('testtest'),
		));
		$user5->role = 'Admin';
		$user5->save();
		
        $user6 = User::create(array(
			'first_name' => 'Kevin',
			'last_name' => 'Yamashiro',
			'email' => 'kevin@sbvbc.org',
			'password' => Hash::make('testtest'),
		));
		$user6->role = 'Admin';
		$user6->save();
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
			'name' => 'SBVBC Open Grass Tournament',
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
			'name' => 'B/BB',
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
