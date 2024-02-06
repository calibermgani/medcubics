<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToClaimdoscptdetails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('claimdoscptdetails', function(Blueprint $table)
		{
			$table->decimal('paid_amt', 10,2)->after('status');
			$table->decimal('co_ins', 10,2)->after('paid_amt');
			$table->decimal('deductable', 10,2)->after('co_ins');
			$table->decimal('with_held', 10,2)->after('deductable');
			$table->decimal('adjustment', 10,2)->after('with_held');
			$table->decimal('balance', 10,2)->after('adjustment');
			$table->string('denial_code',10)->after('balance');
			$table->integer('insurance_id')->after('denial_code');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('claimdoscptdetails', function(Blueprint $table)
		{
			$table->dropColumn('paid_amt');
			$table->dropColumn('co_ins');
			$table->dropColumn('deductable');
			$table->dropColumn('with_held');
			$table->dropColumn('adjustment');
			$table->dropColumn('balance');
			$table->dropColumn('deniel_code');
			$table->dropColumn('insurance_id');
		});
	}

}
