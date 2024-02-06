<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClaimsTableSendPaidAmount extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		Schema::table('claims', function($table)
		{
			$table->dropColumn('is_claim_sentas_first');
		    $table->enum('is_send_paid_amount', ['Yes','No'])->after('claim_armanagement_status');		    
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('claims', function($table)
		{
			$table->tinyInteger('is_claim_sentas_first')->default(0)->after('claim_armanagement_status');;
		    $table->dropColumn('is_send_paid_amount');
		});
	}

}
