<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePatientstatementSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('patientstatement_settings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('paybydate');
			$table->enum('servicelocation', array('Facility','Practice'));
			$table->string('check_add_1', 50)->nullable();
			$table->string('check_add_2', 50)->nullable();
			$table->string('check_city', 50)->nullable();
			$table->string('check_state', 2)->nullable();
			$table->string('check_zip5', 5)->nullable();
			$table->string('check_zip4', 4)->nullable();
			$table->integer('rendering_provider');
			$table->string('callbackphone', 20)->nullable();
			$table->integer('statementsentdays');
			$table->integer('bulkstatement');
			$table->enum('statementcycle', array('All','Billcycle','Facility','Provider','Account','Category'));
			$table->string('week_1_billcycle', 25)->nullable();
			$table->string('week_2_billcycle', 25)->nullable();
			$table->string('week_3_billcycle', 25)->nullable();
			$table->string('week_4_billcycle', 25)->nullable();
			$table->string('week_5_billcycle', 25)->nullable();
			$table->string('week_1_facility', 250)->nullable();
			$table->string('week_2_facility', 250)->nullable();
			$table->string('week_3_facility', 250)->nullable();
			$table->string('week_4_facility', 250)->nullable();
			$table->string('week_5_facility', 250)->nullable();
			$table->string('week_1_provider', 250)->nullable();
			$table->string('week_2_provider', 250)->nullable();
			$table->string('week_3_provider', 250)->nullable();
			$table->string('week_4_provider', 250)->nullable();
			$table->string('week_5_provider', 250)->nullable();
			$table->string('week_1_account', 150)->nullable();
			$table->string('week_2_account', 150)->nullable();
			$table->string('week_3_account', 150)->nullable();
			$table->string('week_4_account', 150)->nullable();
			$table->string('week_5_account', 150)->nullable();
			$table->string('week_1_category', 150)->nullable();
			$table->string('week_2_category', 150)->nullable();
			$table->string('week_3_category', 150)->nullable();
			$table->string('week_4_category', 150)->nullable();
			$table->string('week_5_category', 150)->nullable();
			$table->decimal('minimumpatientbalance', 10);
			$table->enum('displaypayment', array('Payments','InsPatient'));
			$table->integer('latestpaymentinfo');
			$table->integer('paymentmessage');
			$table->text('paymentmessage_1', 65535);
			$table->text('spacial_message_1', 65535);
			$table->enum('cpt_shortdesc', array('Claim','Lineitem'));
			$table->integer('primary_dx');
			$table->integer('insserviceline');
			$table->integer('patserviceline');
			$table->integer('financial_charge');
			$table->integer('alert');
			$table->integer('visa_card');
			$table->integer('mc_card');
			$table->integer('maestro_card');
			$table->integer('gift_card');
			$table->integer('insurance_balance');
			$table->integer('aging_bucket');
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
		Schema::drop('patientstatement_settings');
	}

}
