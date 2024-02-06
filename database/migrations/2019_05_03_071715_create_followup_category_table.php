<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFollowupCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('followup_category', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->string('name', 120)->nullable();
			$table->string('label_name', 120)->nullable();
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
		Schema::drop('followup_category');
	}

}
