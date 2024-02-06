<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePracticemanagecaresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('practicemanagecares', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('practice_id');
			$table->integer('insurance_id');
			$table->bigInteger('providers_id')->unsigned();
			$table->enum('enrollment', array('Par','Non-Par'));
			$table->enum('entitytype', array('Group','Individual'));
			$table->string('provider_id', 20)->nullable();
			$table->date('effectivedate');
			$table->date('terminationdate');
			$table->string('feeschedule')->nullable();
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
		Schema::drop('practicemanagecares');
	}

}
