<?php
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ApiConfigSeeder extends Seeder{
	public function run()
	{
		DB::table('practice_api_configs')->truncate(); 
		\App\Models\Medcubics\ApiConfig::create(array(
	        'api_for' => 'address',
	        'api_name' => 'usps',
			'category' => 'USPS',
			'api_status' => 'Actice',
	        'url' => 'http://testing.shippingapis.com/ShippingAPITest.dll?API=Verify&XML=',
	        'usps_user_id' => '901ANNEX4342'
	    ));
		
		\App\Models\Medcubics\ApiConfig::create(array(
	        'api_for' => 'npi',
	        'api_name' => 'npi',
			'category' => 'NPI',
			'api_status' => 'Actice',
	        'url' => 'https://npiregistry.cms.hhs.gov/api/?number='
	    ));
		
		\App\Models\Medcubics\ApiConfig::create(array(
	        'api_for' => 'imo_cpt',
	        'api_name' => 'imo_cpt',
			'api_status' => 'Actice',
			'category' => 'IMO CPT',
			'host'	=>	'127.0.0.1',
			'port'	=>	'42045',
	        'url' => 'http://sandbox-wps.e-imo.com/IMOTPWS.asmx?op=Execute',
	        'usps_user_id' => 'cfa88e95c719659e'
	    ));
		
		\App\Models\Medcubics\ApiConfig::create(array(
	        'api_for' => 'imo_icd',
	        'api_name' => 'imo_icd',
			'api_status' => 'Actice',
			'category' => 'IMO ICD',
			'host'	=>	'127.0.0.1',
			'port'	=>	'42011',
	        'url' => 'http://sandbox-wps.e-imo.com/IMOTPWS.asmx?op=Execute',
	        'usps_user_id' => 'cfa88e95c719659e'
	    ));
		
		\App\Models\Medcubics\ApiConfig::create(array(
	        'api_for' => 'insurance_eligibility',
	        'api_name' => 'eligibile',
			'api_status' => 'Actice',
			'category' => 'Insurance Eligibility',
			'url' => 'https://gds.eligibleapi.com/v1.5/coverage/all.json',
	        'usps_user_id' => 's9ETtX97Pq-dpnuw-nDyHKVxXvF1naxOowZ5'
	    ));
		
		\App\Models\Medcubics\ApiConfig::create(array(
	        'api_for' => 'medicare_eligibility',
	        'api_name' => 'eligibile',
			'api_status' => 'Actice',
			'category' => 'Medicare Eligibility',
			'url' => 'https://gds.eligibleapi.com/v1.5/coverage/medicare.json',
	        'usps_user_id' => 's9ETtX97Pq-dpnuw-nDyHKVxXvF1naxOowZ5'
	    ));
		
		\App\Models\Medcubics\ApiConfig::create(array(
	        'api_for' => 'benifit_verification',
	        'api_name' => 'apex',
			'api_status' => 'Actice',
			'category' => 'Benifit Verification',
			'url' => '',
	        'usps_user_id' => ''
	    ));
		
		\App\Models\Medcubics\ApiConfig::create(array(
	        'api_for' => 'claim',
	        'api_name' => 'apex',
			'api_status' => 'Actice',
			'category' => 'Claim',
			'url' => '',
	        'usps_user_id' => ''
	    ));
		
		\App\Models\Medcubics\ApiConfig::create(array(
	        'api_for' => 'twilio_sms',
	        'api_name' => 'twilio',
			'api_status' => 'Actice',
			'category' => 'Sms',
			'token' => 'ad23d652476cf169578be8fad547f4ad',
			'host' => '12055308992',
	        'usps_user_id' => 'ACa77a39bbb1ae4b6cceafc4f0f7f1cd4f'
	    ));
		
		\App\Models\Medcubics\ApiConfig::create(array(
	        'api_for' => 'twilio_call',
	        'api_name' => 'twilio',
			'api_status' => 'Actice',
			'category' => 'Call',
			'token' => 'ad23d652476cf169578be8fad547f4ad',
			'host' => '12055308992',
			'url' => 'http://demo.twilio.com/docs/voice.xml',
	        'usps_user_id' => 'ACa77a39bbb1ae4b6cceafc4f0f7f1cd4f'
	    ));
	}
}
