<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMcInvoiceBilltoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mc_invoice_billto', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('practice_id')->unsigned();
			$table->string('contact_name', 100)->nullable();
			$table->string('street_1', 100)->nullable();
			$table->string('street_2', 100)->nullable();
			$table->string('city', 50)->nullable();
			$table->string('state', 50)->nullable();
			$table->integer('zip_5')->unsigned();
			$table->integer('zip_4')->unsigned()->nullable();
			$table->string('contact_no', 20)->nullable();
			$table->string('mobile_no', 20)->nullable();
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
		Schema::drop('mc_invoice_billto');
	}

}
