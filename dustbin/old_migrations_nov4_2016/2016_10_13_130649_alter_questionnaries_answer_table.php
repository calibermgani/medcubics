<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterQuestionnariesAnswerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('questionnaries_answer', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `questionnaries_answer` CHANGE `questionnaries_option_id` `questionnaries_option_id` TEXT NOT NULL");
			$table->bigInteger('patient_id')->after('id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('questionnaries_answer', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `questionnaries_answer` CHANGE `questionnaries_option_id` `questionnaries_option_id` BIGINT NOT NULL");
			$table->dropColumn('patient_id');
		});
	}

}
