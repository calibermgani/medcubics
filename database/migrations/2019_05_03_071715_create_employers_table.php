<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmployersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->enum('employer_status', array('Employed','Self Employed','Unemployed','Retired','Active Military Duty','Employed(Full Time)','Employed(Part Time)','Unknown','Student'));
			$table->string('employer_organization_name', 60)->nullable();
			$table->string('employer_occupation', 60)->nullable();
			$table->enum('employer_student_status', array('Full Time','Part Time','Unknown'))->default('Unknown')->nullable();
			$table->string('employer_name', 50)->nullable();
			$table->string('address1', 50)->nullable();
			$table->string('address2', 50)->nullable();
			$table->string('city', 50)->nullable();
			$table->string('state', 2)->nullable();
			$table->string('zip5', 5)->nullable();
			$table->string('zip4', 4)->nullable();
			$table->string('work_phone', 20)->nullable();
			$table->string('work_phone_ext', 5)->nullable();
			$table->string('work_phone1', 20)->nullable();
			$table->string('work_phone_ext1', 5)->nullable();
			$table->string('fax', 15)->nullable();
			$table->string('emailid', 100)->nullable();
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
		Schema::drop('employers');
	}

}
