<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStaticpagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('staticpages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->enum('type', array('customer','insurance','insurance_types','modifiers','codes','cpt','icd','speciality','taxanomy','pos','id_qualifier','provider_degree','role','admin_user','user_activity','practice','facility','provider','edi','employer','templates','fee_schedule','help','scheduler','registration','superbills','hold_option','charges','payments','claims','documents','reports','messages','patient_registration','patient_appointments','eligibility','e-superbills','billing','referral','ledger','problem_list','task_list','correspondence','patient_reports','user','note','patients','claim_report','charge_report','payment_report','adjustment_report','refund_report','appointment_report','profile'))->nullable();
			$table->string('title', 50)->nullable();
			$table->string('slug', 50)->nullable();
			$table->text('content', 65535)->nullable();
			$table->enum('status', array('Active','Inactive'));
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
		Schema::drop('staticpages');
	}

}
