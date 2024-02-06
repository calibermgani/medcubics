<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAddressflagTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('addressflag', function(Blueprint $table)
		{
			$table->increments('id');
			$table->enum('address_company', array('usps'));
			$table->enum('type', array('patients','practice','facility','provider','insurance','employer','adminuser','customer','customerusers','patientstatementsettings'))->nullable();
			$table->bigInteger('type_id')->unsigned();
			$table->enum('type_category', array('pay_to_address','primary_address','mailling_address','billing_service','general_information','appeal_address','personal_info_address','gurantor_address','emergency_address','employer_address','attorney_address','patient_contact_address','patient_insurance_address'))->nullable();
			$table->string('address2', 25)->nullable();
			$table->string('city', 25)->nullable();
			$table->string('state', 2)->nullable();
			$table->integer('zip5')->nullable();
			$table->integer('zip4')->nullable();
			$table->enum('is_address_match', array('Yes','No'))->default('Yes');
			$table->string('error_message', 50)->nullable();
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
		Schema::drop('addressflag');
	}

}
