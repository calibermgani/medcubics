<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClaimCptInfoV1Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('claim_cpt_info_v1', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('patient_id')->unsigned();
			$table->bigInteger('claim_id')->unsigned();
			$table->date('dos_from');
			$table->date('dos_to');
			$table->string('cpt_code', 10)->nullable();
			$table->string('modifier1', 2)->nullable();
			$table->string('modifier2', 2)->nullable();
			$table->string('modifier3', 2)->nullable();
			$table->string('modifier4', 2)->nullable();
			$table->string('cpt_icd_code', 150)->nullable();
			$table->string('cpt_icd_map_key', 50)->nullable();
			$table->string('unit', 5)->nullable();
			$table->decimal('charge', 10);
			$table->integer('cpt_order')->nullable();
			$table->boolean('is_active')->default(1);
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
		Schema::drop('claim_cpt_info_v1');
	}

}
