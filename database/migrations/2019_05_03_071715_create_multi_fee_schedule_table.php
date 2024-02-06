<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMultiFeeScheduleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('multi_fee_schedule', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->bigInteger('fee_schedule_id');
			$table->string('year', 20)->nullable();
			$table->string('insurance_id', 20)->nullable();
			$table->bigInteger('cpt_id');
			$table->decimal('billed_amount', 15);
			$table->decimal('allowed_amount', 15);
			$table->string('modifier_id', 100)->nullable();
			$table->enum('status', array('Active','Inactive'))->default('Active');
			$table->bigInteger('created_by');
			$table->bigInteger('updated_by');
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
		Schema::drop('multi_fee_schedule');
	}

}
