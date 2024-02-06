<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePatientEligibilityTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('patient_eligibility', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('patient_insurance_id');
			$table->bigInteger('provider_id');
			$table->bigInteger('facility_id');
			$table->bigInteger('patients_id')->unsigned();
			$table->text('content', 65535)->nullable();
			$table->integer('template_id');
			$table->boolean('is_edi_atatched');
			$table->boolean('is_manual_atatched');
			$table->string('edi_filename')->nullable();
			$table->string('edi_file_path')->nullable();
			$table->string('bv_filename', 250)->nullable();
			$table->string('bv_file_path')->nullable();
			$table->date('dos_from');
			$table->date('dos_to');
			$table->bigInteger('temp_patient_id');
			$table->bigInteger('created_by')->unsigned();
			$table->bigInteger('updated_by')->unsigned();
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
		Schema::drop('patient_eligibility');
	}

}
