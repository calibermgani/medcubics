<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CptDatatyeChange extends Migration {
	public function up()
	{
		DB::statement("ALTER TABLE `cpts` DROP `medicare_allowable`, DROP `print_shortdesc`, DROP `print_mediumdesc`, DROP `print_longdesc`, DROP `onlypatient`, DROP `hipaa`");
		DB::statement("ALTER TABLE `cpts` CHANGE `work_rvu` `work_rvu` DECIMAL(15,2) NULL");
		DB::statement("ALTER TABLE `cpts` CHANGE `facility_practice_rvu` `facility_practice_rvu` DECIMAL(15,2) NULL");
		DB::statement("ALTER TABLE `cpts` CHANGE `nonfacility_practice_rvu` `nonfacility_practice_rvu` DECIMAL(15,2) NULL");
		DB::statement("ALTER TABLE `cpts` CHANGE `pli_rvu` `pli_rvu` DECIMAL(15,2) NULL");
		DB::statement("ALTER TABLE `cpts` CHANGE `total_facility_rvu` `total_facility_rvu` DECIMAL(15,2) NULL");
		DB::statement("ALTER TABLE `cpts` CHANGE `total_nonfacility_rvu` `total_nonfacility_rvu` DECIMAL(15,2) NULL");
	}

	public function down()
	{
		DB::statement("ALTER TABLE `cpts` CHANGE `work_rvu` `work_rvu` VARCHAR(6) NOT NULL");
		DB::statement("ALTER TABLE `cpts` CHANGE `facility_practice_rvu` `facility_practice_rvu` VARCHAR(6) NOT NULL");
		DB::statement("ALTER TABLE `cpts` CHANGE `nonfacility_practice_rvu` `nonfacility_practice_rvu` VARCHAR(6) NOT NULL");
		DB::statement("ALTER TABLE `cpts` CHANGE `pli_rvu` `pli_rvu` VARCHAR(6) NOT NULL");
		DB::statement("ALTER TABLE `cpts` CHANGE `total_facility_rvu` `total_facility_rvu` VARCHAR(6) NOT NULL");
		DB::statement("ALTER TABLE `cpts` CHANGE `total_nonfacility_rvu` `total_nonfacility_rvu` VARCHAR(6) NOT NULL");
	}

}
