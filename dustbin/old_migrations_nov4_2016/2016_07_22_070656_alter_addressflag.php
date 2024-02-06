<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddressflag extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('addressflag', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `addressflag` CHANGE `type_category` `type_category` ENUM('pay_to_address','primary_address','mailling_address','billing_service','general_information','appeal_address','personal_info_address','gurantor_address','emergency_address','employer_address','attorney_address','patient_contact_address','patient_insurance_address') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL");
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
			DB::statement("ALTER TABLE `addressflag` CHANGE `type_category` `type_category` ENUM('pay_to_address','primary_address','mailling_address','billing_service','general_information','appeal_address','personal_info_address','gurantor_address','emergency_address','employer_address','attorney_address','patient_contact_address') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL");
		});
	}

}
