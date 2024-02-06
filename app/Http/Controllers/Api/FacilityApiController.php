<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Facilityaddress as Facilityaddress;
use App\Models\Facility as Facility;
use App\Models\AddressFlag as AddressFlag;
use App\Models\Speciality as Speciality;
use App\Models\Taxanomy as Taxanomy;
use App\Models\County as County;
use App\Models\Provider as Provider; 
use App\Models\ProviderScheduler as ProviderScheduler;
use App\Models\Claimformat as ClaimFormat;
use App\Models\Pos as Pos;
use App\Models\NpiFlag as NpiFlag;
use Intervention\Image\ImageManagerStatic as Image;
use App\Models\Scheduler\PatientAppointment as PatientAppointment;
use App\Models\ProviderSchedulerTime as ProviderSchedulerTime;
use Input;
use File;
use Auth;
use Response;
use Request;
use Validator;
use Schema;
use DB;
use Config;
use App\Models\Document as Document;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
use Session;
use Lang;
use Illuminate\Support\Collection;

class FacilityApiController extends Controller 
{
	/*** Start Listing of the Facility ***/ 
	public function getIndexApi($export = "")
	{
		$facilitymodule = $this->getFacilitySearchApi();
		//Export PDF or Excel or CSV format
		if($export != "") {			
			$exportparam 	= array(
				'filename'        =>    'Facility',
				'heading'         =>	'Facility',
				'fields'          =>    array(
				'short_name'	  => 	'Short Name',
				'facility_name'   =>    'Facility',
				'specialities_id' =>	array('table'=>'speciality_details' ,	'column' => 'speciality' , 'label' => 'Speciality'),
				'pos_id'          =>	array('table'=>'pos_details' ,	'column' => 'code' ,	'label' => 'POS'),
				'city'            =>	array('table'=>'facility_address' ,	'column' => 'city' ,	'label' => 'City'),
				'state'           =>	array('table'=>'facility_address' ,	'column' => 'state' ,	'label' => 'State'),
				'phone'           =>	array('table'=>'facility_address' ,	'column' => 'phone' ,	'label' => 'Phone'),
				'status'          =>	'Status',
				));

			$callexport    	= new CommonExportApiController();
			return $callexport->generatemultipleExports($exportparam, $facilitymodule, $export); 
		}
		
		$speciality			= Speciality::has('facility')->pluck('speciality','id')->all();
		$pos				= Pos::has('facility')->orderBy('code', 'ASC')->pluck('id','id')->all();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('facilitymodule','speciality','pos')));
	}
	/*** End Listing of the Facility ***/ 
	
	
	public function getFacilitySearchApi()
	{
		$request = Request::all();
		
		/*** Facility list***/
		$facilityquery     = Facility::orderBy('facility_name', 'asc')->with('facility_address','speciality_details','pos_details');
		
		if(isset($request['short_name']) && $request['short_name']!='') {
			$facilityquery->where('short_name','like', '%' . $request['short_name'] . '%');
		}
		
		if(isset($request['facility_name']) && $request['facility_name']!='') {
			$facilityquery->where('facility_name','like', '%' . $request['facility_name'] . '%');
		}
				
		if(isset($request['pos']) && $request['pos']!='') {
			$facilityquery->where('pos_id',$request['pos']);
		}
		
		if(isset($request['speciality']) && $request['speciality']!='')	{
			$facilityquery->where('speciality_id',$request['speciality']);
		}
		
		if(isset($request['status']) && $request['status']!='') {
			$facilityquery->where('status',$request['status']);
		}
		
		return $facilityquery->get();
	}
	
	/*** Start Create of the Facility ***/ 
	public function getCreateApi()
	{			
		$facility               = Facility::all();
		$specialities           = Speciality::orderBy('speciality','ASC')->pluck('speciality', 'id')->all();
		$speciality_id          = '';
		$taxanomies             = [];
		$taxanomy_id            = '';
		$facilityaddress        = Facilityaddress::all();
		$providers              = Provider::where('status', '=', 'Active')->orderBy('provider_name','ASC')->pluck('provider_name', 'id')->all();
		$default_provider_id    ='';
		$county                 = County::pluck('name','id')->all();
		$pos                    = Pos::select(DB::raw("CONCAT(code,' - ',pos) AS pos_detail"), 'id')->orderBy('code', 'ASC')->pluck('pos_detail', 'id')->all(); 
		$pos_id                 = '';
		$claimformats 			= ClaimFormat::where('claim_format','Professional')->pluck('claim_format', 'id')->all();

		// Get address for usps 
		$addressFlag['general']['address1'] = '';
		$addressFlag['general']['city'] = '';
		$addressFlag['general']['state'] = '';
		$addressFlag['general']['zip5'] = '';
		$addressFlag['general']['zip4'] = '';
		$addressFlag['general']['is_address_match'] = '';
		$addressFlag['general']['error_message'] = '';
		$npiflag_columns       = Schema::getColumnListing('npiflag');		// Get NPI details 
		foreach($npiflag_columns as $columns) {
			$npi_flag[$columns] = '';
		}
		$time['monday_forenoon']     = '00;720';
		$time['tuesday_forenoon']    = '00;720';
		$time['wednesday_forenoon']  = '00;720';
		$time['thursday_forenoon']   = '00;720';
		$time['friday_forenoon']     = '00;720';
		$time['saturday_forenoon']   = '00;720';
		$time['sunday_forenoon']     = '00;720';
		$time['monday_afternoon']    = '720;1480';
		$time['tuesday_afternoon']   = '720;1480';
		$time['wednesday_afternoon'] = '720;1480';
		$time['thursday_afternoon']  = '720;1480';
		$time['friday_afternoon']    = '720;1480';
		$time['saturday_afternoon']  = '720;1480';
		$time['sunday_afternoon']    = '720;1480';
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('claimformats','facility','specialities','county','speciality_id','taxanomies','taxanomy_id','facilityaddress','providers','default_provider_id','pos','pos_id','addressFlag','npi_flag','time')));
	}
	/*** End Create of the Facility ***/ 
	
	/*** Start Store of the Facility ***/ 
	public function getStoreApi($request='')
	{	
		//print_r($request); exit;
		if($request == '')
			$request = Request::all();
		
		if(!empty($request['website'])){
			if(substr($request['website'],0,7) != 'http://' && substr($request['website'],0,8) != 'https://')	
				$request['website'] = "http://".$request['website'];
		}
		
		$is_valid_npi = Helpers::checknpi_valid_process($request['facility_npi']); // check npi valid or not back end validation

		//Validate for avoid same facility name to same place of service.
		Validator::extend('validatepos', function($attribute, $value, $parameters) use($request) {   
			if(Facility::where('facility_name',$request['facility_name'])->count()>0) {
				$collect_posarr = Facility::where('facility_name',$request['facility_name'])->pluck('pos_id')->all();
				if(in_array($value, $collect_posarr)) {
					return false;
				} else {
					return true;
				}
			} else {
				return true;
			}
		});
		Validator::extend('check_npi_api_validator', function($attribute) use($is_valid_npi) {
			if($is_valid_npi == 'No')
				return false;                        
			else 
				return true;                   
		});
		
			// Back end Validation
		$validate_facility_rules = Facility::$rules+array('short_name'=>'required|max:3|min:3|unique:facilities,short_name,NULL,id,deleted_at,NULL','pos_id' => 'required|validatepos','image'=>Config::get('siteconfigs.customer_image.defult_image_size'));
		$validate_facility_rules['facility_npi'] = @$validate_facility_rules['facility_npi'].'|check_npi_api_validator';
		if((is_null($request['email']) == true)) {
			unset($validate_facility_rules['email']);
		}

		if((is_null($request['website']) == true)) {
			unset($validate_facility_rules['website']);
		}

		$validate_facility_msg   = Facility::messages()+array('facility_npi.check_npi_api_validator' => Lang::get("common.validation.npi_validcheck"));
		
		$validator               = Validator::make($request, $validate_facility_rules, $validate_facility_msg);
		
		$collect_schedule = array();
		$schedule = array('monday_forenoon','tuesday_forenoon','wednesday_forenoon','thursday_forenoon','friday_forenoon','saturday_forenoon','sunday_forenoon','monday_afternoon','tuesday_afternoon','wednesday_afternoon','thursday_afternoon','friday_afternoon','saturday_afternoon','sunday_afternoon');
		
		//If error	or not error
		if ($validator->fails()) {
			$errors             = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		} else {	
			$data               = Facility::create($request);
			$user               = Auth::user ()->id;
			$data->created_by   = $user;
			$data->save ();
			if (Input::hasFile('image')) {
				$image              = Input::file('image');
				$filename           = rand(11111,99999);
				$extension          = $image->getClientOriginalExtension();
				$filestoreName      = $filename .'.'.$extension;
				$resize             = array('150','150');
				Helpers::mediauploadpath('','facility',$image,$resize,$filestoreName); 
				$data->avatar_name  = $filename;
				$data->avatar_ext   = $extension;
				$data->save();
			}

			$btdata                 = Facilityaddress::create(Request::all());
			$facility_id            = $data->id;

			if(isset($request['temp_doc_id'])) {
				if($request['temp_doc_id']!="") 
					Document::where('temp_type_id', '=', $request['temp_doc_id'])->update(['type_id' => $facility_id,'temp_type_id' => '']);
			}

			$btdata->facilityid     = $data->id;
			$btdata->save();

			/*** Starts - address flag update ***/				
			$address_flag                       = array();
			$address_flag['type']               = 'facility';
			$address_flag['type_id']            = $facility_id;
			$address_flag['type_category']      = 'general_information';
			$address_flag['address2']           = $request['general_address1'];
			$address_flag['city']               = $request['general_city'];
			$address_flag['state']              = $request['general_state'];
			$address_flag['zip5']               = $request['general_zip5'];
			$address_flag['zip4']               = $request['general_zip4'];
			$address_flag['is_address_match']   = $request['general_is_address_match'];
			$address_flag['error_message']      = $request['general_error_message'];
			AddressFlag::checkAndInsertAddressFlag($address_flag);
			/*** Ends - address flag update ***/	

			/*** Starts - NPI flag update ***/
			$request['company_name']    = 'npi';
			$request['type']            = 'facility';
			$request['type_id']         = $facility_id;
			$request['type_category']   = 'Individual';
			NpiFlag::checkAndInsertNpiFlag($request);
			/*** Ends - NPI flag update ***/
			$FacId = Helpers::getEncodeAndDecodeOfId($data->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.facility_create_msg"),'data'=>$FacId));					
		}
	}
	/*** End Store of the Facility ***/ 
	
	/*** Start Edit of the Facility ***/ 
	public function getEditApi($id)
	{	
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		//Facility Count check 
		if(Facility::where('id', $id )->count()>0 && is_numeric($id)) {		
			$facility               = Facility::with('facility_address')->where('id',$id)->first();
			$specialities_id        = $facility->speciality_id;
			$providers              = Provider::where('status', '=', 'Active')->orderBy('provider_name','ASC')->pluck('provider_name', 'id')->all();
			$claimformats 			= ClaimFormat::where('claim_format','Professional')->pluck('claim_format', 'id')->all();
			$default_provider_id    = $facility->default_provider_id;
			$county                 = County::pluck('name','id')->all();
			$specialities           = Speciality::pluck('speciality', 'id')->all();
			$speciality_id          = $facility->speciality_id;
			$taxanomies             = Taxanomy::where('speciality_id',$specialities_id)->pluck('code','id')->all();
			$taxanomy_id            = $facility->taxanomy_id;
			$btdata                 = Facilityaddress::where('facilityid', '=', $id)->pluck('id')->all();
			$facilityaddress        = Facilityaddress::findOrFail($btdata);
			$pos                    = Pos::select(DB::raw("CONCAT(code,' - ',pos) AS pos_detail"), 'id')->orderBy('code', 'ASC')->pluck('pos_detail', 'id')->all();
			$pos_id                 = $facility->pos_id;
			//Facility working time set
			$time['monday_forenoon']    = $facility->monday_forenoon;
			$time['tuesday_forenoon']   = $facility->tuesday_forenoon;
			$time['wednesday_forenoon'] = $facility->wednesday_forenoon;
			$time['thursday_forenoon']  = $facility->thursday_forenoon;
			$time['friday_forenoon']    = $facility->friday_forenoon;
			$time['saturday_forenoon']  = $facility->saturday_forenoon;
			$time['sunday_forenoon']    = $facility->sunday_forenoon;
			$time['monday_afternoon']   = $facility->monday_afternoon;
			$time['tuesday_afternoon']  = $facility->tuesday_afternoon;
			$time['wednesday_afternoon']= $facility->wednesday_afternoon;
			$time['thursday_afternoon'] = $facility->thursday_afternoon;
			$time['friday_afternoon']   = $facility->friday_afternoon;
			$time['saturday_afternoon'] = $facility->saturday_afternoon;
			$time['sunday_afternoon']   = $facility->sunday_afternoon;

			$general_address_flag   	= AddressFlag::getAddressFlag('facility',$facility->id,'general_information'); // Get address for usps 
			$addressFlag['general'] 	= $general_address_flag;
			$npi_flag 					= NpiFlag::getNpiFlag('facility',$facility->id,'Individual');  // Get NPI details

			if(!$npi_flag) {
				$npiflag_columns = Schema::getColumnListing('npiflag');
				foreach($npiflag_columns as $columns) {
					$npi_flag[$columns] = '';
				}			
			}
			
			$documents_fda = Document::where('document_type','facility')->where('category','fda')->where('type_id',$id)->first();
			$documents_npi = Document::where('document_type','facility')->where('category','npi')->where('type_id',$id)->first();
			$documents_tax_id = Document::where('document_type','facility')->where('category','tax_id')->where('type_id',$id)->first();
			$documents_clia_id = Document::where('document_type','facility')->where('category','clia_id')->where('type_id',$id)->first();

			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('documents_fda','claimformats','facility','providers','default_provider_id','county','specialities','speciality_id','taxanomies','taxanomy_id','facilityaddress','pos','pos_id','addressFlag','npi_flag','time','documents_npi','documents_tax_id','documents_clia_id')));
		} else {
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}	
	}
	/*** End Edit of the Facility ***/ 
	
	/*** Start Update of the Facility ***/ 
    public function getUpdateApi($type, $id, $request='')
	{	
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Facility::where('id', $id )->count()>0 && is_numeric($id)) {
			if($request == '')
			   $request = Request::all();
			
			if(!empty($request['website'])) {
				if(substr($request['website'],0,7) != 'http://' && substr($request['website'],0,8) != 'https://')	
					$request['website'] = "http://".$request['website'];
			}

			/* Remove claim error message */
			ClaimInfoV1::ClearingClaimErrors($id,'Facility');
			/* Remove claim error message */
			$is_valid_npi = Helpers::checknpi_valid_process($request['facility_npi']); // check npi valid or not back end validation
		   
			//Validate for avoid same facility name to same place of service.
			Validator::extend('validatepos', function($attribute, $value, $parameters) use($request,$id) {   
				if(Facility::where('facility_name',$request['facility_name'])->where('id','!=',$id)->count()>0)	{
					$collect_posarr = Facility::where('facility_name',$request['facility_name'])->where('id','!=',$id)->pluck('pos_id')->all();
					if(in_array($value, $collect_posarr)) {
						return false;
					} else {
						return true;
					}
				} else {
					return true;
				}
			});
			
			Validator::extend('check_npi_api_validator', function($attribute) use($is_valid_npi) {
				if($is_valid_npi == 'No')
					return false;                        
				else 
					return true;                   
			});

			$validate_facility_rules = Facility::$rules+array('short_name'=>'required|max:3|min:3|unique:facilities,short_name,'.$id.',id,deleted_at,NULL','pos_id' => 'required|validatepos','image'=>Config::get('siteconfigs.customer_image.defult_image_size'));
			$validate_facility_rules['facility_npi'] = @$validate_facility_rules['facility_npi'].'|check_npi_api_validator';
			// dd($validate_facility_rules);
			$validate_facility_msg   = Facility::messages()+array('facility_npi.check_npi_api_validator' => Lang::get("common.validation.npi_validcheck"));
			
			$validator               = Validator::make($request, $validate_facility_rules, $validate_facility_msg);
			//Validation issue means if condition otherwise else 
			if ($validator->fails()) {
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			} else {
				$facility = Facility::findOrFail($id);
				//Facility Images 
				if (Input::hasFile('image')) {
					$image 				= Input::file('image');
					$filename  			= rand(11111,99999);

					$old_filename   	= $facility->avatar_name;
					$old_extension  	= $facility->avatar_ext;

					$extension 			= $image->getClientOriginalExtension();
					$filestoreName 		= $filename .'.'.$extension;
					$filestoreoldName 	= $old_filename .'.'.$old_extension;
					$resize 			= array('150','150');
					Helpers::mediauploadpath('','facility',$image,$resize,$filestoreName,$filestoreoldName);  
					$facility->avatar_name  = $filename;
					$facility->avatar_ext 	= $extension;
				}
				//$this->scheduledAppointment($request,$id);
				/*****  Start - Display alert message when change the facility time, If already scheduled provider to the facility time ***/
				
				$schedule_error = array();
				$schedule_error['monday_forenoon'] =$schedule_error['sunday_afternoon'] =$schedule_error['saturday_afternoon'] = $schedule_error['friday_afternoon']=$schedule_error['thursday_afternoon'] = $schedule_error['wednesday_afternoon']= $schedule_error['tuesday_afternoon'] =$schedule_error['monday_afternoon']= $schedule_error['sunday_forenoon'] =$schedule_error['thursday_forenoon']=$schedule_error['friday_forenoon'] = $schedule_error['saturday_forenoon']= $schedule_error['wednesday_forenoon'] = $schedule_error['tuesday_forenoon']= 'Already scheduled provider this time';
				 
				$collect_schedule = array();
				$schedule = array('monday_forenoon','tuesday_forenoon','wednesday_forenoon','thursday_forenoon','friday_forenoon','saturday_forenoon','sunday_forenoon','monday_afternoon','tuesday_afternoon','wednesday_afternoon','thursday_afternoon','friday_afternoon','saturday_afternoon','sunday_afternoon');
				
				// Get the facility new changes.
				$collectscheduleerror = array();
				
				// Define the value to get column value form table.
				$selecteddate  				= array();
				$selecteddate['monday'] 	= 'monday_selected_times';
				$selecteddate['tuesday'] 	= 'tuesday_selected_times';
				$selecteddate['wednesday'] 	= 'wednesday_selected_times';
				$selecteddate['thursday'] 	= 'thursday_selected_times';
				$selecteddate['friday'] 	= 'friday_selected_times';
				$selecteddate['saturday'] 	= 'saturday_selected_times';
				$selecteddate['sunday'] 	= 'sunday_selected_times';
								
				if(count($collect_schedule)>0) {
					$get_facilityslat = array();
					foreach($collect_schedule as $get_schdule) {
						$get_chsch 			= explode("_",$get_schdule);
						
						$get_facilitynoonslat = array();
						
						// Pass current selected time and noon.
						$get_facilitytime 		= Helpers::sliderTimeDisplay($request[$get_schdule],$get_chsch[1]);
						$changedfacility_time	= explode("-",$get_facilitytime);
						$facilitycurrentfromtime	= $changedfacility_time[0];
						$facilitycurrenttotime		= $changedfacility_time[1]; 
						
						// Pass old selected time and noon.
						$get_prevfacilitytime 		= Helpers::sliderTimeDisplay($facility[$get_schdule],$get_chsch[1]);
						$changedprevfacility_time	= explode("-",$get_prevfacilitytime);
						$facilityprevfromtime	= $changedprevfacility_time[0];
						$facilityprevtotime		= $changedprevfacility_time[1]; 
						
						$facilityfromslat = array();
						if($facilitycurrentfromtime != $facilityprevfromtime) {
							$newfacilityprevfromtime = str_replace('00:','12:',$facilityprevfromtime);
							$newfacilitycurrentfromtime = str_replace('00:','12:',$facilitycurrentfromtime); 
							
							// Get time slot from current From time and exist From time.
							if(strtotime($newfacilitycurrentfromtime)>strtotime($newfacilityprevfromtime))
								$facilityfromslat 	= Helpers::getTimeSlotByGivenTime($newfacilityprevfromtime,$newfacilitycurrentfromtime,5);
							else
								$facilityfromslat 	= Helpers::getTimeSlotByGivenTime($newfacilitycurrentfromtime,$newfacilityprevfromtime,5);
						}
						
						$facilitytoslat = array();
						if($facilitycurrenttotime != $facilityprevtotime) {
							$newfacilityprevtotime = str_replace('00:','12:',$facilityprevtotime);
							$newfacilitycurrenttotime = str_replace('00:','12:',$facilitycurrenttotime); 
							
							// Get time slot from current To time and exist To time.
							if(strtotime($newfacilitycurrenttotime)>strtotime($newfacilityprevtotime)) {
								$facilitytoslat 	= Helpers::getTimeSlotByGivenTime($newfacilityprevtotime,$newfacilitycurrenttotime,5); 
							} else {
							$facilitytoslat 	= Helpers::getTimeSlotByGivenTime($newfacilitycurrenttotime,$newfacilityprevtotime,5); }
						}
						
						if(is_array($facilityfromslat) && count($facilityfromslat)>0 && (is_array($facilitytoslat) && count($facilitytoslat)>0)) {
							$get_facilitynoonslat = array_merge($facilityfromslat,$facilitytoslat);
						} elseif(is_array($facilityfromslat) && count($facilityfromslat)>0) {
							$get_facilitynoonslat = $facilityfromslat;
						} elseif(is_array($facilitytoslat) && count($facilitytoslat)>0) {
							$get_facilitynoonslat = $facilitytoslat;
						}
						
						$get_facilityslat = array_merge($get_facilityslat,$get_facilitynoonslat);
						
						// Get coloumn "monday_selected_times" etc & future date values
						
						$get_to_value 		= ProviderScheduler::where('facility_id','=',$id)->where($selecteddate[$get_chsch[0]],'!=',',,')->whereRaw('(end_date >= ? or end_date = "0000-00-00")', array(date('Y-m-d')))->select($selecteddate[$get_chsch[0]])->get()->toArray();
						
						
						if(count($get_to_value)>0) {
							foreach($get_to_value as $providervalue) {
								$collect_date 		= str_replace(',,','',$providervalue[$selecteddate[$get_chsch[0]]]);
								$collect_sep_date 	= explode("-",$collect_date);
								$providerfromtime	= $collect_sep_date[0];
								$providertotime		= $collect_sep_date[1];
								
								// Get time slot from provider given time.
								$getproviderslot 	= Helpers::getTimeSlotByGivenTime($providerfromtime,$providertotime,5);
								
								if(is_array($getproviderslot)) {
									// Display error msg if match the provider time and facility time.
									$result=array_intersect($getproviderslot,$get_facilityslat);
									
									if(count($result)>0) {
										$collectscheduleerror[$get_schdule] = $schedule_error[$get_schdule];
									}
								}
							}
						}
					}
					
				}
				
				if(count($collectscheduleerror)>0)
					return Response::json(array('status'=>'error', 'message'=>$collectscheduleerror,'data'=>''));

				/*****  End - Display alert message when change the facility time, If already scheduled provider to the facility time ***/	
				$facility->update($request);
				$user 					= Auth::user ()->id;
				$facility->updated_by 	= $user;
				$facility->save();
				$row 					= DB::table('facilityaddresses')->where('facilityid', '=', $id)->first();
				$btdata 				= $row->id;
				$facilityaddress 		= Facilityaddress::findOrFail($btdata);
				$facilityaddress->update(Request::all());				
				/*** Starts - Pay to address flag update ***/
				$address_flag 					= array();
				$address_flag['type'] 			= 'facility';
				$address_flag['type_id']		= $facility->id;
				$address_flag['type_category'] 	= 'general_information';
				$address_flag['address2'] 		= $request['general_address1'];
				$address_flag['city']			= $request['general_city'];
				$address_flag['state'] 			= $request['general_state'];
				$address_flag['zip5'] 			= $request['general_zip5'];
				$address_flag['zip4'] 			= $request['general_zip4'];
				$address_flag['is_address_match']= $request['general_is_address_match'];
				$address_flag['error_message'] 	 = $request['general_error_message'];
				AddressFlag::checkAndInsertAddressFlag($address_flag);
				/*** Ends - Pay to address ***/

				/*** Starts - NPI flag update ***/
				$request['company_name'] 	= 'npi';
				$request['type'] 			= 'facility';
				$request['type_id'] 		= $facility->id;
				$request['type_category'] 	= 'Individual';
				NpiFlag::checkAndInsertNpiFlag($request);
				/*** Ends - NPI flag update ***/

				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.facility_update_msg"),'data'=>''));					
			}
		} else {
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}
	}
	/*** End Update of the Facility ***/ 
	
	/*** Start Delete of the Facility ***/
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		//Delete the facility	
		if(Facility::where('id', $id )->count()>0 && is_numeric($id)) {
			Facility::where('id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
		} else {
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}
	}
	/*** End Delete of the Facility ***/ 
	
	/*** Start View of the Facility ***/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		//Show the Facility detail
		if(Facility::where('id', $id )->count()>0 && is_numeric($id)) {
			$facility 				= Facility::with('county','claimformat_details','facility_address','speciality_details','pos_details','provider_details','taxanomy_details')->where('id',$id)->first();	
			$general_address_flag 	= AddressFlag::getAddressFlag('facility',$facility->id,'general_information');  // Get address for usps 
			$addressFlag['general'] = $general_address_flag;
			$npi_flag 				= NpiFlag::getNpiFlag('facility',$facility->id,'Individual');   		// Get NPI details
			$claimformats 			= ClaimFormat::pluck('claim_format', 'id')->all();	
			$documents_fda = Document::where('document_type','facility')->where('category','fda')->where('type_id',$id)->first();
			$documents_npi = Document::where('document_type','facility')->where('category','npi')->where('type_id',$id)->first();
			$documents_tax_id = Document::where('document_type','facility')->where('category','tax_id')->where('type_id',$id)->first();
			$documents_clia_id = Document::where('document_type','facility')->where('category','clia_id')->where('type_id',$id)->first();
			if(!$npi_flag) {
				$npiflag_columns 	= Schema::getColumnListing('npiflag');
				foreach($npiflag_columns as $columns) {
					$npi_flag[$columns] = '';
				}			
			}                
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('claimformats','facility','addressFlag','npi_flag','documents_fda','documents_npi','documents_tax_id','documents_clia_id')));	
		} else {
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}		
	}
	/*** End View of the Facility ***/ 
	
	/*** Delete Avatar in Facility table start ***/
	public function avatarapipicture($id,$p_name)
	{	
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$delete_avr = Facility::where('id',$id)->first();
		$delete_avr->avatar_name = "";
		$delete_avr->avatar_ext = "";
		$delete_avr->save();
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
	}
	/*** Delete Avatar in Facility table end ***/
	
	public function appointmentcheck()
	{
		$request = Request::all();
		if(!empty($request['facility_id'])) {
			$faility_id = Helpers::getEncodeAndDecodeOfId($request['facility_id'], 'decode');
			$current_date = date('Y-m-d');
			$appointment = '';
			$day_week = $request['schedule_day'];
			$schedule_day = explode('_',$day_week);	
			//Split the Am OR Pm	
			$time_noon = ($schedule_day[1]= 'forenoon') ? 'am' :'pm';
			$week_of_day = $schedule_day[0];
			//Date calculate the day of week & time for appointment
			$app_end_time =PatientAppointment::where('facility_id',$faility_id)->whereRaw("(DAYNAME( DATE( `scheduled_on` ) )= '".$week_of_day."') and `appointment_time`like '%$time_noon%'")->where('scheduled_on','>=' ,$current_date)->whereRaw("replace(SUBSTRING_INDEX(SUBSTRING_INDEX(appointment_time, '-', 2), ',', -1) , '".$week_of_day."', '')")->pluck('appointment_time')->all();
			$old_start_val ='';
			$providerScheTime = ProviderSchedulerTime::where('facility_id',$faility_id)->count();
			//After current appointment date
			if(!empty($app_end_time)>0) {
				foreach($app_end_time as $app_end_time) {
					$time_split[] = explode(' ',$app_end_time);
				}				
				
				foreach($time_split as $time_split)	{
					$time_start_val[] =$time_split[0];
					$end_split[] = explode('-',$time_split[1]);
				}
				foreach($end_split as $end_split) {
					$end_app_time[] = $end_split[1];
				}
				$old_start_val =min($time_start_val);
				$old_app_time =max($end_app_time);
				//Seleted time for the day
				$select_time = explode(';',$request["select_time"]);
				/* if(($request["select_time"] !='00:00;00:00') ) {
					return Response::json(array('status'=>'error', 'msg' => 'Set start time and end time is 00:00','data'=>'start_time'));
				}*/
				
				if($providerScheTime > 0)
					return Response::json(array('status'=>'error', 'msg' => 'appointment already exists','data'=>'start_time'));	
				 if(($select_time[0] > $old_start_val) && ($select_time[0] < $old_app_time)) {
					return Response::json(array('status'=>'error', 'msg' => 'appointment already exists','data'=>'start_time'));
				} elseif(($select_time[1] < $old_app_time) &&($select_time[1] < $old_start_val)){
					return Response::json(array('status'=>'error', 'msg' => 'appointment already exists','data'=>'end_time'));
				} else {
					return Response::json(array('status'=>'success'));
				}
			}	
		}
	}

	public function scheduledAppointment($request,$id)
	{
		/* $old_sche = Facility::select('monday_forenoon','tuesday_forenoon','wednesday_forenoon','thursday_forenoon','friday_forenoon','saturday_forenoon','sunday_forenoon','monday_afternoon','tuesday_afternoon','wednesday_afternoon','thursday_afternoon','friday_afternoon','saturday_afternoon','sunday_afternoon')->where('id',$id)->first();
		dd($request);
		$current_date = date('Y-m-d');			
		if(PatientAppointment::where('facility_id',$id)->where('scheduled_on','>=' ,$current_date)->count()>0){	

			$request['monday_forenoon'] = $old_sche['monday_forenoon'];
			$request['tuesday_forenoon'] = $old_sche['tuesday_forenoon'];
			$request['wednesday_forenoon'] = $old_sche['wednesday_forenoon'];
			$request['thursday_forenoon'] = $old_sche['thursday_forenoon'];
			$request['friday_forenoon'] = $old_sche['friday_forenoon'];
			$request['saturday_forenoon'] = $old_sche['saturday_forenoon'];
			$request['sunday_forenoon'] = $old_sche['sunday_forenoon'];
			$request['wednesday_afternoon'] = $old_sche['wednesday_afternoon'];
			$request['friday_afternoon'] = $old_sche['friday_afternoon'];
			$request['saturday_afternoon'] = $old_sche['saturday_afternoon'];
			$request['sunday_afternoon'] = $old_sche['sunday_afternoon'];
			$request['monday_afternoon'] = $old_sche['monday_afternoon'];
			$request['tuesday_afternoon'] = $old_sche['tuesday_afternoon'];
			$request['thursday_afternoon'] = $old_sche['thursday_afternoon'];
		}
		//$request  */
	}

	function __destruct() 
	{
    }
}
