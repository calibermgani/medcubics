<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePmtClaimFinV1Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pmt_claim_fin_v1', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('claim_id')->unsigned();
			$table->bigInteger('patient_id');
			$table->decimal('total_charge', 10);
			$table->decimal('total_allowed', 10);
			$table->decimal('patient_paid', 10);
			$table->decimal('insurance_paid', 10);
			$table->decimal('patient_due', 10);
			$table->decimal('insurance_due', 10);
			$table->decimal('patient_adj', 10);
			$table->decimal('insurance_adj', 10);
			$table->decimal('withheld', 10);
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
		Schema::drop('pmt_claim_fin_v1');
	}

}
