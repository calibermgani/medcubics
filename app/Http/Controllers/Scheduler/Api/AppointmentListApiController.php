<?php namespace App\Http\Controllers\Scheduler\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Scheduler\PatientAppointment as PatientAppointment;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Patients\Patient as Patients;
use App\Http\Controllers\Claims\ClaimControllerV1 as ClaimControllerV1;
use App\Models\Provider as Provider;
use App\Models\Facility;
use Response;
use Request;
use DB;
class AppointmentListApiController extends Controller {

	/**
	 	* Display a listing of the resource.
	 *
		* Scheduler Appointment listing page
	 	* @return Response
	 */
	public function getIndexApi($export = '', $type = '', $appCheck = '')
	{
		$request = Request::all();
			/* Converting value to default search based */
		if(isset($request['export']) && $request['export'] == 'yes'){
			foreach($request as $key=>$value){
				if(strpos($value, ',') !== false && $key != 'patient_name'){
					$request['dataArr']['data'][$key] = json_encode(explode(',',$value));
				}else{
					$request['dataArr']['data'][$key] = json_encode($value);	
				}
			}
		}
		//$insurances = App\Models\Patients\PatientInsurance::getAllPatientInsuranceList(); 
		$start = $len = 1;
		$search = $searchFor = $torderByField ='';
		$orderByField = 'scheduled_on';
		$orderByDir = 'DESC';
		$count = 0;
		$patient_app = PatientAppointment::with('patient','reasonforvisit','facility','facility.speciality_details','facility.pos_details','facility.facility_address','provider','provider.provider_type_details','provider.provider_types','provider.degrees')->where('patient_appointments.patient_id', '<>', 0);

		/*
			* Sortting order by field wise 
		*/
		if(!empty($request['order'])){
			$orderByField = ($request['order'][0]['column']) ? $request['order'][0]['column'] : 'percentage';
			switch ($orderByField) {
				case '0':
					$torderByField = 'patient.account_no';
					$orderByField = 'created_at';
					break;

				case '1':
					$torderByField = 'patient.last_name';
					$orderByField = 'created_at';
					break;

				case '2':
					$torderByField = 'patient.dob';
					$orderByField = 'created_at';
					break;

				case '3':
					$torderByField = 'facility.short_name';
					$orderByField = 'created_at';
					break;

				case '4':
					$torderByField = 'provider.short_name';
					$orderByField = 'created_at';
					break;
				case '5':
					$torderByField = 'scheduled_on';
					$orderByField = 'scheduled_on';
					break;	
										
				case '6':
					$torderByField = 'appointment_time';	
					$orderByField = 'appointment_time';
					break;
				case '8':
					$torderByField = 'status';	
					$orderByField = 'status';
				default:
					$orderByField = 'created_at';
					break;
			}
			$orderByDir = ($request['order'][0]['dir']) ? $request['order'][0]['dir'] : 'ASC';
		}

		if(count($request)>0){
			$start = (!empty($request['start'])) ? $request['start'] :'';
			$len = (!empty($request['start'])) ? $request['start'] :'';	
			if(!empty($request['search']['value'])) {
				$search= trim($request['search']['value']);
			}
			if(!empty($request['columns'])){
				foreach ($request['columns'] as $columns) {					
					if($columns['searchable'] == 'true'){
						$searchFor = $columns['data'];
					}
				}
			}
			/*
				* Join Query table in Patient, table, Facility, Provider table
			*/
			
			$patient_app->Where(function ($patient_app) use ($search, $searchFor) {
				if(!empty($search)) {
					if($searchFor == '11') { 
						/* Patient Account no. serach */
						$patient_app->whereHas('patient',function($patient_app) use ($search) {
							$patient_app->where('account_no' ,'LIKE', '%'.$search.'%');						
						});
						/*
						* Patient Name serach
						*/
						$patient_app->orWhere(function ($patient_app) use ($search) { 
							$patient_app->WhereHas('patient', function($patient_app)use($search) {
								/*
								* Patient name serach comma(,) split option used to search
								*/
								if(strpos(strtolower($search), ",") !== false ){							
									$searchValues = array_filter(explode(",", $search));
									foreach ($searchValues as $value) {
										//$patient_app->orwhere(function ($q) use ($value) {
											if($value !== '') {										
												$patient_app->where('last_name', 'like', "%{$value}%")
													->orWhere('first_name', 'like', "%{$value}%")
								    				->orWhere('middle_name', 'like', "%{$value}%");							    				
								    			//whereRaw("(last_name LIKE '%{$value}%' or first_name LIKE '%{$value}%' or middle_name LIKE '%{$value}%')"); 
											}
										//});							    				
									}	
								} else {
									/*
									* Normal search option 
									*/
									$patient_app->whereRaw("(last_name LIKE '%$search%' or first_name LIKE '%$search%' or middle_name LIKE '%$search%')"); 
								}		
									
							});
						});
						/*
						* DOB serach option in patient
						*/
						$patient_app->orWhere(function ($patient_app) use ($search) { 
							$time = strtotime($search);
							$newformat = date('Y-m-d',$time);
							$patient_app->WhereHas('patient', function($q)use($newformat) {
								$q->whereRaw("(dob LIKE '%$newformat%' )"); 
							});
							$search = strtolower($search);
						});
						/*
						* Provider short name serach option *
						*/					
						$patient_app->orWhereHas('provider',function($patient_app) use ($search) {
							$patient_app->whereRaw("(short_name LIKE '%$search%')");							
						});					
					
						/*
						* Facility short name 
						*/
						$patient_app->orWhereHas('facility',function($patient_app) use ($search) {
							$patient_app->whereRaw("(short_name LIKE '%$search%')");						
						});
						/*
						* scheduled on date based search is Appointment date
						*/
						if(strpos(strtolower($search), "/") !== false ){	
							$dateSearch = date("Y-m-d", strtotime(@$search)); 
							$patient_app = $patient_app->orWhere('scheduled_on', 'LIKE', '%'.$dateSearch.'%');							
						} else {
							$patient_app = $patient_app->orWhere('scheduled_on', 'LIKE', '%'.$search.'%');
						}
						/*
						* Appointment time base search
						*/
						if(strpos(strtolower($search), ":") !== false ){	
							$patient_app = $patient_app->orWhere('appointment_time', 'LIKE', $search.'%');							
						}	
						/*
						   ### Or where status check for the search option
						*/
						$patient_app = $patient_app->orWhere('status', 'LIKE', $search.'%');	
					}
				}
			});	

			$patient_app->orderBy('created_at','desc');
			 /*
	         * Appointment search option Default optionClaim count 0, patient create date,  
	         */
	        /* Start for patients listing by Thilagavathy */
	        $get_list_header = [];
	   		if(!empty(json_decode(@$request['dataArr']['data']['patient_name']))){
	            $dynamic_name = json_decode($request['dataArr']['data']['patient_name']);	      
				$patient_app->WhereHas('patient', function($q)use($dynamic_name) {
					$q->Where(DB::raw('CONCAT(last_name,", ", first_name)'),  'like', "%{$dynamic_name}%" );
				});					
				$get_list_header["Patient Name"] =  $dynamic_name;		
	        } 

			if(!empty(json_decode(@$request['dataArr']['data']['reason_for_visits']))){
	            $reason_for_visits = json_decode($request['dataArr']['data']['reason_for_visits']);
		    	if(is_array($reason_for_visits)){ 				
					$patient_app->whereIn('reason_for_visit',$reason_for_visits);       		
				} else {
					$patient_app->where('reason_for_visit',$reason_for_visits);
				}
				 $reason = ReasonForVisit::selectRaw("GROUP_CONCAT(reason SEPARATOR ', ') as facility_name")->whereIn('id', json_decode(@$request['dataArr']['data']['reason_for_visits']))->get()->toArray();
           		 $get_list_header["Reason for Visit"] =  @array_flatten($reason)[0];
			
			}

	       	if(!empty(json_decode(@$request['dataArr']['data']['acc_no']))){
	            $account_num =  json_decode($request['dataArr']['data']['acc_no']);
	         	 $patient_app->WhereHas('patient', function($q)use($account_num) {
					$q->Where(DB::raw('account_no'),  'like', "%{$account_num}%" );
				});
         	    $get_list_header["Reason for Visit"] =  $account_num;
	        }

	     	if(!empty(json_decode(@$request['dataArr']['data']['facility_id']))){
	            $facility_id = json_decode(@$request['dataArr']['data']['facility_id']);            
	            if(is_array($facility_id)){             
	             	 $patient_app->WhereHas('facility',function($patient_app) use ($facility_id) {
						$patient_app->WhereIn(DB::raw('id'), $facility_id );					
					});          
	            }else{
	               	 $patient_app->orWhereHas('facility',function($patient_app) use ($facility_id) {
						$patient_app->Where(DB::raw('id'), json_decode(@$request['dataArr']['data']['facility_id']) );					
					}); 
	            }
	            $facility = Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ', ') as facility_name")->whereIn('id', json_decode($request['dataArr']['data']['facility_id']))->get()->toArray();
	            $get_list_header["Facility"] =  @array_flatten($facility)[0];
	        }    
        
	        if (!empty(json_decode(@$request['dataArr']['data']['appt_status']))){
	            if(is_array(json_decode(@$request['dataArr']['data']['appt_status'])))
	                if(in_array("All", json_decode($request['dataArr']['data']['appt_status']))){                    
	                    $patient_app->whereIn('status', ['Complete','Scheduled','No Show','Canceled','Rescheduled','Encounter']);
	                }else{
	                    $patient_app->whereIn('status', json_decode($request['dataArr']['data']['appt_status']));
	                }
	            else
	                $patient_app->where('status', json_decode($request['dataArr']['data']['appt_status']));
	            $get_list_header["Status"] =  json_decode($request['dataArr']['data']['appt_status']);
	        }
      	
	      	if(!empty(json_decode(@$request['dataArr']['data']['rendering_provider_id']))){
	            $rendering_provider_id = json_decode(@$request['dataArr']['data']['rendering_provider_id']);            
	            if(is_array($rendering_provider_id)){             
	             	 $patient_app->WhereHas('provider',function($patient_app) use ($rendering_provider_id) {
						$patient_app->WhereIn(DB::raw('id'), $rendering_provider_id );					
					});          
	            }else{
	               	 $patient_app->orWhereHas('provider',function($patient_app) use ($rendering_provider_id) {
						$patient_app->Where(DB::raw('id'), json_decode(@$request['dataArr']['data']['rendering_provider_id']) );					
					}); 
	            }
	            $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name")->whereIn('id', json_decode($request['dataArr']['data']['rendering_provider_id']))->get()->toArray();
	            $get_list_header["Rendering Provider"] =  @array_flatten($provider)[0];
	        }

	       	if(!empty(json_decode(@$request['dataArr']['data']['created_at']))){
	       	 	$date = explode('-',json_decode($request['dataArr']['data']['created_at']));          
	            $from = date("Y-m-d", strtotime($date[0]));
	            if($from == '1970-01-01'){
	                $from = '0000-00-00';
	            }

	            $to = date("Y-m-d", strtotime($date[1]));
	            $from = Helpers::utcTimezoneStartDate($from);
	            $to = Helpers::utcTimezoneEndDate($to);
	            $patient_app->where(DB::raw('DATE(created_at)'),'>=',$from)->where(DB::raw('DATE(created_at)'),'<=',$to);
	            $get_list_header["Created Date"] = date("m/d/Y",strtotime($from)) . "  To " . date("m/d/Y",strtotime($to));           
	        }

	        if(!empty(json_decode(@$request['dataArr']['data']['scheduled_at']))){
	       	 	$date = explode('-',json_decode($request['dataArr']['data']['scheduled_at']));          
	            $from = date("Y-m-d", strtotime($date[0]));
	            if($from == '1970-01-01'){
	                $from = '0000-00-00';
	            }

	            $to = date("Y-m-d", strtotime($date[1]));
	         /*   $from = Helpers::utcTimezoneStartDate($from);
	            $to = Helpers::utcTimezoneEndDate($to);*/
	            $patient_app->where(DB::raw('DATE(scheduled_on)'),'>=',$from)->where(DB::raw('DATE(scheduled_on)'),'<=',$to);
	            $get_list_header["Appointment Date"] = date("m/d/Y",strtotime($from)) . "  To " . date("m/d/Y",strtotime($to));           
	        }
        	/* End for patients listing by Thilagavathy */
			/*
				* All Appointment is check box condition
			*/
			if(!empty($appCheck)) {
				$patient_app= $patient_app->whereIn('status',['Complete','Scheduled','No Show','Canceled','Rescheduled','Encounter']);
			} else {
				$patient_app = $patient_app->whereIn('status',['Complete','Scheduled','No Show','Canceled','Rescheduled','Encounter']);
				## changed for if all status will be showing 
				//$patient_app = $patient_app->whereIn('status',['Complete','Scheduled','No Show']); 
			}	
		}
		/*
			* Sortting order given in join table or normal default order
		*/
		if(($orderByDir == 'desc') && ($torderByField != '')) {
			$patient_app = $patient_app->orderBy($orderByField, $orderByDir)->get()->sortByDesc($torderByField);
		} elseif(($orderByDir == 'asc') && ($torderByField != '') ) {	
			//dd($orderByDir);
			$patient_app = $patient_app->orderBy($orderByField, $orderByDir)->get()->sortBy($torderByField);
		} else {
			$patient_app = $patient_app->orderBy($orderByField, $orderByDir)->get();
		}	
		$count = count($patient_app);
		$ClaimController  = new ClaimControllerV1();   
	    $search_fields_data = $ClaimController->generateSearchPageLoad('appointment_list');
	    $searchUserData = $search_fields_data['searchUserData'];
	    $search_fields = $search_fields_data['search_fields'];
	     //dd($searchUserData);
	    //Icon, selected tab
        \View::share ( 'heading', 'Scheduler' );
        \View::share ( 'selected_tab', 'Reports' );
        \View::share( 'heading_icon', 'fa-calendar-o'); 
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('patient_app','count','searchUserData','search_fields','get_list_header')));
	}

	/*** Start to Listing the Insurance	 ***/
	public function getschedulertablevalues()
	{		
		$request = Request::all();				

		$start = $request['start'];
		$len = $request['length'];			

		$cloum = intval($request["order"][0]["column"]);
		$order = $request['columns'][$cloum]['data'];
		$order_decs = $request["order"][0]["dir"];				
		
		$search = '';
		if(!empty($request['search']['value']))
		{
			$search= $request['search']['value'];
		}
		$patient_qry = PatientAppointment::where('patient_appointments.patient_id', '<>', 0);
			// All Patient checked 
			// join started
		//dd($patient_qry->get());
		$patient_qry->leftjoin(DB::raw("(SELECT 
				  patients.id,patients.account_no,patients.first_name,patients.last_name,patients.middle_name,patients.dob,patients.address1,patients.address2,patients.city,patients.state,patients.zip5,patients.zip4,patients.phone
			      FROM patients
			      WHERE patients.deleted_at IS NULL
			      GROUP BY patients.id
			      ) as patients"),function($join){
			        $join->on('patient_appointments.patient_id', '=', 'patients.id');
			})->where('patient_appointments.id','<>','');
		$insurances = $patient_qry->get();
		$total_rec_count = $patient_qry->count();
			//dd($insurances);
/*
		$patient_qry->Leftjoin('patients', function($join)
        {

            $join->on('patient_appointments.patient_id', '=', 'patients.id')
                 ->where('patients.account_no', '<>','');
        });
*/
			
		
		foreach($insurances as $insurance)
		{//dd($insurance);

			$insurances_details = $insurance;
			$insurances_details['id'] = Helpers::getEncodeAndDecodeOfId($insurance['id']);
			$insurances_details['patient_name'] = Helpers::getNameformat(@$insurance['last_name'],@$insurance['first_name'],@$insurance['middle_name']);
			$insurances_details['birthday'] =$insurance['dob'] = Helpers::dateFormat($insurance['dob'],'dob');			
			$insurances_details['facility_id'] = Facility::getFacilityShortName($insurance['facility_id']);
			$insurances_details['provider_id'] = Provider::getProviderShortName($insurance['provider_id']);
			$time_arr = explode("-",@$insurance['appointment_time']); 
			$insurances_details['appt_time'] = $time_arr[0];
			$insurances_details['appt_date'] = Helpers::dateFormat($insurance['scheduled_on'],'date');
			$insurances_details['status'] = $insurance['status'];
			$insurances_details['appt_id'] =  Helpers::getEncodeAndDecodeOfId($insurance['id'],'decode');
			$insurance_arr_details[] = $insurances_details;
		}      
		
		if(count($insurances)==0){
			$insurance_arr_details = array();
		}
		//dd($insurance_arr_details);
		$data['data'] = $insurance_arr_details;	
		$data = array_merge($data,$request);
		$data['recordsTotal'] = $total_rec_count;
		$data['recordsFiltered'] = $total_rec_count;	
		return Response::json($data);
	}
}
