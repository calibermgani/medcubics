<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClaimInfoV1Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('claim_info_v1', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('claim_number')->nullable();
			$table->bigInteger('patient_id')->unsigned();
			$table->integer('template_id');
			$table->date('date_of_service');
			$table->enum('charge_add_type', array('esuperbill','ehr','manual','billing','app'));
			$table->text('icd_codes', 65535)->nullable();
			$table->text('primary_cpt_code', 65535)->nullable();
			$table->bigInteger('rendering_provider_id');
			$table->bigInteger('refering_provider_id');
			$table->bigInteger('billing_provider_id');
			$table->bigInteger('facility_id');
			$table->integer('insurance_id');
			$table->integer('pos_id');
			$table->enum('self_pay', array('Yes','No'));
			$table->string('insurance_category', 20)->nullable();
			$table->integer('patient_insurance_id')->nullable();
			$table->string('auth_no', 30);
			$table->integer('copay_id');
			$table->date('doi');
			$table->date('admit_date');
			$table->date('discharge_date');
			$table->integer('anesthesia_id');
			$table->decimal('total_charge', 10);
			$table->integer('hold_reason_id');
			$table->enum('status', array('E-bill','Hold','Ready','Patient','Submitted','Paid','Denied','Pending','Rejection','Complete'));
			$table->integer('no_of_issues');
			$table->text('error_message', 65535);
			$table->enum('claim_type', array('electronic','paper'));
			$table->date('submited_date');
			$table->date('last_submited_date');
			$table->date('filed_date');
			$table->integer('claim_submit_count');
			$table->integer('pmt_count');
			$table->string('claim_armanagement_status', 50)->nullable();
			$table->enum('is_send_paid_amount', array('Yes','No'));
			$table->string('payer_claim_number', 50)->nullable();
			$table->enum('payment_hold_reason', array('','Patient','Insurance'));
			$table->string('claim_reference', 25)->nullable();
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
		Schema::drop('claim_info_v1');
	}

}
