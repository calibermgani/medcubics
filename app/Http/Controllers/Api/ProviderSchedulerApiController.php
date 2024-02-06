<?php namespace App\Http\Controllers\Api;

use Auth;
use Request;
use View;
use Response;
use App\Http\Controllers\Controller;
use App\Models\Provider as Provider;
use App\Models\Speciality as Speciality;
use App\Models\Provider_type as ProviderType;
use App\Models\Facility as Facility;
use App\Models\ProviderScheduler as ProviderScheduler;
use App\Models\ProviderSchedulerTime as ProviderSchedulerTime;
use App\Models\Scheduler\PatientAppointment as PatientAppointment;
use Illuminate\Support\Collection;
use Config;
use App;
use DB;
use \DateTime;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Http\Helpers\Helpers as Helpers;
use Lang;
use Carbon\Carbon;

class ProviderSchedulerApiController extends Controller 
{
  
    /********************** Start Display a listing of the provider scheduler ***********************************/
	public function getIndexApi($export='')
    {
        
		$request 	= Request::all();
			
		$query = Provider::with('speciality','provider_types','degrees')->whereIn('provider_types_id',['1'])->where('status','Active');
				   
		if(@$request['sch_shortname'] != ''){
			$sch_shortname = $request['sch_shortname'];
			$query->whereRaw("(short_name LIKE '%$sch_shortname%' or short_name LIKE '%$sch_shortname' or short_name LIKE '$sch_shortname%')");
		}
		if(@$request['sch_provider'] != ''){
			$sch_providername = $request['sch_provider'];
			$query->whereRaw("(provider_name LIKE '%$sch_providername%' or provider_name LIKE '%$sch_providername' or provider_name LIKE '$sch_providername%')");
		}
		if(@$request['sch_type'] != ''){
			$query->whereIn('provider_types_id',$request['sch_type']);
		}
		if(@$request['sch_npi'] != ''){
			$srch_npi			= $request['sch_npi'];
			$search_npi_like 	= " npi like '$srch_npi' or npi like '$srch_npi%' or npi like '%$srch_npi' or npi like '%$srch_npi%' ";
			$query->whereRaw("($search_npi_like)");
		}
		
		$providers 	= $query->orderBy('short_name','ASC')->get()->toArray();
		
		if(@$request['sch_scheduled'] == 'Yes'){
			$providers_all 	= $providers;
			$providers 		= array();
			if(count($providers_all)){
				$cc = 0;
				foreach($providers_all as $ini_pri_arr){
					if((ProviderScheduler::where('provider_id',$ini_pri_arr['id'])->count())>0){
						$providers[$cc] = $ini_pri_arr;
						$cc++;
					}
				}
			}
		}
		
		if(@$request['sch_scheduled'] == 'No'){
			$providers_all 	= $providers;
			$providers 		= array();
			if(count($providers_all)){
				$cc = 0;
				foreach($providers_all as $ini_pri_arr){
					if((ProviderScheduler::where('provider_id',$ini_pri_arr['id'])->count())==0){
						$providers[$cc] = $ini_pri_arr;
						$cc++;
					}
				}
			}
		}
		
		if($export != "") 
		{
			$prv_r = $prv_list = array();
			foreach($providers as $key=>$prv_value)
			{
				$prv_r['short_name']	= @$prv_value['short_name'];
				$prv_r['provider_name']	= @$prv_value['provider_name'].' '.@$prv_value['degrees']['degree_name'];
				$prv_r['type']			= @$prv_value['provider_types']['name'];
				$prv_r['npi']			= @$prv_value['npi'];
				
				$scheduled_count		= ProviderScheduler::getScheduledCountByProviderId(Helpers::getEncodeAndDecodeOfId(@$prv_value['id'],'encode'),'Provider');
				if($scheduled_count > 0){
					$prv_r['scheduled'] = "Yes";
				}
				else{
					$prv_r['scheduled'] = "No";
				}
				
				$prv_list[$key] 		= $prv_r;
				unset($prv_r);
			}
			
			$get_prv_list = json_decode(json_encode($prv_list));
			
			$exportparam = array(
				'filename'		=>	'Providers_Schedular',
				'heading'		=>	'Providers_Schedular',
				'fields' 		=>	array(
					'short_name'	=> 'Short name',		
					'provider_name'	=> 'Provider',	
					'type'			=> 'Type',
					'npi'			=> 'NPI',
					'scheduled'		=> 'Scheduled'
					)
			);
			
			$callexport = new CommonExportApiController();
			return $callexport->generatemultipleExports($exportparam, $get_prv_list, $export); 
		}
		
		/*$providers_rec = DB::table('providers as p')
						->leftJoin('provider_degrees as pd', 'pd.id', '=', 'p.provider_degrees_id')
						->selectRaw('CONCAT(p.provider_name," ",IFNULL(pd.degree_name,"")) as concatname, p.id, p.short_name')
						->whereIn('p.provider_types_id',['1','3','4'])->where('p.status', '=', 'Active')->where('p.deleted_at',NULL);
			
			
		$all_short_nam     	= $providers_rec->orderBy('short_name','ASC')->pluck('short_name','short_name')->all();
		$all_prov_nam      	= $providers_rec->orderBy('concatname','ASC')->pluck('concatname','id')->all();*/
		
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('providers')));
		
    }
	/********************** End Display a listing of the provider scheduler ***********************************/
	
	/********************** Start view provider scheduler list page ***********************************/
    public function viewProviderSchedulerApi($provider_id, $export='')
    {
		$encode_provider_id = $provider_id;
		$provider_id 		= Helpers::getEncodeAndDecodeOfId($provider_id,'decode');

		if(Provider::where('id', $provider_id)->count()>0 && is_numeric($provider_id)) 
		{
            $providerschedulers = ProviderScheduler::with('facility','provider')->where('provider_id',$provider_id)->groupBy('facility_id')->orderBy('facility_id','ASC')->get();
            
            $provider = Provider::with('speciality','provider_types','degrees')->where('id',$provider_id)->where('status','Active')->first();
            //Encode ID for provider
            $temp = new Collection($provider);
            $temp_id = $temp['id'];
            $temp->pull('id');
            $temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
            $temp->prepend($temp_encode_id, 'id');
            $data = $temp->all();
            $provider = json_decode(json_encode($data), FALSE);
            //Encode ID for provider
			if($export != "") {
				
				$prv_sch_r = $prv_sch_list = array();
				foreach($providerschedulers as $key_ff=>$prv_sch_value_ff) {
					$encode_facility_id 	= Helpers::getEncodeAndDecodeOfId($prv_sch_value_ff->facility_id,'encode');
					$allproviderschedulers 	= ProviderScheduler::getAllProviderSchedulerByFacilityId($encode_facility_id,$encode_provider_id);
					
					foreach($allproviderschedulers as $key=>$prv_sch_value) {
					
						$prv_sch_r['provider_name']	= @$prv_sch_value->provider->provider_name;
						$prv_sch_r['facility_name']	= @$prv_sch_value->facility->facility_name;
						$prv_sch_r['schedule_type']	= @$prv_sch_value->schedule_type;
						$prv_sch_r['start_date']	= Helpers::dateFormat(@$prv_sch_value->start_date,'date');
						$prv_sch_r['end_date']		= (@$prv_sch_value->end_date_option != 'never')?Helpers::dateFormat(@$prv_sch_value->end_date,'date'):'Never' ;
						$prv_sch_r['no_of_occurrence']	= (@$prv_sch_value->end_date_option == 'after')?@$prv_sch_value->no_of_occurrence:'--' ;
						$prv_sch_r['repeat_every']	= '';
						
						if(@$prv_sch_value->repeat_every > 1){
							$prv_sch_r['repeat_every']	.= @$prv_sch_value->repeat_every;
						}
						
						if(@$prv_sch_value->schedule_type == 'Daily'){
							$prv_sch_r['repeat_every']	.= " Days";
						} elseif(@$prv_sch_value->schedule_type == 'Weekly'){
							$prv_sch_r['repeat_every']	.= " Weeks";
						} elseif(@$prv_sch_value->schedule_type == 'Monthly'){
							$prv_sch_r['repeat_every']	.= " Months";
						}
						
						$prv_sch_list[$key_ff.$key] 		= $prv_sch_r;
						unset($prv_sch_r);
					
					}
				}
				
				$get_prv_sch_list = json_decode(json_encode($prv_sch_list));
				
				$exportparam = array(
					'filename'		=>	'Providers_View_Schedular',
					'heading'		=>	'Providers_View_Schedular',
					'fields' 		=>	array(
						'provider_name'		=> 'Provider',		
						'facility_name'		=> 'Facility',	
						'schedule_type'		=> 'Schedule Type',
						'start_date'		=> 'Start Date',
						'end_date'			=> 'End Date',
						'no_of_occurrence'	=> 'No of Occurrence',
						'repeat_every'		=> 'Repeat Every'
						)
				);
				
				$callexport = new CommonExportApiController();
				return $callexport->generatemultipleExports($exportparam, $get_prv_sch_list, $export);
			}
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('providerschedulers','provider')));
		} 
		else 
		{
			return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
    }
	/********************** End view provider scheduler list page ***********************************/
	
	/********************** Start add provider scheduler process ***********************************/
    public function addProviderSchedulerApi($provider_id,$scheduler_id='')
    {
		if(!is_numeric($provider_id))
			$provider_id = Helpers::getEncodeAndDecodeOfId($provider_id,'decode');

        $facility_arr = '';
        if($scheduler_id != '')
        { 
            //$scheduler_details = ProviderScheduler::with('facility','facility.facility_address')->where('id',$scheduler_id)->first();
            $facility_arr = $this->GetFacilityAndTimingsById($scheduler_id, 'scheduler');           
            $scheduler_details = $facility_arr['scheduler_details'];
            $facility_details = $facility_arr['facility_details'];
            //$days = $facility_arr['days'];

            $scheduler_details['facility_id'] = $scheduler_details->facility_id;
            $scheduler_details['hideClass'] = '';

            $scheduler_details['start_date'] = $scheduler_details->start_date;
            $scheduler_details['end_date_option'] = $scheduler_details->end_date_option;
            $scheduler_details['end_date'] = $scheduler_details->end_date;
            $scheduler_details['no_of_occurrence'] = ($scheduler_details->no_of_occurrence>0)?$scheduler_details->no_of_occurrence:1;
            $scheduler_details['schedule_type'] = $scheduler_details->schedule_type;        
            $scheduler_details['repeat_every'] = $scheduler_details->repeat_every;
            $scheduler_details['weekly_available_days'] = explode(',',$scheduler_details->weekly_available_days);
            $scheduler_details['monthly_visit_type'] = $scheduler_details->monthly_visit_type;
            $scheduler_details['monthly_visit_type_date'] = $scheduler_details->monthly_visit_type_date;
            $scheduler_details['monthly_visit_type_day_week'] = $scheduler_details->monthly_visit_type_day_week;
            $scheduler_details['monthly_visit_type_day_dayname'] = $scheduler_details->monthly_visit_type_day_dayname;
            $scheduler_details['monthly_visit_type_week'] = $scheduler_details->monthly_visit_type_week;        
            $scheduler_details['provider_reminder_sms'] = ($scheduler_details->provider_reminder_sms=='off')?null:true;
            $scheduler_details['provider_reminder_phone'] = ($scheduler_details->provider_reminder_phone=='off')?null:true;
            $scheduler_details['provider_reminder_email'] = ($scheduler_details->provider_reminder_email=='off')?null:true;
			
            $scheduler_details['notes'] = $scheduler_details->notes;

           /* $monday_selected_times = $scheduler_details->monday_selected_times;
            $tuesday_selected_times = $scheduler_details->tuesday_selected_times;
            $wednesday_selected_times = $scheduler_details->wednesday_selected_times;
            $thursday_selected_times = $scheduler_details->thursday_selected_times;
            $friday_selected_times = $scheduler_details->friday_selected_times;
            $saturday_selected_times = $scheduler_details->saturday_selected_times;
            $sunday_selected_times = $scheduler_details->sunday_selected_times;*/       

            $scheduler_details['monday'] = App\Http\Helpers\Helpers::getDaysTimeListByDay($scheduler_details->monday_selected_times,$facility_details['facility_timings_details']['monday']); 
            $scheduler_details['tuesday'] = App\Http\Helpers\Helpers::getDaysTimeListByDay($scheduler_details->tuesday_selected_times,$facility_details['facility_timings_details']['tuesday']);
            $scheduler_details['wednesday'] = App\Http\Helpers\Helpers::getDaysTimeListByDay($scheduler_details->wednesday_selected_times,$facility_details['facility_timings_details']['wednesday']);
            $scheduler_details['thursday'] = App\Http\Helpers\Helpers::getDaysTimeListByDay($scheduler_details->thursday_selected_times,$facility_details['facility_timings_details']['thursday']);
            $scheduler_details['friday'] = App\Http\Helpers\Helpers::getDaysTimeListByDay($scheduler_details->friday_selected_times,$facility_details['facility_timings_details']['friday']);
            $scheduler_details['saturday'] = App\Http\Helpers\Helpers::getDaysTimeListByDay($scheduler_details->saturday_selected_times,$facility_details['facility_timings_details']['saturday']);
            $scheduler_details['sunday'] = App\Http\Helpers\Helpers::getDaysTimeListByDay($scheduler_details->sunday_selected_times,$facility_details['facility_timings_details']['sunday']);
            //$facility_arr['scheduler_details'] = $scheduler_details;
        }
		$api['practice_sms_enable'] = (DBConnectionController::getUserAPIIds('twilio_sms')==1)?"1":"0";
		$api['practice_call_enable'] =(DBConnectionController::getUserAPIIds('twilio_call')==1)?"1":"0";
					
        $facilities_ori = Facility::where('status','Active')->orderBy('facility_name','ASC')->pluck('facility_name','id')->all();
		$facilities_arr 	= array_flip($facilities_ori);  
		
		$facilities = array();
		array_walk($facilities_ori, function (&$value,$key) use (&$facilities) {
			$new_key = $this->myfunction($key);
			$facilities[$new_key] = $value;
		});
		
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('facilities','provider_id','facility_arr','api')));
    }
	/********************** End add provider scheduler process ***********************************/
	function myfunction($num) {
	  return(Helpers::getEncodeAndDecodeOfId($num,'encode'));
	}
    /********************** Start get available working hours facility and provider ***********************************/
	public function getAvailableWorkingHoursByFacilityAndProvider($provider_id, $facility_id='') { 
		$provider_id = Helpers::getEncodeAndDecodeOfId($provider_id,'decode');
        $getTimeSlot 		= Config::get('app.scheduler_provider_slot');
		$facility_arr 		= $this->GetFacilityAndTimingsById($facility_id);
        $facility_details 	= $facility_arr['facility_details'];
        $days 				= $facility_arr['days'];
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('days','facility_details')));
    }
	/********************** End get available workinh hours facility and provider ***********************************/
	
	/********************** Start get provider facility and timing ***********************************/
    public function GetFacilityAndTimingsById($id, $type = 'facility')
    {
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
        $scheduler_details = '';
        if($type == 'scheduler')
        {
            $scheduler_details = ProviderScheduler::with('facility','facility.facility_address')->where('id',$id)->first();
            $facility = $scheduler_details->facility;
        }
        else
            $facility = Facility::with('facility_address')->where('id',$id)->where('status','Active')->first();
        
        $facility_details['facility_timings_details'] = App\Http\Helpers\Helpers::getFacilityWorkingTimingsList($facility);  
        $days = App\Http\Helpers\Helpers::getFacilityWorkingTimingsDropDownListOption($facility_details['facility_timings_details']);

        /// Get and set facility avilable timings in caption ///
        $facility_details['monday_available_time'] 		= App\Http\Helpers\Helpers::getAvailableTimings($facility->monday_forenoon,$facility->monday_afternoon);
        $facility_details['tuesday_available_time'] 	= App\Http\Helpers\Helpers::getAvailableTimings($facility->tuesday_forenoon,$facility->tuesday_afternoon);
        $facility_details['wednesday_available_time'] 	= App\Http\Helpers\Helpers::getAvailableTimings($facility->wednesday_forenoon,$facility->wednesday_afternoon);
        $facility_details['thursday_available_time'] 	= App\Http\Helpers\Helpers::getAvailableTimings($facility->thursday_forenoon,$facility->thursday_afternoon);
        $facility_details['friday_available_time'] 		= App\Http\Helpers\Helpers::getAvailableTimings($facility->friday_forenoon,$facility->friday_afternoon);
        $facility_details['saturday_available_time'] 	= App\Http\Helpers\Helpers::getAvailableTimings($facility->saturday_forenoon,$facility->saturday_afternoon);
        $facility_details['sunday_available_time'] 		= App\Http\Helpers\Helpers::getAvailableTimings($facility->sunday_forenoon,$facility->sunday_afternoon);

        /// Set facility details ///
        $facility_details['name'] 			= $facility->facility_name;
        $facility_details['start_date'] = !empty($facility->timezone)?Helpers::facilityTimezone(date('Y-m-d H:i:s'), "m/d/Y", $facility->timezone):date("m/d/Y");
        $facility_details['end_date'] = !empty($facility->timezone)?Helpers::facilityTimezone(date('Y-m-d H:i:s', strtotime('+2 Years')), "m/d/Y", $facility->timezone):date("m/d/Y");
        $facility_details['address'] 		= $facility->facility_address->address1;
        if($facility->facility_address->address2 != '')
            $facility_details['address'] .= ', '.$facility->facility_address->address2;
        $facility_details['zipcode'] = $facility->facility_address->city.', '.$facility->facility_address->state.', '.$facility->facility_address->pay_zip5.'-'.$facility->facility_address->pay_zip4;
        $facility_details['email'] = $facility->email;
        $facility_details['phone'] = $facility->phone;
        $filename = $facility->avatar_name . '.' . $facility->avatar_ext;
		$facility_details['filename'] = $filename;
		$img_details = [];
		$img_details['module_name']='facility';
		$img_details['file_name']=$filename;
		$img_details['practice_name']="";
		$img_details['need_url'] = 'yes';
		$img_details['alt'] = 'facility-image';
        $facility_details['facility_icon'] = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
        
        $scheduler_arr['facility_details'] = $facility_details;
        //$scheduler_arr['days'] = $facility_details['facility_timings_details'];
		$scheduler_arr['days'] = $days;
        $scheduler_arr['scheduler_details'] = $scheduler_details;
        return $scheduler_arr;
    }
	/********************** End get provider facility and timing ***********************************/
	
	/********************** Start provider scheduler store process ***********************************/
	public function storeProviderScheduleSettings()
    {           
        $request 			= Request::all();
        if(!is_numeric($request['provider_id']))
        	$request['provider_id'] = Helpers::getEncodeAndDecodeOfId($request['provider_id'],'decode');
		
		if(!is_numeric($request['facility_id']))
			$request['facility_id'] = Helpers::getEncodeAndDecodeOfId($request['facility_id'],'decode');		
		$today_date 		= date("Y-m-d");
        /// Starts - Get and set request in variable ///
        $schedule_type 		= $request['schedule_type'];
        $repeat_every 		= $request['repeat_every'];
        $no_of_occurrence 	= $request['no_of_occurrence'];
        $end_date_option 	= $request['end_date_option'];
        $request['start_date'] 	= date("Y-m-d",strtotime($request['start_date']));
        $request['end_date'] 	= date("Y-m-d",strtotime($request['end_date']));
        $start_date         = $request['start_date'];
        $end_date           = $request['end_date'];
        $start_date_day 	= strtolower(date('l', strtotime($start_date))); 
        $provider_id 		= $request['provider_id'];
		$scheduler_id 		= $request['scheduler_id'];
        $facility_id 		= $request['facility_id'];
        $monday_from 		= $request['monday_from'];
        $monday_to 			= $request['monday_to'];
        $tuesday_from 		= $request['tuesday_from'];
        $tuesday_to 		= $request['tuesday_to'];
        $wednesday_from 	= $request['wednesday_from'];
        $wednesday_to 		= $request['wednesday_to'];
        $thursday_from 		= $request['thursday_from'];
        $thursday_to 		= $request['thursday_to'];
        $friday_from 		= $request['friday_from'];
        $friday_to 			= $request['friday_to'];
        $saturday_from 		= $request['saturday_from'];
        $saturday_to 		= $request['saturday_to']; 
        $sunday_from 		= $request['sunday_from'];
        $sunday_to 			= $request['sunday_to'];
        $weekly_available_days = @$request['weekly_available_days'];
       // dd($request);
        /// Ends - Get and set request in variable ///
        /// CONDITION CHECK 1 - Check start and end date is valid or not ///
        if($start_date != '' && (($scheduler_id == '' ) || ($scheduler_id !='')) && (($end_date_option == 'never') || ($end_date_option == 'after') || ($end_date_option == 'on' && $end_date > $start_date)))
        {
			if($scheduler_id != '' && $start_date < $today_date)
				$request['start_date'] = $start_date = $today_date;			
			
            /// Get facility details ///
            $facility = Facility::where('id',$facility_id)->where('status','Active')->first();

            /// Get Facility availability time list ///
            $facility_timings_details = App\Http\Helpers\Helpers::getFacilityWorkingTimingsList($facility); 

            /// Starts - Check and set timings by day to insert in provider scheduler table day time field and use it code below for conditions ///            
            // Initialize days time array - We will insert this in provider scheduler table for time selection by day
            $request['monday_selected_times'] = $request['tuesday_selected_times'] = $request['wednesday_selected_times'] = 
            $request['thursday_selected_times'] = $request['friday_selected_times'] = $request['saturday_selected_times'] = 
            $request['sunday_selected_times'] = '';

            // Initialize from and to time selected count by day - We used this to check condition if all 3 from and to time selected equally or not
            $selected_time_count_from_monday = $selected_time_count_from_tuesday = $selected_time_count_from_wednesday = 
            $selected_time_count_from_thursday = $selected_time_count_from_friday = $selected_time_count_from_saturday = 
            $selected_time_count_from_sunday = $selected_time_count_to_monday = $selected_time_count_to_tuesday = 
            $selected_time_count_to_wednesday = $selected_time_count_to_thursday = $selected_time_count_to_friday = 
            $selected_time_count_to_saturday = $selected_time_count_to_sunday = 0;

            // Initialize selected time count by day - We used this to check condition time selected by day or not
            $selected_time_count_by_day['monday']  = $selected_time_count_by_day['tuesday'] = $selected_time_count_by_day['wednesday'] = 
            $selected_time_count_by_day['thursday'] = $selected_time_count_by_day['friday'] = $selected_time_count_by_day['saturday'] =
            $selected_time_count_by_day['sunday'] = 0;
            
            $mismatch_time_count_by_day['monday']  = $mismatch_time_count_by_day['tuesday'] = $mismatch_time_count_by_day['wednesday'] = 
            $mismatch_time_count_by_day['thursday'] = $mismatch_time_count_by_day['friday'] = $mismatch_time_count_by_day['saturday'] =
            $mismatch_time_count_by_day['sunday'] = 0;

            $user_selected_days_array = [];
            $user_selected_day_time_count = 0;

            /// Starts - Check and set 3 timings by day ///
            for($i=1;$i<=3;$i++)
            {
                if($i != 1)
                {
                    $request['monday_selected_times'] .= ',';
                    $request['tuesday_selected_times'] .= ',';
                    $request['wednesday_selected_times'] .= ',';
                    $request['thursday_selected_times'] .= ',';
                    $request['friday_selected_times'] .= ',';
                    $request['saturday_selected_times'] .= ',';
                    $request['sunday_selected_times'] .= ',';
                }

                /// Set and store format is 12:00 am-01:00 am,03:00 am-05:00 am,02:00 pm-03:00 pm
                if($monday_from[$i] != '' && $monday_to[$i] != '')
				{
                    $request['monday_selected_times'] .= $monday_from[$i].'-'.$monday_to[$i];
                    if($selected_time_count_by_day['monday'] == 0)
					{
                        $selected_time_count_by_day['monday'] = 1;
                        $user_selected_days_array[] = 'monday';
                    }
                } 
				elseif($monday_from[$i] != '')
				{
                    $selected_time_count_from_monday++;
                    $mismatch_time_count_by_day['monday'] = $mismatch_time_count_by_day['monday']+1;
                } 
				elseif($monday_to[$i] != '')
				{
                    $selected_time_count_to_monday++;
                    $mismatch_time_count_by_day['monday'] = $mismatch_time_count_by_day['monday']+1;
                } 

                if($tuesday_from[$i] != '' && $tuesday_to[$i] != '')
				{
                    $request['tuesday_selected_times'] .= $tuesday_from[$i].'-'.$tuesday_to[$i];
                    if($selected_time_count_by_day['tuesday'] == 0)
					{
                        $selected_time_count_by_day['tuesday'] = 1;
                        $user_selected_days_array[] = 'tuesday';
                    }
                } 
				elseif($tuesday_from[$i] != '')
				{
                    $selected_time_count_from_tuesday++;
                    $mismatch_time_count_by_day['tuesday'] = $mismatch_time_count_by_day['tuesday']+1;
                } 
				elseif($tuesday_to[$i] != '')
				{
                    $selected_time_count_to_tuesday++;
                    $mismatch_time_count_by_day['tuesday'] = $mismatch_time_count_by_day['tuesday']+1;
                } 

                if($wednesday_from[$i] != '' && $wednesday_to[$i] != '')
				{
                    $request['wednesday_selected_times'] .= $wednesday_from[$i].'-'.$wednesday_to[$i];
                    if($selected_time_count_by_day['wednesday'] == 0)
					{
                        $selected_time_count_by_day['wednesday'] = 1;
                        $user_selected_days_array[] = 'wednesday';
                    }
                } 
				elseif($wednesday_from[$i] != '')
				{
                    $selected_time_count_from_wednesday++;
                    $mismatch_time_count_by_day['wednesday'] = $mismatch_time_count_by_day['wednesday']+1;
                } 
				elseif($wednesday_to[$i] != '')
				{
                    $selected_time_count_to_wednesday++;
                    $mismatch_time_count_by_day['wednesday'] = $mismatch_time_count_by_day['wednesday']+1;
                } 

                if($thursday_from[$i] != '' && $thursday_to[$i] != '')
				{
                    $request['thursday_selected_times'] .= $thursday_from[$i].'-'.$thursday_to[$i];
                    if($selected_time_count_by_day['thursday'] == 0)
					{
                        $selected_time_count_by_day['thursday'] = 1;
                        $user_selected_days_array[] = 'thursday';
                    }
                } 
				elseif($thursday_from[$i] != '')
				{
                    $selected_time_count_from_thursday++;
                    $mismatch_time_count_by_day['thursday'] = $mismatch_time_count_by_day['thursday']+1;
                } 
				elseif($thursday_to[$i] != '')
				{
                    $selected_time_count_to_thursday++;
                    $mismatch_time_count_by_day['thursday'] = $mismatch_time_count_by_day['thursday']+1;
                } 

                if($friday_from[$i] != '' && $friday_to[$i] != '')
				{
                    $request['friday_selected_times'] .= $friday_from[$i].'-'.$friday_to[$i];
                    if($selected_time_count_by_day['friday'] == 0)
					{
                        $selected_time_count_by_day['friday'] = 1;
                        $user_selected_days_array[] = 'friday';
                    }
                } 
				elseif($friday_from[$i] != '')
				{
                    $selected_time_count_from_friday++;
                    $mismatch_time_count_by_day['friday'] = $mismatch_time_count_by_day['friday']+1;
                } 
				elseif($friday_to[$i] != '')
				{
                    $selected_time_count_to_friday++;
                    $mismatch_time_count_by_day['friday'] = $mismatch_time_count_by_day['friday']+1;
                } 

                if($saturday_from[$i] != '' && $saturday_to[$i] != '')
				{
                    $request['saturday_selected_times'] .= $saturday_from[$i].'-'.$saturday_to[$i];
                    if($selected_time_count_by_day['saturday'] == 0)
					{
                        $selected_time_count_by_day['saturday'] = 1;
                        $user_selected_days_array[] = 'saturday';
                    }
                } 
				elseif($saturday_from[$i] != '')
				{
                    $selected_time_count_from_saturday++;
                    $mismatch_time_count_by_day['saturday'] = $mismatch_time_count_by_day['saturday']+1;
                } 
				elseif($saturday_to[$i] != '')
				{
                    $selected_time_count_to_saturday++;
                    $mismatch_time_count_by_day['saturday'] = $mismatch_time_count_by_day['saturday']+1;
                } 

                if($sunday_from[$i] != '' && $sunday_to[$i] != '')
				{
                    $request['sunday_selected_times'] .= $sunday_from[$i].'-'.$sunday_to[$i];
                    if($selected_time_count_by_day['sunday'] == 0)
					{
                        $selected_time_count_by_day['sunday'] = 1;
                        $user_selected_days_array[] = 'sunday';
                    }
                } 
				elseif($sunday_from[$i] != '')
				{
                    $selected_time_count_from_sunday++;
                    $mismatch_time_count_by_day['sunday'] = $mismatch_time_count_by_day['sunday']+1;
                } 
				elseif($sunday_to[$i] != '')
				{
                    $selected_time_count_to_sunday++;
                    $mismatch_time_count_by_day['sunday'] = $mismatch_time_count_by_day['sunday']+1;
                } 
            }
            /// Ends - Check and set 3 timings by day ///
            /// Ends - Check and set timings by day to insert in provider scheduler table day time field and use it code below for conditions ///

            /// CONDITION CHECK 2 - Check if all selected day time selected or not - may from time selected and to time not selected and wise versa ///
            if($selected_time_count_from_monday == 0 && $selected_time_count_to_monday == 0 && 
                $selected_time_count_from_tuesday == 0 && $selected_time_count_to_tuesday == 0 && 
                $selected_time_count_from_wednesday == 0 && $selected_time_count_to_wednesday == 0 && 
                $selected_time_count_from_thursday == 0 && $selected_time_count_to_thursday == 0 &&  
                $selected_time_count_from_friday == 0 && $selected_time_count_to_friday == 0 &&  
                $selected_time_count_from_saturday == 0 && $selected_time_count_to_saturday == 0 &&  
                $selected_time_count_from_sunday == 0 && $selected_time_count_to_sunday == 0 )
            {
                // Set facility working days count by day - To check facility has atleast one working day or not below
                $facility_working_count_monday = count($facility_timings_details['monday']);
                $facility_working_count_tuesday = count($facility_timings_details['tuesday']);
                $facility_working_count_wednesday = count($facility_timings_details['wednesday']);
                $facility_working_count_thursday = count($facility_timings_details['thursday']);
                $facility_working_count_friday = count($facility_timings_details['friday']);
                $facility_working_count_saturday = count($facility_timings_details['saturday']);
                $facility_working_count_sunday = count($facility_timings_details['sunday']);

                // Check and set facility working days in an array varibale - To check it depending upon date and time selection below
                if($facility_working_count_monday > 0)
                    $facility_days_working_array[] = 'monday';
                if($facility_working_count_tuesday > 0)
                    $facility_days_working_array[] = 'tuesday';
                if($facility_working_count_wednesday > 0)
                    $facility_days_working_array[] = 'wednesday';
                if($facility_working_count_thursday > 0)
                    $facility_days_working_array[] = 'thursday';
                if($facility_working_count_friday > 0)
                    $facility_days_working_array[] = 'friday';
                if($facility_working_count_saturday > 0)
                    $facility_days_working_array[] = 'saturday';
                if($facility_working_count_sunday > 0)
                    $facility_days_working_array[] = 'sunday';

                // Calculate # of working days by facility
                $facility_working_days_count = $facility_working_count_monday+$facility_working_count_tuesday+$facility_working_count_wednesday+
                                                $facility_working_count_thursday+$facility_working_count_friday+$facility_working_count_saturday+
                                                $facility_working_count_sunday;
                /// CONDITION CHECK 3 - Check selected facility has working days or not /// 
                if($facility_working_count_monday > 0 || $facility_working_count_tuesday > 0 || $facility_working_count_wednesday > 0 || 
                   $facility_working_count_thursday > 0 || $facility_working_count_friday > 0 || $facility_working_count_saturday > 0 || 
                   $facility_working_count_sunday > 0)
                {
                    /***** Starts - Check and set available dates by schedule type whether daily/weekly/monthly also check working condition with selected dates *****/
                    /// CONDITION CHECK 4 - To check schedule type - Daily ///
                    if($schedule_type == 'Daily')
                    {                      
                        /// CONDITION CHECK 4A starts - Check start date is working or not and valid or not ///
                        if(!in_array($start_date_day,$facility_days_working_array))
                        {
                            $error = Lang::get("practice/practicemaster/providerscheduler.validation.slt_another_str_date");
                            $error_array['error_type'] = 'start_date';
                            $error_array['error_type_value'] = Lang::get("practice/practicemaster/providerscheduler.validation.slt_another_date");
                            return Response::json(array('status'=>'error', 'message'=>$error,'data'=>compact('error_array')));
                        }
                       /// CONDITION CHECK 4A ends - Check start date is working or not and valid or not ///

                        /// CONDITION CHECK 4B-1 - Check end date option is after ///
                        if($end_date_option == 'after')
                        {
                            $not_working_days_count = 0;
                            if($facility_working_days_count < 7)
                                $not_working_days_count = 7-$facility_working_days_count;

                            $cur_date = $start_date;
                            $no_of_days_array['dates'][$cur_date] = $cur_date;
                            $no_of_days_array['days'][$cur_date] = App\Http\Helpers\Helpers::getDayNameByDate($cur_date);
                            for($j = 1; $j<$no_of_occurrence; $j++)
                            {                      
                                if($repeat_every > 1)
                                {
                                    $next_day_repeat = $repeat_every-1;
                                    $cur_date = date("Y-m-d",strtotime($cur_date."+ 1 days"));
                                    $next_date = date("Y-m-d",strtotime($cur_date."+ ".$next_day_repeat." days"));
                                    $between_dates = App\Http\Helpers\Helpers::GetNoOfDatesBetween2DatesAndCheckWithFacilityWorkingDays($cur_date, $next_date,$facility_days_working_array); 
                                    $get_between_dates_count = count($between_dates['dates']);
                                    $next_date = $between_dates['dates'][$get_between_dates_count-1];
                                    $no_of_days_array['dates'][$next_date] = $next_date;
                                    $no_of_days_array['days'][$next_date] = App\Http\Helpers\Helpers::getDayNameByDate($next_date);
                                    $cur_date = $next_date;                                            
                                }
                                else
                                {
                                    $cur_date = date("Y-m-d",strtotime($cur_date."+ 1 days"));
                                    $day = App\Http\Helpers\Helpers::getDayNameByDate($cur_date);
                                    if(in_array($day,$facility_days_working_array))
                                    {
                                        $no_of_days_array['dates'][$cur_date] = $cur_date;
                                        $no_of_days_array['days'][$cur_date] = $day;
                                    }
                                    else
                                        $j = $j-1;
                                }
                            }                                
                            $request['end_date'] = $cur_date;                         
                        }
                        /// CONDITION CHECK 4B-2 - Check end date option is never/ on /// 
                        elseif($end_date_option == 'never' || $end_date_option == 'on')
                        {
                            $request['no_of_occurrence'] = 0;
                            if($request['end_date_option'] == 'never')
                                $request['end_date'] = date("Y-m-d",strtotime($start_date."+2 years"));
                            $end_date = $request['end_date'];
                            $dates_array = App\Http\Helpers\Helpers::GetNoOfDatesBetween2Dates($start_date, $end_date);

                            $no_of_days_array['dates'][$start_date] = $start_date;
                            $no_of_days_array['days'][$start_date] = App\Http\Helpers\Helpers::getDayNameByDate($start_date);
                            unset($dates_array['dates'][$start_date]);
                            foreach($dates_array['dates'] as $key => $value)
                            {                      
                                if($repeat_every > 1)
                                {
                                    if(isset($dates_array['dates'][$value]))
                                    {
                                        $next_day_repeat = $repeat_every-1;
                                        $cur_date = $value;
                                        $to = date("Y-m-d",strtotime($value."+ ".$next_day_repeat." days"));
                                        $from_date=strtotime($cur_date);
                                        $to_date=strtotime($to);
                                        $current=$from_date;
                                        while($current<=$to_date)
                                        { 

                                            $date = date("Y-m-d",$current);
                                            $remove_current_date = [$date];
                                            $dates_array['dates'] = array_diff($dates_array['dates'],$remove_current_date);
                                          	//print_r( $dates_array['dates']);
                                            unset($dates_array['dates'][$date]);
                                            $day = App\Http\Helpers\Helpers::getDayNameByDate($current,'date');
                                            if(in_array($day,$facility_days_working_array))
                                            {
                                                $current=$current+86400;                                             
                                            }
                                            else
                                            {

                                                $to = date("Y-m-d",strtotime($to."+ 1 days"));
                                                $to_date = strtotime($to);
                                                $current=$current+86400;
                                            }                                         
                                        }   
                                        $no_of_days_array['dates'][$date] = $date;
                                        
                                        $no_of_days_array['days'][$date] = App\Http\Helpers\Helpers::getDayNameByDate($date);
                                    }
                                }
                                else
                                {
                                    $day = $dates_array['days'][$value];
                                    if(in_array($day,$facility_days_working_array))
                                    {
                                        $no_of_days_array['dates'][$value] = $value;
                                        $no_of_days_array['days'][$value] = $day;
                                    }
                                }
                            }                            
                        }  
 
                        $no_of_days_array['avaialble_days'] = array_values(array_unique($no_of_days_array['days']));
						$day_start_of = strtotime($request['start_date']);
						$day_end_of = strtotime($request['end_date']);
						$curr_diff = $day_end_of-$day_start_of;
					// Current Day plus one JIRA issue MED-2893	
					if($curr_diff == 86400){					
                       foreach($no_of_days_array as $key=> $no_of_days )
                        {
                        	array_pop($no_of_days_array[$key]);
    						//unset ($array[$key][count($array[$key])-1]);                        	
                  	      }
                  	}      
                        //$no_of_days_array['avaialble_days'] = array_shift(array_values(array_unique($no_of_days_array['days']));

                        $user_not_selected_days = array_diff($no_of_days_array['avaialble_days'],$user_selected_days_array);

                        /// CONDITION CHECK 4B-3 - Check user selected days by selected dates(# of dates) /// 
                        if(count($user_not_selected_days)>0)
                        {
                            $arrange_days_ascending = App\Http\Helpers\Helpers::arrangeDaysByAscending($user_not_selected_days);
                            $need_to_select_days_error = implode(', ', $arrange_days_ascending);
                            $error = Lang::get("practice/practicemaster/providerscheduler.validation.select_timing_days")." ".$need_to_select_days_error;
                            $error_array['error_type'] = 'days_timings';
                            $error_array['error_type_value'] = $need_to_select_days_error;
                            return Response::json(array('status'=>'error', 'message'=>$error,'data'=>compact('error_array')));
                        }
                    }
                    /// CONDITION CHECK 5 - To check schedule type - Weekly ///
                    elseif($schedule_type == 'Weekly')
                    {             	
                        //$weekly_available_days = $weekly_available_days;
                        // Before remove all option ///
                        $selected_weekly_day_before_delete_all = $weekly_available_days;
                        $get_weekly_day_selected_count = count($weekly_available_days);
                        if($get_weekly_day_selected_count == 0)
                        {
                            $error = Lang::get("practice/practicemaster/providerscheduler.validation.slt_one_day_schedule");
                            $error_array['error_type'] = 'week_day_selection';
                            $error_array['error_type_value'] = 'empty';
                            return Response::json(array('status'=>'error', 'message'=>$error,'data'=>compact('error_array')));
                        } 
						
						/// To remove array all option from an array                        
                        $get_all_key = array_search('all',$weekly_available_days);
                        
                        if($get_all_key == 0 && $weekly_available_days[0]=='all')
                            unset($weekly_available_days[$get_all_key]); 
							
                        $user_not_selected_timings_for_selected_week_days = [];
                        foreach($weekly_available_days as $days_name)
                        {
                            if(!in_array($days_name, $user_selected_days_array))
                               $user_not_selected_timings_for_selected_week_days[] = ucfirst($days_name);
                        }                        
                        
                        if(count($user_not_selected_timings_for_selected_week_days) > 0)
                        {
							//$user_not_selected_timings_for_selected_week_days_ascending = App\Http\Helpers\Helpers::arrangeDaysByAscending($user_not_selected_timings_for_selected_week_days);
                            $day_name_arary = implode(', ',$user_not_selected_timings_for_selected_week_days);
                            $error = Lang::get("practice/practicemaster/providerscheduler.validation.select_selcted_days")." (".$day_name_arary.") timings"; 
                            $error_array['error_type'] = 'days_timings';
                            $error_array['error_type_value'] = $day_name_arary;
                            return Response::json(array('status'=>'error', 'message'=>$error,'data'=>compact('error_array')));
                       }
                        
                        $no_of_days_array['facility_not_working_error'] = [];
                        foreach($weekly_available_days as $day_name)
                        {
                            if(!in_array($day_name, $facility_days_working_array))
                                $no_of_days_array['facility_not_working_error'][] = ucfirst($day_name);
                        }

                        /// Condition check 6 - To check if any month not working on selected date in month->date option ///                
                        if(count($no_of_days_array['facility_not_working_error'])>0)
                        {
							$facility_not_working_error_ascending = App\Http\Helpers\Helpers::arrangeDaysByAscending($no_of_days_array['facility_not_working_error']);
                            $day_name_arary = implode(', ',$facility_not_working_error_ascending);
                            $error = Lang::get("practice/practicemaster/providerscheduler.validation.facility_closed_day")." ".$day_name_arary;
                            $error_array['error_type'] = 'days_timings';
                            $error_array['error_type_value'] = $day_name_arary;
                            return Response::json(array('status'=>'error', 'message'=>$error,'data'=>compact('error_array')));                            
                        }   
                        if($end_date_option == 'after')
                        {
                            $cur_date = $start_date;                            
                            $repeat = ($repeat_every-1)*7;
                            $repeat = $repeat+1;
							$i=1;                  
                            for($j = 1; $j<=$no_of_occurrence; $j++)
                            {
                                $week_end_date = date("Y-m-d",strtotime($cur_date."+ 6 days"));                                    
                                $dates_array = App\Http\Helpers\Helpers::GetNoOfDatesBetween2Dates($cur_date, $week_end_date);

                                foreach($dates_array['dates'] as $key => $value)
                                {                                   
                                    $day = App\Http\Helpers\Helpers::getDayNameByDate($value);
                                    if(in_array($day, $weekly_available_days))
                                    {
                                        if(in_array($day,$facility_days_working_array))
                                        {
                                        	if(($i<= $no_of_occurrence) || ($no_of_occurrence == 0)) {
	                                            $no_of_days_array['dates'][$value] = $value;
	                                            $no_of_days_array['days'][$value] = $day;
	                                            $no_of_days_array['avaialble_days'][] = $day;
	                                            $i++;
	                                        } 
                                        }
                                        else
                                        {
                                            $no_of_days_array['facility_not_working_error'][] = ucfirst($day);
                                        }
                                    }
                                    $cur_date = date("Y-m-d",strtotime($week_end_date."+ ".$repeat." day"));   
                                }
                            }          
                             $request['end_date'] = $week_end_date;    
                        }
                        /// CONDITION CHECK 6C - Check end date option never and calculate end date /// 
                        elseif($end_date_option == 'never' || $end_date_option == 'on')
                        {
                            $request['no_of_occurrence'] = 0;
                            if($end_date_option == 'never')
                                $request['end_date'] = date("Y-m-d",strtotime($start_date."+2 years"));
                            $end_date = $request['end_date']; 
                        
                            $dates_listing = App\Http\Helpers\Helpers::GetNoOfDatesBetween2Dates($start_date, $end_date);
                            $dates_listing_array = array_values($dates_listing['dates']);
                            $dates_listing_count = count($dates_listing_array);
                            $quotient = (int)($dates_listing_count / 7);
                            $remainder = $dates_listing_count % 7;
                            $dates_listing_occurence = $quotient;
                            if($remainder > 0)
                                $dates_listing_occurence = $quotient+1;

                            $cur_date = $start_date;
                            $repeat = ($repeat_every-1)*7;
                            $repeat = $repeat+7;
                            for($j = 1; $j <= $dates_listing_occurence; $j++)
                            {
                                $array_key = array_search($cur_date,$dates_listing_array);
                                $week_array = array_slice($dates_listing_array,$array_key,7);

                                foreach($week_array as $key => $value)
                                {                                   
                                    $day = $dates_listing['days'][$value];
                                    if(in_array($day, $weekly_available_days))
                                    {
                                        if(in_array($day,$facility_days_working_array))
                                        { 
                                            $no_of_days_array['dates'][$value] = $value;
                                            $no_of_days_array['days'][$value] = $day;
                                            $no_of_days_array['avaialble_days'][] = $day;
                                        }
                                        else
                                        {
                                            $no_of_days_array['facility_not_working_error'][] = ucfirst($day);
                                        }
                                    }                                   
                                }
                                $cur_date = date("Y-m-d",strtotime($cur_date."+ ".$repeat." day"));
                                if($repeat_every > 1)
                                    $j = $j+($repeat_every-1);
                            }
                        }
                        $request['weekly_available_days'] = implode(',',$selected_weekly_day_before_delete_all);
                    }
                    /// CONDITION CHECK 6 - To check schedule type - Monthly ///
                    elseif($schedule_type == 'Monthly')
                    {
                        $visit_type = $request['monthly_visit_type'];
                        /// CONDITION CHECK 6A - Check user selected visit type, day/week/date /// 
                        if($visit_type == '' || ($visit_type == 'date' && $request['monthly_visit_type_date'] == '') || 
                          ($visit_type == 'day' && $request['monthly_visit_type_day_dayname'] == '') || 
                          ($visit_type == 'week' && $request['monthly_visit_type_week'] == ''))
                        {
                            $error = Lang::get("practice/practicemaster/providerscheduler.validation.monthly_visit_type");
                            $error_array['error_type'] = 'monthly_visit_type';
                            $error_array['error_type_value'] = 'empty';
                            return Response::json(array('status'=>'error', 'message'=>$error,'data'=>compact('error_array')));
                        }

                        /// CONDITION CHECK 6B - Check end date option afer and calculate end date /// 
                        if($end_date_option == 'after')
                        {
                            if($no_of_occurrence > 1)
                            {
                                $no_of_occurrence = $no_of_occurrence-1;
                                $total_count_by_occurrence = ($repeat_every*$no_of_occurrence);
                                $request['end_date'] = date("Y-m-t",strtotime($start_date."+ ".$total_count_by_occurrence." months"));
                            }
                            else
                                $request['end_date'] = date("Y-m-t",strtotime($start_date));
                        }
                        /// CONDITION CHECK 6C - Check end date option never and calculate end date /// 
                        elseif($end_date_option == 'never')
                        {
                            $request['no_of_occurrence'] = 0;
                            if($request['end_date_option'] == 'never')
                                $request['end_date'] = date("Y-m-t",strtotime($start_date."+2 years"));
                        }
                        /// CONDITION CHECK 6D - Check end date option on and set end date /// 
                        else
                        {
                            $request['end_date'] = date("Y-m-t",strtotime($request['end_date']));
                            $request['no_of_occurrence'] = 0;
                        }
                        $end_date = $request['end_date'];

                        /// CONDITION CHECK 6E - Check and set available days in visit is date /// 
                        if($visit_type == 'date')
                        {
                            $visit_type_date = $request['monthly_visit_type_date'];                                
                            $no_of_days_array['dates'] = [];
                            $no_of_days_array['days'] = [];
                            $no_of_days_array['avaialble_days'] = [];
                            $no_of_days_array['facility_not_working_error'] = [];
                            $time   = strtotime($start_date);
                            $month = date('Y-m', $time);
                            $start_day = date('d',$time);
                            /* Scheduled No of occurrence count*/
                            $mnth = 0;
                            if($visit_type_date >= $start_day)	{
								$mnth = 0;
							}else
							{
								// Current date not equal to add to one month
								$mnth = 1;
							}	
                            if(($request['no_of_occurrence'] != 0) && ($request['schedule_type'] == 'Monthly'))
                            {
                            	$calculate_mnth = $request['no_of_occurrence'] + $request['repeat_every']+ $mnth;
                            	$end_date = date("Y-m-t",strtotime($request['start_date']."+".$calculate_mnth. " months"));
                          	}
                          	elseif(($request['no_of_occurrence'] != 0) && ($request['schedule_type'] == 'Weekly'))	{
                          		$calculate_mnth = $request['no_of_occurrence'] + $request['repeat_every'];
   								$end_date = date("Y-m-t",strtotime($request['start_date']."+".$calculate_mnth. " week"));
   							}	
   							elseif(($request['no_of_occurrence'] != 0) && ($request['schedule_type'] == 'Daily'))
   							{	
   								$calculate_mnth = $request['no_of_occurrence'] + $request['repeat_every'];
   								$end_date = date("Y-m-t",strtotime($request['start_date']."+".$calculate_mnth. " date"));
   							}	
   							$last   = date('Y-m-t', strtotime($end_date));
                            while (strtotime($month) <= strtotime($last))
							{ 

                                $month = date('Y-m', $time);
                                $total_days = date('t', $time);
								$start_day = date('d',$time);
								// monthly_visit_type_date select current check
								if($visit_type_date >= $start_day)	{
									$check_month = date('Y-m-'.$visit_type_date, strtotime($month));

								}else
								{
									// Current date not equal to add to one month
									$check_month = date('Y-m-'.$visit_type_date, strtotime($month."+1 months"));
									$year_mnth= strtotime($check_month);
									$month = date('Y-m', $year_mnth);
									//
								}
								$day = strtolower(date('l', strtotime($check_month)));

                                if($check_month >= $start_date && $check_month <= $end_date)
                                { 
                                    if($visit_type_date <= $total_days )
                                    {
                                        if(in_array($day, $facility_days_working_array))
                                        {
                                            $no_of_days_array['dates'][$check_month] = $check_month;                                                       
                                            $no_of_days_array['days'][$check_month] = $day;
                                            $no_of_days_array['avaialble_days'][] = $day;
                                        }
                                        else
                                        {
                                            $no_of_days_array['facility_not_working_error'][] = $check_month.'('.ucfirst($day).')';
                                        }
                                    }
                                    else
                                    {
                                        $no_of_days_array['date_not_exist_error'][] = $check_month;
                                    }
                                }
                                else
                                {
                                    $no_of_days_array['chk_month_error'][] = $check_month;
                                }
                                $time = strtotime($month."+ ".$repeat_every." month");
                               
                                //$time = strtotime('+1 month', $time);
                            }
                            
                            /// Condition check 6 - To check if any month not working on selected date in month->date option ///                
                            if(count($no_of_days_array['facility_not_working_error'])>0)
                            {
                                $date_with_day_name = implode(', ',$no_of_days_array['facility_not_working_error']);
                                $error = Lang::get("practice/practicemaster/providerscheduler.validation.facility_closed_day")." ".$date_with_day_name." ".Lang::get("practice/practicemaster/providerscheduler.validation.facility_not_working");
                                $error_array['error_type'] = 'days_timings';
                                $error_array['error_type_value'] = $date_with_day_name;
                                return Response::json(array('status'=>'error', 'message'=>$error,'data'=>compact('error_array')));
                            }  

                            $no_of_days_array['avaialble_days'] = array_values(array_unique($no_of_days_array['avaialble_days']));
                            /// Condition check 7 - To check number of selected days by user and needed days selection by selected date in month->date option ///     
							$user_not_selected_days = array_diff($no_of_days_array['avaialble_days'],$user_selected_days_array);
                            if(count($user_not_selected_days)>0)
                            {
								$arrange_days_ascending = App\Http\Helpers\Helpers::arrangeDaysByAscending($user_not_selected_days);
								$need_to_select_days_error = implode(', ', $arrange_days_ascending);
								$error = Lang::get("practice/practicemaster/providerscheduler.validation.select_timing_days")." ".$need_to_select_days_error;
								$error_array['error_type'] = 'days_timings';
								$error_array['error_type_value'] = $need_to_select_days_error;
								return Response::json(array('status'=>'error', 'message'=>$error,'data'=>compact('error_array')));
							}                                     
                        }
                        elseif($visit_type == 'day')
                        {
                            $monthly_visit_day_name = $request['monthly_visit_type_day_dayname'];
                            if(!in_array($monthly_visit_day_name, $facility_days_working_array))
                            {
                                $error =Lang::get("practice/practicemaster/providerscheduler.validation.unable_process")." ".$monthly_visit_day_name;
                                $error_array['error_type'] = 'days_timings';
                                $error_array['error_type_value'] = $monthly_visit_day_name;
                                return Response::json(array('status'=>'error', 'message'=>$error,'data'=>compact('error_array')));
                            }
	                        $start_date_timestamp = strtotime($start_date);
	                        $end_date_timestamp = strtotime($end_date);        
                            $iday=0;
                            //request Schedule type based 
                           if($request['schedule_type']== 'Monthly')
                           {
                           		$current_culac = '+'.$request['repeat_every'].'month';
                           } elseif($request['schedule_type']== 'Weekly') {
                           		$current_culac = '+'.$request['repeat_every'].'week';
                           } elseif($request['schedule_type']== 'daily') {
                           		$current_culac = '+'.$request['repeat_every'].'day';	
                           }
                           $ddate = strtotime($monthly_visit_day_name, $start_date_timestamp);

                           $firstOfMonth = strtotime(date("Y-m-01", strtotime($ddate)));
								   //Apply above formula.
								   $monthOfcount = intval(date("W", strtotime($ddate))) - intval(date("W", $firstOfMonth)) ;//+ 1;
							// Looping the are days in select based. visted option also JIRA MED-2892 issue fixed.
						 	$fac = $this->weekOfMonth(date('Y-m-d',strtotime($monthly_visit_day_name, $start_date_timestamp)));
							
                           for($i = strtotime($monthly_visit_day_name, $start_date_timestamp); $i <= $end_date_timestamp; $i = strtotime($current_culac, $i))
                            {
                            	//no of occurrence based.
                            	if(($request['no_of_occurrence']> $iday) || ($request['no_of_occurrence'] == 0))
	                            {
                      	        	$e = strtotime($monthly_visit_day_name, ($i)); 
	                                $date = date('Y-m-j', $e);
	                                $firstOfMonth = strtotime(date("Y-m-t", strtotime($date)));
								   //Apply above formula.
								   $monthOfcount = intval(date("W", strtotime($date))) - intval(date("W", $firstOfMonth)) ;//+ 1;
								    $no_of_days_array['dates'][$date] = $date;
	                                $no_of_days_array['days'][$date] = $monthly_visit_day_name;                                    
	                            }
	                            $iday = $iday+1;   
                            }
                            $no_of_days_array['avaialble_days'][] = $monthly_visit_day_name;
                            $user_not_selected_days = array_diff($no_of_days_array['avaialble_days'],$user_selected_days_array);
                            $currMonthWeek = array_values($no_of_days_array['dates'])[0];
                            $currMonthday = array_values($no_of_days_array['days'])[0];
                            //$currMonthWeek= date('d/m/Y',$currMonthWeek);
                            
                            /// CONDITION CHECK 4B-3 - Check user selected days by selected dates(# of dates) /// 
                            if(count($user_not_selected_days)>0)
                            {
                                $error = Lang::get("practice/practicemaster/providerscheduler.validation.select_time_day")." ".$monthly_visit_day_name;
                                $error_array['error_type'] = 'days_timings';
                                $error_array['error_type_value'] = $monthly_visit_day_name;
                                return Response::json(array('status'=>'error', 'message'=>$error,'data'=>compact('error_array')));
                            }                               
                        }
                        elseif($visit_type == 'week')
                        {
                            $no_of_days_array['facility_not_working_error'] = [];
                            if(count($user_selected_days_array) == 0)
                            {
                                $error = Lang::get("practice/practicemaster/providerscheduler.validation.select_one_day");
                                $error_array['error_type'] = 'days_timings';
                                $error_array['error_type_value'] = 'empty';
                                return Response::json(array('status'=>'error', 'message'=>$error,'data'=>compact('error_array')));
                            }

                            foreach($user_selected_days_array as $day_name)
                            {
                                if(!in_array($day_name, $facility_days_working_array))
                                    $no_of_days_array['facility_not_working_error'][] = ucfirst($day_name);
                            }

                            /// Condition check 6 - To check if any month not working on selected date in month->date option ///                
                            if(count($no_of_days_array['facility_not_working_error'])>0)
                            {
								$facility_not_working_error_ascending = App\Http\Helpers\Helpers::arrangeDaysByAscending($no_of_days_array['facility_not_working_error']);
                                $day_name_arary = implode(', ',$facility_not_working_error_ascending);
                                $error =Lang::get("practice/practicemaster/providerscheduler.validation.days_timings").$day_name_arary;
                                $error_array['error_type'] = 'days_timings';
                                $error_array['error_type_value'] = $day_name_arary;
                                return Response::json(array('status'=>'error', 'message'=>$error,'data'=>compact('error_array')));
                            }  

                            $monthly_visit_type_week = $request['monthly_visit_type_week']; 
                            $no_of_days_array['dates'] = [];
                            $no_of_days_array['days'] = [];
                            $no_of_days_array['avaialble_days'] = [];

                            $time = strtotime($start_date);
                            $end_date_updated = $end_date;                                

                            if(date("Y-m",strtotime($start_date)) == date("Y-m",strtotime($end_date)))
                                $end_date_updated = date("Y-m-t",strtotime($end_date."+ 1 months"));

                            $last   = date('Y-m', strtotime($end_date_updated));
                            $month = date('Y-m', $time);
							while (strtotime($month) <= strtotime($last))
							{
                                $month = date('Y-m', $time);
								$dates_array_all = App\Http\Helpers\Helpers::findListOfWeeksAndStartAndEndDates($month, $monthly_visit_type_week, $no_of_days_array);

                                foreach($dates_array_all['dates'] as $value)
                                {
                                    $day = $dates_array_all['days'][$value];
                                    if(in_array($day, $user_selected_days_array))
                                    {
                                        $no_of_days_array['dates'][$value] = $value;
                                        $no_of_days_array['days'][$value] = $day; 
                                        $no_of_days_array['avaialble_days'][] = $day;
                                    }
                                }
                                $time = strtotime($month."+ ".$repeat_every." month");
                                if(date("Y-m",strtotime($start_date)) == date("Y-m",strtotime($end_date)))
                                    break;
                            }
							$no_of_days_array['avaialble_days'] = array_values(array_unique($no_of_days_array['avaialble_days']));                              
                        }
                    }
                    /***** Ends - Check and set avialable dates by schedule type whether daily/weekly/monthly also check working condition with selected dates *****/

                    // Set Facility available timings before check array diff
                    $facility_timings_details_arr = $facility_timings_details;
                    /// Get Already scheduled timings list in an array ///
                    //dates is missing 
                if(isset($no_of_days_array['dates'])) 
                {
	                    $scheduled_timings = ProviderSchedulerTime::getProviderScheduledTimings($provider_id, $no_of_days_array['dates'], $scheduler_id);
	     
	                    $available_timings['monday'] = array_diff($facility_timings_details['monday'],$scheduled_timings['monday']);
	                    $available_timings['tuesday'] = array_diff($facility_timings_details['tuesday'],$scheduled_timings['tuesday']);
	                    $available_timings['wednesday'] = array_diff($facility_timings_details['wednesday'],$scheduled_timings['wednesday']);
	                    $available_timings['thursday'] = array_diff($facility_timings_details['thursday'],$scheduled_timings['thursday']);
	                    $available_timings['friday'] = array_diff($facility_timings_details['friday'],$scheduled_timings['friday']);
	                    $available_timings['saturday'] = array_diff($facility_timings_details['saturday'],$scheduled_timings['saturday']);
	                    $available_timings['sunday'] = array_diff($facility_timings_details['sunday'],$scheduled_timings['sunday']); 
	                $user_selected_timings = [];            
                    $user_selected_timings['monday'] = [];
                    $user_selected_timings['monday'] = App\Http\Helpers\Helpers::checkScheduledTimeAvailablity($user_selected_timings['monday'], $monday_from, $monday_to, $available_timings['monday'],$facility_timings_details_arr['monday']);
                    $user_selected_timings['tuesday'] = [];
                    $user_selected_timings['tuesday'] = App\Http\Helpers\Helpers::checkScheduledTimeAvailablity($user_selected_timings['tuesday'], $tuesday_from, $tuesday_to, $available_timings['tuesday'], $facility_timings_details_arr['tuesday']);
                    $user_selected_timings['wednesday'] = [];
                    $user_selected_timings['wednesday'] = App\Http\Helpers\Helpers::checkScheduledTimeAvailablity($user_selected_timings['wednesday'], $wednesday_from, $wednesday_to, $available_timings['wednesday'],$facility_timings_details_arr['wednesday']);
                    $user_selected_timings['thursday'] = [];
                    $user_selected_timings['thursday'] = App\Http\Helpers\Helpers::checkScheduledTimeAvailablity($user_selected_timings['thursday'], $thursday_from, $thursday_to, $available_timings['thursday'],$facility_timings_details_arr['thursday']);
                    $user_selected_timings['friday'] = [];
                    $user_selected_timings['friday'] = App\Http\Helpers\Helpers::checkScheduledTimeAvailablity($user_selected_timings['friday'], $friday_from, $friday_to, $available_timings['friday'],$facility_timings_details_arr['friday']);
                    $user_selected_timings['saturday'] = [];
                    $user_selected_timings['saturday'] = App\Http\Helpers\Helpers::checkScheduledTimeAvailablity($user_selected_timings['saturday'], $saturday_from, $saturday_to, $available_timings['saturday'],$facility_timings_details_arr['saturday']);
                    $user_selected_timings['sunday'] = [];
                    $user_selected_timings['sunday'] = App\Http\Helpers\Helpers::checkScheduledTimeAvailablity($user_selected_timings['sunday'], $sunday_from, $sunday_to, $available_timings['sunday'],$facility_timings_details_arr['sunday']);

                    
                    /// Starts - Check if any errors/conflicts in selected dates and timings and insert/update record ///
                    if($user_selected_timings['monday']['error_count'] == 0 && $user_selected_timings['tuesday']['error_count'] == 0 &&
                       $user_selected_timings['wednesday']['error_count'] == 0 && $user_selected_timings['thursday']['error_count'] == 0 && 
                       $user_selected_timings['friday']['error_count'] == 0 && $user_selected_timings['saturday']['error_count'] == 0 && 
                       $user_selected_timings['sunday']['error_count'] == 0)
                    {
					$unset_days_time_array = array_diff(array('monday','tuesday','wednesday','thursday','friday','saturday','sunday'),$no_of_days_array['avaialble_days']);

						foreach($unset_days_time_array as $unset_day_name)
						{
							$request[$unset_day_name.'_selected_times'] = ',,';
						}
                        /// Start - set reminder settings ///
                       
                        if(@$request['provider_reminder_sms'] == 'sms')
                            $request['provider_reminder_sms'] = 'on';
                        else
                            $request['provider_reminder_sms'] = 'off';

                        if(@$request['provider_reminder_phone'] == 'phone')
                            $request['provider_reminder_phone'] = 'on';
                        else
                            $request['provider_reminder_phone'] = 'off';

                        if(@$request['provider_reminder_email'] == 'email')
                            $request['provider_reminder_email'] = 'on';
                        else
                            $request['provider_reminder_email'] = 'off';
						
                        /// Start - set reminder settings ///

                        /// Starts - Reset monthly and weekly options ///
                        if($schedule_type == 'Weekly')
                        {
                            $request['monthly_visit_type_date'] = '';  
                            $request['monthly_visit_type_day_week'] = '';   
                            $request['monthly_visit_type_day_dayname'] = '';   
                            $request['monthly_visit_type_week'] = '';   
                        }
                        if($schedule_type == 'Daily')
                        {
                            $request['weekly_available_days'] = '';
                            $request['monthly_visit_type_date'] = '';  
                            $request['monthly_visit_type_day_week'] = '';   
                            $request['monthly_visit_type_day_dayname'] = '';   
                            $request['monthly_visit_type_week'] = '';   
                        }
						if($schedule_type == 'Monthly')
                        {
                            $request['weekly_available_days'] = '';
                        }
                        /// Ends - Reset monthly and weekly options ///
						$request['created_by'] = Auth::user()->id;
                        $request['updated_by'] = Auth::user()->id;
                        if($request['end_date_option'] == 'never')
                            $request['end_date'] = '0000-00-00';

						if($scheduler_id == '') {
							if(!isset($request['status']))
								$request['status'] = 'Active';
							$provider_scheduler = ProviderScheduler::create($request);   
						} else {
							$provider_scheduler = ProviderScheduler::find($scheduler_id);
							// End date validation for after choose option
							if ($request['end_date_option']== 'after' && isset($no_of_days_array['dates']) ) {
								$request['end_date']  = end($no_of_days_array['dates']);
							} else {
								$request['end_date'] = $end_date;
							}
							$provider_scheduler->update($request);
							ProviderSchedulerTime::where('provider_scheduler_id',$scheduler_id)->where('schedule_date','>=',$today_date)->delete();
						}
						$provider_scheduler_id = $provider_scheduler->id;
							$i=1;
						$error_flag = 0;$insert_arr = [];		
                        foreach($no_of_days_array['dates'] as $date_value)
                        {
                            $day = $no_of_days_array['days'][$date_value];
 
                            if($user_selected_timings[$day]['available_count']>0)
                            {
                                $no_of_time_selection_name  = $day.'_selected_times';
                                $split_day_time = explode(',', $request[$no_of_time_selection_name]);
                                //$count = count($split_day_time);
                               
                                for($s=0;$s<$user_selected_timings[$day]['available_count'];$s++) 
                                { 
                                	if( ($i<= $provider_scheduler->no_of_occurrence) || ( $provider_scheduler->no_of_occurrence == 0)) 
                                	{
	                                    $time_split = explode('-',$split_day_time[$s]);                            
	                                    $from_time = $time_split[0];
	                                    $to_time = $time_split[1];
	                                    $scheduler_time['provider_scheduler_id'] = $provider_scheduler_id;
	                                    $scheduler_time['facility_id'] = $facility_id;
	                                    $scheduler_time['provider_id'] = $provider_id;
	                                    $scheduler_time['schedule_date'] = $date_value;
	                                    $scheduler_time['day'] = $day;
	                                    $scheduler_time['from_time'] = $from_time;
	                                    $scheduler_time['to_time'] = $to_time;
	                                    $scheduler_time['schedule_type'] = $schedule_type;
	                                    $scheduler_time['created_by'] = Auth::user()->id;
	                                    $scheduler_time['updated_by'] = Auth::user()->id;  
										$scheduler_time['sms_reminder_status'] = ($request['provider_reminder_sms']=='off')?"Yes":"No";
										$scheduler_time['phone_reminder_status'] = ($request['provider_reminder_phone']=='off')?"Yes":"No";
										$scheduler_time['email_reminder_status'] = ($request['provider_reminder_email']=='off')?"Yes":"No";
	                                    $providerschedulers_cnt = PatientAppointment::where('provider_id',$provider_id)->where('facility_id',$facility_id)->where('scheduled_on','=',$date_value)->where('checkin_time','<=',strtoupper($from_time))->where('checkout_time','>=',strtoupper($to_time))->count();									
	                                    if($providerschedulers_cnt != 0){
											$error_flag = 1;											
										}else{					
											$insert_arr[] = $scheduler_time;		                                    
	                               		 }
	                                    
	                                }$i=$i+1;
                                }
                            }                   
                        }
                       if($error_flag != 1){                        	
                        	foreach($insert_arr as $arr){                       		                 	
                        	 ProviderSchedulerTime::create($arr);
                        	}
                        	 return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg")));
                        }else{
                        	\Log::info($error_flag);
                        	 return Response::json(array('status'=>'error', 'message'=>'Patient Appointment Exist','data'=>compact('error_array')));                        	 
                        }  
                    }
                    else
                    {
   
						$show_error_msg = '';
                        if($user_selected_timings['monday']['error_count'] > 0)
                            $show_error_msg .= 'Monday ('.$user_selected_timings['monday']['error_msg'].')';
                        
                        if($user_selected_timings['tuesday']['error_count'] > 0)
                        {
                            if($show_error_msg != '')
                                $show_error_msg .= ', ';
                        
                            $show_error_msg .= 'Tuesday ('.$user_selected_timings['tuesday']['error_msg'].')';
                        }
                        
                        if($user_selected_timings['wednesday']['error_count'] > 0)
                        {
                            if($show_error_msg != '')
                                $show_error_msg .= ', ';
                            $show_error_msg .= 'Wednesday ('.$user_selected_timings['wednesday']['error_msg'].')';
                        }
                        
                        
                        if($user_selected_timings['thursday']['error_count'] > 0)
                        {
                            if($show_error_msg != '')
                                $show_error_msg .= ', ';    
                            $show_error_msg .= 'Thursday ('.$user_selected_timings['thursday']['error_msg'].')';
                        }
                        
                        if($user_selected_timings['friday']['error_count'] > 0)
                        {
                            if($show_error_msg != '')
                                $show_error_msg .= ', ';
                            $show_error_msg .= 'Friday ('.$user_selected_timings['friday']['error_msg'].')';
                        }
                        
                        if($user_selected_timings['saturday']['error_count'] > 0)
                        {
                            if($show_error_msg != '')
                                $show_error_msg .= ', ';
                            $show_error_msg .= 'Saturday ('.$user_selected_timings['saturday']['error_msg'].')';
                        }
                        
                        if($user_selected_timings['sunday']['error_count'] > 0)
                        {
                            if($show_error_msg != '')
                                $show_error_msg .= ', ';
                            $show_error_msg .= 'Sunday ('.$user_selected_timings['sunday']['error_msg'].')';
                        }
                        $error = Lang::get("practice/practicemaster/providerscheduler.validation.conflict_error").' '.$show_error_msg;
                        $error_array['error_type'] = 'conflict_error';
                        $error_array['error_type_value'] = $show_error_msg;
                        return Response::json(array('status'=>'error', 'message'=>$error,'data'=>compact('error_array')));
                    }
                }
                else {
                	$error = Lang::get("practice/practicemaster/providerscheduler.validation.start_end_date_work");
                    $error_array['error_type'] = 'facility_wont_work';
                    $error_array['error_type_value'] = '';
                    return Response::json(array('status'=>'error', 'message'=>$error,'data'=>compact('error_array')));
                   /// Starts - Check if any errors/conflict in selected dates and timings and insert/update record ///

               	}
                }
                else
                {
                    $error = Lang::get("practice/practicemaster/providerscheduler.validation.facility_wont_work");
                    $error_array['error_type'] = 'facility_wont_work';
                    $error_array['error_type_value'] = '';
                    return Response::json(array('status'=>'error', 'message'=>$error,'data'=>compact('error_array')));
                }
            } 
            else
            {
                $error = Lang::get("practice/practicemaster/providerscheduler.validation.mismatch_time");
                $error_array['error_type'] = 'mismatch_time_selection';
                $error_array['error_type_value'] = $mismatch_time_count_by_day;
                return Response::json(array('status'=>'error', 'message'=>$error,'data'=>compact('error_array')));
            }
        }
        else
        {
            $error = Lang::get("practice/practicemaster/providerscheduler.validation.from_to_time");
            $error_array['error_type'] = 'invalid_start_end_date';
            $error_array['error_type_value'] = '';
            return Response::json(array('status'=>'error', 'message'=>$error,'data'=>compact('error_array')));
        }
    }
	/********************** End provider scheduler store process ***********************************/
	
	/********************** Start view provider scheduler details individual ***********************************/
    public function viewProviderSchedulerDetailsByIdApi($provider_id,$schedule_id)
    {
		$provider_id = Helpers::getEncodeAndDecodeOfId($provider_id,'decode');
		$schedule_id = Helpers::getEncodeAndDecodeOfId($schedule_id,'decode');
		
		if(ProviderScheduler::where('provider_id', $provider_id)->where('id',$schedule_id)->count()>0 && is_numeric($provider_id) && is_numeric($schedule_id)) 
		{
			$providerschedulers = ProviderScheduler::with('facility')->where('provider_id',$provider_id)->where('id',$schedule_id)->orderBy('facility_id','ASC')->first();
			
			if($providerschedulers)
			{
				/// Split and display seelcted timings by day ///
				// $providerschedulers->monday_selected_times = App\Http\Helpers\Helpers::checkAndsetVisitTimingsByDay($providerschedulers->monday_selected_times);
				// $providerschedulers->tuesday_selected_times = App\Http\Helpers\Helpers::checkAndsetVisitTimingsByDay($providerschedulers->tuesday_selected_times);
				// $providerschedulers->wednesday_selected_times = App\Http\Helpers\Helpers::checkAndsetVisitTimingsByDay($providerschedulers->wednesday_selected_times);
				// $providerschedulers->thursday_selected_times = App\Http\Helpers\Helpers::checkAndsetVisitTimingsByDay($providerschedulers->thursday_selected_times);
				// $providerschedulers->friday_selected_times = App\Http\Helpers\Helpers::checkAndsetVisitTimingsByDay($providerschedulers->friday_selected_times);
				// $providerschedulers->saturday_selected_times = App\Http\Helpers\Helpers::checkAndsetVisitTimingsByDay($providerschedulers->saturday_selected_times);
				// $providerschedulers->sunday_selected_times = App\Http\Helpers\Helpers::checkAndsetVisitTimingsByDay($providerschedulers->sunday_selected_times);

                //Encode ID for providerschedulers
                $temp = new Collection($providerschedulers);

                $temp_monday_selected_times = $temp['monday_selected_times'];
                $temp->pull('monday_selected_times');
                $temp_encode_monday_selected_times = App\Http\Helpers\Helpers::checkAndsetVisitTimingsByDay($temp_monday_selected_times);
                $temp->prepend($temp_encode_monday_selected_times, 'monday_selected_times');

                $temp_tuesday_selected_times = $temp['tuesday_selected_times'];
                $temp->pull('tuesday_selected_times');
                $temp_encode_tuesday_selected_times = App\Http\Helpers\Helpers::checkAndsetVisitTimingsByDay($temp_tuesday_selected_times);
                $temp->prepend($temp_encode_tuesday_selected_times, 'tuesday_selected_times');

                $temp_wednesday_selected_times = $temp['wednesday_selected_times'];
                $temp->pull('wednesday_selected_times');
                $temp_encode_wednesday_selected_times = App\Http\Helpers\Helpers::checkAndsetVisitTimingsByDay($temp_wednesday_selected_times);
                $temp->prepend($temp_encode_wednesday_selected_times, 'wednesday_selected_times');

                $temp_thursday_selected_times = $temp['thursday_selected_times'];
                $temp->pull('thursday_selected_times');
                $temp_encode_thursday_selected_times = App\Http\Helpers\Helpers::checkAndsetVisitTimingsByDay($temp_thursday_selected_times);
                $temp->prepend($temp_encode_thursday_selected_times, 'thursday_selected_times');

                $temp_friday_selected_times = $temp['friday_selected_times'];
                $temp->pull('friday_selected_times');
                $temp_encode_friday_selected_times = App\Http\Helpers\Helpers::checkAndsetVisitTimingsByDay($temp_friday_selected_times);
                $temp->prepend($temp_encode_friday_selected_times, 'friday_selected_times');

                $temp_saturday_selected_times = $temp['saturday_selected_times'];
                $temp->pull('saturday_selected_times');
                $temp_encode_saturday_selected_times = App\Http\Helpers\Helpers::checkAndsetVisitTimingsByDay($temp_saturday_selected_times);
                $temp->prepend($temp_encode_saturday_selected_times, 'saturday_selected_times');

                $temp_sunday_selected_times = $temp['sunday_selected_times'];
                $temp->pull('sunday_selected_times');
                $temp_encode_sunday_selected_times = App\Http\Helpers\Helpers::checkAndsetVisitTimingsByDay($temp_sunday_selected_times);
                $temp->prepend($temp_encode_sunday_selected_times, 'sunday_selected_times');

                $data = $temp->all();
                $providerschedulers = json_decode(json_encode($data), FALSE);
                //Encode ID for providerschedulers
			
				$monday_dates = ProviderSchedulerTime::where('provider_id',$provider_id)->where('provider_scheduler_id',$schedule_id)->where('day','monday')->where('schedule_date','>=',$providerschedulers->start_date)->groupBy('schedule_date')->orderBy('schedule_date','ASC')->get();
				$tuesday_dates = ProviderSchedulerTime::where('provider_id',$provider_id)->where('provider_scheduler_id',$schedule_id)->where('day','tuesday')->where('schedule_date','>=',$providerschedulers->start_date)->groupBy('schedule_date')->orderBy('schedule_date','ASC')->get();
				$wednesday_dates = ProviderSchedulerTime::where('provider_id',$provider_id)->where('provider_scheduler_id',$schedule_id)->where('day','wednesday')->where('schedule_date','>=',$providerschedulers->start_date)->groupBy('schedule_date')->orderBy('schedule_date','ASC')->get();
				$thursday_dates = ProviderSchedulerTime::where('provider_id',$provider_id)->where('provider_scheduler_id',$schedule_id)->where('day','thursday')->where('schedule_date','>=',$providerschedulers->start_date)->groupBy('schedule_date')->orderBy('schedule_date','ASC')->get();
				$friday_dates = ProviderSchedulerTime::where('provider_id',$provider_id)->where('provider_scheduler_id',$schedule_id)->where('day','friday')->where('schedule_date','>=',$providerschedulers->start_date)->groupBy('schedule_date')->orderBy('schedule_date','ASC')->get();
				$saturday_dates = ProviderSchedulerTime::where('provider_id',$provider_id)->where('provider_scheduler_id',$schedule_id)->where('day','saturday')->where('schedule_date','>=',$providerschedulers->start_date)->groupBy('schedule_date')->orderBy('schedule_date','ASC')->get();
				$sunday_dates = ProviderSchedulerTime::where('provider_id',$provider_id)->where('provider_scheduler_id',$schedule_id)->where('day','sunday')->where('schedule_date','>=',$providerschedulers->start_date)->groupBy('schedule_date')->orderBy('schedule_date','ASC')->get();
					
				$provider_schedulers_dates_listing_arr['monday'] = $monday_dates;
				$provider_schedulers_dates_listing_arr['tuesday'] = $tuesday_dates;
				$provider_schedulers_dates_listing_arr['wednesday'] = $wednesday_dates;
				$provider_schedulers_dates_listing_arr['thursday'] = $thursday_dates;
				$provider_schedulers_dates_listing_arr['friday'] = $friday_dates;
				$provider_schedulers_dates_listing_arr['saturday'] = $saturday_dates;
				$provider_schedulers_dates_listing_arr['sunday'] = $sunday_dates;
                $provider = Provider::with('speciality','provider_types','degrees')->where('id',$provider_id)->first();
                //Encode ID for provider
                $temp = new Collection($provider);
                $temp_id = $temp['id'];
                $temp->pull('id');
                $temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
                $temp->prepend($temp_encode_id, 'id');
                $data = $temp->all();
                $provider = json_decode(json_encode($data), FALSE);
                //Encode ID for provider
				return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('provider_schedulers_dates_listing_arr','providerschedulers','provider')));
			}
			else
			{
				return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
			}
		} 
		else 
		{
			return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
    }
	/********************** End view provider scheduler details individual ***********************************/
	public function getDeleteApi($provider_id,$id)
	{
		$provider_id = Helpers::getEncodeAndDecodeOfId($provider_id,'decode');
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$ldate = date('Y-m-d');
		
		if(ProviderScheduler::where('id', $id)->count()>0 && is_numeric($id)){
			$facility_id = ProviderScheduler::where('id', $id)->value('facility_id');
			$providerschedulers_cnt = PatientAppointment::where('provider_id',$provider_id)->where('facility_id',$facility_id)->where('scheduled_on','>=',$ldate)->count();
			if($providerschedulers_cnt>0){
				return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.provider_alert_msg"),'data'=>''));
			}
			else{
				$ProviderScheduler = ProviderScheduler::where('id',$id)->delete();
				$ProviderScheduler = ProviderSchedulerTime::where('provider_scheduler_id',$id)->delete();
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
			}
		}
		else{
			return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
	}

	public function getSaturdayDay($year, $month, $position) {
	   $firstDay = date('w', mktime(0, 0, 0, $month, 1, $year));
	   $diff = 6 - $firstDay;

	   return 1 + $diff + $position * 7;
	}
public function weekOfMonth($date) {
   $firstOfMonth = date("Y-m-01", strtotime($date));
   return intval(date("W", strtotime($date))) - intval(date("W", strtotime($firstOfMonth)));
}

	
}