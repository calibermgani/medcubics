<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePmtInfoV1Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pmt_info_v1', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('pmt_no', 20)->nullable();
			$table->enum('pmt_type', array('Payment','Refund','Adjustment','Credit Balance'));
			$table->bigInteger('patient_id')->unsigned();
			$table->integer('insurance_id');
			$table->decimal('pmt_amt', 10);
			$table->decimal('amt_used', 10);
			$table->decimal('balance', 10);
			$table->enum('source', array('scheduler','charge','posting','addwallet','refundwallet'));
			$table->bigInteger('source_id')->unsigned();
			$table->enum('pmt_method', array('Insurance','Patient',''));
			$table->enum('pmt_mode', array('Check','Cash','Credit','EFT','Credit Balance','Money Order'));
			$table->bigInteger('pmt_mode_id');
			$table->string('reference', 20)->nullable();
			$table->boolean('void_check')->nullable();
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
		Schema::drop('pmt_info_v1');
	}

}
