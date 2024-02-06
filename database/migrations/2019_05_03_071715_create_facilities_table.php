<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFacilitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('facilities', function(Blueprint $table)
		{
			$table->increments('id');
			$table->text('description', 65535);
			$table->string('facility_name', 200);
			$table->string('short_name', 3);
			$table->string('avatar_name', 20);
			$table->string('avatar_ext', 5);
			$table->integer('speciality_id');
			$table->integer('taxanomy_id');
			$table->string('phone', 15);
			$table->string('phoneext', 4);
			$table->string('fax', 15);
			$table->string('email', 100);
			$table->string('website', 100);
			$table->integer('county');
			$table->string('timezone', 100);
			$table->string('facility_tax_id', 20);
			$table->string('facility_npi', 20);
			$table->string('clia_number', 20);
			$table->integer('pos_id');
			$table->bigInteger('default_provider_id')->unsigned();
			$table->string('fda', 20);
			$table->integer('claim_format');
			$table->string('monday_forenoon', 10);
			$table->string('monday_afternoon', 10);
			$table->string('tuesday_forenoon', 10);
			$table->string('tuesday_afternoon', 10);
			$table->string('wednesday_forenoon', 10);
			$table->string('wednesday_afternoon', 10);
			$table->string('thursday_forenoon', 10);
			$table->string('thursday_afternoon', 10);
			$table->string('friday_forenoon', 10);
			$table->string('friday_afternoon', 10);
			$table->string('saturday_forenoon', 10);
			$table->string('saturday_afternoon', 10);
			$table->string('sunday_forenoon', 10);
			$table->string('sunday_afternoon', 10);
			$table->string('facility_manager', 100);
			$table->string('facility_manager_phone', 20);
			$table->string('facility_manager_ext', 10);
			$table->string('facility_manager_email', 100);
			$table->string('facility_biller', 100);
			$table->string('facility_biller_phone', 20);
			$table->string('facility_biller_ext', 10);
			$table->string('facility_biller_email', 100);
			$table->enum('scheduler', array('Yes','No'));
			$table->enum('superbill', array('Available','Not Available'));
			$table->enum('statement_address', array('Pay to Address','Mailing Address','Primary Location'));
			$table->enum('medication_prescr', array('Yes','No'));
			$table->enum('credit_cart_accepted', array('Accepted','Not Accepted'));
			$table->integer('no_of_visit_per_week');
			$table->enum('status', array('Active','Inactive'));
			$table->timestamps();
			$table->bigInteger('created_by')->unsigned();
			$table->bigInteger('updated_by')->unsigned();
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
		Schema::drop('facilities');
	}

}
