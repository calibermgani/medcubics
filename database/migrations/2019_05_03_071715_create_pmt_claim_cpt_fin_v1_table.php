<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePmtClaimCptFinV1Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pmt_claim_cpt_fin_v1', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('claim_id')->unsigned();
			$table->bigInteger('claim_cpt_info_id')->unsigned()->index('claim_cpt_info_id');
			$table->decimal('cpt_charge', 10);
			$table->decimal('cpt_allowed_amt', 10);
			$table->decimal('paid_amt', 10);
			$table->decimal('co_ins', 10);
			$table->decimal('co_pay', 10);
			$table->decimal('deductable', 10);
			$table->decimal('with_held', 10);
			$table->decimal('adjustment', 10);
			$table->decimal('patient_paid', 10);
			$table->decimal('insurance_paid', 10);
			$table->decimal('patient_adjusted', 10);
			$table->decimal('insurance_adjusted', 10);
			$table->decimal('patient_balance', 10);
			$table->decimal('insurance_balance', 10);
			$table->timestamps();
			$table->bigInteger('created_by')->unsigned();
			$table->bigInteger('updated_by')->unsigned();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pmt_claim_cpt_fin_v1');
	}

}
