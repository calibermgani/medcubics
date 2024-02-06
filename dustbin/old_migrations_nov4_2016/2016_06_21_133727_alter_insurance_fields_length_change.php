<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterInsuranceFieldsLengthChange extends Migration {

	public function up()
	{
		Schema::table('cpts', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `cpts` DROP `statement_description`");
			DB::statement("ALTER TABLE `cpts` CHANGE `applicable_sex` `applicable_sex` ENUM('Male','Female','Others') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
			DB::statement("ALTER TABLE `cpts` CHANGE `revenue_code` `revenue_code` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
			DB::statement("ALTER TABLE `cpts` CHANGE `ndc_number` `ndc_number` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
			DB::statement("ALTER TABLE `cpts` CHANGE `anesthesia_unit` `anesthesia_unit` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
			DB::statement("ALTER TABLE `cpts` CHANGE `service_id_qualifier` `service_id_qualifier` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
			DB::statement("ALTER TABLE `cpts` CHANGE `clia_id` `clia_id` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
		});
	}

	public function down()
	{
		Schema::table('cpts', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `cpts` DROP `statement_description`");
			DB::statement("ALTER TABLE `cpts` CHANGE `applicable_sex` `applicable_sex` ENUM('Male','Female','Others') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
			DB::statement("ALTER TABLE `cpts` CHANGE `revenue_code` `revenue_code` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
			DB::statement("ALTER TABLE `cpts` CHANGE `ndc_number` `ndc_number` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
			DB::statement("ALTER TABLE `cpts` CHANGE `anesthesia_unit` `anesthesia_unit` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
			DB::statement("ALTER TABLE `cpts` CHANGE `service_id_qualifier` `service_id_qualifier` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
			DB::statement("ALTER TABLE `cpts` CHANGE `clia_id` `clia_id` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
		});
	}

}
