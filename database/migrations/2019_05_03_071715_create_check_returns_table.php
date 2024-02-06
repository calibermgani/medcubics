<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCheckReturnsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('check_returns', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('patient_id');
			$table->date('check_date');
			$table->string('check_no', 25)->nullable();
			$table->decimal('financial_charges', 10);
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
		Schema::drop('check_returns');
	}

}
