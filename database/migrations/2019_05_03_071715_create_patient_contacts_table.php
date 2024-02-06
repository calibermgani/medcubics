<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePatientContactsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('patient_contacts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('patient_id')->unsigned();
			$table->enum('category', array('Guarantor','Emergency Contact','Employer','Attorney'));
			$table->string('guarantor_last_name', 50);
			$table->string('guarantor_middle_name', 1)->nullable();
			$table->string('guarantor_first_name', 50);
			$table->enum('guarantor_relationship', array('Others','Child','Father','Mother','Spouse','Neighbor','GrandMother','GrandFather','GrandChild','Friend','Brother','Sister','Guardian','Self'));
			$table->string('guarantor_home_phone', 20)->nullable();
			$table->string('guarantor_cell_phone', 20)->nullable();
			$table->string('guarantor_email', 50)->nullable();
			$table->string('guarantor_address1', 50)->nullable();
			$table->string('guarantor_address2', 50)->nullable();
			$table->string('guarantor_city', 50)->nullable();
			$table->string('guarantor_state', 2)->nullable();
			$table->string('guarantor_zip5', 5)->nullable();
			$table->string('guarantor_zip4', 4)->nullable();
			$table->string('emergency_last_name', 50);
			$table->string('emergency_middle_name', 1)->nullable();
			$table->string('emergency_first_name', 50);
			$table->enum('emergency_relationship', array('Child','Father','Mother','Spouse','Neighbor','GrandMother','GrandFather','GrandChild','Friend','Brother','Sister','Guardian','Others'));
			$table->string('emergency_home_phone', 20)->nullable();
			$table->string('emergency_cell_phone', 20)->nullable();
			$table->string('emergency_email', 50)->nullable();
			$table->string('emergency_address1', 50)->nullable();
			$table->string('emergency_address2', 50)->nullable();
			$table->string('emergency_city', 50)->nullable();
			$table->string('emergency_state', 2)->nullable();
			$table->string('emergency_zip5', 5)->nullable();
			$table->string('emergency_zip4', 4)->nullable();
			$table->enum('employer_status', array('Employed','Self Employed','Unemployed','Retired','Active Military Duty','Employed(Full Time)','Employed(Part Time)','Unknown','Student'));
			$table->string('employer_organization_name', 60)->nullable();
			$table->string('employer_occupation', 60)->nullable();
			$table->enum('employer_student_status', array('Full Time','Part Time','Unknown'))->default('Unknown');
			$table->string('employer_name', 50)->nullable();
			$table->string('employer_work_phone', 20)->nullable();
			$table->string('employer_phone_ext', 5)->nullable();
			$table->string('employer_address1', 50)->nullable();
			$table->string('employer_address2', 50)->nullable();
			$table->string('employer_city', 50)->nullable();
			$table->string('employer_state', 2)->nullable();
			$table->string('employer_zip5', 5)->nullable();
			$table->string('employer_zip4', 4)->nullable();
			$table->string('attorney_adjuster_name', 50)->nullable();
			$table->date('attorney_doi')->nullable();
			$table->string('attorney_claim_num', 20)->nullable();
			$table->string('attorney_work_phone', 20)->nullable();
			$table->string('attorney_phone_ext', 5)->nullable();
			$table->string('attorney_fax', 20)->nullable();
			$table->string('attorney_email', 25)->nullable();
			$table->string('attorney_address1', 50)->nullable();
			$table->string('attorney_address2', 50)->nullable();
			$table->string('attorney_city', 50)->nullable();
			$table->string('attorney_state', 2)->nullable();
			$table->string('attorney_zip5', 5)->nullable();
			$table->string('attorney_zip4', 4)->nullable();
			$table->enum('same_patient_address', array('no','yes'))->nullable();
			$table->bigInteger('created_by')->unsigned()->nullable();
			$table->bigInteger('updated_by')->unsigned()->nullable();
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
		Schema::drop('patient_contacts');
	}

}
