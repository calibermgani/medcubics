<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePagePermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('page_permissions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('module', 50)->nullable();
			$table->string('menu')->nullable();
			$table->string('submenu')->nullable();
			$table->string('title')->nullable();
			$table->text('title_url', 65535);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('page_permissions');
	}

}
