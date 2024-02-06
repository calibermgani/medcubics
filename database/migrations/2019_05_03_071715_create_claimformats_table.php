<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClaimformatsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('claimformats', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('claim_format', 50)->nullable();
			$table->timestamps();
			$table->bigInteger('created_by');
			$table->bigInteger('updated_by');
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
		Schema::drop('claimformats');
	}

}
