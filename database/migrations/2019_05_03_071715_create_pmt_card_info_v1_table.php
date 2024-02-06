<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePmtCardInfoV1Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pmt_card_info_v1', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->enum('card_type', array('Visa Card','Master Card','Maestro Card','Gift Card'));
			$table->string('card_first_4', 4)->nullable();
			$table->string('card_center', 4)->nullable();
			$table->string('card_last_4', 4)->nullable();
			$table->string('name_on_card', 25)->nullable();
			$table->date('expiry_date');
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
		Schema::drop('pmt_card_info_v1');
	}

}
