<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDivisionTeamTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('division_team', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('division_id')->unsigned()->index();
			$table->foreign('division_id')->references('id')->on('divisions')->onDelete('cascade');
			$table->integer('team_id')->unsigned()->index();
			$table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('division_team');
	}

}
