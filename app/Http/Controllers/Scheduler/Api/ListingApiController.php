<?php namespace App\Http\Controllers\Scheduler\Api;
use Request;
use Response;
use DB;
use App\Http\Controllers\Controller;
use App\Models\Provider as Provider;
use App\Models\Facility as Facility;
use App\Models\Patients\Patient as Patients;
use App\Models\Scheduler\PatientAppointment as PatientAppointment;
use App\Http\Controllers\Api\CommonExportApiController;
use App\Http\Helpers\Helpers as Helpers;

class ListingApiController extends Controller 
{
	/**** Listing page starts ***/ 
    public function getIndexApi($pro_id='',$fac_id='',$date='',$pat_id='',$export='',$request='')
    {
		if(Request::ajax()) //Coming for ajax request in search page
			$request 	  	= Request::all();
		
		//Get appointment list starts
		$get_list = PatientAppointment::with('provider','provider.provider_types','provider.degrees','provider.provider_type_details','facility','facility.facility_address','facility.speciality_details','facility.pos_details','facility.county','patient');
		$provider_id 	= (Request::ajax()) ? $request["Provider"] : $pro_id;
		$facility_id 	= (Request::ajax()) ? $request["Facility"] : $fac_id;	
		$patient_id		= (Request::ajax()) ? $request["Patient"] : $pat_id;
		
		//Decode for provider, facility, patient id from getting values 
		$provider_id = ($provider_id !='') ? Helpers::getEncodeAndDecodeOfId($provider_id,'decode') : '';
		$facility_id = ($facility_id!='') ? Helpers::getEncodeAndDecodeOfId($facility_id,'decode') : '';
		$patient_id = ($patient_id!='') ? Helpers::getEncodeAndDecodeOfId($patient_id,'decode') : '';
		
		//Get appointment list based on condition starts
		if($provider_id !='')
			$get_list->where("provider_id",$provider_id);
		if($facility_id !='') 
			$get_list->where("facility_id",$facility_id);
		if($patient_id !='') 
			$get_list->where("patient_id",$patient_id);
		
		if(Request::ajax())
		{
			if($request["Date"] =="Today") 	
				$get_list->whereRaw('Date(scheduled_on) = CURDATE()');
			elseif($request["Date"] =="This Week") 	
				$get_list->where("scheduled_on", ">",DB::raw('DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
			elseif($request["Date"] =="This Month") 
				$get_list->where(DB::raw('MONTH(scheduled_on)'), '=', date('m'));
			elseif($request["Date"] =="Prev Week") 	
				$get_list->where("scheduled_on", ">",DB::raw('DATE_SUB(NOW(), INTERVAL 2 WEEK)'))->where("scheduled_on", "<",DB::raw('DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
			elseif($request["Date"] =="Prev Month") 
				$get_list->where(DB::raw('MONTH(scheduled_on)'), '=', (date('m'))-1);
		}
		/*else
		{
			if($date !='') 
				$get_list->where("scheduled_on",$date);
		}*/
		if(Request::ajax())
			if($request["Status"] !='') 
				$get_list->where("status",$request["Status"]);
		
		$app_list = $get_list->orderBy("scheduled_on","DESC")->get(); //Get total appointment list based on condition
		/*** Export option starts here ***/
		
		if($export != "")
		{ 
			$exprt = array(
						'filename'	=>	'Appoinments List',
						'heading'	=>	'',
						'fields' 	=>	array(
											'Acc No'			=>	array('table'=>'patient' ,'column' => 'account_no', 'label' => 'Acc No'),
											'Patient Name'		=>	array('table'=>'patient' ,'column' => ['last_name','first_name'], 'label' => 'Patient Name'),
											'DOB'				=>	array('table'=>'patient' ,'column' => 'dob', 'label' => 'DOB'),
											'Provider Name'		=>	array('table'=>'provider' ,	'column' => 'provider_name', 'label' => 'Provider Name'),
											'Facility Name'		=>	array('table'=>'facility' ,	'column' => 'facility_name', 'label' => 'Facility Name'),
											'scheduled_on'		=>	'Appt Date',	
											'appointment_time'	=>	'Appt Time',	
											'status'			=>	'Status',
										)
						);
						
			$callexport = new CommonExportApiController();
			return $callexport->generatemultipleExports($exprt, $app_list, $export); 
		}
		/*** Export option ends here ***/
		$view_type = (Request::ajax()) ? $request["list_view"]."view" : ''; //Page view type using ajax
		$facility_list	=  	Facility::getAllfacilities();//Getting all facilities detail
		$provider_list	=  	Provider::getAllprovider();//Getting all provider detail
		$patients_list	=  	Patients::getAllpatients();//Getting all patients detail
		
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('app_list','facility_list','provider_list','patients_list','view_type')));
    }
	/**** Listing page ends ***/ 
}