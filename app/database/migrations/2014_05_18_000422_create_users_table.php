<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('users', function($table) {
			$table->timestamps();
			$table->increments('id');
			$table->string('email')->unique();
			$table->string('password');
			$table->string('remember_token', 100)->nullable();
			$table->string('first_name');
			$table->string('last_name');
			$table->enum('role',
				array('Normal', 'Admin'))->default('Normal');
			$table->enum('rating',
				array('Unrated', 'AA', 'A', 'BB', 'B'))->default('Unrated');
			$table->string('stripe_id')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('users');
	}

}
