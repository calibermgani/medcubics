<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFollowupQuestionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('followup_question', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->text('question', 65535)->nullable();
			$table->text('question_label', 65535)->nullable();
			$table->string('hint', 120)->nullable();
			$table->integer('category_id');
			$table->enum('field_type', array('','date','number','text'));
			$table->enum('field_validation', array('number','text','both','phone_number'));
			$table->enum('date_type', array('','single_date','double_date'));
			$table->enum('status', array('Active','Inactive'));
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
		Schema::drop('followup_question');
	}

}
