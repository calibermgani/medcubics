<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMessageDetailStructureTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('message_detail_structure', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->integer('message_id');
			$table->integer('parent_message_id');
			$table->integer('label_id')->default(0);
			$table->integer('sender_id');
			$table->integer('receiver_id');
			$table->integer('draft_message')->default(0);
			$table->integer('read_status')->default(0);
			$table->integer('sender_trash_status')->default(0);
			$table->integer('receiver_trash_status')->default(0);
			$table->string('attachment_file', 220)->nullable();
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
		Schema::drop('message_detail_structure');
	}

}
