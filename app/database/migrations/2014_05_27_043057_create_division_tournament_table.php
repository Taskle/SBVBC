<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDivisionTournamentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('division_tournament', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('division_id')->unsigned()->index();
			$table->foreign('division_id')->references('id')->on('divisions')->onDelete('cascade');
			$table->integer('tournament_id')->unsigned()->index();
			$table->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('cascade');
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
		Schema::drop('division_tournament');
	}

}
