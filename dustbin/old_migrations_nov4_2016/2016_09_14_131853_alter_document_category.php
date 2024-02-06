<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDocumentCategory extends Migration {

	
	public function up()
	{
		Schema::table('document_categories', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `document_categories` ADD `created_by` BIGINT(20) NOT NULL AFTER `created_at`;");
			DB::statement("ALTER TABLE `document_categories` ADD `updated_by` BIGINT(20) NOT NULL AFTER `updated_at`");
		});
	}

	
	public function down()
	{
		//
	}

}
