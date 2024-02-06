<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNpiflagTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('npiflag', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('company_name', 20)->nullable();
			$table->enum('type', array('Practice','Facility','Provider'));
			$table->bigInteger('type_id')->unsigned();
			$table->enum('type_category', array('Group','Individual'));
			$table->string('location_address_1', 50)->nullable();
			$table->string('location_address_2', 50)->nullable();
			$table->string('location_address_type', 10)->nullable();
			$table->string('location_city', 50)->nullable();
			$table->string('location_state', 2)->nullable();
			$table->string('location_country_code', 2)->nullable();
			$table->string('location_country_name', 25)->nullable();
			$table->string('location_postal_code', 10)->nullable();
			$table->string('location_telephone_number', 20)->nullable();
			$table->string('location_fax_number', 20)->nullable();
			$table->string('mailling_address_1', 50)->nullable();
			$table->string('mailling_address_2', 50)->nullable();
			$table->string('mailling_address_type', 10)->nullable();
			$table->string('mailling_city', 50)->nullable();
			$table->string('mailling_state', 2)->nullable();
			$table->string('mailling_country_code', 2)->nullable();
			$table->string('mailling_country_name', 25)->nullable();
			$table->string('mailling_postal_code', 10)->nullable();
			$table->string('mailling_telephone_number', 20)->nullable();
			$table->string('mailling_fax_number', 20)->nullable();
			$table->string('basic_credential', 5)->nullable();
			$table->string('basic_first_name', 25)->nullable();
			$table->string('basic_last_name', 25)->nullable();
			$table->string('basic_middle_name', 25)->nullable();
			$table->enum('basic_gender', array('M','F'))->nullable();
			$table->string('basic_name_prefix', 5)->nullable();
			$table->enum('basic_sole_proprietor', array('Yes','No'))->default('Yes');
			$table->string('basic_authorized_official_credential', 20)->nullable();
			$table->string('basic_authorized_official_first_name', 200)->nullable();
			$table->string('basic_authorized_official_last_name', 150)->nullable();
			$table->string('basic_authorized_official_name_prefix', 20)->nullable();
			$table->string('basic_authorized_official_telephone_number', 25)->nullable();
			$table->string('basic_authorized_official_title_or_position', 30)->nullable();
			$table->text('basic_organization_name', 65535)->nullable();
			$table->string('basic_organizational_subpart', 100)->nullable();
			$table->string('basic_status', 5)->nullable();
			$table->date('basic_enumeration_date');
			$table->date('basic_last_updated');
			$table->bigInteger('created_epoch');
			$table->string('enumeration_type', 15)->nullable();
			$table->string('identifiers_code', 10)->nullable();
			$table->string('identifiers_desc', 25)->nullable();
			$table->string('identifiers_identifier', 10)->nullable();
			$table->string('identifiers_issuer', 25)->nullable();
			$table->string('identifiers_state', 5)->nullable();
			$table->bigInteger('last_updated_epoch');
			$table->integer('number');
			$table->string('taxonomies_code', 20)->nullable();
			$table->string('taxonomies_desc', 25)->nullable();
			$table->string('taxonomies_license', 20)->nullable();
			$table->string('taxonomies_primary', 5)->nullable();
			$table->string('taxonomies_state', 2)->nullable();
			$table->enum('is_valid_npi', array('Yes','No'));
			$table->text('npi_error_message', 65535)->nullable();
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
		Schema::drop('npiflag');
	}

}
