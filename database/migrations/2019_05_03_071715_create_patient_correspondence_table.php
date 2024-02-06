<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePatientCorrespondenceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('patient_correspondence', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('patient_id')->unsigned();
			$table->string('template_id', 25)->nullable();
			$table->string('email_id', 100)->nullable();
			$table->integer('insurance_id')->nullable();
			$table->string('claim_number', 50)->nullable();
			$table->date('dos')->nullable();
			$table->text('message', 65535)->nullable();
			$table->text('subject', 65535)->nullable();
			$table->bigInteger('created_by')->unsigned();
			$table->bigInteger('updated_by')->unsigned();
			$table->softDeletes();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('patient_correspondence');
	}

}
