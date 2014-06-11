<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDivisionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('divisions', function(Blueprint $table)
		{
			DB::statement('ALTER TABLE divisions MODIFY COLUMN solo_price DECIMAL(6, 2)');
			DB::statement('ALTER TABLE divisions MODIFY COLUMN team_price DECIMAL(6, 2)');
			DB::statement('ALTER TABLE divisions MODIFY COLUMN additional_team_member_price DECIMAL(6, 2)');
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
			DB::statement('ALTER TABLE divisions MODIFY COLUMN solo_price integer');
			DB::statement('ALTER TABLE divisions MODIFY COLUMN team_price integer');
			DB::statement('ALTER TABLE divisions MODIFY COLUMN additional_team_member_price integer');
		});
	}

}
