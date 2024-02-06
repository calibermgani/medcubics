<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsToClaimsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{		
		DB::statement("ALTER TABLE `claims` ADD `total_paid` DECIMAL(10,2) NOT NULL AFTER `total_charge`;");
		DB::statement("ALTER TABLE `claims` ADD `balance_amt` DECIMAL(10,2) NOT NULL AFTER `total_charge`");
		DB::statement("ALTER TABLE `claims` ADD `total_allowed` DECIMAL(10,2) NOT NULL AFTER `balance_amt`; ");
		DB::statement("ALTER TABLE `claims` CHANGE `status` `status` ENUM('E-bill','Hold','Ready to submit','Patient','Submitted','Paid','P.Paid','Denied', 'Pending') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
		DB::statement("ALTER TABLE `claims` ADD `pateint_paid` DECIMAL(10,2) NULL AFTER `total_paid`;");
    }

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `claims` DROP `total_paid`;");
		DB::statement("ALTER TABLE `claims` DROP `balance_amt`;");
		DB::statement("ALTER TABLE `claims` DROP `total_allowed`; ");
		DB::statement("ALTER TABLE `claims` CHANGE `status` `status` ENUM('E-bill','Hold','Ready to submit','Patient','Submitted','Paid','P.Paid','Denied') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
		DB::statement("ALTER TABLE `claims` DROP `pateint_paid`;");
	}

}
