<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContactdetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contactdetails', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('practiceceo', 50)->nullable();
			$table->string('mobileceo', 15)->nullable();
			$table->string('phoneceo', 15)->nullable();
			$table->string('phoneceo_ext', 4)->nullable();
			$table->string('faxceo', 15)->nullable();
			$table->string('emailceo', 100)->nullable();
			$table->string('practicemanager', 50)->nullable();
			$table->string('mobilemanager', 15)->nullable();
			$table->string('phonemanager', 15)->nullable();
			$table->string('phonemanager_ext', 4)->nullable();
			$table->string('faxmanager', 15)->nullable();
			$table->string('emailmanager', 100)->nullable();
			$table->string('companyname', 50)->nullable();
			$table->string('contactperson', 50)->nullable();
			$table->string('address1', 50)->nullable();
			$table->string('address2', 50)->nullable();
			$table->string('city', 50)->nullable();
			$table->string('state', 2)->nullable();
			$table->string('zipcode5', 5)->nullable();
			$table->string('zipcode4', 4)->nullable();
			$table->string('phone', 15)->nullable();
			$table->string('phone_ext', 4)->nullable();
			$table->string('fax', 15)->nullable();
			$table->string('emailid', 100)->nullable();
			$table->string('website', 100)->nullable();
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
		Schema::drop('contactdetails');
	}

}
