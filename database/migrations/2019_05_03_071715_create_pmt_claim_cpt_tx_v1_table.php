<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePmtClaimCptTxV1Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pmt_claim_cpt_tx_v1', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('payment_id')->unsigned();
			$table->bigInteger('claim_id')->unsigned();
			$table->bigInteger('pmt_claim_tx_id')->unsigned()->index('pmt_claim_tx_id');
			$table->bigInteger('claim_cpt_info_id')->unsigned()->index('claim_cpt_info_id');
			$table->decimal('allowed', 10);
			$table->decimal('deduction', 10);
			$table->decimal('copay', 10);
			$table->decimal('coins', 10);
			$table->decimal('withheld', 10);
			$table->decimal('writeoff', 10);
			$table->decimal('paid', 10);
			$table->string('denial_code', 100)->nullable();
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
		Schema::drop('pmt_claim_cpt_tx_v1');
	}

}
