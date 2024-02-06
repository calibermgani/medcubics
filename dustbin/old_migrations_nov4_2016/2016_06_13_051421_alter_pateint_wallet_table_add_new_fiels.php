<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPateintWalletTableAddNewFiels extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pateint_wallet', function(Blueprint $table)
		{
			$table->integer('adjustment_reason_id')->after('patient_id');
			$table->integer('money_order_no')->after('payment_mode');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('pateint_wallet', function(Blueprint $table)
		{
			 $table->dropColumn('adjustment_reason_id');
			 $table->dropColumn('money_order_no');
		});
	}

}
