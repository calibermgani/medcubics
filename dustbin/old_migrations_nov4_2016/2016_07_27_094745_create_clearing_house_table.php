<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClearingHouseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	    Schema::create('clearing_house', function($table) {
            $table->bigIncrements('id')->unsigned();            
            $table->string('name', 50);
            $table->text('description')->nullable();
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
			$table->string('eligibility_web_service_url', 255)->nullable();
			$table->string('eligibility_web_service_user_id', 20)->nullable();
			$table->string('eligibility_web_service_password', 20)->nullable();
			$table->string('eligibility_provider_npi', 11)->nullable();
			$table->text('eligibility_payer_id_link_list')->nullable();
			$table->string('eligibility_call_type', 10)->nullable();
			$table->string('ftp_address', 255)->nullable();
			$table->string('ftp_user_id', 20)->nullable();
			$table->string('ftp_password', 20)->nullable();
			$table->string('ftp_folder', 200)->nullable();
			$table->string('ftp_file_extension_professional', 10)->nullable();
			$table->string('ftp_file_extension_institutional', 10)->nullable();
			$table->enum('status', array('Active','Inactive'));            
            $table->timestamp('created_at')->default("0000-00-00 00:00:00");
            $table->timestamp('updated_at')->default("0000-00-00 00:00:00");
            $table->bigInteger('created_by');
            $table->bigInteger('updated_by');
            $table->timestamp('deleted_at')->nullable();
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
