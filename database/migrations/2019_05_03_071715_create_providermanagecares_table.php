<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProvidermanagecaresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('providermanagecares', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('insurance_id');
			$table->bigInteger('providers_id')->unsigned();
			$table->enum('enrollment', array('Par','Non-Par'));
			$table->enum('entitytype', array('Group','Individual'));
			$table->string('provider_id', 20)->nullable();
			$table->date('effectivedate');
			$table->date('terminationdate');
			$table->string('feeschedule', 20)->nullable();
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
		Schema::drop('providermanagecares');
	}

}
