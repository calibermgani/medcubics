<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateModifiersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('modifiers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('modifiers_type_id');
			$table->string('code', 2)->nullable();
			$table->text('description', 65535);
			$table->string('name')->nullable();
			$table->string('anesthesia_base_unit', 6)->nullable();
			$table->enum('status', array('Active','Inactive','Deleted'));
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
		Schema::drop('modifiers');
	}

}
