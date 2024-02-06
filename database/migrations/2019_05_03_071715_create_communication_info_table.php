<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommunicationInfoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('communication_info', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('sid', 100)->nullable();
			$table->string('com_provider', 10)->nullable();
			$table->string('from', 20)->nullable();
			$table->string('to', 20)->nullable();
			$table->enum('direction', array('Incoming','Outgoing','Outbound-Dial'));
			$table->bigInteger('patient_id')->unsigned();
			$table->bigInteger('claim_id')->unsigned();
			$table->enum('com_type', array('Phone','Robo','Sms','Fax'));
			$table->dateTime('start_time');
			$table->string('duration', 20)->nullable();
			$table->string('status', 20)->nullable();
			$table->decimal('cost', 10);
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
		Schema::drop('communication_info');
	}

}
