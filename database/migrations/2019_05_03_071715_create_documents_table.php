<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDocumentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('documents', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('practice_id');
			$table->bigInteger('type_id');
			$table->enum('document_type', array('practice','facility','provider','insurance','cpt','patients','patient_document','payments'))->nullable();
			$table->enum('document_sub_type', array('','managed_care','overrides','insurance','Authorization'));
			$table->enum('clinical_note', array('No','Yes'))->default('No');
			$table->bigInteger('main_type_id');
			$table->string('temp_type_id', 320)->nullable();
			$table->bigInteger('claim_id')->nullable();
			$table->bigInteger('facility_id')->nullable();
			$table->bigInteger('provider_id')->unsigned()->nullable();
			$table->integer('payment_id');
			$table->date('dos')->nullable();
			$table->enum('upload_type', array('webcam','browse','scanner'))->nullable();
			$table->string('document_path', 300)->nullable();
			$table->string('document_extension', 100)->nullable();
			$table->string('document_domain', 300)->nullable();
			$table->string('title', 100)->nullable();
			$table->string('description')->nullable();
			$table->string('category', 320)->nullable();
			$table->bigInteger('document_categories_id')->nullable();
			$table->string('filename')->nullable();
			$table->float('filesize', 10, 4);
			$table->bigInteger('page');
			$table->string('payer', 200)->nullable();
			$table->string('checkno', 100)->nullable();
			$table->date('checkdate');
			$table->float('checkamt', 10);
			$table->string('user_email', 100)->nullable();
			$table->string('claim_number_data', 100)->nullable();
			$table->string('mime', 50)->nullable();
			$table->string('original_filename')->nullable();
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
		Schema::drop('documents');
	}

}
