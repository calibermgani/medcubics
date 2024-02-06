<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClearingHouseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('clearing_house', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('name', 50)->nullable();
			$table->integer('practice_id');
			$table->text('description', 65535)->nullable();
			$table->enum('enable_837', array('Yes','No'));
			$table->string('ISA01', 20)->nullable();
			$table->string('ISA02', 10)->nullable();
			$table->string('ISA03', 20)->nullable();
			$table->string('ISA04', 10)->nullable();
			$table->string('ISA05', 20)->nullable();
			$table->string('ISA06', 15)->nullable();
			$table->string('ISA07', 20)->nullable();
			$table->string('ISA08', 15)->nullable();
			$table->enum('ISA14', array('1','0'));
			$table->enum('ISA15', array('P','T'));
			$table->string('contact_name', 50)->nullable();
			$table->string('contact_phone', 15)->nullable();
			$table->string('contact_fax', 15)->nullable();
			$table->enum('enable_eligibility', array('Yes','No'));
			$table->string('eligibility_ISA02', 10)->nullable();
			$table->string('eligibility_ISA04', 10)->nullable();
			$table->string('eligibility_ISA06', 15)->nullable();
			$table->string('eligibility_ISA08', 15)->nullable();
			$table->string('eligibility_web_service_url')->nullable();
			$table->string('eligibility_web_service_user_id', 20)->nullable();
			$table->string('eligibility_web_service_password', 20)->nullable();
			$table->string('eligibility_provider_npi', 11)->nullable();
			$table->text('eligibility_payer_id_link_list', 65535)->nullable();
			$table->string('eligibility_call_type', 10)->nullable();
			$table->string('ftp_address')->nullable();
			$table->integer('ftp_port');
			$table->string('ftp_user_id', 20)->nullable();
			$table->string('ftp_password', 20)->nullable();
			$table->string('ftp_folder', 200)->nullable();
			$table->string('edi_report_folder', 200);
			$table->string('ftp_file_extension_professional', 10)->nullable();
			$table->string('ftp_file_extension_institutional', 10)->nullable();
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
		Schema::drop('clearing_house');
	}

}
