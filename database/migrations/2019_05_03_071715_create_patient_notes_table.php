<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePatientNotesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('patient_notes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title', 100)->nullable();
			$table->text('content', 65535)->nullable();
			$table->text('follow_up_content', 65535)->nullable();
			$table->enum('notes_type', array('patient','eligibility'));
			$table->enum('patient_notes_type', array('alert_notes','patient_notes','claim_notes','claim_denial_notes','claim_nis','claim_in_process','claim_paid','claim_denied','left_voice_message','claim_pending','payment_notes','followup_notes','statement_notes'));
			$table->bigInteger('claim_id')->unsigned();
			$table->bigInteger('notes_type_id')->unsigned();
			$table->enum('status', array('Active','Inactive'))->default('Active');
			$table->integer('user_id');
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
		Schema::drop('patient_notes');
	}

}
