<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategorizePrivateMessageDetailsTable extends Migration {

	
	public function up()
	{
		Schema::table('private_message_details', function(Blueprint $table)
		{
			$table->bigInteger('recipient_category_id')->after('sender_stared');
			$table->bigInteger('send_category_id')->after('recipient_category_id');
			$table->bigInteger('label_category_id')->after('send_category_id');
			$table->bigInteger('trash_category_id')->after('label_category_id');
		});	
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('private_message_details', function(Blueprint $table)
		{
			$table->dropColumn('recipient_category_id');
            $table->dropColumn('send_category_id');
            $table->dropColumn('label_category_id');
            $table->dropColumn('trash_category_id');
		});
	}

}
