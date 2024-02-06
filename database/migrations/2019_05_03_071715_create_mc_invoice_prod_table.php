<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMcInvoiceProdTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mc_invoice_prod', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('invoice_id')->unsigned();
			$table->date('product_start_date');
			$table->date('product_end_date');
			$table->text('description', 65535)->nullable();
			$table->float('unit_price', 10, 0);
			$table->float('quantity', 10, 0);
			$table->float('total_price', 10, 0);
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
		Schema::drop('mc_invoice_prod');
	}

}
