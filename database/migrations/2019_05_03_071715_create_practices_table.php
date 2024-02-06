<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePracticesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('practices', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('customer_id')->unsigned();
			$table->string('practice_name', 100)->nullable();
			$table->text('practice_description', 65535)->nullable();
			$table->string('email', 50)->nullable();
			$table->string('timezone', 100)->nullable();
			$table->string('hostname', 15)->nullable();
			$table->string('hostpassword', 200)->nullable();
			$table->string('ipaddress', 50)->nullable();
			$table->string('website', 50)->nullable();
			$table->string('facebook', 100)->nullable();
			$table->string('twitter', 100)->nullable();
			$table->string('phone', 20)->nullable();
			$table->string('phoneext', 4)->nullable();
			$table->string('fax', 20)->nullable();
			$table->string('avatar_name', 20)->nullable();
			$table->string('avatar_ext', 5)->nullable();
			$table->string('practice_link', 150)->nullable();
			$table->string('doing_business_s', 100)->nullable();
			$table->integer('speciality_id');
			$table->integer('taxanomy_id');
			$table->integer('language_id');
			$table->enum('entity_type', array('Group','Individual'));
			$table->enum('billing_entity', array('No','Yes'));
			$table->integer('tax_id');
			$table->integer('group_tax_id');
			$table->integer('npi');
			$table->integer('group_npi');
			$table->string('medicare_ptan', 20)->nullable();
			$table->string('medicaid', 20)->nullable();
			$table->string('bcbs_id', 15)->nullable();
			$table->string('mail_add_1', 50)->nullable();
			$table->string('mail_add_2', 50)->nullable();
			$table->string('mail_city', 50)->nullable();
			$table->string('mail_state', 2)->nullable();
			$table->string('mail_zip5', 5)->nullable();
			$table->string('mail_zip4', 4)->nullable();
			$table->string('pay_add_1', 50)->nullable();
			$table->string('pay_add_2', 50)->nullable();
			$table->string('pay_city', 50)->nullable();
			$table->string('pay_state', 2)->nullable();
			$table->string('pay_zip5', 5)->nullable();
			$table->string('pay_zip4', 4)->nullable();
			$table->string('primary_add_1', 50)->nullable();
			$table->string('primary_add_2', 50)->nullable();
			$table->string('primary_city', 50)->nullable();
			$table->string('primary_state', 2)->nullable();
			$table->string('primary_zip5', 5)->nullable();
			$table->string('primary_zip4', 4)->nullable();
			$table->string('monday_forenoon', 10)->nullable();
			$table->string('monday_afternoon', 10)->nullable();
			$table->string('tuesday_forenoon', 10)->nullable();
			$table->string('tuesday_afternoon', 10)->nullable();
			$table->string('wednesday_forenoon', 10)->nullable();
			$table->string('wednesday_afternoon', 10)->nullable();
			$table->string('thursday_forenoon', 10)->nullable();
			$table->string('thursday_afternoon', 10)->nullable();
			$table->string('friday_forenoon', 10)->nullable();
			$table->string('friday_afternoon', 10)->nullable();
			$table->string('saturday_forenoon', 10)->nullable();
			$table->string('saturday_afternoon', 10)->nullable();
			$table->string('sunday_forenoon', 10)->nullable();
			$table->string('sunday_afternoon', 10)->nullable();
			$table->bigInteger('practice_db_id');
			$table->string('api_ids')->nullable();
			$table->enum('backDate', array('Yes','No'))->default('No');
			$table->enum('status', array('In Progress','Active','Inactive'));
			$table->timestamps();
			$table->bigInteger('created_by')->unsigned();
			$table->bigInteger('updated_by')->unsigned();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('practices');
	}

}
