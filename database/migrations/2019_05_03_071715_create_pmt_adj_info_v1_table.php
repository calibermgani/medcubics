<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePmtAdjInfoV1Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pmt_adj_info_v1', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->enum('adj_type', array('Insurance','Patient',''));
			$table->bigInteger('patient_id')->unsigned();
			$table->integer('insurance_id');
			$table->decimal('adj_amount', 10);
			$table->integer('adj_reason_id');
			$table->string('reference', 20)->nullable();
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
		Schema::drop('pmt_adj_info_v1');
	}

}
