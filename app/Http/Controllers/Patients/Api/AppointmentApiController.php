<?php namespace App\Http\Controllers\Patients\Api;

use App\Http\Controllers\Controller;
use App\Models\Patients\Patient as Patient;
use App\Models\Scheduler\PatientAppointment as PatientAppointment;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Models\Provider as Provider;
use App\Models\ReasonForVisit as ReasonForVisit;
use App\Models\Facility as Facility;
use App\Models\Payments\PMTInfoV1;
use App\Models\Payments\ClaimInfoV1;
use Config;
use Auth;
use Response;
use Request;
use Validator;
use Schema;
use DB;
use Lang;

class AppointmentApiController extends Controller 
{
	/*** List the Patient Appointments start ***/
	public function getIndexApi($id,$export='')
	{  
		
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode'); 
		if(Patient::where('id', $id)->count()>0 && is_numeric($id)) 
		{
			$patients = Patient::with('provider_details','facility_details','patient_appointment','ethnicity_details','insurance_details')->where('id',$id)->first();
			//Get patient appontment list
			$patient_appointment = '';
			if(!Request::ajax())
				$patient_appointment=PatientAppointment::with('patient','provider','provider.provider_type_details','provider.provider_types','provider.degrees','facility','facility.facility_address','facility.pos_details','reasonforvisit','facility.speciality_details','facility.pos_details','facility.county')->where('patient_id',$id)->get();
			else
			{
				$request = Request::all();	
				$patient_appointment=PatientAppointment::with('patient','provider','provider.provider_type_details','provider.provider_types','provider.degrees','facility','facility.facility_address','facility.pos_details','reasonforvisit','facility.speciality_details','facility.pos_details','facility.county')->where('patient_id',$id);

				//Appointment Date
				$from_date = $to_date = "";
				if(isset($request['date_option'])) {
					if($request['date_option'] == 'enter_date')	{	
						$from_date = date('Y-m-d', strtotime($request['from_date']));
						$to_date = date('Y-m-d', strtotime($request['to_date']));
						$patient_appointment->whereRaw("DATE(scheduled_on) >= '$from_date' and DATE(scheduled_on) <= '$to_date'");
						$get_list_header["Trans Date"] = "[ ".Helpers::dateFormat($from_date,'date')."  To ".Helpers::dateFormat($to_date,'date')." ]";					
					} elseif($request['date_option'] == 'daily'){
						$from_date = $to_date = date('Y-m-d');
						$patient_appointment->whereRaw("DATE(scheduled_on) >= '$from_date' and DATE(scheduled_on) <= '$to_date'");
						$get_list_header["Trans Date"] = "[ ".Helpers::dateFormat($from_date,'date')."  To ".Helpers::dateFormat($to_date,'date')." ]";
					} elseif($request['date_option'] == 'current_month'){
						$from_date = date('Y-m-01');
						$to_date = date('Y-t-d');
						$patient_appointment->whereRaw("DATE(scheduled_on) >= '$from_date' and DATE(scheduled_on) <= '$to_date'");
						$get_list_header["Trans Date"] = "[ ".Helpers::dateFormat($from_date,'date')."  To ".Helpers::dateFormat($to_date,'date')." ]";
					} elseif($request['date_option'] == 'previous_month'){
						$from_date = date('Y-m-d', strtotime('first day of last month'));
						$to_date =  date('Y-m-d', strtotime('last day of last month'));
						$patient_appointment->whereRaw("DATE(scheduled_on) >= '$from_date' and DATE(scheduled_on) <= '$to_date'");
						$get_list_header["Trans Date"] = "[ ".Helpers::dateFormat($from_date,'date')."  To ".Helpers::dateFormat($to_date,'date')." ]";
					} elseif($request['date_option'] == 'current_year'){
						$from_date = date('Y-m-d', strtotime(date('Y-01-01'))); $to_date =  date('Y-t-d');
						$patient_appointment->whereRaw("DATE(scheduled_on) >= '$from_date' and DATE(scheduled_on) <= '$to_date'");
						$get_list_header["Trans Date"] = "[ ".Helpers::dateFormat($from_date,'date')."  To ".Helpers::dateFormat($to_date,'date')." ]";
					} elseif($request['date_option'] == 'previous_year'){
						$from_date = date('Y-01-01', strtotime("-1 year")); $to_date =  date('Y-12-31', strtotime("-1 year"));
						$prev_year = date('Y', strtotime("-1 year"));
						$patient_appointment->whereRaw("DATE(scheduled_on) >= '$from_date' and DATE(scheduled_on) <= '$to_date'");
						$get_list_header["Trans Date"] = "[ ".Helpers::dateFormat($from_date,'date')."  To ".Helpers::dateFormat($to_date,'date')." ]";
					}
				}
					
				//Provider name
				if(isset($request["provider_id"]) && $request["provider_id"] !="" && $request['provider_id'] != 'all') {
					$provider_id =$request["provider_id"]; 
					$patient_appointment->whereHas('provider', function($q)use($provider_id) {$q->where('id',$provider_id); });
					$get_list_header["Provider"] = Provider::getProviderNamewithDegree($provider_id);
					$hide_col["rendering"] = 1;
				}

				//Appointment Facility
				if(isset($request["facility_id"]) && $request["facility_id"] !="" && $request["facility_id"] !="all") {	
					$faci_id = $request["facility_id"];
					$patient_appointment->whereHas('facility', function($q)use($faci_id) {$q->where('facility_id',$faci_id); });
					$get_list_header["Facility Name"] = Facility::getFacilityName($request['facility_id']);
					$hide_col["facility"] = 1;
				}

				//Appointment Reason
				if(isset($request["reason_id"]) && $request["reason_id"] !="Patient" && $request["reason_id"] !="all") {
					$reason_id = $request["reason_id"];
					$patient_appointment->whereHas('reasonforvisit', function($q)use($reason_id) {$q->where('id',$reason_id); });
					$hide_col["reason_id"] = 1;
				}

				//Appointment check in
				if(isset($request["check_in_time"]) && $request["check_in_time"] !="" && $request["check_in_time"] !="all") {
					$check_in_time = $request["check_in_time"];
					$patient_appointment->where('appointment_time','%'.$check_in_time); 
					$hide_col["reason_id"] = 1;
				}
				//Appointment Status
				if(isset($request["status"]) && $request["status"] !="" && $request["status"] !="all") {
					$status = $request["status"];
					$patient_appointment->where('status',$status); 
				}
				$patient_appointment=$patient_appointment->get();
			}	
			
			//Filter option
			$rendering_provider =Provider::typeBasedProviderlist('Rendering');
			$reason = ReasonForVisit::pluck('reason','id')->all();
			$facility = Facility::pluck('facility_name','id')->all();
			if($export != "") {
				$exportparam 	= array(
					'filename'        =>    'Patient_appointments',
					'heading'         =>	'Patient Appointments',
					'fields'          =>    array(
					'scheduled_on'   =>    'Date',
					'appointment_time'  => 	'Time',
					'provider_id' =>	array('table'=>'provider' ,	'column' => 'provider_name' , 'label' => 'Provider'),
					'facility_id'          =>	array('table'=>'facility' ,	'column' => 'facility_name' ,	'label' => 'Facility'),
					'reason_for_visit'     =>	array('table'=>'reasonforvisit' ,	'column' => 'reason' ,	'label' => 'Reason for visit'),
					'status'           =>	'Status'
					));

				$callexport    	= new CommonExportApiController();
				return $callexport->generatemultipleExports($exportparam, $patient_appointment, $export); 
			}
			$patients->stats = $this->appointmentStats($id);
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('patients','id','patient_appointment','rendering_provider','reason','facility')));
		}
		else 
		{
			return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
	}
	
	public function appointmentStats($pid){
		$stats = [];
		$end_date = date("Y-m-d");
		$appts = PatientAppointment::where('patient_id', $pid)
					->select(DB::raw('	sum(case when (status = "Scheduled" || status = "Rescheduled" || status = "Canceled" || status = "No Show" || status = "Encounter" ||  status = "Complete")  then 1 else 0 end) as tot_apts, 
										sum(case when status = "Scheduled" then 1 else 0 end) as sch_apts, 
										sum(case when status = "Canceled" then 1 else 0 end) as can_apts,
										sum(case when status = "Complete" then 1 else 0 end) as comp_apts,
										sum(case when status = "No Show" then 1 else 0 end) as no_shows,
										sum(case when status = "Encounter" then 1 else 0 end) as enc_apts
										'))
					->first()->toArray();		
		// Total Appointments, Scheduled, Canceled, Visits, Encounters, Claims, Payment Type, Statement Type, Bill Cycle	
		$stats['total_appointment'] = isset($appts['tot_apts']) ? $appts['tot_apts'] : 0; 
		$stats['scheduled'] = isset($appts['sch_apts']) ? $appts['sch_apts'] : 0; 
		$stats['canceled'] = isset($appts['can_apts']) ? $appts['can_apts'] : 0; 
		$stats['visits'] = $stats['complete'] = isset($appts['comp_apts']) ? $appts['comp_apts'] : 0; 		
		$stats['encounter'] = isset($appts['enc_apts']) ? $appts['enc_apts'] : 0; 		
		$stats['no_show'] = isset($appts['no_shows']) ? $appts['no_shows'] : 0; 
		$stats['claims'] = ClaimInfoV1::whereNotIn('status', ['Hold'])->where('patient_id', $pid)->count();
		$stats['payment_type'] = PMTInfoV1::where('patient_id', $pid)->whereNotIn('pmt_mode', ['Credit Balance',''])->orderBy('id', 'DESC')->pluck('pmt_mode')->first();
		return $stats; 		
	}

	/*** List the Patient Appointments stop ***/
	function __destruct() {
		// 
    }
}