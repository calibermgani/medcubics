<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePatientAuthorizationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('patient_authorizations', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('patient_id')->unsigned();
			$table->string('authorization_no', 30);
			$table->date('requested_date')->nullable();
			$table->string('authorization_contact_person', 100)->nullable();
			$table->enum('alert_appointment', array('Yes','No'))->nullable();
			$table->string('allowed_visit', 3)->nullable();
			$table->integer('insurance_id');
			$table->integer('pos_id')->nullable();
			$table->date('start_date')->nullable();
			$table->date('end_date')->nullable();
			$table->string('authorization_phone', 20)->nullable();
			$table->string('authorization_phone_ext', 4)->nullable();
			$table->enum('alert_billing', array('Yes','No'))->nullable();
			$table->decimal('allowed_amt', 10)->nullable();
			$table->decimal('amt_used', 10)->nullable();
			$table->decimal('amt_remaining', 10)->nullable();
			$table->decimal('alert_amt', 10)->nullable();
			$table->text('authorization_notes', 65535)->nullable();
			$table->bigInteger('document_save_id')->nullable();
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
		Schema::drop('patient_authorizations');
	}

}
