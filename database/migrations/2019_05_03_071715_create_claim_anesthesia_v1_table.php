<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClaimAnesthesiaV1Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('claim_anesthesia_v1', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('anesthesia_start', 10)->nullable();
			$table->string('anesthesia_stop', 10)->nullable();
			$table->string('anesthesia_minute', 10)->nullable();
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
		Schema::drop('claim_anesthesia_v1');
	}

}
