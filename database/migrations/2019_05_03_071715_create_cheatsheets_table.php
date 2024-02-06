<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCheatsheetsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cheatsheets', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('resource_id');
			$table->bigInteger('facility_id');
			$table->bigInteger('provider_id');
			$table->string('visit_type_id')->nullable();
			$table->string('cpt', 50)->nullable();
			$table->string('icd', 50)->nullable();
			$table->string('claimstatus', 50)->nullable();
			$table->string('feeschedules', 50)->nullable();
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
		Schema::drop('cheatsheets');
	}

}
