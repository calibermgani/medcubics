<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEdiReports extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edi_reports', function($table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('file_name', 250)->nullable();
            $table->text('file_path')->nullable();
            $table->enum('is_read', array('No','Yes'));
            $table->enum('is_archive', array('No','Yes'));
            $table->bigInteger('created_by');
            $table->timestamp('created_at')->default("0000-00-00 00:00:00");
            $table->timestamp('updated_at')->default("0000-00-00 00:00:00");            
            $table->timestamp('deleted_at')->nullable();
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
