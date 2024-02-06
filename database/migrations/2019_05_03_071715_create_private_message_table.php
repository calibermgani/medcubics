<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePrivateMessageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('private_message', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('message_id', 240)->nullable();
			$table->string('subject', 420)->nullable();
			$table->text('message_body', 65535)->nullable();
			$table->bigInteger('recipient_users_id')->unsigned();
			$table->bigInteger('send_user_id')->unsigned();
			$table->string('attachment_file', 420)->nullable();
			$table->enum('draft_message', array('0','1'));
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
		Schema::drop('private_message');
	}

}
