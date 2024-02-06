<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCptsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cpts', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('short_description', 28)->nullable();
			$table->string('medium_description', 100)->nullable();
			$table->text('long_description', 65535);
			$table->boolean('print_statedesc');
			$table->string('cpt_hcpcs', 30)->nullable();
			$table->string('procedure_category', 120)->nullable();
			$table->string('code_type', 50)->nullable();
			$table->string('type_of_service', 50)->nullable();
			$table->integer('pos_id')->nullable();
			$table->enum('applicable_sex', array('','Male','Female','Others'));
			$table->enum('referring_provider', array('Yes','No'))->default('No');
			$table->string('age_limit', 3)->nullable();
			$table->decimal('allowed_amount', 15)->nullable();
			$table->decimal('billed_amount', 15)->nullable();
			$table->string('modifier_id', 100)->nullable();
			$table->string('revenue_code', 15)->nullable();
			$table->string('drug_name')->nullable();
			$table->string('ndc_number', 15)->nullable();
			$table->string('min_units', 10)->nullable();
			$table->string('max_units', 10)->nullable();
			$table->string('anesthesia_unit', 15)->nullable();
			$table->string('service_id_qualifier', 15)->nullable();
			$table->string('medicare_global_period', 3)->nullable();
			$table->enum('required_clia_id', array('Yes','No'));
			$table->string('clia_id', 15)->nullable();
			$table->string('icd', 100)->nullable();
			$table->decimal('work_rvu', 15)->nullable();
			$table->decimal('facility_practice_rvu', 15)->nullable();
			$table->decimal('nonfacility_practice_rvu', 15)->nullable();
			$table->decimal('pli_rvu', 15)->nullable();
			$table->decimal('total_facility_rvu', 15)->nullable();
			$table->decimal('total_nonfacility_rvu', 15)->nullable();
			$table->date('effectivedate')->nullable();
			$table->date('terminationdate')->nullable();
			$table->enum('unit_code', array('F2','GR','ME','ML','UN'));
			$table->string('unit_cpt', 25)->nullable();
			$table->string('unit_ndc', 25)->nullable();
			$table->float('unit_value', 10, 0)->nullable();
			$table->enum('status', array('Active','Inactive'));
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
		Schema::drop('cpts');
	}

}
