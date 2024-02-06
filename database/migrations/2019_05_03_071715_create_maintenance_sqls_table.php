<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMaintenanceSqlsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('maintenance_sqls', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->enum('status', array('Incomplete','Error','Success'));
			$table->string('query', 5000)->nullable();
			$table->dateTime('applied_date')->nullable();
			$table->string('success_practice', 100)->nullable();
			$table->string('failure_practice', 100)->nullable();
			$table->string('developer_name', 20)->nullable();
			$table->bigInteger('user');
			$table->dateTime('developed_date')->nullable();
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
		Schema::drop('maintenance_sqls');
	}

}
