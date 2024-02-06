<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEdiTransmissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edi_transmissions', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->enum('transmission_type', array('Electronic','Paper'));
			$table->integer('total_claims');
			$table->decimal('total_billed_amount', 10);
			$table->string('file_path', 250)->nullable();
			$table->enum('is_transmitted', array('No','Yes'));
			$table->timestamps();
			$table->bigInteger('created_by')->unsigned();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('edi_transmissions');
	}

}
