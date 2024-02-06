<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePatientInsuranceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('patient_insurance', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('patient_id')->unsigned();
			$table->integer('insurance_id');
			$table->integer('medical_secondary_code')->nullable();
			$table->enum('category', array('Primary','Secondary','Tertiary','Workers Comp','Liability','Auto Accident','Attorney','Others'))->nullable();
			$table->enum('relationship', array('Self','Spouse','Child','Others'));
			$table->string('insured_phone', 15)->nullable();
			$table->enum('insured_gender', array('Male','Female','Others'));
			$table->string('last_name', 50)->nullable();
			$table->string('first_name', 50)->nullable();
			$table->string('middle_name', 1)->nullable();
			$table->string('insured_ssn', 20)->nullable();
			$table->date('insured_dob')->default('1901-01-01');
			$table->string('insured_address1', 50)->nullable();
			$table->string('insured_address2', 50)->nullable();
			$table->string('insured_city', 50)->nullable();
			$table->string('insured_state', 2)->nullable();
			$table->string('insured_zip5', 5)->nullable();
			$table->string('insured_zip4', 4)->nullable();
			$table->string('policy_id', 50)->nullable();
			$table->string('group_name', 100)->nullable();
			$table->date('effective_date')->nullable();
			$table->date('termination_date')->nullable();
			$table->string('adjustor_ph', 20)->nullable();
			$table->string('adjustor_fax', 20)->nullable();
			$table->integer('orderby_category');
			$table->bigInteger('document_save_id');
			$table->enum('eligibility_verification', array('None','Active','Inactive','Error'));
			$table->enum('same_patient_address', array('no','yes'));
			$table->dateTime('active_from')->nullable();
			$table->dateTime('active_to')->nullable();
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
		Schema::drop('patient_insurance');
	}

}
