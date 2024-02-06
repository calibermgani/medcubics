<?php
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		//$this->call('ApiConfigSeeder');
		$this->call('UserSeeder');
    	DB::table('practice_registration')->truncate(); 
    	DB::table('practice_registration')->insert(array(            
	        array(
	            'id' => 1,
				'email_id'=> 1,
				'driving_license'=> 1, 
				'ethnicity'=> 1, 
				'race'=> 1, 
				'preferred_language'=> 1,
				'marital_status'=> 1,
				'student_status'=> 1,
				'primary_care_provider'=> 1,
				'primary_facility'=> 1,
				'send_email_notification'=> 1,
				'auto_phone_call_reminder'=> 1,
				'preferred_communication'=> 1,
				'insured_ssn'=> 1, 
				'insured_dob'=> 1,
				'group_name'=> 1,
				'group_id'=> 1,
				'adjustor_ph'=> 1,
				'adjustor_fax'=> 1,
				'guarantor'=> 1,
				'emergency_contact'=> 1,
				'employer'=> 1,
				'attorney'=> 1,
				'requested_date'=> 1,
				'contact_person'=> 1,
				'alert_on_appointment'=> 1,
				'allowed_visit'=> 1,
				'visits_used'=> 1,
				'alert_on_visit_remains'=> 1,
				'visit_remaining'=> 1,
				'work_phone'=> 1,
				'alert_on_billing'=> 1,
				'total_allowed_amount'=> 1,
				'amount_used'=> 1,
				'amount_remaining'=> 1,
				'documents'=> 1,
				'notes'=> 1,
				'created_by' => 0,
	            'updated_by' => 0,
				'created_at' => '0000-00-00 00:00:00',
	            'updated_at' => '0000-00-00 00:00:00',
	            'deleted_at' => NULL,
	        )
	    ));
		/*Model::unguard();	
		$seeder_name = getenv('DB_DATABASE').'TableSeeder';
		$this->call($seeder_name);*/
	}

}
