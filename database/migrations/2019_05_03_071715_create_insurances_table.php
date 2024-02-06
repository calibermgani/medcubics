<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsurancesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('insurances', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('insurance_name', 50)->nullable();
			$table->string('short_name', 15)->nullable();
			$table->string('insurance_desc')->nullable();
			$table->string('avatar_name', 20)->nullable();
			$table->string('avatar_ext', 5)->nullable();
			$table->string('address_1', 50)->nullable();
			$table->string('address_2', 50)->nullable();
			$table->string('city', 50)->nullable();
			$table->string('state', 2)->nullable();
			$table->string('zipcode5', 5)->nullable();
			$table->string('zipcode4', 4)->nullable();
			$table->string('phone1', 20)->nullable();
			$table->string('phoneext', 4)->nullable();
			$table->string('fax', 20)->nullable();
			$table->string('email', 100)->nullable();
			$table->string('website', 100)->nullable();
			$table->enum('enrollment', array('Unknown','Yes','No'));
			$table->integer('insurancetype_id');
			$table->integer('insuranceclass_id');
			$table->string('managedcareid', 50)->nullable();
			$table->string('medigapid', 50)->nullable();
			$table->string('payerid', 50)->nullable();
			$table->string('era_payerid', 50)->nullable();
			$table->string('eligibility_payerid', 50)->nullable();
			$table->string('feeschedule')->nullable();
			$table->string('primaryfiling', 3)->nullable();
			$table->string('secondaryfiling', 3)->nullable();
			$table->string('appealfiling', 3)->nullable();
			$table->string('claim_ph', 20)->nullable();
			$table->string('claim_ext', 5)->nullable();
			$table->string('eligibility_ph', 20)->nullable();
			$table->string('eligibility_ext', 5)->nullable();
			$table->string('eligibility_ph2', 20)->nullable();
			$table->string('eligibility_ext2', 5)->nullable();
			$table->string('enrollment_ph', 20)->nullable();
			$table->string('enrollment_ext', 5)->nullable();
			$table->string('prior_ph', 20)->nullable();
			$table->string('prior_ext', 5)->nullable();
			$table->string('claim_fax', 20)->nullable();
			$table->enum('claimtype', array('Electronic','Paper'));
			$table->string('eligibility_fax', 20)->nullable();
			$table->string('eligibility_fax2', 20)->nullable();
			$table->string('enrollment_fax', 20)->nullable();
			$table->string('prior_fax', 20)->nullable();
			$table->enum('status', array('Active','Inactive'));
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
		Schema::drop('insurances');
	}

}
