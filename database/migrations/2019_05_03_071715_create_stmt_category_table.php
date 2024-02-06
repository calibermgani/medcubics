<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStmtCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stmt_category', function(Blueprint $table)
		{
			$table->increments('id');
			$table->enum('stmt_option', array('Yes','Hold','Insurance Only'));
			$table->string('category', 120)->nullable();
			$table->bigInteger('hold_reason');
			$table->date('hold_release_date')->nullable();
			$table->enum('status', array('Active','Inactive'));
			$table->bigInteger('created_by')->unsigned();
			$table->bigInteger('updated_by')->unsigned();
			$table->softDeletes();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('stmt_category');
	}

}
