<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDivisionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('divisions', function($table) {
			$table->timestamps();
			$table->increments('id');
			$table->string('name');
			$table->string('ratings_allowed')->nullable(); // comma-separated list
			$table->tinyInteger('min_teams')->default(0);
			$table->tinyInteger('max_teams')->nullable();
			$table->tinyInteger('min_team_members')->default(0);
			$table->tinyInteger('max_team_members')->nullable();
			$table->boolean('allow_team_registration')->default(true);
			$table->boolean('allow_solo_registration')->default(true);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('divisions');
	}

}
