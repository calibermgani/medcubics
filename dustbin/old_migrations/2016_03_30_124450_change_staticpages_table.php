<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeStaticpagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('staticpages', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `staticpages` CHANGE `type` `type` ENUM('customer','insurance','insurance_types','modifiers','codes','cpt','icd','speciality','taxanomy','pos','id_qualifier','provider_degree','role','admin_user','user_activity','practice','facility','provider','edi','employer','codes','templates','fee_schedule','help','scheduler','registration','superbills','hold_option','charges','payments','claims','documents','reports','messages','patient_registration','patient_appointments','eligibility','e-superbills','billing','referral','ledger','problem_list','task_list','correspondence','patient_reports','user','note','patients') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('staticpages', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `staticpages` CHANGE `type` `type` ENUM('customer','insurance','insurance_types','modifiers','codes','cpt','icd','speciality','taxanomy','pos','id_qualifier','provider_degree','role','admin_user','user_activity','practice','facility','provider','edi','employer','codes','templates','fee_schedule','help','scheduler','registration','superbills','hold_option','charges','payments','claims','documents','reports','messages','patient_registration','patient_appointments','eligibility','e-superbills','billing','referral','ledger','problem_list','task_list','correspondence','patient_reports','user','note') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL");
		});
	}

}
