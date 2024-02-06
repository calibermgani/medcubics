<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSuperbillTemplateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('superbill_template', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('template_name', 50)->nullable();
			$table->bigInteger('provider_id')->unsigned();
			$table->text('header_list', 65535)->nullable();
			$table->enum('status', array('Active','Inactive'));
			$table->text('get_list_order', 65535)->nullable();
			$table->text('order_header', 65535)->nullable();
			$table->text('header_style', 65535)->nullable();
			$table->text('office_visit', 65535)->nullable();
			$table->text('office_procedures', 65535)->nullable();
			$table->text('laboratory', 65535);
			$table->text('well_visit', 65535)->nullable();
			$table->text('medicare_preventive_services', 65535)->nullable();
			$table->text('skin_procedures', 65535)->nullable();
			$table->text('consultation_preop_clearance', 65535)->nullable();
			$table->text('vaccines', 65535)->nullable();
			$table->text('medications', 65535)->nullable();
			$table->text('other_services', 65535)->nullable();
			$table->text('skin_procedures_units', 65535)->nullable();
			$table->text('medications_units', 65535)->nullable();
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
		Schema::drop('superbill_template');
	}

}
