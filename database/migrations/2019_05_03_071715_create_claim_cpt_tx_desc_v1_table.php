<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClaimCptTxDescV1Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('claim_cpt_tx_desc_v1', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('claim_tx_desc_id')->unsigned();
			$table->enum('transaction_type', array('New Charge','Patient Payment','Insurance Payment','Change Payment','Responsibility','Denials','Insurance Refund','Patient Refund','Patient Adjustment','Insurance Adjustment','Wallet','Patient Credit Balance','Edit Charge','Submitted','Submitted Paper','Resubmitted','Resubmitted Paper','Payer Rejected','Payer Accepted','Clearing House Rejection','Clearing House Accepted','Void Check'));
			$table->bigInteger('claim_id')->unsigned();
			$table->bigInteger('claim_cpt_info_id')->unsigned();
			$table->bigInteger('payment_id')->unsigned();
			$table->bigInteger('txn_id')->unsigned();
			$table->bigInteger('responsibility')->unsigned();
			$table->decimal('pat_bal', 10);
			$table->decimal('ins_bal', 10);
			$table->text('value_1', 65535);
			$table->string('value_2')->nullable();
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
		Schema::drop('claim_cpt_tx_desc_v1');
	}

}
