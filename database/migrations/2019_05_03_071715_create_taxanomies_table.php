<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTaxanomiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('taxanomies', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('speciality_id');
			$table->string('description')->nullable();
			$table->string('code', 100)->nullable();
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
		Schema::drop('taxanomies');
	}

}
