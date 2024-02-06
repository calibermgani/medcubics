<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFildsToClaims extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `claims` ADD `cmsform` VARCHAR(255) NOT NULL AFTER `claim_ids`");
		DB::statement("ALTER TABLE `claims` ADD `document_path` VARCHAR(255) NOT NULL AFTER `cmsform`");
		DB::statement("ALTER TABLE `claims` ADD `document_domain` VARCHAR(255) NOT NULL AFTER `document_path`;");
		DB::statement("ALTER TABLE `claims` ADD `localfilename` VARCHAR(255) NOT NULL AFTER `document_domain`;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `claims` DROP COLUMN `cmsform`");
		DB::statement("ALTER TABLE `claims` DROP COLUMN `document_path`");
		DB::statement("ALTER TABLE `claims` DROP COLUMN `document_domain`");
		DB::statement("ALTER TABLE `claims` DROP COLUMN `localfilename`");
	}

}
