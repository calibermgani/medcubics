<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePrivateMessageDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('private_message_details', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('message_id');
			$table->bigInteger('parent_message_id');
			$table->bigInteger('send_user_id')->unsigned();
			$table->bigInteger('user_id')->unsigned();
			$table->enum('recipient_read', array('0','1'));
			$table->dateTime('recipient_read_time');
			$table->enum('recipient_deleted', array('0','1','2'));
			$table->enum('sender_deleted', array('0','1','2'));
			$table->dateTime('sender_deleted_time');
			$table->dateTime('recipient_deleted_time');
			$table->integer('label_list_type');
			$table->enum('recipient_stared', array('0','1'));
			$table->enum('sender_stared', array('0','1'));
			$table->bigInteger('recipient_category_id');
			$table->bigInteger('send_category_id');
			$table->bigInteger('label_category_id');
			$table->bigInteger('trash_category_id');
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
		Schema::drop('private_message_details');
	}

}
