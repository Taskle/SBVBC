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
			$table->string('description', 512);
			$table->string('cost_description', 512);
			$table->string('ratings_allowed')->nullable(); // comma-separated list
			$table->tinyInteger('team_size')->default(6);
			$table->tinyInteger('min_teams')->default(0);
			$table->tinyInteger('max_teams')->nullable();
			$table->tinyInteger('min_team_members')->default(0);
			$table->tinyInteger('max_team_members')->nullable();
			$table->integer('solo_price')->nullable();
			$table->integer('team_price')->nullable();
			$table->integer('additional_team_member_price')->nullable();
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
