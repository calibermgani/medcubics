<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAdminpagePermissionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('adminpage_permission', function(Blueprint $table)
		{
			$table->integer('id')->unsigned()->primary();
			$table->string('menu', 50)->nullable();
			$table->string('submenu', 50)->nullable();
			$table->string('title', 20)->nullable();
			$table->text('title_url', 65535)->nullable();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('adminpage_permission');
	}

}
