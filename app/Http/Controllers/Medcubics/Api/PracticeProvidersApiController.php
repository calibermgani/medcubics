<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Models\Medcubics\Customer as Customer;
use App\Models\Medcubics\Provider as Provider;
use App\Models\Medcubics\Practice as Practice;
use App\Models\Medcubics\Facility as Facility;
use App\Models\Medcubics\Provider_degree as ProviderDegree;
use App\Models\Medcubics\Provider_type as ProviderType;
use App\Models\Medcubics\Speciality as Speciality;
use App\Models\Medcubics\Taxanomy as Taxanomy;
use App\Models\Medcubics\AddressFlag as AddressFlag;
use App\Models\Medcubics\NpiFlag as NpiFlag;
use App\Models\Medcubics\State as State;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Medcubics\Insurance as Insurance;
use Intervention\Image\ImageManagerStatic as Image;
use App\Http\Helpers\Helpers as Helpers;
use Auth;
use Hash;
use Response;
use Request;
use Config;
use Validator;
use Input;
use Schema;
use App;
use DB;
use File;
use Lang;
use Log;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Models\Medcubics\ApiConfig as ApiConfig;
use App\Models\ProviderScheduler as ProviderScheduler;

class PracticeProvidersApiController extends Controller 
{
	
	/********************** Start Display a listing of the providers ***********************************/
	public function getIndexApi($practice_id,$export='')
	{
        $practice_id = Helpers::getEncodeAndDecodeOfId($practice_id,'decode');
		if(Practice::where('id', $practice_id )->count())
		{
			$practice 		= Practice::with('taxanomy_details', 'speciality_details','languages_details')->where('id', $practice_id)->first();		
			$customer_id 	= $practice->customer_id;
			$customer 		= Customer::where('id',$practice->customer_id)->first();
            $providers 		= Provider::with('provider_type_details','degrees','taxanomy','facility_details','speciality','provider_types')->where('customer_id', $customer_id)->where('practice_id', $practice_id)->get();
                
            if($export != "")
            {
                $exportparam = array(
								'filename' 	=> 'provider',
								'heading' 	=> 'Provider',
								'fields' 	=> array(
												'provider_name' => 'Provider Name',
												'short_name' => 'Short Name',
												'provider_types_id' => array('table'=>'provider_types' ,'column' => 'name' ,'label' => 'Type'),
												'ssn' => 'SSN',
												'etin_type_number' => 'Tax ID',
												'npi' => 'NPI',
												'gender' =>'Gender',
												'speciality' => array('table'=>'speciality' ,'column' => 'speciality' ,'label' => 'Speciality'),
												'status' => 'Status',
												)
									);
                 $callexport = new CommonExportApiController();
                 return $callexport->generatemultipleExports($exportparam, $providers, $export);
            }
			$customer_id = Helpers::getEncodeAndDecodeOfId($customer_id,'encode');			
            return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('customer_id','customer','providers','practice')));
		}
        else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>null));
		} 
    }
	/********************** End Display a listing of the providers ***********************************/
	
	/********************** Start Display the provider create page ***********************************/
	public function getCreateApi($practice_id)
	{
            $practice_id = Helpers::getEncodeAndDecodeOfId($practice_id,'decode');
		if(Practice::where('id', $practice_id )->count())
		{
			$providers 		= Provider::where('status','Active')->get()->all();					
			$taxanomies 	= '';
			$states 		= State::orderBy('code','ASC')->pluck( 'code', 'code' )->all();		
			$insurances 	= Insurance::orderBy('insurance_name','ASC')->pluck( 'insurance_name', 'id' )->all();
			//$facilities 	= Facility::orderBy('facility_name', 'asc')->pluck( 'facility_name', 'id' )->all();
			$specialities 	= Speciality::orderBy('speciality', 'asc')->pluck( 'speciality', 'id' )->all();
			$provider_type 	= ProviderType::orderBy('name', 'asc')->pluck( 'name', 'id' )->all();
			$provider_degree = ProviderDegree::orderBy('degree_name', 'asc')->pluck( 'degree_name', 'id' )->all();		

			/// Get address for usps ///
			$addressFlag['general']['address1'] = '';
			$addressFlag['general']['city'] 	= '';
			$addressFlag['general']['state'] 	= '';
			$addressFlag['general']['zip5'] 	= '';
			$addressFlag['general']['zip4']		= '';
			$addressFlag['general']['is_address_match'] = '';
			$addressFlag['general']['error_message'] 	= '';

			/// Get NPI details ///
			$npiflag_columns = Schema::getColumnListing('npiflag');
			foreach($npiflag_columns as $columns)
			{
				$npi_flag[$columns] = '';
			}
			
			$practice_name      =   Practice::where('id', $practice_id )->pluck('practice_name')->first();
			$dbconnection 		= new DBConnectionController();
			$practice_db_name 	= $dbconnection->getpracticedbname($practice_name);
			$dbconnection->configureConnectionByName($practice_db_name);
			$facilities 		= 	Facility::on($practice_db_name)->orderBy('facility_name', 'asc')->pluck( 'facility_name', 'id' )->all();
			
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('providers','specialities', 'taxanomies', 'facilities', 'provider_degree', 'provider_type','addressFlag','npi_flag','states','insurances')));
		}
        else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>null));
		}     
	}
	/********************** End Display the provider create page ***********************************/
	
	/********************** Start provider added process ***********************************/
	public function getStoreApi($request='')
	{ 
		if($request == '') 
		$request = Request::all();
		
		/*$npi_api = ApiConfig::where('api_for','npi')->where('api_status','Active')->first();
		if($npi_api)
		{
			$npi 	= $request['npi'];
			$url 	= $npi_api->url.$npi.'&skip=&pretty=on';
			
			$curl 	= curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $url,
			CURLOPT_SSL_VERIFYPEER => false
			));
			$resp 	= curl_exec($curl);
			if(!curl_exec($curl))
			die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
			curl_close($curl);
			$result_array = json_decode($resp);
			
			if(isset($result_array->Errors))
				$is_valid_npi = 'No';
			elseif(!$result_array->result_count == 1) 
				$is_valid_npi = 'No';
			else
				$is_valid_npi = 'Yes';
		}*/
		$is_valid_npi = Helpers::checknpi_valid_process($request['npi']); // check npi valid or not back end validation
		
		if(!isset($request['def_provider_added']))
		{
			$request['def_provider_added'] = 'no';
		}
		if(@$request['provider_dob']!='')              
			$request['provider_dob'] = date('Y-m-d',strtotime($request['provider_dob']));
		$request['practice_id']	= Helpers::getEncodeAndDecodeOfId($request['practice_id'],'decode');
		$request['customer_id'] = Helpers::getEncodeAndDecodeOfId($request['customer_id'],'decode');  
			
		Validator::extend('provider_type_validator', function($attribute, $value, $parameters)
		{	/* @ allowed same NPI with same type 
			if(Input::get($parameters[3]) == 'NPI-2')
			{
				$organization_name = Input::get($parameters[2]);
				$npi		   = Input::get($parameters[4]);
				$practice_id 	   = $parameters[5];
				if($value > 0)
				{
					$count = Provider::where('organization_name',$organization_name)->where('provider_types_id',$value)->where('npi',$npi)->where('practice_id',$practice_id)->count();
					if($count != 0)
						return false;
				}                     
			}
			else 
			{
				$last_name 	= Input::get($parameters[0]);
				$first_name = Input::get($parameters[1]);
				$npi		= Input::get($parameters[4]);
				$practice_id = $parameters[5];
				if($value > 0)
				{
					$count = Provider::where('last_name',$last_name)->where('first_name',$first_name)->where('provider_types_id',$value)->where('npi',$npi)->where('practice_id',$practice_id)->count();
					if($count != 0)
						return false;
				}                
			}*/
			return true;                
		});

		Validator::extend('additional_provider_type_validator', function($attribute, $value, $parameters)
		{
			if(Input::get($parameters[3]) == 'NPI-2')
			{
				$organization_name = Input::get($parameters[2]);
				$npi			   = Input::get($parameters[4]);
				$practice_id 	   = $parameters[5];
				if(count($value) > 0)
				{
					$count = Provider::where('organization_name',$organization_name)->where('npi',$npi)->whereIn('provider_types_id',$value)->where('practice_id',$practice_id)->count();
					if($count != 0)
						return false;
				}                        
			}
			else 
			{
				$last_name 	= Input::get($parameters[0]);
				$first_name = Input::get($parameters[1]);
				$npi		= Input::get($parameters[4]);
				$practice_id = $parameters[5];
				if(count($value) > 0)
				{
					$count = Provider::where('last_name',$last_name)->where('first_name',$first_name)->where('npi',$npi)->whereIn('provider_types_id',$value)->where('practice_id',$practice_id)->count();
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
		### Short name unique validation should be considered ###
		Validator::extend('short_name_provider_type', function($attribute, $value, $parameters){
			$short_name = Input::get($parameters[0]);
			$provider_types_id = Input::get($parameters[1]);
			$provider_practice_id = $parameters[2];		  	
			$count = Provider::where('short_name',$short_name)->where('provider_types_id',$provider_types_id)->where('practice_id',$provider_practice_id)->count();

			$count_sql = Provider::where('short_name',$short_name)->get();
			if($count > 0) {
				return false;
			}	
			else{
                return true;            
			}
		});

		if(@$request['def_provider_added']!='yes')
		{
			$rules = Provider::$rules+array('short_name'=> 'short_name_provider_type:short_name,provider_types_id,'.$request['practice_id'],'provider_types_id' => 'required|provider_type_validator:last_name,first_name,organization_name,enumeration_type,npi,'.$request['practice_id'].','.$request['short_name'],'additional_provider_type' => 'additional_provider_type_validator:last_name,first_name,organization_name,enumeration_type,npi,'.$request['practice_id'],'npi' => 'digits:10|check_npi_api_validator');
			
		}
		else
		{
			$rules = array('short_name'=> 'short_name_provider_type:short_name,provider_types_id','npi' => 'check_npi_api_validator');
		}
		
		$validator = Validator::make($request, $rules+array('image'=>Config::get('siteconfigs.customer_image.defult_image_size')), Provider::$messages+array('npi.check_npi_api_validator' => 'Enter valid npi number!')+array('short_name_provider_type' => 'Short name already exists')+array('image.mimes'=>Config::get('siteconfigs.customer_image.defult_image_message')));
		//dd($validator->fails());	
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{
			$request['created_by'] = Auth::user()->id;								
			if($request['enumeration_type'] == 'NPI-2')
			{
				$request['provider_name'] 	= $request['organization_name'];
				$request['first_name'] 		= '';
				$request['last_name'] 		= '';
			}
			else
			{
				$request['provider_name'] = $request['last_name'].', '.$request['first_name'].' '.$request['middle_name'];
				$request['organization_name'] = '';
			}
			//$request['practice_id'] = Helpers::getEncodeAndDecodeOfId($request['practice_id'],'decode');
			$practice_info 	= Practice::where('id', $request['practice_id'])->first();
			
			$provider_id 	= $this->createProvider($request, $practice_info->id); 
			$dbconnection 	= new DBConnectionController();
			//update provider id for practice db field
			$dbconnection->updatepracticedbproviderid($provider_id);
			//create provider in practice provider table
			$from_admin_new = isset($request['from_admin_new']) ? $request['from_admin_new'] : 'no';
			if( config('siteconfigs.is_enable_provider_add') && $from_admin_new=='no')
			{
				$practice_db_name 		= $dbconnection->getpracticedbname($practice_info->practice_name);	
				$request['practice_db_provider_id'] = $provider_id;
				$provider_data 			= Provider::where('id',$provider_id)->select('id', 'avatar_name','avatar_ext','digital_sign_name','digital_sign_ext')->first();
				$data['avatar_name'] 	= $provider_data['avatar_name'];
				$data['avatar_ext'] 	= $provider_data['avatar_ext'];
				$data['digital_sign_name'] 	= $provider_data['avatar_name'];
				$data['digital_sign_ext'] 	= $provider_data['digital_sign_ext'];
				$dbconnection->createProviderinOtherDB($request,$practice_db_name,$data);
			}
			unset($request['practice_db_provider_id']);
			if(isset($request['additional_provider_type']) && count($request['additional_provider_type']) > 0)
			{
				foreach($request['additional_provider_type'] as $provider_type)
				{
					$request['provider_types_id'] 	= $provider_type;
					$add_provider_id 				= $this->createProvider($request, $practice_info->id); 
					if( config('siteconfigs.is_enable_provider_add'))
					{
						//update provider id for practice db field
						$dbconnection->updatepracticedbproviderid($add_provider_id);
						$request['practice_db_provider_id'] = $add_provider_id;
						$dbconnection->createProviderinOtherDB($request,$practice_db_name);
						unset($request['practice_db_provider_id']);
					}
				}			
			}		
			$provider_id = Helpers::getEncodeAndDecodeOfId($provider_id,'encode');
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$provider_id));
		}
	}
	/********************** End provider added process ***********************************/
	
	/********************** Start provider edit page display ***********************************/
	public function getEditApi($cust_id,$practice_id,$id)
	{	
		$cust_id	= 	Helpers::getEncodeAndDecodeOfId($cust_id,'decode');
		$id 		= 	Helpers::getEncodeAndDecodeOfId($id,'decode');
		$practice_id=	Helpers::getEncodeAndDecodeOfId($practice_id,'decode');
		if(Customer::where('id', $cust_id )->count())
		{
            if(Practice::where('id', $practice_id )->count())
			{		
				if((isset($id) && is_numeric($id))  && (isset($id) && is_numeric($id)) && Provider::where('id', $id)->count())
				{
					$provider 			= 	Provider::findOrFail($id);
					if($provider['provider_dob'] != '0000-00-00' )
						$provider['provider_dob']  =       date('m/d/Y',strtotime($provider['provider_dob']));
					else
						$provider['provider_dob']  =     '';
                             
					$specialities_id 	=   $provider->speciality_id;
                    $specialities_id2   =       $provider->speciality_id2;
					//$facilities 		= 	Facility::orderBy('facility_name', 'asc')->pluck( 'facility_name', 'id' )->all();
					$specialities 		= 	Speciality::orderBy('speciality', 'asc')->pluck( 'speciality', 'id' )->all();
					$provider_degree 	= 	ProviderDegree::orderBy('degree_name', 'asc')->pluck( 'degree_name', 'id' )->all();
					$provider_type 		= 	ProviderType::orderBy('name', 'asc')->pluck( 'name', 'id' )->all();
					$practice_name      =   Practice::where('id', $practice_id )->select('practice_name','id')->first(); // To display provider tab image from practice media folder
					
					$taxanomies 		= Taxanomy::where('speciality_id',$specialities_id)->pluck('code','id')->all();
                    $taxanomies2 		= Taxanomy::where('speciality_id',$specialities_id2)->pluck('code','id')->all();
                    $taxanomy_id		= $provider->taxanomy_id;
					$general_address_flag 	= AddressFlag::getAddressFlag('provider',$provider->id,'general_information'); // Get address for usps
					$addressFlag['general'] = $general_address_flag;
                    $insurances 			= Insurance::orderBy('insurance_name','ASC')->pluck( 'insurance_name', 'id' )->all();		
                    $states 				= State::orderBy('code','ASC')->pluck( 'code', 'code' )->all();
                    $npi_flag = NpiFlag::getNpiFlag('provider',$provider->id,'Individual'); // Get NPI details
			
					if(!$npi_flag)
					{
						$npiflag_columns = Schema::getColumnListing('npiflag');
						foreach($npiflag_columns as $columns)
						{
							$npi_flag[$columns] = '';
						}			
					}
					$practice_name->id = Helpers::getEncodeAndDecodeOfId($practice_name->id,'encode');
					
					$dbconnection 		= new DBConnectionController();
					$practice_db_name 	= $dbconnection->getpracticedbname($practice_name->practice_name);
					$dbconnection->configureConnectionByName($practice_db_name);
					$facilities 		= 	Facility::on($practice_db_name)->orderBy('facility_name', 'asc')->pluck('facility_name', 'id' )->all();
					
					return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('provider','specialities', 'taxanomies','taxanomies2','facilities', 'provider_degree', 'provider_type','taxanomy_id','addressFlag','npi_flag','insurances','states','practice_name')));
				}
				else
				{
					return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
				}
			}
			else
			{
				return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>'null'));
			}
        }
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}
	}
	/********************** End provider edit page display ***********************************/

	/********************** Start provider update process ***********************************/
	public function getUpdateApi($id)
	{
            $id = Helpers::getEncodeAndDecodeOfId($id,'decode');    
             $request = Request::all();
             $request['customer_id'] = Helpers::getEncodeAndDecodeOfId($request['customer_id'],'decode');  
             $request['practice_id'] = Helpers::getEncodeAndDecodeOfId($request['practice_id'],'decode');  
			
		Validator::extend('provider_type_validator', function($attribute, $value, $parameters)
		{ 
			/* allowed same NPI with same type
			if(Input::get($parameters[3]) == 'NPI-2')
			{
				$organization_name 	= Input::get($parameters[2]);
				$id 				= $parameters[4];
				$npi 				= Input::get($parameters[5]);
				$practice_id 		= $parameters[6];
				if($value > 0)
				{
					$count = Provider::where('organization_name',$organization_name)->where('id','!=',$id)->where('provider_types_id',$value)->where('npi',$npi)->where('practice_id',$practice_id)->count();
					if($count != 0)
						return false;
				}                    
			}
			else 
			{
				$last_name 	= Input::get($parameters[0]);
				$first_name = Input::get($parameters[1]);
				$id 		= $parameters[4];
				$npi 		= Input::get($parameters[5]);
				$practice_id = $parameters[6];
				if($value > 0)
				{
					$count = Provider::where('last_name',$last_name)->where('id','!=',$id)->where('first_name',$first_name)->where('provider_types_id',$value)->where('npi',$npi)->where('practice_id',$practice_id)->count();
					if($count != 0)
						return false;
				}                 
			} */
			return true;                
		});
		### MR-2250 Short name unique validation should be considered ###
		Validator::extend('short_name_provider_type', function($attribute, $value, $parameters)
			{
				$short_name = $parameters[0];
				$provider_types_id = $parameters[1];
				$pro_id = $parameters[2];
				$pro_sht_not_change = isset($parameters[3])?$parameters[3]:0;
				$pro_typeId_not_change = isset($parameters[4])?$parameters[4]:0;
				$pro_practice_id = isset($parameters[5])?$parameters[5]:0;
				$count_qry = Provider::where('provider_types_id',$provider_types_id)->where('short_name',$short_name)->where('practice_id',$pro_practice_id);
				
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
				}	
				else
					 return true;            
			});
			
			$input_old = Provider::where('id',$id)->value('short_name');
			if($input_old == $request['short_name'])
				$input_old =0;
			else 	
				$input_old = 1;
			$input_old_type_id = Provider::where('id',$id)->value('provider_types_id');
			if($input_old_type_id == $request['provider_types_id'])
				$input_old_type_id = 0;
			else 	
				$input_old_type_id = 1;
		$rules = Provider::$rules+array('provider_types_id' => 'required|provider_type_validator:last_name,first_name,organization_name,enumeration_type,'.$id.',npi,short_name',)+array('short_name'=> 'required|max:3|min:3|short_name_provider_type:'.$request["short_name"].','.$request["provider_types_id"].','.$id.','.$input_old.','.$input_old_type_id.','.$request["practice_id"])+array('image'=>Config::get('siteconfigs.customer_image.defult_image_size'));
			//Rules, message are model access on here.	
			$validator = Validator::make($request, $rules, Provider::$messages+array('image.mimes'=>Config::get('siteconfigs.customer_image.defult_image_message'))+array('short_name_provider_type' => 'Short name already exists'));

		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{			
			$provider 		= Provider::findOrFail($id);
			$practice_info 	= Practice::where('id', $request['practice_id'])->first();
			$data 			= array();
			if (Input::hasFile('image'))
			{
				$image 			= Input::file('image');
				$filename  		= rand(11111,99999);
				$old_filename  	= $provider->avatar_name;
				$old_extension  = $provider->avatar_ext;
				$extension 		= $image->getClientOriginalExtension();
				$filestoreName 	= $filename .'.'.$extension;
				$filestoreoldName 	= $old_filename .'.'.$old_extension;
				$unique_practice 	= md5('P'.$practice_info->id); 
				$resize 			= array('150','150');
				Helpers::mediauploadpath($unique_practice,'provider',$image,$resize,$filestoreName,$filestoreoldName);
				$provider->avatar_name 	= $filename;
				$provider->avatar_ext 	= $extension;
				$data['avatar_name'] 	= $filename;
				$data['avatar_ext'] 	= $extension;
			}
			if (Input::hasFile('digital_sign'))
			{
				$image 				= Input::file('digital_sign');
				$signfilename  		= rand(11111,99999);
				$old_signfilename  	= $provider->digital_sign_name;
				$old_signextension  = $provider->digital_sign_ext;
				$signextension 		= $image->getClientOriginalExtension();
				$signfilestoreName 	= $signfilename .'.'.$signextension;
				$signfilestoreoldName = $old_signfilename .'.'.$old_signextension;
				$unique_practice 	= md5('P'.$practice_info->id); 
				$resize 			= array('150','150');
				Helpers::mediauploadpath($unique_practice,'provider',$image,$resize,$signfilestoreName,$signfilestoreoldName);
				$provider->digital_sign_name 	= $signfilename;
				$provider->digital_sign_ext 	= $signextension;
				$data['digital_sign_name'] 		= $signfilename;
				$data['digital_sign_ext'] 		= $signextension;
			}
			
			/// Starts - Pay to address flag update ///
			$address_flag = array();
			$address_flag['type'] 			= 'provider';
			$address_flag['type_id'] 		= $provider->id;
			$address_flag['type_category'] 	= 'general_information';
			$address_flag['address2'] 		= $request['general_address1'];
			$address_flag['city'] 			= $request['general_city'];
			$address_flag['state'] 			= $request['general_state'];
			$address_flag['zip5'] 			= $request['general_zip5'];
			$address_flag['zip4'] 			= $request['general_zip4'];
			$address_flag['is_address_match'] = $request['general_is_address_match'];
			$address_flag['error_message'] 	= $request['general_error_message'];
			AddressFlag::checkAndInsertAddressFlag($address_flag);
			/* Ends - Pay to address */

			/* Starts - NPI flag update */
			$request['company_name'] 	= 'npi';
			$request['type'] 			= 'provider';
			$request['type_id'] 		= $provider->id;
			$request['type_category'] 	= 'Individual';
			NpiFlag::checkAndInsertNpiFlag($request);
			/* Ends - NPI flag update */
			
			
			if($request['provider_dob']!='')   
				$request['provider_dob'] = date('Y-m-d',strtotime($request['provider_dob']));
		   
			if($request['enumeration_type'] == 'NPI-2')
			{
				$request['provider_name'] 	= $request['organization_name'];
				$request['first_name'] 		= '';
				$request['last_name'] 		= '';
			}                    
			else
			{
				$request['provider_name'] 		= $request['last_name'].', '.$request['first_name'].' '.$request['middle_name'];
				if($request['provider_entity_type'] != 'NonPersonEntity')
					$request['organization_name'] 	= '';
			}
			
			if($request['provider_entity_type'] == 'NonPersonEntity'){
				$request['provider_name'] 	= $request['organization_name'];
			}
			$provider->update($request);
			
			if( config('siteconfigs.is_enable_provider_add')){ 
				//update practie provider table
				$dbconnection = new DBConnectionController();
				$practice_db_name = $dbconnection->getpracticedbname($practice_info->practice_name);
				$dbconnection->updatepracticedbprovider($request,$provider->practice_db_provider_id,$practice_db_name, $data);
			}
			
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));					
		}
	}
	/********************** End provider update process ***********************************/
	
	/******************** Start default provider created when practice created time **********************/
	public function createProvider($request, $practice_name = null)
	{
		$result = Provider::create($request);
		$result->practice_db_provider_id =  $result->id;
		$result->save();
		if(Input::hasFile('image'))
		{
			$image 			= Input::file('image');
			$filename  		= rand(11111,99999);
			$extension 		= $image->getClientOriginalExtension();
			$filestoreName 	= $filename .'.'.$extension;
            $unique_practice = md5('P'.$practice_name); 
			$resize 		= array('150','150');
			Helpers::mediauploadpath($unique_practice,'provider',$image,$resize,$filestoreName);
			$result->avatar_name 	= $filename;
			$result->avatar_ext 	= $extension;
			$result->save();
		}
		if(Input::hasFile('digital_sign'))
		{
			$image 				= Input::file('digital_sign');
			$signfilename  		= rand(11111,99999);
			$signextension 		= $image->getClientOriginalExtension();
			$signfilestoreName 	= $signfilename .'.'.$signextension;
            $unique_practice 	= md5('P'.$practice_name); 
			$resize 			= array('150','150');
			Helpers::mediauploadpath($unique_practice,'provider',$image,$resize,$signfilestoreName);
            $result->digital_sign_name = $signfilename;
			$result->digital_sign_ext = $signextension;
			$result->save();
		}
		
		/// Starts - address flag update ///				
		$address_flag = array();
		$address_flag['type'] 			= 'provider';
		$address_flag['type_id'] 		= $result->id;
		$address_flag['type_category'] 	= 'general_information';
		$address_flag['address2'] 		= $request['general_address1'];
		$address_flag['city'] 			= $request['general_city'];
		$address_flag['state'] 			= $request['general_state'];
		$address_flag['zip5'] 			= $request['general_zip5'];
		$address_flag['zip4'] 			= $request['general_zip4'];
		$address_flag['is_address_match'] = $request['general_is_address_match'];
		$address_flag['error_message'] 	= $request['general_error_message'];
		AddressFlag::checkAndInsertAddressFlag($address_flag);
		/* Ends - address flag update  */

		/* Starts - NPI flag update */
		$request['company_name'] 	= 'npi';
		$request['type'] 			= 'provider';
		$request['type_id'] 		= $result->id;
		$request['type_category'] 	= 'Individual';
		NpiFlag::checkAndInsertNpiFlag($request);
		/* Ends - NPI flag update */
                return $result->id;
	}
	/******************** End default provider created when practice created time **********************/
	
	/********************** Start provider details show page ***********************************/
	public function getShowApi($cust_id,$practice_id,$id)
	{	
            $cust_id = Helpers::getEncodeAndDecodeOfId($cust_id,'decode');
            $practice_id = Helpers::getEncodeAndDecodeOfId($practice_id,'decode');
            $id = Helpers::getEncodeAndDecodeOfId($id,'decode');
            if(Customer::where('id', $cust_id )->count())
            {
                if(Practice::where('id', $practice_id )->where('status','Active')->count())
			{			
				if(Provider::where('id', $id )->count())
				{
					$provider 				= Provider::with('provider_type_details','degrees','speciality','speciality2','taxanomy','taxanomy2','facility_details')->where('id', $id )->first();				
					$general_address_flag 	= AddressFlag::getAddressFlag('provider',$provider->id,'general_information'); // Get address for usps
					$addressFlag['general'] = $general_address_flag;
					$practice_name      	=   Practice::where('id', $practice_id )->select('practice_name','id')->first(); // To display provider tab image from practice media folder 
					$npi_flag 				= NpiFlag::getNpiFlag('provider',$provider->id,'Individual'); // Get NPI details
					
					$dbconnection 		= new DBConnectionController();
					$practice_db_name 	= $dbconnection->getpracticedbname($practice_name->practice_name);
					$dbconnection->configureConnectionByName($practice_db_name);
					$facility_name 		= 	Facility::on($practice_db_name)->where('id',$provider->def_facility)->value('facility_name');

					if(!$npi_flag)
					{
						$npiflag_columns = Schema::getColumnListing('npiflag');
						foreach($npiflag_columns as $columns)
						{
							$npi_flag[$columns] = '';
						}			
					}

					return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>compact('provider','addressFlag','npi_flag','practice_name','facility_name')));
				}
				else
				{
					return Response::json(array('status'=>'error', 'message'=>'No provider details found.','data'=>'null'));
                }
            }
			else
			{
				return Response::json(array('status'=>'error', 'message'=>'No practice details found.','data'=>'null'));
			}
        }
		else
		{
			return Response::json(array('status'=>'error', 'message'=>'No customer details found.','data'=>'null'));
		}
	}
	/********************** End provider details show page ***********************************/
	
	/********************** Start provider deleted process ***********************************/
	public function getDeleteApi($practice_id,$id)
	{	
        $practice_id = Helpers::getEncodeAndDecodeOfId($practice_id,'decode');
        $id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Provider::where('id', $id )->count())
		{
			$practice_info 		 = Practice::where('id', $practice_id)->first();
			$dbconnection 		 = new DBConnectionController();
			$practice_db_name 	 = $dbconnection->getpracticedbname($practice_info->practice_name);
			$dbconnection->configureConnectionByName($practice_db_name);
			$provider_info_prc_db = Provider::on($practice_db_name)->where('practice_db_provider_id', $id)->first();
			$provider_info_pid	  = $provider_info_prc_db->id;
			$pro_scheduler_cnt 	  = ProviderScheduler::on($practice_db_name)->where('provider_id',$provider_info_pid)->count();
			if($pro_scheduler_cnt>0){
				return Response::json(array('status'=>'relation_error', 'message'=>Lang::get("practice/practicemaster/provider.validation.provider_sch_del_err")));	
			}
			else{
				$pro_charges_cnt = ClaimInfoV1::on($practice_db_name)->whereRaw('(rendering_provider_id = ? or refering_provider_id = ? or billing_provider_id = ?)', array($provider_info_pid,$provider_info_pid,$provider_info_pid))->count();
				if($pro_charges_cnt>0){
					return Response::json(array('status'=>'relation_error', 'message'=>Lang::get("practice/practicemaster/provider.validation.provider_chg_del_err")));	
				}
				else{
					$result = Provider::find($id)->delete();
					Provider::on($practice_db_name)->where('practice_db_provider_id', $id)->delete();
					return Response::json(array('status'=>'success', 'message'=>'Provider deleted successfully','data'=>''));
				}
			}
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>'Invalid Provider Details.','data'=>'null'));
		}
	}
	/********************** End provider deleted process ***********************************/
	public function getavatarProvider($id,$practice_id,$provider_id,$picture_name)
	{
            $id = Helpers::getEncodeAndDecodeOfId($id,'decode');
            $practice_id = Helpers::getEncodeAndDecodeOfId($practice_id,'decode');
            $provider_id = Helpers::getEncodeAndDecodeOfId($provider_id,'decode');
            
		$delete_avr = Provider::where('customer_id',$id)->where('practice_id',$practice_id)->where('id',$provider_id)->first();
		
		$delete_avr->avatar_name = "";
		$delete_avr->avatar_ext = "";
		$delete_avr->save();
		return Response::json(array('status'=>'success', 'message'=>' deleted successfully','data'=>''));
	}
	/*** Delete Avatar in practice table end ***/
	
	function __destruct() 
	{
    }
	
}