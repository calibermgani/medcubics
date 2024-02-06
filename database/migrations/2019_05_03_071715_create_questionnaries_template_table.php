<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQuestionnariesTemplateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('questionnaries_template', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('template_id')->unsigned();
			$table->string('title')->nullable();
			$table->text('question', 65535)->nullable();
			$table->enum('answer_type', array('text','radio','checkbox'))->nullable();
			$table->integer('question_order');
			$table->bigInteger('created_by')->unsigned();
			$table->bigInteger('updated_by')->unsigned();
			$table->timestamps();
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
		Schema::drop('questionnaries_template');
	}

}
