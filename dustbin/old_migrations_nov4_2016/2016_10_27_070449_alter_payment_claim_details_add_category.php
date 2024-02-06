<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPaymentClaimDetailsAddCategory extends Migration {	
	public function up()
	{
		Schema::table('payment_claim_details', function($table)
		{
			$table->string('insurance_category',20)->after('insurance_id');			
		});
	}	
	public function down()
	{
		Schema::table('payment_claim_details', function(Blueprint $table)
		{
			$table->dropColumn('insurance_category');			
		});
	}
}
