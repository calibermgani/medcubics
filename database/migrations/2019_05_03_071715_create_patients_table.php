<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePatientsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('patients', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('account_no', 100)->nullable();
			$table->enum('is_self_pay', array('No','Yes'));
			$table->string('last_name', 50)->nullable();
			$table->string('middle_name', 1)->nullable();
			$table->string('first_name', 50)->nullable();
			$table->enum('title', array('Mr','Mrs','Ms','Sr','Jr','Dr'))->nullable();
			$table->string('address1', 50)->nullable();
			$table->string('address2', 50)->nullable();
			$table->string('city', 50)->nullable();
			$table->string('state', 2)->nullable();
			$table->string('zip5', 5)->nullable();
			$table->string('zip4', 4)->nullable();
			$table->enum('gender', array('Male','Female','Others'));
			$table->string('ssn', 20)->nullable();
			$table->date('dob')->default('1901-01-01');
			$table->string('phone', 20)->nullable();
			$table->string('work_phone', 20)->nullable();
			$table->string('work_phone_ext', 4)->nullable();
			$table->string('mobile', 20)->nullable();
			$table->string('email', 50)->nullable();
			$table->string('driver_license', 15)->nullable();
			$table->integer('ethnicity_id')->nullable();
			$table->enum('race', array('Asian','Aslakan Eskimo','Black','Native American','Pacific Islander','Patient Declined','Unknown','White'))->nullable()->default('Unknown');
			$table->integer('language_id');
			$table->enum('employment_status', array('Employed','Self Employed','Unemployed','Retired','Student(Full Time)','Student(Part Time)','Unknown','Student'))->nullable();
			$table->string('employer_name', 50)->nullable();
			$table->enum('marital_status', array('Single','Married','Divorced','Partnered','Unknown','Separated','Widowed'))->default('Unknown')->nullable();
			$table->string('organization_name', 60)->nullable();
			$table->string('occupation', 60)->nullable();
			$table->enum('student_status', array('Full Time','Part Time','Unknown'))->default('Unknown');
			$table->bigInteger('provider_id')->unsigned()->nullable();
			$table->integer('facility_id')->nullable();
			$table->enum('email_notification', array('Yes','No'));
			$table->enum('phone_reminder', array('Yes','No'));
			$table->enum('preferred_communication', array('Text Message','Voice Calls','Regular Mail','Email','Unknown'))->default('Unknown')->nullable();
			$table->enum('statements', array('Yes','No','Hold','Insurance Only','Unknown'))->default('Unknown');
			$table->bigInteger('stmt_category')->nullable();
			$table->bigInteger('hold_reason');
			$table->date('hold_release_date')->nullable();
			$table->integer('statements_sent')->nullable();
			$table->enum('bill_cycle', array('A - G','H - M','N - S','T - Z'));
			$table->date('deceased_date')->nullable();
			$table->string('medical_chart_no', 10)->nullable();
			$table->enum('eligibility_verification', array('None','Active','Inactive','Error'));
			$table->enum('demographic_status', array('Complete','Incomplete'));
			$table->enum('status', array('Active','Inactive'));
			$table->integer('percentage');
			$table->integer('demo_percentage');
			$table->integer('ins_percentage');
			$table->integer('contact_percentage');
			$table->integer('auth_percentage');
			$table->date('last_statement_sent_date');
			$table->string('avatar_name', 50)->nullable();
			$table->string('avatar_ext', 5)->nullable();
			$table->enum('patient_from', array('app','web'))->default('web');
			$table->bigInteger('claim_count');
			$table->bigInteger('created_by');
			$table->bigInteger('updated_by');
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
		Schema::drop('patients');
	}

}
