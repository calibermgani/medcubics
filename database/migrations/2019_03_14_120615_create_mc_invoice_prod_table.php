<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMcInvoiceProdTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mc_invoice_prod', function(Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('invoice_id')->unsigned();
            $table->date('product_start_date');
            $table->date('product_end_date');
            $table->text('description');
            $table->decimal('unit_price',20, 2);
            $table->decimal('quantity',20, 2);
            $table->decimal('total_price',20, 2);
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
		//
	}

}
