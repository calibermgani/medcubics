<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailTemplatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('email_templates', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('template_for', 250)->nullable();
			$table->string('subject', 250)->nullable();
			$table->text('content', 65535);
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
		Schema::drop('email_templates');
	}

}
