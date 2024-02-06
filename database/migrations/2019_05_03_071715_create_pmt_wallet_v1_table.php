<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePmtWalletV1Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pmt_wallet_v1', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('patient_id')->unsigned();
			$table->bigInteger('pmt_info_id')->unsigned();
			$table->enum('tx_type', array('Credit','Debit'));
			$table->decimal('applied', 10);
			$table->float('balance', 10);
			$table->integer('wallet_Ref_Id');
			$table->decimal('amount', 10);
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
		Schema::drop('pmt_wallet_v1');
	}

}
