<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProvidersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('providers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('customer_id')->unsigned();
			$table->bigInteger('practice_id');
			$table->text('provider_name', 65535)->nullable();
			$table->text('organization_name', 65535)->nullable();
			$table->string('short_name', 3)->nullable();
			$table->string('last_name', 50)->nullable();
			$table->string('first_name', 50)->nullable();
			$table->string('middle_name', 50)->nullable();
			$table->text('description', 65535)->nullable();
			$table->string('avatar_name', 20)->nullable();
			$table->string('avatar_ext', 5)->nullable();
			$table->integer('provider_types_id');
			$table->date('provider_dob');
			$table->enum('gender', array('Male','Female','Others'));
			$table->string('ssn', 9)->nullable();
			$table->integer('provider_degrees_id');
			$table->string('job_title', 150)->nullable();
			$table->string('address_1', 50)->nullable();
			$table->string('address_2', 50)->nullable();
			$table->string('city', 50)->nullable();
			$table->string('state', 2)->nullable();
			$table->string('zipcode5', 5)->nullable();
			$table->string('zipcode4', 4)->nullable();
			$table->string('phone', 15)->nullable();
			$table->string('phoneext', 4)->nullable();
			$table->string('fax', 15)->nullable();
			$table->string('email', 100)->nullable();
			$table->string('website', 100)->nullable();
			$table->enum('etin_type', array('SSN','TAX ID'));
			$table->string('etin_type_number', 20)->nullable();
			$table->string('npi', 15)->nullable();
			$table->string('tax_id', 15)->nullable();
			$table->integer('speciality_id')->nullable();
			$table->integer('taxanomy_id')->nullable();
			$table->integer('speciality_id2')->nullable();
			$table->integer('taxanomy_id2')->nullable();
			$table->string('statelicense', 100)->nullable();
			$table->string('state_1', 2)->nullable();
			$table->string('statelicense_2', 100)->nullable();
			$table->string('state_2', 2)->nullable();
			$table->string('upin', 25)->nullable();
			$table->string('state_upin', 25)->nullable();
			$table->string('specialitylicense', 100)->nullable();
			$table->string('state_speciality', 2)->nullable();
			$table->string('deanumber', 50)->nullable();
			$table->string('state_dea', 50)->nullable();
			$table->string('tat', 25)->nullable();
			$table->string('mammography', 25)->nullable();
			$table->string('careplan', 25)->nullable();
			$table->string('medicareptan', 15)->nullable();
			$table->string('medicaidid', 15)->nullable();
			$table->string('bcbsid', 15)->nullable();
			$table->string('aetnaid', 25)->nullable();
			$table->string('uhcid', 25)->nullable();
			$table->string('otherid', 25)->nullable();
			$table->string('otherid_ins', 25)->nullable();
			$table->string('otherid2', 25)->nullable();
			$table->string('otherid_ins2', 25)->nullable();
			$table->string('otherid3', 25)->nullable();
			$table->string('otherid_ins3', 25)->nullable();
			$table->enum('req_super', array('Yes','No'));
			$table->string('super_pro')->nullable();
			$table->string('def_billprov')->nullable();
			$table->integer('def_facility');
			$table->enum('stmt_add', array('Pay to Address','Mailing Address','Primary Location'));
			$table->enum('hospice_emp', array('Yes','No'));
			$table->string('digital_sign')->nullable();
			$table->string('digital_sign_name', 20)->nullable();
			$table->string('digital_sign_ext', 5)->nullable();
			$table->enum('status', array('Active','Inactive'));
			$table->integer('practice_db_provider_id');
			$table->enum('provider_entity_type', array('Person','NonPersonEntity'))->default('Person');
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
		Schema::drop('providers');
	}

}
