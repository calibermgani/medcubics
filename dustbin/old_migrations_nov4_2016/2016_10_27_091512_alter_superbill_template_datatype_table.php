<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSuperbillTemplateDatatypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('superbill_template', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `superbill_template` CHANGE `provider_id` `provider_id` BIGINT UNSIGNED NOT NULL;");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('superbill_template', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `superbill_template` CHANGE `provider_id` `provider_id` INT NOT NULL;");
		});
	}

}
