<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuperbillsTemplateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::dropIfExists('superbill_template');
		Schema::create('superbill_template',function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('template_name',50);
			$table->integer('provider_id');
			$table->text('header_list');
			$table->enum('status', ['Active', 'Inactive']);
			$table->text('get_list_order');
			$table->text('order_header');
			$table->text('header_style');
			$table->text('office_visit');
			$table->text('office_procedures');
			$table->text('laboratory');
			$table->text('well_visit');
			$table->text('medicare_preventive_services');
			$table->text('skin_procedures');
			$table->text('consultation_preop_clearance');
			$table->text('vaccines');
			$table->text('medications');
			$table->text('other_services');
			$table->text('skin_procedures_units');
			$table->text('medications_units');
			$table->timestamp('created_at');
			$table->timestamp('updated_at');
			$table->integer('created_by');
			$table->integer('updated_by');
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
		Schema::drop('superbill_template');
	}

}