<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DivisionTournamentsPartOfDivisionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('divisions', function(Blueprint $table)
		{
			DB::statement('ALTER TABLE divisions ADD tournament_id int(10) unsigned NOT NULL AFTER id');
			DB::statement('UPDATE divisions SET tournament_id = (SELECT tournament_id FROM division_tournament WHERE division_tournament.division_id = divisions.id)');
			DB::statement('ALTER TABLE divisions ADD CONSTRAINT `tournament_id_foreign` FOREIGN KEY (tournament_id) REFERENCES tournaments (id) ON DELETE CASCADE');
			DB::statement('ALTER TABLE teams ADD division_id int(10) unsigned NOT NULL AFTER id');
			DB::statement('UPDATE teams SET division_id = (SELECT division_id FROM division_team WHERE division_team.team_id = teams.id)');
			DB::statement('ALTER TABLE teams ADD CONSTRAINT `division_id_foreign` FOREIGN KEY (division_id) REFERENCES divisions (id) ON DELETE CASCADE');
			DB::statement('DROP TABLE division_tournament');
			DB::statement('DROP TABLE team_tournament');
			DB::statement('DROP TABLE tournament_user');
			DB::statement('DROP TABLE division_team');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('divisions', function(Blueprint $table)
		{			
			Schema::create('tournament_user', function(Blueprint $table2) {
				$table2->increments('id');
				$table2->integer('tournament_id')->unsigned()->index();
				$table2->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('cascade');
				$table2->integer('user_id')->unsigned()->index();
				$table2->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
				$table2->timestamps();
			});
			
			Schema::create('division_tournament', function(Blueprint $table2) {
				$table2->increments('id');
				$table2->integer('division_id')->unsigned()->index();
				$table2->foreign('division_id')->references('id')->on('divisions')->onDelete('cascade');
				$table2->integer('tournament_id')->unsigned()->index();
				$table2->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('cascade');
				$table2->timestamps();
			});
			
			Schema::create('team_tournament', function(Blueprint $table2) {
				$table2->increments('id');
				$table2->integer('team_id')->unsigned()->index();
				$table2->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
				$table2->integer('tournament_id')->unsigned()->index();
				$table2->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('cascade');
				$table2->timestamps();
			});

			Schema::create('division_team', function(Blueprint $table2) {
				$table2->increments('id');
				$table2->integer('division_id')->unsigned()->index();
				$table2->foreign('division_id')->references('id')->on('divisions')->onDelete('cascade');
				$table2->integer('team_id')->unsigned()->index();
				$table2->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
				$table2->timestamps();
			});
			
			DB::statement('INSERT INTO division_team (team_id, division_id) SELECT teams.id, teams.division_id FROM teams');
			DB::statement('INSERT INTO team_tournament (team_id, tournament_id) SELECT teams.id, divisions.tournament_id FROM teams, divisions, division_team WHERE teams.id = division_team.team_id AND divisions.id = division_team.division_id;');
			DB::statement('INSERT INTO division_tournament (tournament_id, division_id) SELECT divisions.tournament_id, divisions.id FROM divisions');
			DB::statement('INSERT INTO tournament_user (tournament_id, user_id) SELECT division_tournament.tournament_id, division_user.user_id FROM division_user, division_tournament WHERE division_user.division_id = division_tournament.division_id');
			DB::statement('ALTER TABLE teams DROP FOREIGN KEY division_id_foreign');
			DB::statement('ALTER TABLE teams DROP division_id');
			DB::statement('ALTER TABLE divisions DROP FOREIGN KEY tournament_id_foreign');
			DB::statement('ALTER TABLE divisions DROP tournament_id');
		});
	}

}
