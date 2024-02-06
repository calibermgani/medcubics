<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMcInvoiceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mc_invoice', function(Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_no',20);
            $table->longText('header')->nullable();
            $table->bigInteger('practice_id');
            $table->date('invoice_date');
            $table->date('invoice_start_date');
            $table->date('invoice_end_date');
            $table->decimal('invoice_amt', 20, 2);
            $table->decimal('tax', 20, 2)->nullable();
            $table->decimal('previous_amt_due', 20, 2);
            $table->decimal('total_amt', 20, 2);
            $table->text('notes', 65535)->nullable();
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
