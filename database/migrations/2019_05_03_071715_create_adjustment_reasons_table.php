<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAdjustmentReasonsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('adjustment_reasons', function(Blueprint $table)
		{
			$table->increments('id');
			$table->enum('adjustment_type', array('Insurance','Patient'));
			$table->text('adjustment_reason', 65535);
			$table->string('adjustment_shortname', 110)->nullable();
			$table->enum('status', array('Active','Inactive'));
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
		Schema::drop('adjustment_reasons');
	}

}
