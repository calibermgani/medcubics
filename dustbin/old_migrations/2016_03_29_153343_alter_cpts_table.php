<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCptsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cpts', function(Blueprint $table)
		{
			DB::statement('ALTER TABLE cpts MODIFY COLUMN  allowed_amount DECIMAL(15,2)');
			DB::statement('ALTER TABLE cpts MODIFY COLUMN  billed_amount DECIMAL(15,2)');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cpts', function(Blueprint $table)
		{
			//DB::statement('ALTER TABLE cpts MODIFY COLUMN  allowed_amount VARCHAR(100)');
			//DB::statement('ALTER TABLE cpts MODIFY COLUMN  billed_amount VARCHAR(100)');
		});
	}

}
