<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTournamentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tournaments', function(Blueprint $table)
		{
			DB::statement('ALTER TABLE tournaments ADD COLUMN registration_deadline DATETIME DEFAULT NULL');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tournaments', function(Blueprint $table)
		{
			DB::statement('ALTER TABLE tournaments DROP COLUMN registration_deadline');
		});
	}

}
