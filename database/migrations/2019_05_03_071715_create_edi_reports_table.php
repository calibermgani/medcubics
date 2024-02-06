<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEdiReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edi_reports', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('file_name', 250)->nullable();
			$table->text('file_path', 65535)->nullable();
			$table->date('file_created_date');
			$table->string('file_type', 120)->nullable();
			$table->string('file_size', 120)->nullable();
			$table->enum('is_read', array('No','Yes'));
			$table->enum('is_archive', array('No','Yes'));
			$table->bigInteger('created_by')->unsigned();
			$table->timestamps();
			$table->softDeletes();
			$table->date('server_file_delete_date');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('edi_reports');
	}

}
