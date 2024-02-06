<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProviderReportController as ProviderReportController;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Models\Provider as Provider;
use App\Models\Speciality as Speciality;
use App\Models\Taxanomy as Taxanomy;
use App\Models\Facility as Facility;
use App\Models\Provider_degree as ProviderDegree;
use App\Models\Provider_type as ProviderType;
use Intervention\Image\ImageManagerStatic as Image;
use App\Models\NpiFlag as NpiFlag;
use App\Models\AddressFlag as AddressFlag;
use App\Models\State as State;
use App\Models\Insurance as Insurance;
use App\Models\Document as Document;
use App\Http\Helpers\Helpers as Helpers;
use Illuminate\Support\Collection;
use Auth;
Use Log;
use Request;
use Response;
use Validator;
use Input;
use Excel;
use App;
use File;
use Schema;
use DB;
use Session;
use Config;
use Lang;
use App\Models\Medcubics\ApiConfig as ApiConfig;
use App\Models\ProviderScheduler as ProviderScheduler;
use App\Models\Payments\ClaimInfoV1;

class ProviderApiController extends Controller 
{
	public function getIndexApi($export='',$search ='')
	{
        $providers = Provider::orderBy('short_name', 'asc')->with('provider_type_details','degrees','speciality','taxanomy','facility_details','provider_types');
		$provider_type 		= ProviderType::orderBy('name','asc')->pluck('name','id')->all();
		//Export all codes format likes pdf,excel,csv
		if($export != "")
		{	
			$search = (isset($search) && $search =='') ? Request::all() :[];
		}
		if(isset($search) && $search !='' && count($search)>0)
		{
			$speciality = trim(@$search['speciality']);
			$provider_type = isset($search['provider_type']) ? $search['provider_type'] : array_keys($provider_type);
			$providers = $providers->where(function($query) use($search,$provider_type)
					{ return $query->where('short_name', 'LIKE', '%'.@$search['short_name'].'%')
					->where('provider_name', 'LIKE', '%'.@$search['provider_name'].'%')
					->where('etin_type', 'LIKE', '%'.@$search['etin_type'].'%')
					->whereIn('provider_types_id',@$provider_type)
					->where('etin_type_number', 'LIKE', '%'.@$search['tax_id'].'%')
					->where('npi', 'LIKE', '%'.@$search['npi'].'%')
					->where('status','LIKE',@$search["status"].'%'); });
			if($speciality !='')
				$providers = $providers->where('speciality_id', '!=', '')->whereHas('speciality', function($q) use ($speciality){ $q->where('speciality', 'LIKE', '%'.$speciality.'%');});
		}
        if($export != ""){
            $providers->join('provider_types', 'providers.provider_types_id', '=', 'provider_types.id')->orderBy('provider_types.name', 'ASC');
        }
		$providers = $providers->get();
        /*
                if($export != "")
        {
			$exportparam = array(
							'filename'  =>	'Provider',
							'heading' 	=> 'Provider',
							'fields' 	=> array(
										'short_name'		=> 'Short Name',
										'provider_name'		=> 'Provider Name',
										'provider_types_id' => array('table'=>'provider_types','column'=>'name','label'=>'Type'),
										'etin_type'			=> 'ETIN Type',
										'etin_type_number'	=> 'Tax ID/SSN',
										'npi'				=> 'NPI',
										'speciality_id' 	=> array('table'=>'speciality','column'=>'speciality','label'=>'Speciality'),
										'status'			=>	'Status'
										)
						);
            $callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($exportparam, $providers, $export); 
        }
        */
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('providers','provider_type')));
	}
		
	public function getCreateApi()
	{
		$provider 			= Provider::where('status','Active')->get()->first();
		$taxanomies 		= '';
        $states 			= State::orderBy('code','ASC')->pluck( 'code', 'code' )->all();
        $insurances 		= Insurance::orderBy('insurance_name','ASC')->pluck( 'insurance_name', 'id' )->all();
		$facilities 		= Facility::orderBy('facility_name', 'asc')->pluck( 'facility_name', 'id' )->all();
		$specialities 		= Speciality::orderBy('speciality', 'asc')->pluck( 'speciality', 'id' )->all();
		$provider_type 		= ProviderType::orderBy('name', 'asc')->whereNotIn('name',['Rendering','Billing'])->pluck( 'name', 'id' )->all();
		$provider_degree 	= ProviderDegree::orderBy('degree_name', 'asc')->pluck( 'degree_name', 'id' )->all();

		/// Get address for usps ///
		$addressFlag['general']['address1'] 	= '';
		$addressFlag['general']['city'] 		= '';
		$addressFlag['general']['state'] 		= '';
		$addressFlag['general']['zip5'] 		= '';
		$addressFlag['general']['zip4'] 		= '';
		$addressFlag['general']['is_address_match'] = '';
		$addressFlag['general']['error_message'] 	= '';

		/// Get NPI details ///
		$npiflag_columns = Schema::getColumnListing('npiflag');
		foreach($npiflag_columns as $columns)
		{
			$npi_flag[$columns] = '';
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('provider','specialities', 'taxanomies', 'facilities', 'provider_degree', 'provider_type','addressFlag','npi_flag','states','insurances')));
	}
	/**
	 * Request comes on the ProviderController.
	 *
	 * @return Json file
	 */
	public function getStoreApi($request='')
	{
		//request type not empty
        if($request == '') {
			$request = Request::all();
		}
		$is_valid_npi = Helpers::checknpi_valid_process($request['npi'],'NPI-1'); // check npi valid or not back end validation
		//if select provider DOB 
		if($request['provider_dob'] != '') 
			$request['provider_dob'] = date('Y-m-d',strtotime($request['provider_dob']));
		//Validator check 	
		// website checking
		if(!empty($request['website'])){
			if(substr($request['website'],0,7) != 'http://' && substr($request['website'],0,8) != 'https://')	
				$request['website'] = "http://".$request['website'];
		}
		
        Validator::extend('provider_type_validator', function($attribute, $value, $parameters,$short_name) {			
            if(Input::get($parameters[3]) == 'NPI-2') {
				//NPI 2
                $organization_name = Input::get($parameters[2]);
				$npi			   = Input::get($parameters[4]);
				$short_name		   = $parameters[5];
                if($value > 0) {
					$count = Provider::where('organization_name',$organization_name)->where('short_name',$short_name)->where('provider_types_id',$value)->where('npi',$npi)->count();
                    if($count != 0)
						return false;
                }                     
            } else  {//NPI 1
			    $last_name 	= Input::get($parameters[0]);
                $first_name = Input::get($parameters[1]);
				$npi		= Input::get($parameters[4]);
				$short_name	= $parameters[5];
				if($value > 0) {
                    $count = Provider::where('last_name',$last_name)->where('first_name',$first_name)->where('provider_types_id',$value)->where('short_name',$short_name)->where('npi',$npi)->count();
                    if($count != 0)
                        return false;
                }                
            }                               
            return true;                
        });
            
        Validator::extend('additional_provider_type_validator', function($attribute, $value, $parameters) {
        	if(Input::get($parameters[3]) == 'NPI-2') {
                $organization_name = Input::get($parameters[2]);
				$npi			   = Input::get($parameters[4]);
                if(count($value) > 0) {
					$count = Provider::where('organization_name',$organization_name)->where('npi',$npi)->whereIn('provider_types_id',$value)->count();
					if($count != 0)
						return false;
				}                   
            } else {
				$last_name 	= Input::get($parameters[0]);
                $first_name = Input::get($parameters[1]);
				$npi		= Input::get($parameters[4]);
                if(count($value) > 0) {
					$count = Provider::where('last_name',$last_name)->where('first_name',$first_name)->where('npi',$npi)->whereIn('provider_types_id',$value)->count();
                    if($count != 0)
						return false;
                }
            } 
			return true;                   
        });

		Validator::extend('check_npi_api_validator', function($attribute) use($is_valid_npi)
		{
			if($is_valid_npi == 'No')
				return false;                        
			else 
				return true;                   
		});
		
		Validator::extend('short_name_provider_type', function($attribute, $value, $parameters){
			$short_name = Input::get($parameters[0]);
			$provider_types_id = Input::get($parameters[1]);
			
			$count = Provider::where('short_name',$short_name)->where('provider_types_id',$provider_types_id)->count();
			$count_sql = Provider::where('short_name',$short_name)->get();
			if($count > 0) {
				return false;
			}	
			else
                return true;            
		});
			
        if(@$request['def_provider_added']!='yes')	{
			$rules = Provider::$rules+array('short_name'=> 'short_name_provider_type:short_name,provider_types_id')+array('provider_types_id' => "required|provider_type_validator:last_name,first_name,organization_name,enumeration_type,npi,".$request['short_name'],'additional_provider_type' => "additional_provider_type_validator:last_name,first_name,organization_name,enumeration_type,npi",'npi' => 'digits:10|check_npi_api_validator')+array('image'=>Config::get('siteconfigs.customer_image.defult_image_size'));
		} else {
			//Short name unique validation check, maxmum, minmum 3 letters. provider type is required. image size defult set
			$rules = array('short_name'=> 'short_name_provider_type:short_name,provider_types_id','npi'=>'check_npi_api_validator');
		}

		// Rule - different only applicable if speaciality_id and speciality_id2 is not empty.
		if(is_null($request['speciality_id']) || is_null($request['speciality_id2'])) {
			unset($rules['speciality_id']);
			unset($rules['speciality_id2']);
		}
		
		$validator = Validator::make($request, $rules, Provider::$messages+array('npi.check_npi_api_validator' => Lang::get("common.validation.npi_validcheck"))+array('short_name_provider_type' => 'Short name already exists')+array('image.mimes'=>Config::get('siteconfigs.customer_image.defult_image_message')));
		if($validator->fails()) {
            $errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
        } else {
            $request['created_by'] = Auth::user()->id;
            if($request['enumeration_type'] == 'NPI-2') {
                $request['provider_name'] 	= $request['organization_name'];
                $request['first_name'] 		= '';
                $request['last_name'] 		= '';
            } else {
                $request['provider_name'] = $request['last_name'].', '.$request['first_name'].' '.$request['middle_name'];
                $request['organization_name'] = '';
            }
			//Admin db also store the data
			$admin_db_name = 'responsive';//getenv('DB_DATABASE');
			$dbconnection = new DBConnectionController();
			if(Session::has('practice_dbid')) {
				$request['practice_id'] = Session::get('practice_dbid'); 
				$customer = DB::connection($admin_db_name)->table('practices')->where('id', $request['practice_id'])->value('customer_id');
				$request['customer_id'] = $customer;
			}
			//Create the provider		
			$provider_id = $this->createProvider($request); 			
			//document api store start
            if(isset($request['temp_doc_id'])) {
				if($request['temp_doc_id']!="") Document::where('temp_type_id', '=', $request['temp_doc_id'])->update(['type_id' => $provider_id,'temp_type_id' => '']);
			}
			//document api store end
			$practice_db_provider_id = $provider_id;
			    
			if(config('siteconfigs.is_enable_provider_add')) { 
				$provider_data = Provider::where('id',$provider_id)->select('id', 'avatar_name','avatar_ext','digital_sign_name','digital_sign_ext')->first();
                $data = array();
                $data['avatar_name'] = $provider_data['avatar_name'];
                $data['avatar_ext'] = $provider_data['avatar_ext'];
                $data['digital_sign_name'] = $provider_data['avatar_name'];
                $data['digital_sign_ext'] = $provider_data['digital_sign_ext'];
                $admin_provider_id = $dbconnection->createProviderinOtherDB($request,$admin_db_name,$data);
				$practice_db_provider_id = $admin_provider_id;
				Provider::on($admin_db_name)->where('id', $admin_provider_id)->update(['practice_db_provider_id'=>$practice_db_provider_id]);	
			}
			
			Provider::where('id', $provider_id)->update(['practice_db_provider_id'=>$practice_db_provider_id]);
			//If addtion provider type check in form blade		
			if(count((array)@$request['additional_provider_type']) > 0) {
                foreach($request['additional_provider_type'] as $provider_type) {
                    $request['provider_types_id'] = $provider_type;
                    $sub_provider_id = $this->createProvider($request); 
					$practice_db_sub_provider_id = $sub_provider_id;
					if(config('siteconfigs.is_enable_provider_add')) {
						$admin_sub_provider_id = $dbconnection->createProviderinOtherDB($request,$admin_db_name);
						$practice_db_sub_provider_id = $admin_sub_provider_id;
						Provider::on($admin_db_name)->where('id', $admin_sub_provider_id)->update(['practice_db_provider_id'=>$practice_db_sub_provider_id]);	
					}
					Provider::where('id', $sub_provider_id)->update(['practice_db_provider_id'=>$practice_db_sub_provider_id]);
				}
            }
			//Encode the provider id
			$provider_id = Helpers::getEncodeAndDecodeOfId($provider_id,'encode');  
            return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.provider_create_msg"),'data'=>$provider_id));
		}
	}
        
	public function createProvider($request)
    {
        $result = Provider::create($request);
		$user = Auth::user ()->id;
		$result->created_by = $user;
		$result->save ();
        if(Input::hasFile('image'))
        {
			$image = Input::file('image');
            $filename  = rand(11111,99999);
            $extension = $image->getClientOriginalExtension();
            $filestoreName = $filename .'.'.$extension;
            $resize = array('150','150');
            Helpers::mediauploadpath('','provider',$image,$resize,$filestoreName); 
            $result->avatar_name = $filename;
            $result->avatar_ext = $extension;
            $result->save();
        }
        if(Input::hasFile('digital_sign'))
        {
            $image 				= Input::file('digital_sign');
            $signfilename  		= rand(11111,99999);
            $signextension 		= $image->getClientOriginalExtension();
            $signfilestoreName 	= $signfilename .'.'.$signextension;
            $resize 			= array('150','150');
            Helpers::mediauploadpath('','provider',$image,$resize,$signfilestoreName);
            $result->digital_sign_name 	= $signfilename;
            $result->digital_sign_ext 	= $signextension;
            $result->save();
        }
        /// Starts - address flag update ///				
        $address_flag 				= array();
        $address_flag['type'] 		= 'provider';
        $address_flag['type_id'] 	= $result->id;
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

        /* Starts - NPI flag update */
        $request['company_name'] 	= 'npi';
        $request['type'] 		 	= 'provider';
        $request['type_id'] 		= $result->id;
        $request['type_category'] 	= 'Individual';
        NpiFlag::checkAndInsertNpiFlag($request);
        /* Ends - NPI flag update */
        return $result->id;
    }
	
	public function getEditApi($id)
	{ 
        $id = Helpers::getEncodeAndDecodeOfId($id,'decode');  
        
		if((isset($id) && is_numeric($id))  && (isset($id) && is_numeric($id)) && Provider::where('id', $id)->count())
		{
			$provider 	= 	Provider::findOrFail($id);
			
            if(isset($provider['provider_dob']) && $provider['provider_dob'] != '0000-00-00')    
                $provider['provider_dob']       = date('m/d/Y',strtotime($provider['provider_dob']));
            else
                $provider['provider_dob']       = '';
			
            $specialities_id    =   $provider->speciality_id;
            $specialities_id2   =   $provider->speciality_id2;
			$facilities 		= 	Facility::orderBy('facility_name', 'asc')->pluck ( 'facility_name', 'id' )->all();
			$specialities 		= 	Speciality::orderBy('speciality', 'asc')->pluck ( 'speciality', 'id' )->all();
			$provider_degree 	= 	ProviderDegree::orderBy('degree_name', 'asc')->pluck ( 'degree_name', 'id' )->all();
			$provider_type 		= 	ProviderType::whereNotIn('name',['Rendering','Billing'])->orderBy('name', 'asc')->pluck ( 'name', 'id' )->all();
			$taxanomies 		= Taxanomy::where('speciality_id',$specialities_id)->pluck('code','id')->all();                        
            $taxanomies2 		= Taxanomy::where('speciality_id',$specialities_id2)->pluck('code','id')->all();
            $document = Document::where('document_type','provider')->where('type_id',$id);
			$documents_personal_ssn = $document->where('category','personal_ssn')->first();
			$documents_PTAN = Document::where('document_type','provider')->where('type_id',$id)->where('category','medicare_ptan')->first();
			$documents_medicaid_id = Document::where('document_type','provider')->where('type_id',$id)->where('category','medicaid_id')->first();
			$documents_bcbs_id = Document::where('document_type','provider')->where('type_id',$id)->where('category','bcbs_id')->first();
			$documents_aetna_id = Document::where('document_type','provider')->where('type_id',$id)->where('category','aetna_id')->first();
			$documents_uhc_id = Document::where('document_type','provider')->where('type_id',$id)->where('category','uhc_id')->first();
			$documents_other_id1 = Document::where('document_type','provider')->where('type_id',$id)->where('category','other_id1')->first();
			$documents_other_id2 = Document::where('document_type','provider')->where('type_id',$id)->where('category','other_id2')->first();
			$documents_other_id3 = Document::where('document_type','provider')->where('type_id',$id)->where('category','other_id3')->first();
			$documents_tax_id = Document::where('document_type','provider')->where('type_id',$id)->where('category','tax_id')->first();
			$documents_state_license1 = Document::where('document_type','provider')->where('type_id',$id)->where('category','state_license1')->first();
			$documents_state_license2 = Document::where('document_type','provider')->where('type_id',$id)->where('category','state_license2')->first();
			$documents_state_license3 = Document::where('document_type','provider')->where('type_id',$id)->where('category','state_license3')->first();
			$documents_dea_number = Document::where('document_type','provider')->where('type_id',$id)->where('category','dea_number')->first();
			$documents_mammography_cert = Document::where('document_type','provider')->where('type_id',$id)->where('category','mammography_cert')->first();
			$documents_care_plan_oversight = Document::where('document_type','provider')->where('type_id',$id)->where('category','care_plan')->first();
			// Added missing query 
			// Revision 1 - Ref: MED-2829 5 Augest 2019: Pugazh			
			$documents_npi_id = Document::where('document_type','provider')->where('type_id',$id)->where('category','npi')->first();
			// dd($documents_npi_id);
			
			/// Get address for usps ///
			$general_address_flag 	= AddressFlag::getAddressFlag('provider',$provider->id,'general_information');
			$addressFlag['general'] = $general_address_flag;
            $insurances 			= Insurance::orderBy('insurance_name','ASC')->pluck( 'insurance_name', 'id' )->all();		
            $states 				= State::orderBy('code','ASC')->pluck( 'code', 'code' )->all();
                         
			/// Get NPI details ///
			$npi_flag = NpiFlag::getNpiFlag('provider',$provider->id,'Individual');
            //echo "<pre>";
			
			if(!$npi_flag)
			{
				$npiflag_columns = Schema::getColumnListing('npiflag');
				foreach($npiflag_columns as $columns)
				{
					$npi_flag[$columns] = '';
				}			
			}

			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('provider','specialities', 'taxanomies','taxanomies2','facilities', 'provider_degree', 'provider_type','addressFlag','npi_flag','insurances','states','documents_personal_ssn','documents_PTAN','documents_medicaid_id','documents_bcbs_id','documents_aetna_id','documents_uhc_id','documents_other_id1','documents_other_id2','documents_other_id3','documents_tax_id','documents_state_license1','documents_state_license2','documents_state_license3','documents_dea_number','documents_mammography_cert','documents_care_plan_oversight','documents_npi_id')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
		/**
	 * Request comes on the ProviderController.
	 *
	 * @return response Json file
	 */

	public function getUpdateApi($id)
	{		
		//Decode the provider number
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');  
		// get all Request .
        $request = Request::all();
		if(!empty($request['website'])){
			if(substr($request['website'],0,7) != 'http://' && substr($request['website'],0,8) != 'https://')	
				$request['website'] = "http://".$request['website'];
		}
		// check the id is number also db check
		if((isset($id) && is_numeric($id))  && (isset($id) && is_numeric($id)) && Provider::where('id', $id)->count()) {
			//Validator check the function
			Validator::extend('provider_type_validator', function($attribute, $value, $parameters) { 
				//NPI 2 is organization  
				if(Input::get($parameters[3]) == 'NPI-2') {
					//organization name
					$organization_name 	= Input::get($parameters[2]);
					$id 				= $parameters[4];
					$npi 				= Input::get($parameters[5]);
					$short_name 				= Input::get($parameters[6]);
					//NPI type
					if($value > 0) {
						//Count of provider
						$count = Provider::where('organization_name',$organization_name)->where('id','!=',$id)->where('short_name',$short_name)->where('provider_types_id',$value)->where('npi',$npi)->count();
						if($count != 0)
							return false;
					}                    
				} else {
					$last_name 	= Input::get($parameters[0]);
					$first_name = Input::get($parameters[1]);
					$id 		= $parameters[4];
					$npi 		= Input::get($parameters[5]);
					$short_name 				= Input::get($parameters[6]);
					if($value > 0)	{
						$count = Provider::where('last_name',$last_name)->where('id','!=',$id)->where('short_name',$short_name)->where('first_name',$first_name)->where('provider_types_id',$value)->where('npi',$npi)->count();
						if($count != 0)
						   return false;
					}                
				}
				return true;                
			});

			Validator::extend('short_name_provider_type', function($attribute, $value, $parameters)	{
				$short_name = $parameters[0];
				$provider_types_id = $parameters[1];
				$pro_id = $parameters[2];
				$pro_sht_not_change = isset($parameters[3])?$parameters[3]:0;
				$pro_typeId_not_change = isset($parameters[4])?$parameters[4]:0;
				$count_qry = Provider::where('provider_types_id',$provider_types_id)->where('short_name',$short_name);
				
				if(($pro_typeId_not_change == 1 )&& ($pro_sht_not_change == 1 )){
					$count_qry = $count_qry->where('short_name',$short_name)->where('provider_types_id',$provider_types_id);
				}
				elseif($pro_sht_not_change == 1)
					$count_qry = $count_qry->where('short_name',$short_name);
				elseif($pro_typeId_not_change == 1 )
					$count_qry = $count_qry->where('provider_types_id',$provider_types_id);
				$count = $count_qry->where('id','<>',$pro_id)->count();
				//$count = Provider::where('short_name',$short_name)->where('provider_types_id',$provider_types_id)->where('id','<>',$pro_id)->count();
				if($count > 0) {
					return false;
				} else {
					 return true;            
				}	 
			});
			
			$input_old = Provider::where('id',$id)->pluck('short_name')->first();
			if($input_old == $request['short_name'])
				$input_old =0;
			else 	
				$input_old = 1;
			$input_old_type_id = Provider::where('id',$id)->pluck('provider_types_id')->first();
			if($input_old_type_id == $request['provider_types_id'])
				$input_old_type_id = 0;
			else 	
				$input_old_type_id = 1;
			//Short name unique validation check, maxmum, minmum 3 letters. provider type is required. image size defult set
			$rules = Provider::$rules+array('provider_types_id' => 'required|provider_type_validator:last_name,first_name,organization_name,enumeration_type,'.$id.',npi,short_name',)+array('short_name'=> 'required|max:3|min:3|short_name_provider_type:'.$request["short_name"].','.$request["provider_types_id"].','.$id.','.$input_old.','.$input_old_type_id)+array('image'=>Config::get('siteconfigs.customer_image.defult_image_size'));
			//Rules, message are model access on here.	
			if( ((is_null($request['email'])) == true) ) {
				unset($rules['email']);
			}
			if( ((is_null($request['website'])) == true) ) {
				unset($rules['website']);
			}
			if( ((is_null($request['speciality_id'])) == true) ) {
				unset($rules['speciality_id']);
			}
			if( ((is_null($request['speciality_id2'])) == true) ) {
				unset($rules['speciality_id2']);
			}
			// Rule - different only applicable if speaciality_id and speciality_id2 is not empty.
			if(is_null($request['speciality_id']) || is_null($request['speciality_id2'])) {
				unset($rules['speciality_id']);
				unset($rules['speciality_id2']);
			}
		

			$validator = Validator::make($request, $rules, Provider::$messages+array('image.mimes'=>Config::get('siteconfigs.customer_image.defult_image_message'))+array('short_name_provider_type' => 'Short name already exists'));
			//if any error comes to fails messages show on edit page
			//Quaery DATA
			if($validator->fails()) {
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			} else {						
				//No issue its comes here find the provider 
				$provider 	= Provider::findOrFail($id);
				
				/* Remove claim error message */
				if($provider->provider_types_id == Config::get('siteconfigs.providertype.Billing'))
					ClaimInfoV1::ClearingClaimErrors($id,'Billing');
				elseif($provider->provider_types_id == Config::get('siteconfigs.providertype.Rendering'))
					ClaimInfoV1::ClearingClaimErrors($id,'Rendering');
				/* Remove claim error message */
				
				$data 		= array();
				//Input type image uploaded time its on the file
				if(Input::hasFile('image')) {
					//flie type
					$image 			= Input::file('image');
					//File name
					$filename  		= rand(11111,99999);
					//if already added image
					$old_filename  	= $provider->avatar_name;
					// image extration 
					$old_extension  = $provider->avatar_ext;
					$extension 		= $image->getClientOriginalExtension();
					$filestoreName 	= $filename .'.'.$extension;
					$filestoreoldName = $old_filename .'.'.$old_extension;
					//file size	
					$resize 		= array('150','150');
					//common file type goto helper file
					Helpers::mediauploadpath('','provider',$image,$resize,$filestoreName,$filestoreoldName);  
					//image name
					$provider->avatar_name 	= $filename;
					// image extration type
					$provider->avatar_ext 	= $extension;
					$data['avatar_name'] 	= $filename;
					$data['avatar_ext'] 	= $extension;
				}
				//if digital signature
				if(Input::hasFile('digital_sign')) {
					$image 				= Input::file('digital_sign');
					$signfilename  		= rand(11111,99999);
					$old_signfilename  	= $provider->digital_sign_name;
					$old_signextension  = $provider->digital_sign_ext;
					$signextension = $image->getClientOriginalExtension();
					$signfilestoreName 		= $signfilename .'.'.$signextension;
					$signfilestoreoldName 	= $old_signfilename .'.'.$old_signextension;
					$resize 				= array('150','150');
					Helpers::mediauploadpath('','provider',$image,$resize,$signfilestoreName,$signfilestoreoldName);  
					$provider->digital_sign_name 	= $signfilename;
					$provider->digital_sign_ext 	= $signextension;
					$data['digital_sign_name'] 		= $signfilename;
					$data['digital_sign_ext'] 		= $signextension;
				}
				//IF provider given the DOB convert yyyy/mm/dd formst change
				if(isset($request['provider_dob']) && $request['provider_dob'] != '') 
					$request['provider_dob'] = date('Y-m-d',strtotime($request['provider_dob']));

				/// Starts - Pay to address flag update ///
				$address_flag 				= array();
				$address_flag['type'] 		= 'provider';
				$address_flag['type_id'] 	= $provider->id;
				$address_flag['type_category'] = 'general_information';
				$address_flag['address2'] 	= $request['general_address1'];
				$address_flag['city'] 		= $request['general_city'];
				$address_flag['state'] 		= $request['general_state'];
				$address_flag['zip5'] 		= $request['general_zip5'];
				$address_flag['zip4'] 		= $request['general_zip4'];
				$address_flag['is_address_match'] 	= $request['general_is_address_match'];
				$address_flag['error_message'] 		= $request['general_error_message'];
				AddressFlag::checkAndInsertAddressFlag($address_flag);
				/* Ends - Pay to address */

				/* Starts - NPI flag update */
				$request['company_name'] 	= 'npi';
				$request['type'] 			= 'provider';
				$request['type_id'] 		= $provider->id;
				$request['type_category'] 	= 'Individual';
				NpiFlag::checkAndInsertNpiFlag($request);
				/* Ends - NPI flag update */
				//Provider NPI type 2	
				if($request['enumeration_type'] == 'NPI-2') {
					$request['provider_name'] = $request['organization_name'];
					$request['first_name'] = '';
					$request['last_name'] = '';
				} else { 
					//NPI 1 last name, first name need
					$request['provider_name'] = $request['last_name'].', '.$request['first_name'].' '.$request['middle_name'];
					$request['organization_name'] = '';
				}
				//update the changes on provider
				$provider->update($request);
				$user = Auth::user ()->id;
				$provider->updated_by = $user;
				$provider->save ();	
				$data_key= array('short_name','provider_name','first_name','last_name','middle_name','organization_name','description','provider_dob','gender','ssn','provider_degrees_id','address_1','address_2','city','state','zipcode5','zipcode4','phone','phoneext','fax','email','website','etin_type','etin_type_number','speciality_id','taxanomy_id','speciality_id2','taxanomy_id2','statelicense','medicareptan','medicaidid','bcbsid','aetnaid');
				
				foreach($data_key as $keys) {
					if(isset($request[$keys]))
						$data_arr[$keys] = $request[$keys];
				}
				$provider_list = Provider::where('id','<>',$id)->where('short_name',$request['short_name'])->update($data_arr);
				
				if(config('siteconfigs.is_enable_provider_add')) { // Just for temporary tofix database issue
					// Update admin database starts
					$admin_db_name 	= 'responsive';//getenv('DB_DATABASE');
					$dbconnection 	= new DBConnectionController();
					$dbconnection->updatepracticedbprovider($request,$provider->practice_db_provider_id,$admin_db_name,$data);
					// Update admin database ends 
				} 
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.provider_update_msg"),'data'=>''));					
			}
		} else {
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}

	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');  
		if(Provider::where('id', $id )->count()>0 && is_numeric($id))
		{
			$pro_scheduler_cnt = ProviderScheduler::where('provider_id',$id)->count();
			if($pro_scheduler_cnt>0){
				return Response::json(array('status'=>'relation_error', 'message'=>Lang::get("practice/practicemaster/provider.validation.provider_sch_del_err")));	
			} else {
				$pro_charges_cnt = ClaimInfoV1::whereRaw('(rendering_provider_id = ? or refering_provider_id = ? or billing_provider_id = ?)', array($id,$id,$id))->count();
				if($pro_charges_cnt>0){
					return Response::json(array('status'=>'relation_error', 'message'=>Lang::get("practice/practicemaster/provider.validation.provider_chg_del_err")));	
				} else {
					$provider_res = Provider::find($id);
					$practice_db_provider_id = $provider_res['practice_db_provider_id'];
					$provider_res->delete();
					$admin_db_name = 'responsive';//getenv('DB_DATABASE');
					Provider::on($admin_db_name)->where('practice_db_provider_id', $practice_db_provider_id)->delete();	
					return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.provider_delete_msg"),'data'=>''));
				}
			}
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}
	}

	public function getShowApi($id)
	{
        $id = Helpers::getEncodeAndDecodeOfId($id,'decode');
        if(Provider::where('id', $id )->count()>0 && is_numeric($id))
        {
			$provider = Provider::with('provider_type_details','degrees','speciality','speciality2','taxanomy','taxanomy2','facility_details')->where('id', $id )->first();
			/// Get address for usps ///
			$general_address_flag = AddressFlag::getAddressFlag('provider',$provider->id,'general_information');
			$addressFlag['general'] = $general_address_flag;
			$document = Document::where('document_type','provider')->where('type_id',$id);
			$documents_personal_ssn = $document->where('category','personal_ssn')->first();
			$documents_PTAN = Document::where('document_type','provider')->where('type_id',$id)->where('category','medicare_ptan')->first();
			$documents_medicaid_id = Document::where('document_type','provider')->where('type_id',$id)->where('category','medicaid_id')->first();
			$documents_bcbs_id = Document::where('document_type','provider')->where('type_id',$id)->where('category','bcbs_id')->first();
			$documents_aetna_id = Document::where('document_type','provider')->where('type_id',$id)->where('category','aetna_id')->first();
			$documents_uhc_id = Document::where('document_type','provider')->where('type_id',$id)->where('category','uhc_id')->first();
			$documents_other_id1 = Document::where('document_type','provider')->where('type_id',$id)->where('category','other_id1')->first();
			$documents_other_id2 = Document::where('document_type','provider')->where('type_id',$id)->where('category','other_id2')->first();
			$documents_other_id3 = Document::where('document_type','provider')->where('type_id',$id)->where('category','other_id3')->first();
			$documents_tax_id = Document::where('document_type','provider')->where('type_id',$id)->where('category','tax_id')->first();
			$documents_state_license1 = Document::where('document_type','provider')->where('type_id',$id)->where('category','state_license1')->first();
			$documents_state_license2 = Document::where('document_type','provider')->where('type_id',$id)->where('category','state_license2')->first();
			$documents_state_license3 = Document::where('document_type','provider')->where('type_id',$id)->where('category','state_license3')->first();
			$documents_dea_number = Document::where('document_type','provider')->where('type_id',$id)->where('category','dea_number')->first();
			$documents_mammography_cert = Document::where('document_type','provider')->where('type_id',$id)->where('category','mammography_cert')->first();
			$documents_care_plan_oversight = Document::where('document_type','provider')->where('type_id',$id)->where('category','care_plan')->first();
			// Added missing query 
			// Revision 1 - Ref: MED-2654  06 Augest 2019: Pugazh			
			$documents_npi_id = Document::where('document_type','provider')->where('type_id',$id)->where('category','npi')->first();

			/// Get NPI details ///
			$npi_flag = NpiFlag::getNpiFlag('provider',$provider->id,'Individual');

			if(!$npi_flag)
			{
				$npiflag_columns = Schema::getColumnListing('npiflag');
				foreach($npiflag_columns as $columns)
				{
					$npi_flag[$columns] = '';
				}			
			}
			//Encode ID for provider
			$temp = new Collection($provider);
			$temp_id = $temp['id'];
			$temp->pull('id');
			$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
			$temp->prepend($temp_encode_id, 'id');
			$data = $temp->all();
			$provider = json_decode(json_encode($data), FALSE);
			//Encode ID for provider
            return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('provider','addressFlag','npi_flag','documents_personal_ssn','documents_PTAN','documents_medicaid_id','documents_bcbs_id','documents_aetna_id','documents_uhc_id','documents_other_id1','documents_other_id2','documents_other_id3','documents_tax_id','documents_state_license1','documents_state_license2','documents_state_license3','documents_dea_number','documents_mammography_cert','documents_care_plan_oversight','documents_npi_id')));
        }
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}
	}
	
	public function api_get_sel_provider_type_display($sel_provider_id) 
	{
		if(count(explode(';',$sel_provider_id))>0)
		{
			$get_Providerid = explode(';',$sel_provider_id);
			$sel_provider_id = $get_Providerid[0]; 
		}
		
		if(!is_numeric ($sel_provider_id))
			$sel_provider_id = Helpers::getEncodeAndDecodeOfId($sel_provider_id,'decode'); 
		
		$sel_provider_id_arr 	= explode(";",$sel_provider_id);
		$address_part_arr 		= Provider::with('provider_types')->where('id',$sel_provider_id_arr[0])->get()->toArray();
		$provider_type_name     = $address_part_arr[0]['provider_types']['name'];
		return $provider_type_name;
    }
	
	/*** Delete Avatar in Provider table start ***/
	public function avatarapipicture($id,$p_name)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');  
		$delete_avr = Provider::where('id',$id)->first();
		$delete_avr->avatar_name = "";
		$delete_avr->avatar_ext = "";
		$delete_avr->save();
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
	}
	/*** Delete Avatar in Provider table end ***/
	
	
	public function getStoreTrailApi($request='')
	{
		//request type not empty
        if($request == '') {
			$request = Request::all();
		}
		//echo "<pre>";print_r($request);die;
		$is_valid_npi = Helpers::checknpi_valid_process($request['npi'],'NPI-1'); // check npi valid or not back end validation
		//if select provider DOB 
		if($request['provider_dob'] != '') 
			$request['provider_dob'] = date('Y-m-d',strtotime($request['provider_dob']));
		//Validator check 	
		// website checking
		if(!empty($request['website'])){
			if(substr($request['website'],0,7) != 'http://' && substr($request['website'],0,8) != 'https://')	
				$request['website'] = "http://".$request['website'];
		}
		
        Validator::extend('provider_type_validator', function($attribute, $value, $parameters,$short_name) {			
            if(Input::get($parameters[3]) == 'NPI-2') {
				//NPI 2
                $organization_name = Input::get($parameters[2]);
				$npi			   = Input::get($parameters[4]);
				$short_name		   = $parameters[5];
                if($value > 0) {
					$count = Provider::where('organization_name',$organization_name)->where('short_name',$short_name)->where('provider_types_id',$value)->where('npi',$npi)->count();
                    if($count != 0)
						return false;
                }                     
            } else  {//NPI 1
			    $last_name 	= Input::get($parameters[0]);
                $first_name = Input::get($parameters[1]);
				$npi		= Input::get($parameters[4]);
				$short_name	= $parameters[5];
				if($value > 0) {
                    $count = Provider::where('last_name',$last_name)->where('first_name',$first_name)->where('provider_types_id',$value)->where('short_name',$short_name)->where('npi',$npi)->count();
                    if($count != 0)
                        return false;
                }                
            }                               
            return true;                
        });
            
        Validator::extend('additional_provider_type_validator', function($attribute, $value, $parameters) {
        	if(Input::get($parameters[3]) == 'NPI-2') {
                $organization_name = Input::get($parameters[2]);
				$npi			   = Input::get($parameters[4]);
                if(count($value) > 0) {
					$count = Provider::where('organization_name',$organization_name)->where('npi',$npi)->whereIn('provider_types_id',$value)->count();
					if($count != 0)
						return false;
				}                   
            } else {
				$last_name 	= Input::get($parameters[0]);
                $first_name = Input::get($parameters[1]);
				$npi		= Input::get($parameters[4]);
                if(count($value) > 0) {
					$count = Provider::where('last_name',$last_name)->where('first_name',$first_name)->where('npi',$npi)->whereIn('provider_types_id',$value)->count();
                    if($count != 0)
						return false;
                }
            } 
			return true;                   
        });

		Validator::extend('check_npi_api_validator', function($attribute) use($is_valid_npi)
		{
			if($is_valid_npi == 'No')
				return false;                        
			else 
				return true;                   
		});
		
		Validator::extend('short_name_provider_type', function($attribute, $value, $parameters){
			$short_name = Input::get($parameters[0]);
			$provider_types_id = Input::get($parameters[1]);
			
			$count = Provider::where('short_name',$short_name)->where('provider_types_id',$provider_types_id)->count();
			$count_sql = Provider::where('short_name',$short_name)->get();
			if($count > 0) {
				return false;
			}	
			else
                return true;            
		});
			
        if(@$request['def_provider_added']!='yes')	{
			$rules = Provider::$rules+array('short_name'=> 'short_name_provider_type:short_name,provider_types_id')+array('provider_types_id' => "required|provider_type_validator:last_name,first_name,organization_name,enumeration_type,npi,".$request['short_name'],'additional_provider_type' => "additional_provider_type_validator:last_name,first_name,organization_name,enumeration_type,npi",'npi' => 'digits:10|check_npi_api_validator')+array('image'=>Config::get('siteconfigs.customer_image.defult_image_size'));
		} else {
			//Short name unique validation check, maxmum, minmum 3 letters. provider type is required. image size defult set
			$rules = array('short_name'=> 'short_name_provider_type:short_name,provider_types_id','npi'=>'check_npi_api_validator');
		}

		// Rule - different only applicable if speaciality_id and speciality_id2 is not empty.
		if(is_null($request['speciality_id']) || is_null($request['speciality_id2'])) {
			unset($rules['speciality_id']);
			unset($rules['speciality_id2']);
		}
		
		$validator = Validator::make($request, $rules, Provider::$messages+array('npi.check_npi_api_validator' => Lang::get("common.validation.npi_validcheck"))+array('short_name_provider_type' => 'Short name already exists')+array('image.mimes'=>Config::get('siteconfigs.customer_image.defult_image_message')));
		if($validator->fails()) {
            $errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
        } else {
			$providerTypeArr = [1,5];
			foreach($providerTypeArr as $list){
				$request['provider_types_id'] = $list;
				$request['created_by'] = Auth::user()->id;
				if($request['enumeration_type'] == 'NPI-2') {
					$request['provider_name'] 	= $request['organization_name'];
					$request['first_name'] 		= '';
					$request['last_name'] 		= '';
				} else {
					$request['provider_name'] = $request['last_name'].', '.$request['first_name'].' '.$request['middle_name'];
					$request['organization_name'] = '';
				}
				//Admin db also store the data
				$admin_db_name = 'responsive';//getenv('DB_DATABASE');
				$dbconnection = new DBConnectionController();
				if(Session::has('practice_dbid')) {
					$request['practice_id'] = Session::get('practice_dbid'); 
					$customer = DB::connection($admin_db_name)->table('practices')->where('id', $request['practice_id'])->value('customer_id');
					$request['customer_id'] = $customer;
				}
				//Create the provider		
				$provider_id = $this->createProvider($request); 			
				//document api store start
				if(isset($request['temp_doc_id'])) {
					if($request['temp_doc_id']!="") Document::where('temp_type_id', '=', $request['temp_doc_id'])->update(['type_id' => $provider_id,'temp_type_id' => '']);
				}
				//document api store end
				$practice_db_provider_id = $provider_id;
					
				if(config('siteconfigs.is_enable_provider_add')) { 
					$provider_data = Provider::where('id',$provider_id)->select('id', 'avatar_name','avatar_ext','digital_sign_name','digital_sign_ext')->first();
					$data = array();
					$data['avatar_name'] = $provider_data['avatar_name'];
					$data['avatar_ext'] = $provider_data['avatar_ext'];
					$data['digital_sign_name'] = $provider_data['avatar_name'];
					$data['digital_sign_ext'] = $provider_data['digital_sign_ext'];
					$admin_provider_id = $dbconnection->createProviderinOtherDB($request,$admin_db_name,$data);
					$practice_db_provider_id = $admin_provider_id;
					Provider::on($admin_db_name)->where('id', $admin_provider_id)->update(['practice_db_provider_id'=>$practice_db_provider_id]);	
				}
				
				Provider::where('id', $provider_id)->update(['practice_db_provider_id'=>$practice_db_provider_id]);
				//If addtion provider type check in form blade		
				if(count((array)@$request['additional_provider_type']) > 0) {
					foreach($request['additional_provider_type'] as $provider_type) {
						$request['provider_types_id'] = $provider_type;
						$sub_provider_id = $this->createProvider($request); 
						$practice_db_sub_provider_id = $sub_provider_id;
						if(config('siteconfigs.is_enable_provider_add')) {
							$admin_sub_provider_id = $dbconnection->createProviderinOtherDB($request,$admin_db_name);
							$practice_db_sub_provider_id = $admin_sub_provider_id;
							Provider::on($admin_db_name)->where('id', $admin_sub_provider_id)->update(['practice_db_provider_id'=>$practice_db_sub_provider_id]);	
						}
						Provider::where('id', $sub_provider_id)->update(['practice_db_provider_id'=>$practice_db_sub_provider_id]);
					}
				}
			}
			//Encode the provider id
			$provider_id = Helpers::getEncodeAndDecodeOfId($provider_id,'encode');  
            return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.provider_create_msg"),'data'=>$provider_id));
		}
	}
	
	
	
	function __destruct() 
	{
    }
	
}
