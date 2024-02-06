<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDocumentFollowupListTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('document_followup_list', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('document_id');
			$table->integer('patient_id');
			$table->bigInteger('claim_id');
			$table->integer('assigned_user_id');
			$table->enum('priority', array('High','Moderate','Low'));
			$table->date('followup_date');
			$table->enum('status', array('Assigned','Inprocess','Completed','Pending','Review'));
			$table->enum('Assigned_status', array('Active','Inactive'))->default('Active');
			$table->text('notes', 65535);
			$table->integer('page');
			$table->integer('created_by');
			$table->timestamps();
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
		Schema::drop('document_followup_list');
	}

}
