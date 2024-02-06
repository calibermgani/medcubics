<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSearchFieldsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('search_fields', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('page_name', 220)->nullable();
			$table->text('search_fields', 65535)->nullable();
			$table->timestamps();
			$table->dateTime('deleted_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('search_fields');
	}

}
