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
        User::create(array(
			'first_name' => 'David',
			'last_name' => 'Murray',
			'email' => 'david@sbvbc.org',
			'password' => Hash::make('testtest')
		));
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
