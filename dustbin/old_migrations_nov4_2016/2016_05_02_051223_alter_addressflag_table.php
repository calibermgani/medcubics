<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddressflagTable extends Migration {

	public function up()
	{
		Schema::table('addressflag', function($table)
		{
			DB::statement("ALTER TABLE `addressflag` CHANGE `type_category` `type_category` ENUM('pay_to_address','primary_address','mailling_address','billing_service','general_information','appeal_address','personal_info_address','gurantor_address','emergency_address','employer_address','attorney_address','patient_contact_address') ");
		});		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('addressflag', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `addressflag` CHANGE `type_category` `type_category` ENUM('pay_to_address','primary_address','mailling_address','billing_service','general_information','appeal_address','personal_info_address','gurantor_address','emergency_address','employer_address','attorney_address')");
		});
	}

}
