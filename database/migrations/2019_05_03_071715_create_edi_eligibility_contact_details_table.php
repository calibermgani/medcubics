<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEdiEligibilityContactDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edi_eligibility_contact_details', function(Blueprint $table)
		{
			$table->increments('id');
			$table->enum('details_for', array('insurance_sp','medicare'))->default('insurance_sp');
			$table->integer('details_for_id');
			$table->text('entity_code', 65535);
			$table->string('last_name', 50)->nullable();
			$table->string('first_name', 50)->nullable();
			$table->string('identification_type', 200)->nullable();
			$table->string('identification_code', 50)->nullable();
			$table->string('address1', 50)->nullable();
			$table->string('address2', 50)->nullable();
			$table->string('city', 50)->nullable();
			$table->string('state', 2)->nullable();
			$table->integer('zip5');
			$table->integer('zip4');
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
		Schema::drop('edi_eligibility_contact_details');
	}

}
