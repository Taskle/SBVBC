<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFileToTournamentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tournaments', function(Blueprint $table)
		{
			DB::statement('ALTER TABLE tournaments ADD COLUMN schedule_url varchar(255) DEFAULT NULL');
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
			DB::statement('ALTER TABLE tournaments DROP COLUMN schedule_url');
		});
	}

}
