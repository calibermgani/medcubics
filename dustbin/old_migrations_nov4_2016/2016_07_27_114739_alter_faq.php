<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFaq extends Migration {

	public function up()
	{
		Schema::table('faqs', function($table)
		{
			$table->enum('category', array('Gentral','Product'))->after('status');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('faqs', function(Blueprint $table)
		{
			$table->dropColumn('category');
		});
	}

}
