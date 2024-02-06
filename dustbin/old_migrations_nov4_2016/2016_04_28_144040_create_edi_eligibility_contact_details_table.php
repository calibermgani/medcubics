<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEdiEligibilityContactDetailsTable extends Migration {

	public function up()
	{					
		Schema::create('edi_eligibility_contact_details', function(Blueprint $table)
		{
			$table->increments('id');
			$table->enum('details_for', array('insurance_sp'));
                $table->integer('details_for_id');
                $table->text('entity_code');
                $table->string('last_name', 50);
                $table->string('first_name', 50);
                $table->string('identification_type', 200);
                $table->string('identification_code', 50);	
                $table->string('address1', 50);
                $table->string('address2', 50);
                $table->string('city', 50);
                $table->string('state', 2);
                $table->integer('zip5');
                $table->integer('zip4');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
		});
	}

	public function down()
	{
		Schema::drop('edi_eligibility_contact_details');
	}

}
