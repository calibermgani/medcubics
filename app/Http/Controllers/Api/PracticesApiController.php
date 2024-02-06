<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Models\Practice as Practice;
use App\Models\Language as Language;
use App\Models\Speciality as Speciality;
use App\Models\Taxanomy as Taxanomy;
use App\Models\AddressFlag as AddressFlag;
use App\Models\NpiFlag as NpiFlag;
use Intervention\Image\ImageManagerStatic as Image;
use App\Http\Helpers\Helpers as Helpers;
use Auth;
use Response;
use Request;
use Validator;
use Lang;
use Input;
use File;
use Schema;
use Session;
use Cache;
use Config;
use DB;
use Illuminate\Support\Collection;
use App\Http\Controllers\Api\ProviderApiController as ProviderApiController;
use App\Models\Provider as Provider;
use App\Models\Payments\ClaimTXDESCV1;
use App\Models\Payments\ClaimInfoV1;

class PracticesApiController extends Controller 
{
	
	public function getIndexApi()
	{
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>''));
	}
	
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$count=Practice::where('id',$id)->count();
		if($count !=0) {
            $practice 			= Practice::where('id',$id)->first();
			$specialities_id 	= $practice->speciality_id;
            $specialities 		= Speciality::orderBy('speciality','ASC')->pluck('speciality','id')->all();
            $speciality_id 		= $practice->speciality_id;
			$taxanomies 		= Taxanomy::where('speciality_id',$specialities_id)->pluck('code','id')->all();
            $taxanomy_id		= $practice->taxanomy_id;
            $language 			= Language::orderBy('language','ASC')->pluck('language','id')->all();
            $language_id		= $practice->language_id;

            /// Get Pay to address for usps ///
            $pta_address_flag 	= AddressFlag::getAddressFlag('practice',$practice->id,'pay_to_address');					
            $addressFlag['pta'] = $pta_address_flag;

            /// Get mailling address for usps ///
            $ma_address_flag 	= AddressFlag::getAddressFlag('practice',$practice->id,'mailling_address');
            $addressFlag['ma'] 	= $ma_address_flag;

            /// Get primary address for usps ///
            $pa_address_flag 	= AddressFlag::getAddressFlag('practice',$practice->id,'primary_address');
            $addressFlag['pa'] 	= $pa_address_flag;

            /// Get NPI details ///
            $npi_flag 			= NpiFlag::getNpiFlag('practice',$practice->id,$practice->entity_type);

            if(!$npi_flag) {
                $npiflag_columns = Schema::getColumnListing('npiflag');
                foreach($npiflag_columns as $columns) {
                    $npi_flag[$columns] = '';
                }			
            }

            $time['monday_forenoon'] 	= $practice->monday_forenoon;
            $time['tuesday_forenoon'] 	= $practice->tuesday_forenoon;
            $time['wednesday_forenoon'] = $practice->wednesday_forenoon;
            $time['thursday_forenoon'] 	= $practice->thursday_forenoon;
            $time['friday_forenoon'] 	= $practice->friday_forenoon;
            $time['saturday_forenoon'] 	= $practice->saturday_forenoon;
            $time['sunday_forenoon'] 	= $practice->sunday_forenoon;
            $time['monday_afternoon'] 	= $practice->monday_afternoon;
            $time['tuesday_afternoon'] 	= $practice->tuesday_afternoon;
            $time['wednesday_afternoon'] = $practice->wednesday_afternoon;
            $time['thursday_afternoon'] = $practice->thursday_afternoon;
            $time['friday_afternoon'] 	= $practice->friday_afternoon;
            $time['saturday_afternoon'] = $practice->saturday_afternoon;
            $time['sunday_afternoon'] 	= $practice->sunday_afternoon;
            
            return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('practice','specialities','speciality_id','taxanomies','taxanomy_id','language','language_id','addressFlag','npi_flag','time')));
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}
	}
	
	public function getUpdateApi($id, $request='')
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if($request == '')
			$request = Request::all();
		$rules = Practice::$rules;
		if($request['entity_type'] == 'Group') {
            $rules = $rules+array( 	'group_tax_id' 	=>'required|digits:9',
                            		'group_npi' 	=>'required|digits:10' );
		} elseif($request['entity_type'] == 'Individual') {
            $rules = $rules+array(  'tax_id' =>'required|digits:9',
                            		'npi' =>'required|digits:10'  );
		}
		
		// if($request['primary_city'] == "" || (is_null($request['primary_city']) == true) ) {
		// 	unset($rules['primary_city']);
		// }
		// if($request['primary_state'] == "" || (is_null($request['primary_state']) == true) ) {
		// 	unset($rules['primary_state']);
		// }
		// if($request['primary_zip5'] == "" || (is_null($request['primary_zip5']) == true) ) {
		// 	unset($rules['primary_zip5']);
		// }

		$validator = Validator::make($request, $rules+array('image'=>Config::get('siteconfigs.customer_image.defult_image_size')), Practice::messages()+array('image.mimes'=>Config::get('siteconfigs.customer_image.defult_image_message')));

		if ($validator->fails()) {
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		} else {			
			$practice = Practice::find($id);
			
			$filename = '';
            $extension = '';
			if(isset($request['imagefile'])) {
				$request['avatar_name'] = "";
				$request['avatar_ext'] = "";
				unset($request['imagefile']);
				$practice->avatar_name = $filename;
				$practice->avatar_ext = $extension;
			}

			if(Input::hasFile('image')) {
				$image 			= Input::file('image');
				$filename  		= rand(11111,99999);
				$old_filename  	= $practice->avatar_name;
				$old_extension  = $practice->avatar_ext;
				$extension 		= $image->getClientOriginalExtension();
				$filestoreName 	= $filename .'.'.$extension;
				$filestoreoldName = $old_filename .'.'.$old_extension;
                $resize 		= array('150','150');
                //Helpers::mediauploadpath('','practice',$id,$image,$resize,$filestoreName,$filestoreoldName);  
				Helpers::mediauploadpath('','practice',$image,$resize,$filestoreName,$filestoreoldName);  
				$practice->avatar_name = $filename;
				$practice->avatar_ext = $extension;
			}
			$practice->update($request);
			$practice->updated_by = Auth::user ()->id;
			$practice->save ();

			// Clear previously assigned practice details and re-assign	it.
            Cache::forget('practice_details'.$id );
            Session::forget('timezone');
			$practice_id = Session::get('practice_dbid');
	        $practices = Cache::remember('practice_details'.$practice_id , 30, function() use($practice_id) {     
	            $practices = Practice::where('id', $practice_id)->select('practice_name', 'timezone','avatar_name', 'avatar_ext')->first();
	            return $practices;
	        });
	        Session::put('timezone', $practices['timezone']);

			/// Starts - Pay to address flag update ///
			$address_flag = array();
			$address_flag['type'] 			= 'practice';
			$address_flag['type_id'] 		= $practice->id;
			$address_flag['type_category'] 	= 'pay_to_address';
			$address_flag['address2'] 		= $request['pta_address1'];
			$address_flag['city'] 			= $request['pta_city'];
			$address_flag['state'] 			= $request['pta_state'];
			$address_flag['zip5'] 			= $request['pta_zip5'];
			$address_flag['zip4'] 			= $request['pta_zip4'];
			$address_flag['is_address_match'] = $request['pta_is_address_match'];
			$address_flag['error_message'] 	= $request['pta_error_message'];
			AddressFlag::checkAndInsertAddressFlag($address_flag);
			/* Ends - Pay to address */

			/// Starts - Mailling address flag update ///
			$address_flag 				= array();
			$address_flag['type'] 		= 'practice';
			$address_flag['type_id'] 	= $practice->id;
			$address_flag['type_category'] = 'mailling_address';
			$address_flag['address2'] 	= $request['ma_address1'];
			$address_flag['city'] 		= $request['ma_city'];
			$address_flag['state'] 		= $request['ma_state'];
			$address_flag['zip5'] 		= $request['ma_zip5'];
			$address_flag['zip4'] 		= $request['ma_zip4'];
			$address_flag['is_address_match'] = $request['ma_is_address_match'];
			$address_flag['error_message'] = $request['ma_error_message'];
			AddressFlag::checkAndInsertAddressFlag($address_flag);
			/* Ends - Mailling address */

			/// Starts - Primary address flag update ///
			$address_flag 				= array();
			$address_flag['type'] 		= 'practice';
			$address_flag['type_id'] 	= $practice->id;
			$address_flag['type_category'] = 'primary_address';
			$address_flag['address2'] 	= $request['pa_address1'];
			$address_flag['city'] 		= $request['pa_city'];
			$address_flag['state'] 		= $request['pa_state'];
			$address_flag['zip5'] 		= $request['pa_zip5'];
			$address_flag['zip4'] 		= $request['pa_zip4'];
			$address_flag['is_address_match'] 	= $request['pa_is_address_match'];
			$address_flag['error_message'] 		= $request['pa_error_message'];
			AddressFlag::checkAndInsertAddressFlag($address_flag);
			/* Ends - Primary address */

			/* Starts - NPI flag update */
			$request['company_name'] = 'npi';
			$request['type'] = 'practice';
			$request['type_id'] = $practice->id;
			if($request['entity_type'] == 'Group')
				$request['type_category'] = 'Group';
			else
				$request['type_category'] = 'Individual';
			NpiFlag::checkAndInsertNpiFlag($request);
			
			/* Ends - NPI flag update */
            if(config('siteconfigs.is_enable_provider_add')) {
				//update admin practice table				
				$admin_db_name = 'responsive';//getenv('DB_DATABASE');
				$dbconnection = new DBConnectionController();
				$dbconnection->updatepracticeInfoinAdminDB($request,$practice->practice_db_id,$admin_db_name,$filename,$extension);
			}
			
			//Billing Entity Yes create default billing provider
			/*if($request['billing_entity'] == 'Yes')
			{
				$this->create_default_provider($request,$practice);
			}*/
			
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.practice_update_msg"),'data'=>''));					
		}
	}
	
	## update date with timezone for old record claims Start Thilagavathy
	public function updatetimesubmiteddateApi($practice_id) 
	{
	//	$claimstxlists = ClaimTXDESCV1::whereIn('transaction_type',['Insurance Payment','insurance Adjustment','Submitted','Submitted Paper'])->lastest('id')->get();
		$data = ClaimTXDESCV1::groupby('claim_id')->orderBy('id','desc');
		$datas = clone $data;
		$first_submited_claim_tx = $data->whereIn('transaction_type',['Insurance Payment','insurance Adjustment','Submitted','Submitted Paper','Resubmitted','Resubmitted Paper','Patient Refund'])->select('id','claim_id','created_at')->get()->toArray();
		$last_submited_claim_tx =  $datas->whereIn('transaction_type',['Submitted','Submitted Paper','Resubmitted','Resubmitted Paper'])->select(DB::raw('MAX(created_at) AS created_at'), 'claim_id', DB::raw('MAX(id) AS id'))->get()->toArray();	
		if(!empty($first_submited_claim_tx)){
			foreach($first_submited_claim_tx as $list_date){			
				ClaimInfoV1::where('id',$list_date['claim_id'])->update(['submited_date'=> $list_date['created_at'],'last_submited_date' => $list_date['created_at']]);				
			}
		}	
		if(!empty($last_submited_claim_tx)){		
			foreach($last_submited_claim_tx as $list_date){			
				ClaimInfoV1::where('id',$list_date['claim_id'])->update(['last_submited_date' => $list_date['created_at']]);			
			}
		}
		return Response::json(array('status'=>'success', 'message'=>'Successfully Updated'));	
	}
	
	public function updatetimefileddateApi($practice_id) 
	{	
		$data = ClaiminfoV1::where('status','Ready')->whereNull('deleted_at');
		$datas = clone $data;
		$first_filed_claim_tx = $data->select('id','claim_number','created_at')->get()->toArray();
		
		if(!empty($first_filed_claim_tx)){
			foreach($first_filed_claim_tx as $list_date){			
				ClaimInfoV1::where('id',$list_date['id'])->update(['filed_date'=> $list_date['created_at']]);
			}			
		}
		return Response::json(array('status'=>'success', 'message'=>'Successfully Updated'));	
	}
	## update date with timezone for old record claims Thilagavathy End

    public function getShowApi($id) 
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		
		if(Practice::where('practice_db_id',$id)->count())
		{
			$practice = Practice::with('taxanomy_details', 'speciality_details','languages_details')->where('practice_db_id', $id)->first();
			$temp = new Collection($practice);
			$prac_id = $temp['id'];
			$temp->pull('id');
			$practice_id = Helpers::getEncodeAndDecodeOfId($prac_id, 'encode');
			$temp->prepend($practice_id, 'id');
			$prac = $temp->all();
			$practice = json_decode(json_encode($prac), FALSE);

			$specialities 	= Speciality::orderBy('speciality', 'ASC')->pluck('speciality', 'id')->all();
			$speciality_id 	= $practice->speciality_id;
			$taxanomies 	= Taxanomy::orderBy('code', 'ASC')->pluck('code', 'id')->all();
			$taxanomy_id 	= $practice->taxanomy_id;
			$language 		= Language::orderBy('language', 'ASC')->pluck('language', 'id')->all();
			$language_id 	= $practice->language_id;

			/// Get Pay to address for usps ///
			$pta_address_flag 	= AddressFlag::getAddressFlag('practice', $practice->id, 'pay_to_address');
			$addressFlag['pta'] = $pta_address_flag;

			/// Get mailling address for usps ///
			$ma_address_flag 	= AddressFlag::getAddressFlag('practice', $practice->id, 'mailling_address');
			$addressFlag['ma'] 	= $ma_address_flag;

			/// Get primary address for usps ///
			$pa_address_flag 	= AddressFlag::getAddressFlag('practice', $practice->id, 'primary_address');
			$addressFlag['pa'] 	= $pa_address_flag;

			/// Get NPI details ///
			$npi_flag = NpiFlag::getNpiFlag('practice', $practice->id, $practice->entity_type);
			//dd($npi_flag);

			if(!$npi_flag) 
			{
				$npiflag_columns = Schema::getColumnListing('npiflag');
				foreach ($npiflag_columns as $columns) 
				{
					$npi_flag[$columns] = '';
				}
			}
			return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('practice', 'specialities', 'speciality_id', 'taxanomies', 'taxanomy_id', 'language', 'language_id', 'addressFlag', 'npi_flag')));
		}
		else
		{
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
    }

    public function getProviderCount($practice_id) 
	{
		return Practice::getProviderCount($practice_id);
	}
	
	public function getFacilityCount($practice_id)
	{
		return Practice::getFacilityCount($practice_id);
	}
	
	public function getPatientrCount($practice_id)
	{
		return Practice::getPatientrCount($practice_id);
	}
	
	public function getVistiCount($practice_id)
	{
		return Practice::getVistiCount($practice_id);
	}
	
	public function getClaimCount($practice_id)
	{
		return Practice::getClaimCount($practice_id);
	}
		
	public function getCollectionCount($practice_id)
	{
		return Practice::getCollectionCount($practice_id);
	}
		
	public function getTaxanomies()
	{
		$taxanomy_arr = "<option value=''>-- Select --</option>";
		if(!empty(Request::input('specialities_id'))) {
			$specialities_id = Request::input('specialities_id');
			$taxanomy = Taxanomy::where('speciality_id',$specialities_id)->get()->toArray();
			if(count($taxanomy)>0) {
				foreach($taxanomy as $taxanomies) {					
					$taxanomy_arr .= "<option value='".$taxanomies['id']."'>".$taxanomies['code']."</option>";
				}				
			} else {
				$taxanomy_arr = "<option value=''>-- No records found --</option>";
			}
		}
		return  $taxanomy_arr;
	}

	/*** Delete Avatar in practice table start ***/
	public function getDeleteApi($id,$p_name)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$delete_avr = Practice::where('id',$id)->first();
		$delete_avr->avatar_name = "";
		$delete_avr->avatar_ext = "";
		$delete_avr->save();
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
	}
	/*** Delete Avatar in practice table end ***/
	
	public function create_default_provider($request,$practice)
	{
		$def_provider_array = $request;
		
		if($request['enumeration_type']=="NPI-2") {
			$def_provider_array['organization_name'] = $request['basic_organization_name'];
		} elseif($request['enumeration_type']=="NPI-1") {
			$def_provider_array['last_name'] 		 = $request['basic_last_name'];
			$def_provider_array['first_name'] 		 = $request['basic_first_name'];
			$def_provider_array['middle_name'] 		 = $request['basic_middle_name'];
		} else {
			$def_provider_array['last_name'] 		 = $practice->practice_name;
			$def_provider_array['first_name'] 		 = $practice->practice_name;
			$def_provider_array['middle_name'] 		 = "";
		}
		
		if($request['enumeration_type'] == 'NPI-2') {
			if(Provider::where('organization_name',$def_provider_array['organization_name'])->where('provider_types_id','5')->where('npi',$request['npi'])->count()) {
				$provider_err = 'yes';
			} else {
				$provider_err = 'no';
			}
		} else {
			if(Provider::where('last_name',$def_provider_array['last_name'])->where('first_name',$def_provider_array['first_name'])->where('provider_types_id','5')->where('npi',$request['npi'])->count())	{
				$provider_err = 'yes';
			} else {
				$provider_err = 'no';
			}
		}
		
		if($provider_err == 'no') {
			$def_provider_array['npi'] 				 	= ($request['npi'] != '' ? $request['npi'] : $request['group_npi']);
			$def_provider_array['provider_types_id'] 	= '5';
			$def_provider_array['etin_type'] 		 	= 'TAX ID';
			$def_provider_array['etin_type_number']  	= ($request['tax_id'] != '' ? $request['tax_id'] : $request['group_tax_id']);
			$def_provider_array['phone'] 				= @$request['mailling_telephone_number'];
			$def_provider_array['fax'] 					= @$request['mailling_fax_number'];
			$def_provider_array['email'] 				= @$practice->email;
			$def_provider_array['website'] 				= '';
			$def_provider_array['address_1'] 			= ($request['mailling_address_1'] != '' ? $request['mailling_address_1'] : $request['pay_add_1']);
			$def_provider_array['address_2'] 			= ($request['mailling_address_2'] != '' ? $request['mailling_address_2'] : $request['pay_add_2']);
			$def_provider_array['city'] 				= ($request['mailling_city'] != '' ? $request['mailling_city'] : $request['pay_city']);
			$def_provider_array['state'] 				= ($request['mailling_state'] != '' ? $request['mailling_state'] : $request['pay_state']);
			$def_provider_array['zipcode5'] 			= ($request['mailling_postal_code'] != '' ? substr($request['mailling_postal_code'],0,-4) : $request['pay_zip5']);
			$def_provider_array['zipcode4'] 			= ($request['mailling_postal_code'] != '' ? substr($request['mailling_postal_code'],4) : $request['pay_zip4']);
			$def_provider_array['medicareptan'] 		= ($request['identifiers_identifier'] != '' ? $request['identifiers_identifier'] : '');
			$def_provider_array['enumeration_type']  	= $request['enumeration_type'];
			$def_provider_array['speciality_id'] 	  	= "";
			$def_provider_array['provider_dob'] 	  	= "";
			$def_provider_array['def_provider_added'] 	= "yes";
			$def_provider_array['general_address1'] = $def_provider_array['general_city'] = $def_provider_array['general_state'] = $def_provider_array['general_zip5'] = $def_provider_array['general_zip4'] = $def_provider_array['general_is_address_match']  = $def_provider_array['general_error_message'] 	= "";
			$practiceprovider_obj = new ProviderApiController();
			$practiceprovider_obj->getStoreApi($def_provider_array);
		}
		
		return 0;
	}
	
	function __destruct() 
	{
    }
	
}

