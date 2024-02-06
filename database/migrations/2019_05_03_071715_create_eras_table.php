<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateErasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eras', function(Blueprint $table)
		{
			$table->increments('id');
			$table->date('receive_date')->nullable();
			$table->integer('insurance_id');
			$table->string('insurance_name', 220)->nullable();
			$table->integer('provider_npi_id');
			$table->string('check_no', 220)->nullable();
			$table->date('check_date')->nullable();
			$table->decimal('check_amount', 10);
			$table->decimal('check_paid_amount', 10);
			$table->enum('status', array('Yes','No'))->default('No');
			$table->string('pdf_name', 220)->nullable();
			$table->bigInteger('claim_count');
			$table->timestamps();
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
		Schema::drop('eras');
	}

}
