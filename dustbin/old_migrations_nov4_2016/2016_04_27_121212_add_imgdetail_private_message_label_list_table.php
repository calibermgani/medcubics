<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImgdetailPrivateMessageLabelListTable extends Migration {

	
	public function up()
	{
		Schema::table('private_message_label_list', function(Blueprint $table)
		{
			$table->string('label_color', 10)->after('label_id');
			$table->string('label_image', 255)->after('label_color');
		});	
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('private_message_label_list', function(Blueprint $table)
		{
			$table->dropColumn('label_color');
            $table->dropColumn('label_image');
		});
	}

}
