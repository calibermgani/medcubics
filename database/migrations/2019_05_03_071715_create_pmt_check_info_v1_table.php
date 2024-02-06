<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePmtCheckInfoV1Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pmt_check_info_v1', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('check_no', 25)->nullable();
			$table->date('check_date');
			$table->string('bank_name', 25)->nullable();
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
		Schema::drop('pmt_check_info_v1');
	}

}
