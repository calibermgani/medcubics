<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReportExportTaskTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('report_export_task', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->bigInteger('practice_id');
			$table->string('report_name', 120)->nullable();
			$table->text('report_url', 65535)->nullable();
			$table->text('parameter', 65535)->nullable();
			$table->string('report_file_name', 120)->nullable();
			$table->string('report_controller_name', 120)->nullable();
			$table->string('report_controller_func', 120)->nullable();
			$table->enum('status', array('Pending','Inprocess','Completed','Error'))->default('Pending');
			$table->bigInteger('created_by');
			$table->boolean('report_count');
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
		Schema::drop('report_export_task');
	}

}
