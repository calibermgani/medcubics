<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClaimDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('claim_details', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->timestamps();
			$table->bigInteger('attorney_id');
			$table->string('facility_mrn', 50)->nullable();
			$table->bigInteger('provider_id')->unsigned();
			$table->bigInteger('patient_id')->unsigned();
			$table->bigInteger('claim_id')->unsigned();
			$table->enum('is_provider_employed', array('','Yes','No'));
			$table->enum('is_employment', array('','Yes','No'));
			$table->enum('is_autoaccident', array('','Yes','No'));
			$table->string('autoaccident_state', 2)->nullable();
			$table->enum('is_otheraccident', array('','Yes','No'));
			$table->string('other_claim_id', 28)->nullable();
			$table->enum('provider_qualifier', array('','0B','1G','G2','LU'));
			$table->string('provider_otherid', 17)->nullable();
			$table->decimal('lab_charge', 10);
			$table->string('claim_code', 19)->nullable();
			$table->enum('print_signature_onfile_box12', array('Yes','No'));
			$table->enum('print_signature_onfile_box13', array('Yes','No'));
			$table->date('illness_box14');
			$table->enum('other_date_qualifier', array('','454','304','453','439','455','471','090','091','444'));
			$table->date('other_date')->nullable();
			$table->enum('service_facility_qual', array('','0B','G2','LU'));
			$table->string('facility_otherid', 12)->nullable();
			$table->enum('billing_provider_qualifier', array('','0B','G2','ZZ'));
			$table->string('billing_provider_otherid', 20)->nullable();
			$table->date('unable_to_work_from')->nullable();
			$table->date('unable_to_work_to')->nullable();
			$table->date('hospitalization_from')->nullable();
			$table->date('hospitalization_to')->nullable();
			$table->text('additional_claim_info', 65535);
			$table->enum('resubmission_code', array('','7','8'));
			$table->string('original_ref_no', 18)->nullable();
			$table->enum('emergency', array('','Yes','No'));
			$table->enum('box23_type', array('','referal_number','mamography','clia_no'));
			$table->string('box_23', 29)->nullable();
			$table->enum('outside_lab', array('','Yes','No'));
			$table->enum('accept_assignment', array('','Yes','No'));
			$table->bigInteger('created_by')->unsigned();
			$table->bigInteger('updated_by')->unsigned();
			$table->softDeletes();
			$table->enum('epsdt', array('','Yes','No'));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('claim_details');
	}

}
