<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEdiEligibilityInsuranceSpPhysiciansTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edi_eligibility_insurance_sp_physicians', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('edi_eligibility_insurance_id');
			$table->string('insurance_type', 200)->nullable();
			$table->string('eligibility_code', 200)->nullable();
			$table->enum('primary_care', array('Unknown','true','false'));
			$table->enum('restricted', array('Unknown','true','false'));
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
		Schema::drop('edi_eligibility_insurance_sp_physicians');
	}

}
