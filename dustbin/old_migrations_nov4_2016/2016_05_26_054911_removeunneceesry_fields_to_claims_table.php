<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveunneceesryFieldsToClaimsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `claims` DROP `claim_detail_id`, DROP `claim_other_detail_id`, DROP `ambulance_billing_id`, DROP `paid_amt`, DROP `patient_due`, DROP `insurance_due`, DROP `adjust_amt`, DROP `balance_amt`, DROP `payment_type`, DROP `payment_mode`, DROP `payment_batch_no`, DROP `payment_batch_date`, DROP `reference_no`, DROP `cheque_no`, DROP `cheque_date`, DROP `referingprovidertypeid`, DROP `cheque_amt`, DROP `total_paid`, DROP `payment_date`, DROP `deposit_date`, DROP `total_due`, DROP `unupplied`;");

		DB::statement("ALTER TABLE `claims` DROP `patient_type`");	

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `claims` ADD `claim_detail_id`, ADD `claim_other_detail_id`, ADD `ambulance_billing_id`, ADD `paid_amt`, ADD `patient_due`, ADD `insurance_due`, ADD `adjust_amt`, ADD `balance_amt`, ADD `payment_type`, ADD `payment_mode`, ADD `payment_batch_no`, ADD `payment_batch_date`, ADD `reference_no`, ADD `cheque_no`, ADD `cheque_date`, ADD `referingprovidertypeid`, ADD `cheque_amt`, ADD `total_paid`, ADD `payment_date`, ADD `deposit_date`, ADD `total_due`, ADD `unupplied`;");

		DB::statement("ALTER TABLE `claims` ADD `patient_type`");
	}

}
