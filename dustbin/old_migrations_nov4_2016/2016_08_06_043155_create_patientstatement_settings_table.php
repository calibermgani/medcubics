<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
			$table->enum('servicelocation', ['Facility', 'Practice']);
			$table->string('check_add_1', 50);
			$table->string('check_add_2', 50);
			$table->string('check_city', 50);
			$table->string('check_state', 2);
			$table->string('check_zip5', 5);
			$table->string('check_zip4', 4);
			$table->integer('rendering_provider');
			$table->string('callbackphone', 20);
			$table->integer('statementsentdays');
			$table->integer('bulkstatement');
			$table->enum('statementcycle', ['All', 'Billcycle', 'Facility', 'Provider', 'Account']);
			$table->string('week_1_billcycle', 25);
			$table->string('week_2_billcycle', 25);
			$table->string('week_3_billcycle', 25);
			$table->string('week_4_billcycle', 25);
			$table->string('week_5_billcycle', 25);
			$table->string('week_1_facility', 20);
			$table->string('week_2_facility', 20);
			$table->string('week_3_facility', 20);
			$table->string('week_4_facility', 20);
			$table->string('week_5_facility', 20);
			$table->string('week_1_provider', 20);
			$table->string('week_2_provider', 20);
			$table->string('week_3_provider', 20);
			$table->string('week_4_provider', 20);
			$table->string('week_5_provider', 20);
			$table->string('week_1_account', 150);
			$table->string('week_2_account', 150);
			$table->string('week_3_account', 150);
			$table->string('week_4_account', 150);
			$table->string('week_5_account', 150);
			$table->integer('minimumpatientbalance');
			$table->enum('displaypayment', ['Payments', 'InsPatient']);
			$table->integer('latestpaymentinfo');
			$table->integer('paymentmessage');
			$table->text('paymentmessage_1');
			$table->text('paymentmessage_2');
			$table->text('paymentmessage_3');
			$table->bigInteger('updated_by');
			$table->timestamp('created_at')->default("0000-00-00 00:00:00");
            $table->timestamp('updated_at')->default("0000-00-00 00:00:00");
			$table->timestamp('deleted_at')->nullable();
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
