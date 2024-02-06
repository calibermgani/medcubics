<?php namespace App\Http\Controllers\Api;

use App\Models\Patients\Patient as Patient;
use App\Http\Controllers\Controller;
use Auth;
use Response;
use Request;
use Lang;
use App\Models\Practice as Practice;
use App\Models\Facility as Facility;
use App\Models\AddressFlag as AddressFlag;
use App\Models\Provider as Provider;
use App\Models\PatientStatementSettings as PatientStatementSettings;
use App\Models\STMTCategory as STMTCategory;

class PatientstatementsettingsApiController extends Controller 
{
	/*** Patient statement settings form Starts ***/
	public function getIndexApi() 
	{
		$facility = Facility::pluck('facility_name','id')->all();
		$category = STMTCategory::where('stmt_option', 'Yes')->where('status', 'Active')->pluck('category','id')->all();
		$provider = Provider::getBillingAndRenderingProvider("yes");
		$psettings = PatientStatementSettings::first();
		
		if(isset($psettings->minimumpatientbalance) && $psettings->minimumpatientbalance== '0')
		     $psettings->minimumpatientbalance = '';
		 
		/// Get address for usps ///
		$general_address_flag 	= AddressFlag::getAddressFlag('patientstatementsettings','1','general_information');
		$addressFlag['general'] = $general_address_flag;
			
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('facility','provider','psettings','addressFlag','category')));
    }
	/*** Patient statement settings form Ends ***/
	
	// Get address details.
	public function getaddressApi()
	{
		 $practice 	= Practice::first();
		 $facility  = Facility::with('facility_address')->get();
		 $provider	= Provider::with('provider_types','degrees')->get();
		 return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('practice','facility','provider')));
	}
	
	/*** Store Patient statement settings details ***/
	public function getStoreApi($request = '') 
	{
        $request = Request::all();
        $user = Auth::user ()->id;
		if($request)
		{
			PatientStatementSettings::truncate();
			$patientstatment = PatientStatementSettings::create($request);
			$patientstatment->updated_by = $user;
			/* Card type adding option */
			$patientstatment->mc_card = (array_key_exists('mc_card',$request)) ? 1 : 0;
			$patientstatment->visa_card =(array_key_exists('visa_card',$request)) ? 1 : 0;
			$patientstatment->maestro_card = (array_key_exists('maestro_card',$request)) ? 1 : 0;
			$patientstatment->gift_card =(array_key_exists('gift_card',$request)) ? 1 : 0;

			if($request['bulkstatement'] == '1')
			{
				if($request['statementcycle'] == 'Billcycle') 
				{
					if (array_key_exists("billcycleweek1",$request))
						$patientstatment->week_1_billcycle = implode(",",@$request['billcycleweek1']);
					if (array_key_exists("billcycleweek2",$request))
						$patientstatment->week_2_billcycle = implode(",",@$request['billcycleweek2']);
					if (array_key_exists("billcycleweek3",$request))
						$patientstatment->week_3_billcycle = implode(",",@$request['billcycleweek3']);
					if (array_key_exists("billcycleweek4",$request))
						$patientstatment->week_4_billcycle = implode(",",@$request['billcycleweek4']);
					if (array_key_exists("billcycleweek5",$request))
						$patientstatment->week_5_billcycle = implode(",",$request['billcycleweek5']);
				}
				
				if($request['statementcycle'] == 'Facility') 
				{
					if (array_key_exists("facilityweek1",$request))
						$patientstatment->week_1_facility = implode(",",$request['facilityweek1']);
					if (array_key_exists("facilityweek2",$request))
						$patientstatment->week_2_facility = implode(",",$request['facilityweek2']);
					if (array_key_exists("facilityweek3",$request))
						$patientstatment->week_3_facility = implode(",",$request['facilityweek3']);
					if (array_key_exists("facilityweek4",$request))
						$patientstatment->week_4_facility = implode(",",$request['facilityweek4']);
					if (array_key_exists("facilityweek5",$request))
						$patientstatment->week_5_facility = implode(",",$request['facilityweek5']);
				}
				
				if($request['statementcycle'] == 'Provider') 
				{
					if (array_key_exists("providerweek1",$request))
						$patientstatment->week_1_provider = implode(",",$request['providerweek1']);
					if (array_key_exists("providerweek2",$request))
						$patientstatment->week_2_provider = implode(",",$request['providerweek2']);
					if (array_key_exists("providerweek3",$request))
						$patientstatment->week_3_provider = implode(",",$request['providerweek3']);
					if (array_key_exists("providerweek4",$request))
						$patientstatment->week_4_provider = implode(",",$request['providerweek4']);
					if (array_key_exists("providerweek5",$request))
						$patientstatment->week_5_provider = implode(",",$request['providerweek5']);
				}
				if($request['statementcycle'] == 'Account') 
				{
					if (array_key_exists("toaccountweek1",$request))
						$patientstatment->week_1_account = $request['fromaccountweek1'].','.$request['toaccountweek1'];
					if (array_key_exists("toaccountweek2",$request))
						$patientstatment->week_2_account = $request['fromaccountweek2'].','.$request['toaccountweek2'];
					if (array_key_exists("toaccountweek3",$request))
						$patientstatment->week_3_account = $request['fromaccountweek3'].','.$request['toaccountweek3'];
					if (array_key_exists("toaccountweek4",$request))
						$patientstatment->week_4_account = $request['fromaccountweek4'].','.$request['toaccountweek4'];
					if (array_key_exists("toaccountweek5",$request))
						$patientstatment->week_5_account = $request['fromaccountweek5'].','.$request['toaccountweek5'];
				}
				// Statement cyclec if choosen category.
				if($request['statementcycle'] == 'Category') 
				{
					if (array_key_exists("categoryweek1",$request))
						$patientstatment->week_1_category = implode(",",$request['categoryweek1']);
					if (array_key_exists("categoryweek2",$request))
						$patientstatment->week_2_category = implode(",",$request['categoryweek2']);
					if (array_key_exists("categoryweek3",$request))
						$patientstatment->week_3_category = implode(",",$request['categoryweek3']);
					if (array_key_exists("categoryweek4",$request))
						$patientstatment->week_4_category = implode(",",$request['categoryweek4']);
					if (array_key_exists("categoryweek5",$request))
						$patientstatment->week_5_category = implode(",",$request['categoryweek5']);
				}
			}
			
			$patientstatment->save();
			 /// Starts - address flag update ///				
			$address_flag 				= array();
			$address_flag['type'] 		= 'patientstatementsettings';
			$address_flag['type_id'] 	= '1';
			$address_flag['type_category'] = 'general_information';
			$address_flag['address2'] 	= $request['general_address1'];
			$address_flag['city'] 		= $request['general_city'];
			$address_flag['state'] 		= $request['general_state'];
			$address_flag['zip5'] 		= $request['general_zip5'];
			$address_flag['zip4'] 		= $request['general_zip4'];
			$address_flag['is_address_match'] 	= $request['general_is_address_match'];
			$address_flag['error_message'] 		= $request['general_error_message'];
			AddressFlag::checkAndInsertAddressFlag($address_flag);
			/* Ends - address flag update  */
			
			return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.update_msg"), 'data' => ''));
		}
	}
	/*** Store Function Ends ***/
	
	function __destruct() 
	{
    }
}
