<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClaimsAddEdinoteFields extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `claims` ADD `edi_notes` TEXT NULL AFTER `is_claim_sentas_first`, ADD `payer_claim_number` VARCHAR(200) NULL AFTER `edi_notes`;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `claims` DROP `edi_notes`, DROP `payer_claim_number`;");
	}

}
