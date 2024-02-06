<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClaimCptShadedInfoV1Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('claim_cpt_shaded_info_v1', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->bigInteger('claim_cpt_info_v1_id');
			$table->string('box_24_AToG', 61)->nullable();
			$table->timestamps();
			$table->bigInteger('created_by');
			$table->bigInteger('updated_by');
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
		Schema::drop('claim_cpt_shaded_info_v1');
	}

}
