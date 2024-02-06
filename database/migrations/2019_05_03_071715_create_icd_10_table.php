<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIcd10Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('icd_10', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('icd_type')->nullable();
			$table->integer('order');
			$table->string('icd_code')->nullable();
			$table->string('icdid')->nullable();
			$table->string('header')->nullable();
			$table->string('short_description')->nullable();
			$table->string('medium_description')->nullable();
			$table->text('long_description', 65535)->nullable();
			$table->string('statement_description')->nullable();
			$table->string('sex')->nullable();
			$table->string('age_limit_lower')->nullable();
			$table->string('age_limit_upper')->nullable();
			$table->string('effectivedate')->nullable();
			$table->string('inactivedate')->nullable();
			$table->string('cpt_check')->nullable();
			$table->string('map_to_icd9')->nullable();
			$table->timestamps();
			$table->bigInteger('created_by')->unsigned()->default(1);
			$table->bigInteger('updated_by')->unsigned()->nullable();
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
		Schema::drop('icd_10');
	}

}
