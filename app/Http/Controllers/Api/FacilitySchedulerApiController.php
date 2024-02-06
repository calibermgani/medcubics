<?php namespace App\Http\Controllers\Api;

use Auth;
use Request;
use View;
use Response;
use App\Http\Controllers\Controller;
use App\Models\Provider as Provider;
use App\Models\Speciality as Speciality;
use App\Models\Pos as Pos;
use App\Models\Provider_type as ProviderType;
use App\Models\Facility as Facility;
use App\Models\ProviderScheduler as ProviderScheduler;
use App\Models\ProviderSchedulerTime as ProviderSchedulerTime;
use App\Models\Scheduler\PatientAppointment as PatientAppointment;
use App\Http\Helpers\Helpers as Helpers;
use Config;
use App;
use DB;
use Lang;

class FacilitySchedulerApiController extends Controller 
{
    
	/********************** Start Display a listing of the Facility scheduler ***********************************/
	public function getIndexApi( $export='')
    {
        
		$request = Request::all();
		$query 	 = Facility::with('facility_address','speciality_details','pos_details')->where('status','Active');
		
		if(@$request['sch_shortname'] != ''){
			$sch_shortname = $request['sch_shortname'];
			$query->whereRaw("(short_name LIKE '%$sch_shortname%' or short_name LIKE '%$sch_shortname' or short_name LIKE '$sch_shortname%')");
		}
		if(@$request['sch_facility'] != ''){
			$sch_facilityname = $request['sch_facility'];
			$query->whereRaw("(facility_name LIKE '%$sch_facilityname%' or facility_name LIKE '%$sch_facilityname' or facility_name LIKE '$sch_facilityname%')");
		}
		if(@$request['sch_speciality'] != ''){
			$query->where('speciality_id', $request['sch_speciality']);
		}
		if(@$request['sch_pos'] != ''){
			$query->where('pos_id', $request['sch_pos']);
		}
		
		$facilities 	= $query->orderBy('short_name','ASC')->get()->toArray();
		
		if(@$request['sch_scheduled'] == 'Yes'){
			$facilities_all 	= $facilities;
			$facilities 		= array();
			if(count($facilities_all)){
				$cc = 0;
				foreach($facilities_all as $ini_fac_arr){
					if((ProviderScheduler::where('facility_id',$ini_fac_arr['id'])->count())>0){
					$facilities[$cc] = $ini_fac_arr;
					$cc++;
					}
				}
			}
		}
		
		if(@$request['sch_scheduled'] == 'No'){
			$facilities_all 	= $facilities;
			$facilities 		= array();
			if(count($facilities_all)){
				$cc = 0;
				foreach($facilities_all as $ini_fac_arr){
					if((ProviderScheduler::where('facility_id',$ini_fac_arr['id'])->count())==0){
					$facilities[$cc] = $ini_fac_arr;
					$cc++;
					}
				}
			}
		}
		
		if($export != "") 
		{
			$fa_r = $fa_list = array();
			foreach($facilities as $key=>$fa_value)
			{
				$fa_r['short_name']		= @$fa_value['short_name'];
				$fa_r['facility_name']	= @$fa_value['facility_name'];
				$fa_r['speciality_id']	= @$fa_value['speciality_details']['speciality'];
				$fa_r['pos_id']			= @$fa_value['pos_details']['code'];
				$fa_r['city']			= @$fa_value['facility_address']['city'];
				$fa_r['state']			= @$fa_value['facility_address']['state'];
				
				$scheduled_count		= ProviderScheduler::getScheduledCountByProviderId(Helpers::getEncodeAndDecodeOfId(@$fa_value['id'],'encode'),'Facility');
				if($scheduled_count > 0){
					$fa_r['scheduled'] = "Yes";
				}
				else{
					$fa_r['scheduled'] = "No";
				}
				$fa_list[$key] 			= $fa_r;
				unset($fa_r);
			}
			
			$get_fa_list = json_decode(json_encode($fa_list));
			
			$exportparam = array(
				'filename'		=>	'Facility_Schedular',
				'heading'		=>	'Facility_Schedular',
				'fields' 		=>	array(
					'short_name'	=> 'Short name',		
					'facility_name'	=> 'Facility',	
					'speciality_id'	=> 'Speciality',
					'pos_id'		=> 'POS',
					'city'			=> 'City',
					'state'			=> 'State',
					'scheduled'		=> 'Scheduled'
					)
			);
			
			$callexport = new CommonExportApiController();
			return $callexport->generatemultipleExports($exportparam, $get_fa_list, $export); 
		}
		
		/*$facilities_rec = DB::table('facilities')->selectRaw('id, short_name, facility_name')->where('status','Active')->where('deleted_at',NULL);
		$all_short_nam  = $facilities_rec->orderBy('short_name','ASC')->pluck('short_name','short_name')->all();
		$all_facl_nam   = $facilities_rec->orderBy('facility_name','ASC')->pluck('facility_name','id')->all();*/
		$all_speciality	= Speciality::has('facility')->pluck('speciality','id')->all();
		$all_pos		= Pos::has('facility')->pluck('id','id')->all();
		
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('facilities','all_speciality','all_pos')));
		
	}
	/********************** End Display a listing of the Facility scheduler ***********************************/
	
	/********************** Start view facility scheduler list page ***********************************/
    public function viewFacilitySchedulerApi($facility_id, $export='' )
    {
		$facility_id = Helpers::getEncodeAndDecodeOfId($facility_id,'decode');
		
		if(Facility::where('id', $facility_id)->count()>0 && is_numeric($facility_id)) 
		{
			$facilityschedulers = ProviderScheduler::with('provider','provider.degrees')->where('facility_id',$facility_id)->groupBy('provider_id')->orderBy('provider_id','ASC')->get();
			$facility 			= Facility::with('county','facility_address')->where('id',$facility_id)->first();
			$encode_facility_id = Helpers::getEncodeAndDecodeOfId($facility_id,'encode');
			if($export != "")
			{
				$fac_sch_r = $fac_sch_list = array();
				foreach($facilityschedulers as $key_ff=>$fac_sch_value_ff)
				{
					$encode_provider_id 	= Helpers::getEncodeAndDecodeOfId($fac_sch_value_ff->provider_id,'encode');
					$allfacilityschedulers 	= ProviderScheduler::getAllfacilitySchedulerByProviderId($encode_provider_id,$encode_facility_id);
					
					foreach($allfacilityschedulers as $key=>$fac_sch_value)
					{
					
					$fac_sch_r['facility_name']	= @$facility->facility_name;
					$fac_sch_r['provider_name']	= @$fac_sch_value->provider->provider_name;
					$fac_sch_r['schedule_type']	= @$fac_sch_value->schedule_type;
					$fac_sch_r['start_date']	= Helpers::dateFormat(@$fac_sch_value->start_date,'date');
					$fac_sch_r['end_date']		= (@$fac_sch_value->end_date_option != 'never')?Helpers::dateFormat(@$fac_sch_value->end_date,'date'):'Never' ;
					$fac_sch_r['no_of_occurrence']	= (@$fac_sch_value->end_date_option == 'after')?@$fac_sch_value->no_of_occurrence:'--' ;
					$fac_sch_r['repeat_every']	= '';
					
					if(@$fac_sch_value->repeat_every > 1){
						$fac_sch_r['repeat_every']	.= @$fac_sch_value->repeat_every;
					}
					
					if(@$fac_sch_value->schedule_type == 'Daily'){
						$fac_sch_r['repeat_every']	.= " Days";
					}
					elseif(@$fac_sch_value->schedule_type == 'Weekly'){
						$fac_sch_r['repeat_every']	.= " Weeks";
					}
					elseif(@$fac_sch_value->schedule_type == 'Monthly'){
						$fac_sch_r['repeat_every']	.= " Months";
					}
					
					$fac_sch_list[$key_ff.$key] 		= $fac_sch_r;
					unset($fac_sch_r);
					
					}
				}
				
				$get_fac_sch_list = json_decode(json_encode($fac_sch_list));
				
				$exportparam = array(
					'filename'		=>	'Facility_View_Schedular',
					'heading'		=>	'Facility_View_Schedular',
					'fields' 		=>	array(
						'facility_name'		=> 'Facility',	
						'provider_name'		=> 'Provider',		
						'schedule_type'		=> 'Schedule Type',
						'start_date'		=> 'Start Date',
						'end_date'			=> 'End Date',
						'no_of_occurrence'	=> 'No of Occurrence',
						'repeat_every'		=> 'Repeat Every'
						)
				);
				
				$callexport = new CommonExportApiController();
				return $callexport->generatemultipleExports($exportparam, $get_fac_sch_list, $export);
			}
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('facilityschedulers','facility')));
		} 
		else 
		{
			return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
	}
	/********************** End view facility scheduler list page ***********************************/
	
	/********************** Start view facility scheduler details individual ***********************************/
	public function viewFacilitySchedulerDetailsByIdApi($facility_id,$schedule_id)
    {
		$facility_id = Helpers::getEncodeAndDecodeOfId($facility_id,'decode');
		$schedule_id = Helpers::getEncodeAndDecodeOfId($schedule_id,'decode');
	
		if(ProviderScheduler::where('facility_id',$facility_id)->where('id',$schedule_id)->count()>0 && is_numeric($facility_id) && is_numeric($schedule_id))
		{
			$facilityschedulers = ProviderScheduler::with('provider','provider.degrees')->where('facility_id',$facility_id)->where('id',$schedule_id)->orderBy('provider_id','ASC')->first();
        
			if($facilityschedulers)
			{
				/// Split and display seelcted timings by day ///
				$facilityschedulers->monday_selected_times = App\Http\Helpers\Helpers::checkAndsetVisitTimingsByDay($facilityschedulers->monday_selected_times);
				$facilityschedulers->tuesday_selected_times = App\Http\Helpers\Helpers::checkAndsetVisitTimingsByDay($facilityschedulers->tuesday_selected_times);
				$facilityschedulers->wednesday_selected_times = App\Http\Helpers\Helpers::checkAndsetVisitTimingsByDay($facilityschedulers->wednesday_selected_times);
				$facilityschedulers->thursday_selected_times = App\Http\Helpers\Helpers::checkAndsetVisitTimingsByDay($facilityschedulers->thursday_selected_times);
				$facilityschedulers->friday_selected_times = App\Http\Helpers\Helpers::checkAndsetVisitTimingsByDay($facilityschedulers->friday_selected_times);
				$facilityschedulers->saturday_selected_times = App\Http\Helpers\Helpers::checkAndsetVisitTimingsByDay($facilityschedulers->saturday_selected_times);
				$facilityschedulers->sunday_selected_times = App\Http\Helpers\Helpers::checkAndsetVisitTimingsByDay($facilityschedulers->sunday_selected_times);

			
				$monday_dates = ProviderSchedulerTime::where('facility_id',$facility_id)->where('provider_scheduler_id',$schedule_id)->where('day','monday')->where('schedule_date','>=',$facilityschedulers->start_date)->groupBy('schedule_date')->orderBy('schedule_date','ASC')->get();
				$tuesday_dates = ProviderSchedulerTime::where('facility_id',$facility_id)->where('provider_scheduler_id',$schedule_id)->where('day','tuesday')->where('schedule_date','>=',$facilityschedulers->start_date)->groupBy('schedule_date')->orderBy('schedule_date','ASC')->get();
				$wednesday_dates = ProviderSchedulerTime::where('facility_id',$facility_id)->where('provider_scheduler_id',$schedule_id)->where('day','wednesday')->where('schedule_date','>=',$facilityschedulers->start_date)->groupBy('schedule_date')->orderBy('schedule_date','ASC')->get();
				$thursday_dates = ProviderSchedulerTime::where('facility_id',$facility_id)->where('provider_scheduler_id',$schedule_id)->where('day','thursday')->where('schedule_date','>=',$facilityschedulers->start_date)->groupBy('schedule_date')->orderBy('schedule_date','ASC')->get();
				$friday_dates = ProviderSchedulerTime::where('facility_id',$facility_id)->where('provider_scheduler_id',$schedule_id)->where('day','friday')->where('schedule_date','>=',$facilityschedulers->start_date)->groupBy('schedule_date')->orderBy('schedule_date','ASC')->get();
				$saturday_dates = ProviderSchedulerTime::where('facility_id',$facility_id)->where('provider_scheduler_id',$schedule_id)->where('day','saturday')->where('schedule_date','>=',$facilityschedulers->start_date)->groupBy('schedule_date')->orderBy('schedule_date','ASC')->get();
				$sunday_dates = ProviderSchedulerTime::where('facility_id',$facility_id)->where('provider_scheduler_id',$schedule_id)->where('day','sunday')->where('schedule_date','>=',$facilityschedulers->start_date)->groupBy('schedule_date')->orderBy('schedule_date','ASC')->get();
					
				$facility_schedulers_dates_listing_arr['monday'] = $monday_dates;
				$facility_schedulers_dates_listing_arr['tuesday'] = $tuesday_dates;
				$facility_schedulers_dates_listing_arr['wednesday'] = $wednesday_dates;
				$facility_schedulers_dates_listing_arr['thursday'] = $thursday_dates;
				$facility_schedulers_dates_listing_arr['friday'] = $friday_dates;
				$facility_schedulers_dates_listing_arr['saturday'] = $saturday_dates;
				$facility_schedulers_dates_listing_arr['sunday'] = $sunday_dates;
		   
				$facility = Facility::with('county','facility_address')->where('id',$facility_id)->first();
				
				return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('facility_schedulers_dates_listing_arr','facilityschedulers','facility')));
			}
			else
			{
				return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
			}
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/********************** End view facility scheduler details individual ***********************************/
	
	public function getDeleteApi($facility_id, $id)
	{
		$facility_id = Helpers::getEncodeAndDecodeOfId($facility_id,'decode');
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$ldate = date('Y-m-d');
		
		if(ProviderScheduler::where('facility_id',$facility_id)->where('id',$id)->count()>0 && is_numeric($facility_id) && is_numeric($id)){
			$provider_id = ProviderScheduler::where('id', $id)->pluck('provider_id')->first();
			$providerschedulers_cnt = PatientAppointment::where('provider_id',$provider_id)->where('facility_id',$facility_id)->where('scheduled_on','>=',$ldate)->count();
			if($providerschedulers_cnt>0){
				return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.facility_alert_msg"),'data'=>''));
			}
			else{
				$ProviderScheduler = ProviderScheduler::where('id',$id)->delete();
				$ProviderScheduler = ProviderSchedulerTime::where('provider_scheduler_id',$id)->delete();
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
			}
		}
		else{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
		
	}
}
