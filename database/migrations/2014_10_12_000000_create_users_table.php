<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('customer_id')->unsigned();
			$table->integer('role_id');
			$table->string('name', 100)->nullable();
			$table->string('short_name', 5)->nullable();
			$table->string('email', 100)->nullable();
			$table->string('password', 60);
			$table->dateTime('password_change_time');
			$table->enum('user_type', array('Practice','Medcubics'));
			$table->dateTime('last_access_date');
			$table->enum('practice_user_type', array('customer','practice_admin','practice_user'));
			$table->text('admin_practice_id', 65535)->nullable();
			$table->string('firstname', 100)->nullable();
			$table->string('lastname', 100)->nullable();
			$table->date('dob');
			$table->enum('gender', array('Male','Female','Others'));
			$table->string('designation', 100)->nullable();
			$table->enum('status', array('Active','Inactive'))->default('Active');
			$table->string('department', 100)->nullable();
			$table->integer('language_id');
			$table->integer('ethnicity_id');
			$table->string('addressline1', 50)->nullable();
			$table->string('addressline2', 50)->nullable();
			$table->string('city', 50)->nullable();
			$table->string('state', 2)->nullable();
			$table->string('zipcode5', 5)->nullable();
			$table->string('zipcode4', 4)->nullable();
			$table->string('phone', 20)->nullable();
			$table->string('fax', 20)->nullable();
			$table->string('facebook_ac', 250)->nullable();
			$table->string('twitter', 250)->nullable();
			$table->string('linkedin', 250)->nullable();
			$table->string('googleplus', 250)->nullable();
			$table->string('remember_token', 100)->nullable();
			$table->dateTime('reset_start_date')->nullable();
			$table->string('token', 250);
			$table->float('maximum_document_uploadsize', 10, 4);
			$table->string('avatar_name', 50)->nullable();
			$table->string('avatar_ext', 50)->nullable();
			$table->enum('is_logged_in', array('0','1'));
			$table->integer('login_attempt')->unsigned();
			$table->dateTime('attempt_updated')->nullable();
			$table->timestamps();
			$table->bigInteger('created_by')->unsigned();
			$table->bigInteger('updated_by')->unsigned();
			$table->softDeletes();
			$table->enum('useraccess', array('app','web'));
			$table->integer('practice_access_id');
			$table->integer('facility_access_id');
			$table->integer('provider_access_id');
			$table->enum('app_name', array('CHARGECAPTURE','WEB','PATIENTINTAKE'));
			$table->unique(['email','short_name']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
