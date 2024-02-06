<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClaimCptOthersAdjustmentInfoV1Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('claim_cpt_others_adjustment_info_v1', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('claim_id')->unsigned();
			$table->bigInteger('claim_cpt_id')->unsigned();
			$table->bigInteger('claim_cpt_tx_id');
			$table->bigInteger('adjustment_id')->unsigned();
			$table->decimal('adjustment_amt', 10);
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
		Schema::drop('claim_cpt_others_adjustment_info_v1');
	}

}
