<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('customers', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('customer_name')->nullable();
			$table->string('short_name', 5)->nullable();
			$table->text('customer_desc', 65535);
			$table->enum('customer_type', array('Billing','Provider'));
			$table->string('contact_person', 50)->nullable();
			$table->string('designation', 50)->nullable();
			$table->string('email', 100)->nullable();
			$table->enum('gender', array('Male','Female','Other'));
			$table->string('addressline1', 50)->nullable();
			$table->string('firstname', 50)->nullable();
			$table->string('lastname', 50)->nullable();
			$table->string('addressline2', 50)->nullable();
			$table->string('phone', 20)->nullable();
			$table->string('phoneext', 4)->nullable();
			$table->string('mobile', 20)->nullable();
			$table->string('fax', 20)->nullable();
			$table->string('city', 50)->nullable();
			$table->string('state', 2)->nullable();
			$table->string('zipcode5', 5)->nullable();
			$table->string('zipcode4', 4)->nullable();
			$table->enum('status', array('Active','Inactive'));
			$table->string('avatar_name', 50)->nullable();
			$table->string('avatar_ext', 50)->nullable();
			$table->timestamps();
			$table->bigInteger('created_by')->unsigned();
			$table->bigInteger('updated_by')->unsigned();
			$table->softDeletes();
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
		Schema::drop('customers');
	}

}
