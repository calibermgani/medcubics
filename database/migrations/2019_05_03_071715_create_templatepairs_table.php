<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTemplatepairsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('templatepairs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('templatetypes_id')->nullable();
			$table->string('label', 50)->nullable();
			$table->string('input_types', 50)->nullable();
			$table->string('key')->nullable();
			$table->string('value')->nullable();
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
		Schema::drop('templatepairs');
	}

}
