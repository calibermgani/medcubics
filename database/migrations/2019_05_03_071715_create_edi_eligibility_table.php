<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEdiEligibilityTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edi_eligibility', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('patient_eligibility_id')->unsigned();
			$table->string('edi_eligibility_id', 20)->nullable();
			$table->dateTime('edi_eligibility_created');
			$table->bigInteger('patient_id')->unsigned();
			$table->bigInteger('provider_id')->unsigned();
			$table->integer('provider_npi');
			$table->bigInteger('insurance_id');
			$table->string('policy_id', 20)->nullable();
			$table->date('dos');
			$table->date('dos_from');
			$table->date('dos_to');
			$table->integer('service_type');
			$table->string('error_message', 250)->nullable();
			$table->bigInteger('temp_patient_id');
			$table->string('type', 50)->nullable();
			$table->string('plan_type', 50)->nullable();
			$table->string('plan_number', 50)->nullable();
			$table->string('plan_name', 100)->nullable();
			$table->date('plan_begin_date');
			$table->date('plan_end_date');
			$table->string('coverage_status', 50)->nullable();
			$table->string('group_name', 100)->nullable();
			$table->enum('insurance_type', array('Medicare','Others'))->default('Others');
			$table->bigInteger('contact_detail');
			$table->timestamps();
			$table->bigInteger('created_by')->unsigned();
			$table->dateTime('deleted_at');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('edi_eligibility');
	}

}
