<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEdiEligibilityMedicareTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edi_eligibility_medicare', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('plan_type', 250)->nullable();
			$table->string('plan_type_label', 250)->nullable();
			$table->enum('active', array('true','false'))->nullable();
			$table->string('deductible', 250)->nullable();
			$table->string('deductible_remaining', 250)->nullable();
			$table->string('coinsurance_percent', 250)->nullable();
			$table->string('copayment', 250)->nullable();
			$table->string('payer_name', 250)->nullable();
			$table->string('policy_number', 250)->nullable();
			$table->bigInteger('contact_details')->unsigned();
			$table->string('insurance_type', 250)->nullable();
			$table->string('insurance_type_label', 250)->nullable();
			$table->string('mco_bill_option_code', 250)->nullable();
			$table->string('mco_bill_option_label', 250)->nullable();
			$table->enum('locked', array('true','false'))->nullable();
			$table->date('info_valid_till');
			$table->date('start_date');
			$table->date('end_date');
			$table->date('effective_date');
			$table->date('termination_date');
			$table->bigInteger('created_by');
			$table->bigInteger('updated_by');
			$table->timestamps();
			$table->dateTime('deleted_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('edi_eligibility_medicare');
	}

}
