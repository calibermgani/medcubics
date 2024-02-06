<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Intervention\Image\ImageManagerStatic as Image;
use App\Models\Insurance;
use App\Models\Claimformat as ClaimFormat;
use App\Models\Insurancetype as InsuranceType;
use App\Models\Insuranceclass as InsuranceClass;
use App\Models\AddressFlag as AddressFlag;
use App\Models\Document as Document;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Models\Patients\PatientInsuranceArchive as PatientInsuranceArchive;
use App\Models\Medcubics\Insurance as MedcubicInsurances;
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
use Illuminate\Support\Collection;
use Auth;
use Request;
use Response;
use Validator;
use Input;
use File;
use DB;
use Session;
use Config;
use Lang;
use Storage;

class InsuranceApiController extends Controller 
{
    public function __construct()
	{
		
	}
	
	/*** Start to Export the Insurance	 ***/
	public function getIndexApi($export = "")
	{	
        $insurances = Insurance::orderBy('short_name', 'asc')->with('insurancetype','insuranceclass')->get(); 
		
		// if($export != "") 
		// {
		// 	if($export == 'pdf' or $export == 'xlsx' or $export == 'csv') 
		// 	{
		// 		$table      = "insurances";
		// 		$columns    = DB::raw('short_name, insurance_name, CONCAT(address_1, ", ", city, ", ", state, ", ",zipcode5,"-",zipcode4) AS Address,phone1,payerid');
		// 		// IMP : If u any changes in above columns need to change in columns_index also
		// 		$columns_index = ['short_name', 'insurance_name', 'Address','phone1','payerid'];
		// 		$filename   = "Insurance";
		// 		$columnheading = array('Short Name','Insurance Name','Address','Phone','Payer Id');
		// 	}
		// 	// passing argument to get value from other table.
			
		// 	$callexport = new CommonExportApiController();
		// 	return $callexport->generatebulkexport($table,$columns, $filename,$columnheading,$export,$with_table = '',$con_response = 'No', $pcon = '',$columns_index);
		// }
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('insurances')));
	}
	/*** End to Export the Insurance	 ***/

	/*** Start to Create the Insurance	 ***/
	public function getCreateApi()
	{	
		$insurancetypes = InsuranceType::orderBy('type_name','ASC')->pluck('type_name', 'id')->all();
		$insurancetype_id = '';
		$insuranceclasses = InsuranceClass::orderBy('insurance_class','ASC')->pluck('insurance_class', 'id')->all();
		$insuranceclass_id = '';
		$claimformats = ClaimFormat::pluck('claim_format', 'id')->all();

		/*** Start to Get address for usps ***/
		$addressFlag['general']['address1'] = '';
		$addressFlag['general']['city'] = '';
		$addressFlag['general']['state'] = '';
		$addressFlag['general']['zip5'] = '';
		$addressFlag['general']['zip4'] = '';
		$addressFlag['general']['is_address_match'] = '';
		$addressFlag['general']['error_message'] = '';

		$addressFlag['appeal']['address1'] = '';
		$addressFlag['appeal']['city'] = '';
		$addressFlag['appeal']['state'] = '';
		$addressFlag['appeal']['zip5'] = '';
		$addressFlag['appeal']['zip4'] = '';
		$addressFlag['appeal']['is_address_match'] = '';
		$addressFlag['appeal']['error_message'] = '';
		/*** End to Get address for usps ***/

		$inscmstypes = array_combine(Config::get('siteconfigs.cms_insurance_types'), Config::get('siteconfigs.cms_insurance_types'));

		return Response::json(array('status'=>'success', 'message' => null, 'data' => compact('claimformats','insurancetypes', 'insuranceclasses', 'insurancetype_id','insuranceclass_id','addressFlag','inscmstypes')));	
	}
	/*** End to Create the Insurance	 ***/
	
	
	/*** Start to Store the Insurance	 ***/
	public function getStoreApi($request='')
	{
		if($request == '')
		{
			$request = Request::all();
		}
		if(!empty($request['website'])){
			if(substr($request['website'],0,7) != 'http://' && substr($request['website'],0,8) != 'https://')	
				$request['website'] = "http://".$request['website'];
		}
		Validator::extend('insuniqueaddress', function($attribute, $value, $parameters) use($request)
		{
			$numberofrecord = Insurance::where('address_1',$request['address_1'])->where('city',$request['city'])->where('state',$request['state'])->where('zipcode5',$request['zipcode5'])->where('zipcode4',$request['zipcode4'])->where('insurance_name',$request['insurance_name'])->count();
			if($numberofrecord > 0)	return false;
			else			return true;
		});
		
		// Check address is unique and deleted at is null
        $validate_insurance_rules = Insurance::$rules+array('address_1' => 'required|regex:/^[A-Za-z0-9 \t]*$/i|insuniqueaddress')+array('image'=>Config::get('siteconfigs.customer_image.defult_image_size'));

		if( ((is_null($request['email'])) == true) ) {
			unset($validate_insurance_rules['email']);
		}
		if( ((is_null($request['website'])) == true) ) {
			unset($validate_insurance_rules['website']);
		}
		$validator = Validator::make($request, $validate_insurance_rules+array('short_name' => 'required|min:3|max:13|unique:insurances,short_name,NULL,id,deleted_at,NULL'), Insurance::messages());
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{
			$data = Insurance::create($request);
			$user = Auth::user ()->id;
			
				$get_default_id = 0;
			if(InsuranceType::where('type_name','Mutually Defined')->count()>0)
				$get_default_id = InsuranceType::where('type_name','Mutually Defined')->first()->id;
			
			if($request['insurancetype_id'] == 0 || $request['insurancetype_id'] == '')
				$data->insurancetype_id = $get_default_id;
			
			$data->created_by = $user;
			$data->save ();
			
			/*** Update insurance id in document table using temp doc id. ***/	
			if(isset($request['temp_doc_id']))
			{
	            if($request['temp_doc_id']!="") 
				{
					Document::where('temp_type_id', '=', $request['temp_doc_id'])->update(['type_id' => $data->id,'temp_type_id' => '']);
				}
			}
			
			
			if (Input::hasFile('image'))
			{
				$image = Input::file('image');
				$filename  = rand(11111,99999);
				$extension = $image->getClientOriginalExtension();
				$filestoreName = $filename .'.'.$extension;
				$resize = array('150','150');
				Helpers::mediauploadpath('','insurance',$image,$resize,$filestoreName); 
				$data->avatar_name = $filename;
				$data->avatar_ext = $extension;
				$data->save();
			}
			else
			{
				// Copy image from admin to practice.
				if(@$request['avatar_name']!='')
				{
					$chk_env_site   = getenv('APP_ENV');
					$default_view = Config::get('siteconfigs.production.defult_production');
					if($chk_env_site == $default_view)
					{
						$storage_disk = "s3_production";
						$bucket_name  = "medcubicsproduction";
					}
					else
					{
						$storage_disk = "s3";
						$bucket_name  = "medcubicslocal";
					}
					
					$file_name = $request['avatar_name'] .'.'.$request['avatar_ext'];
					
					$avatar_url 	= Storage::disk($storage_disk)->getDriver()->getAdapter()->getClient()->getObjectUrl($bucket_name,"admin/image/insurance/".$file_name);
					
					$image 		=	file_get_contents($avatar_url);
					
					$resize = array('150','150');
					Helpers::mediauploadpath('','insurance',$image,$resize,$file_name); 
					
					$data->avatar_name = $request['avatar_name'];
					$data->avatar_ext = $request['avatar_ext'];
					$data->save();	
				}
				
			}
			
			/*** Starts - address flag update ***/				
			$address_flag = array();
			$address_flag['type'] = 'insurance';
			$address_flag['type_id'] = $data->id;
			$address_flag['type_category'] = 'general_information';
			$address_flag['address2'] = $request['general_address1'];
			$address_flag['city'] = $request['general_city'];
			$address_flag['state'] = $request['general_state'];
			$address_flag['zip5'] =$request['general_zip5'];
			$address_flag['zip4'] = $request['general_zip4'];
			$address_flag['is_address_match'] = $request['general_is_address_match'];
			$address_flag['error_message'] = $request['general_error_message'];
			AddressFlag::checkAndInsertAddressFlag($address_flag);
			/*** Ends - address flag update ***/		
			//Encode ID for data
			$temp = new Collection($data);
			$temp_id = $temp['id'];
			$temp->pull('id');
			$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
			$temp->prepend($temp_encode_id, 'id');
			$data = $temp->all();
			$data = json_decode(json_encode($data), FALSE);
			//Encode ID for data      
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$data->id));					
		}
	}
	/*** End to Store the Insurance	 ***/
	
	/*** Start to Edit the Insurance	 ***/
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if((isset($id) && is_numeric($id))  && Insurance::where('id', $id)->count())
		{
			$insurance = Insurance::where('id',$id)->first();
			$claimtype_id=$insurance->claimtype_id;
			$claimformats = ClaimFormat::pluck('claim_format', 'id')->all();
			$claimformat_id=$insurance->claimformat_id;
			$insurancetypes = InsuranceType::pluck('type_name', 'id')->all();
			$insurancetype_id=$insurance->insurancetype_id;
			$insuranceclasses = InsuranceClass::pluck('insurance_class', 'id')->all();
			$insuranceclass_id=$insurance->insuranceclass_id;
			/*** Start to Get address for usps ***/
			$general_address_flag = AddressFlag::getAddressFlag('insurance',$insurance->id,'general_information');
			$addressFlag['general'] = $general_address_flag;
			
			$appeal_address_flag = AddressFlag::getAddressFlag('insurance',$insurance->id,'appeal_address');
			$addressFlag['appeal'] = $appeal_address_flag;
			/*** End to Get address for usps ***/
			//Encode ID for insurance
			$temp = new Collection($insurance);
			$temp_id = $temp['id'];
			$temp->pull('id');
			$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
			$temp->prepend($temp_encode_id, 'id');
			$data = $temp->all();
			$insurance = json_decode(json_encode($data), FALSE);
			//Encode ID for insurance
			$inscmstypes = array_combine(Config::get('siteconfigs.cms_insurance_types'), Config::get('siteconfigs.cms_insurance_types'));
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('insurance', 'insurancetypes', 'insuranceclasses', 'claimformats','claimtype_id','claimformat_id','insurancetype_id','insuranceclass_id','addressFlag', 'inscmstypes')));
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Edit the Insurance	 ***/

	/*** Start to Update the Insurance	 ***/
	public function getUpdateApi($id)
	{	
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if((isset($id) && is_numeric($id)) && Insurance::where('id', $id)->count())
		{
			$request = Request::all();
			if(!empty($request['website'])){
				if(substr($request['website'],0,7) != 'http://' && substr($request['website'],0,8) != 'https://')	
					$request['website'] = "http://".$request['website'];
			}
			Validator::extend('insuniqueaddress', function($attribute, $value, $parameters) use($request,$id)
			{
				$numberofrecord = Insurance::where('id','!=',$id)->where('address_1',$value)->where('city',$request['city'])->where('state',$request['state'])->where('zipcode5',$request['zipcode5'])->where('zipcode4',$request['zipcode4'])->where('insurance_name',$request['insurance_name'])->count();
				
				if($numberofrecord > 0)	return false;
				else			return true;
			});
			
			// Check address is unique and deleted at is null
			$validate_insurance_rules = Insurance::$rules+array('address_1' => 'required|insuniqueaddress')+array('image'=>Config::get('siteconfigs.customer_image.defult_image_size'));
			//Short name unique validation 	
			if((is_null($request['email']) == true)) unset($validate_insurance_rules['email']);
			if((is_null($request['website']) == true)) unset($validate_insurance_rules['website']);
			// dd($request)	;
			$validator = Validator::make($request, $validate_insurance_rules+array('short_name' => 'required|min:3|max:13|unique:insurances,short_name,'.$id.',id,deleted_at,NULL'), Insurance::messages());
			if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{
				/* Remove claim error message */
				ClaimInfoV1::ClearingClaimErrors($id,'Insurance');
				/* Remove claim error message */
				$insurances = Insurance::findOrFail($id);
				if (Input::hasFile('image'))
				{
					$image = Input::file('image');
					$filename  = rand(11111,99999);

					$old_filename  = $insurances->avatar_name;
					$old_extension  = $insurances->avatar_ext;

					$extension = $image->getClientOriginalExtension();
					$filestoreName = $filename .'.'.$extension;
					$filestoreoldName = $old_filename .'.'.$old_extension;
					$resize = array('150','150');
					Helpers::mediauploadpath('','insurance',$image,$resize,$filestoreName,$filestoreoldName);  
					$insurances->avatar_name = $filename;
					$insurances->avatar_ext = $extension;
				}
				
				$insurances->update ( $request);
				$user = Auth::user ()->id;
				
					$get_default_id = 0;
				if(InsuranceType::where('type_name','Mutually Defined')->count()>0)
					$get_default_id = InsuranceType::where('type_name','Mutually Defined')->first()->id;
				
				if($request['insurancetype_id'] == 0 || $request['insurancetype_id'] == '')
					$insurances->insurancetype_id = $get_default_id;
				/* Eligibility fax 2 is missing for store*/
				$insurances->eligibility_fax2 = $request['eligibility_fax2'];
				$insurances->updated_by = $user;
				$insurances->save ();
				/*** Starts - Pay to address flag update ***/
				$address_flag = array();
				$address_flag['type'] = 'insurance';
				$address_flag['type_id'] = $insurances->id;
				$address_flag['type_category'] = 'general_information';
				$address_flag['address2'] = $request['general_address1'];
				$address_flag['city'] = $request['general_city'];
				$address_flag['state'] = $request['general_state'];
				$address_flag['zip5'] =$request['general_zip5'];
				$address_flag['zip4'] = $request['general_zip4'];
				$address_flag['is_address_match'] = $request['general_is_address_match'];
				$address_flag['error_message'] = $request['general_error_message'];
				AddressFlag::checkAndInsertAddressFlag($address_flag);
				/*** Ends - Pay to address ***/
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.insurance_update_msg"),'data'=>''));					
			}
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Update the Insurance	 ***/

	/*** Start to Destory the Insurance	 ***/
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if((isset($id) && is_numeric($id))  && Insurance::where('id', $id)->count())
		{
			if(PatientInsuranceArchive::where('insurance_id',$id)->count()==0 && PatientInsurance::where('insurance_id',$id)->count()==0)
			{
				Insurance::where('id',$id)->delete();
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.insurance_delete_msg"),'data'=>''));	
			}
			else
			{
				return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));	
			}
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Destory the Insurance	 ***/

	/*** Start to Show the Insurance	 ***/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if((isset($id) && is_numeric($id))  && Insurance::where('id', $id)->count())
		{
			$insurance = Insurance::with('insurancetype','insuranceclass')->find($id);
			$claimformats = ClaimFormat::pluck('claim_format', 'id')->all();
			/*** Start to Get address for usps ***/
			$general_address_flag = AddressFlag::getAddressFlag('insurance',$insurance->id,'general_information');
			$addressFlag['general'] = $general_address_flag;
   
			$appeal_address_flag = AddressFlag::getAddressFlag('insurance',$insurance->id,'appeal_address');
			$addressFlag['appeal'] = $appeal_address_flag;
			/*** End to Get address for usps ***/
			
            //Encode ID for insurance
			$temp = new Collection($insurance);
			$temp_id = $temp['id'];
			$temp->pull('id');
			$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
			$temp->prepend($temp_encode_id, 'id');
			$data = $temp->all();
			$insurance = json_decode(json_encode($data), FALSE);
			//Encode ID for insurance
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('claimformats','insurance','addressFlag')));
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}
	}
	/*** End to Show the Insurance	 ***/
	
	/*** Start to New Select the Insurance	 ***/
	public function addnewApi($addedvalue)
	{			
		$request = Request::all();		
		$tablename = $request['tablename'];
		$fieldname = $request['fieldname'];	
		$db_name = DB::connection()->getDatabaseName();
		$admin_db_name = getenv('DB_DATABASE');
		// For admin no need to use connection
		if($db_name == $admin_db_name) {
			$ins_type = DB::table($tablename);
		} else {	
			$ins_type = DB::connection($db_name)->table($tablename);
		}
		if(isset($request['cms_type'])) {
			$data['cms_type'] = $request['cms_type'];
		}

		if($ins_type->where($fieldname, $addedvalue)->where("deleted_at",null)->count() == 0)
		{			
			$data[$fieldname] = $addedvalue;
			$data['created_by'] = Auth::user ()->id;
			$data['created_at'] = date('Y-m-d h:i:s');
			$ins_type->insert($data);			
			return "1";
		}
		else
		{			
			return "2";
		}	
	}
	/*** End to New Select the Insurance	 ***/
	
	/*** Start to Get Option Values	 ***/
	public function getoptionvalues()
	{
		if(!empty(Request::input('tablename'))) 
		{
			$tablename = Request::input('tablename');
			$fieldname = Request::input('fieldname');	
			$addedvalue = Request::input('addedvalue');		
			$resultarr = DB::table($tablename)->orderBy($fieldname,'ASC')->pluck($fieldname,'id')->all();		
			$result_arr = "<option value=''>-- Select --</option>";
			foreach($resultarr as $k=>$results) 
			{
				if($addedvalue == $results)
				{
					$result_arr .= "<option value='".$k."' selected='selected'>".$results."</option>";
				}
				else
				{
					$result_arr .= "<option value='".$k."'>".$results."</option>";
				}
			}
			$result_arr .= "<optgroup label='Others'><option value=0>Add New</option></optgroup>";
			return  $result_arr;
		}
	}
	/*** End to Get Option Values ***/
	
	/*** Start to Listing the Insurance	 ***/
	public function getinsurancetablevalues()
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
		
		$insurances = Insurance::with('insurancetype')->where(function($q)use($search)
		{
			$q->Where('insurance_name', 'like', '%' . $search . '%')->orWhere('short_name', 'like', '%' . $search . '%')->orWhere('address_1', 'like', '%' . $search . '%')->orWhere('city', 'like', '%' . $search . '%')->orWhere('state', 'like', '%' . $search . '%')->orWhere('zipcode5', 'like', '%' . $search . '%')->orWhere('zipcode4', 'like', '%' . $search . '%')->orWhere('payerid', 'like', '%' . $search . '%')->orWhere('phone1', 'like', '%' . $search . '%');
		})->orderBy($order,$order_decs)->skip($start)->take($len)->get()->toArray();	
		
		$total_rec_count = Insurance::where(function($q)use($search)
							{
								$q->Where('insurance_name', 'like', '%' . $search . '%')->orWhere('short_name', 'like', '%' . $search . '%')->orWhere('address_1', 'like', '%' . $search . '%')->orWhere('city', 'like', '%' . $search . '%')->orWhere('state', 'like', '%' . $search . '%')->orWhere('zipcode5', 'like', '%' . $search . '%')->orWhere('zipcode4', 'like', '%' . $search . '%')->orWhere('payerid', 'like', '%' . $search . '%')->orWhere('phone1', 'like', '%' . $search . '%');
							})->count();	
		
		foreach($insurances as $insurance)
		{
			$insurances_details = $insurance;
			$insurances_details['id'] = Helpers::getEncodeAndDecodeOfId($insurance['id']);
			$insurances_details['insurance_type'] = @$insurance['insurancetype']['type_name'];
			$insurance_arr_details[] = $insurances_details;
		}      
		
		if(count($insurances)==0){
			$insurance_arr_details = array();
		}
		
		$data['data'] = $insurance_arr_details;	
		$data = array_merge($data,$request);
		$data['recordsTotal'] = $total_rec_count;
		$data['recordsFiltered'] = $total_rec_count;		
		return Response::json($data);
	}
	/*** End to Listing the Insurance	 ***/

	public function avatarapipicture($id,$pic_id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$delete_avr = Insurance::where('id',$id)->first();
		$delete_avr->avatar_name = "";
		$delete_avr->avatar_ext = "";
		$delete_avr->save();
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
	}
	
	public static function checkPatientInsurance($insid)
	{
		$id = Helpers::getEncodeAndDecodeOfId($insid,'decode');
		
		if(PatientInsuranceArchive::where('insurance_id',$id)->count()==0 && PatientInsurance::where('insurance_id',$id)->count()==0)
		{
			return 1;
		}
	}
	
	// Get insurance list by key word
	/*public function getInsList($insname,$serach_category)
	{
		$get_Inslist = MedcubicInsurances::where('insurance_name', 'like', '%'.$insname . '%')->where('status','Active')->get();
		return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('get_Inslist')));
	}*/
	// Get Insurance list by Key Word and Category 
	public function getInsList($serach_keyword,$serach_category)
	{
		 $sub_sql = '';	
		if ($serach_category == 'payerid') {
            $sub_sql = "payerid LIKE '%$serach_keyword%' or payerid LIKE '%$serach_keyword' or payerid LIKE '$serach_keyword%'";
        } elseif ($serach_category == 'address') {
            $serach_keywords = array_map("trim", explode(',', $serach_keyword));
            foreach ($serach_keywords as $serach_keyword) {
                if ($serach_keyword != "") {
                    $sub_sql = ($sub_sql <> "") ? $sub_sql . " or " : $sub_sql;
                    $sub_sql .= "address_1 LIKE '%$serach_keyword%' or city LIKE '%$serach_keyword%' or state LIKE '%$serach_keyword%' or zipcode5 LIKE '%$serach_keyword%' or zipcode4 LIKE '%$serach_keyword%'";
                }
            }
        } else {
            //$sub_sql = "insurance_name LIKE '%$serach_keyword%' or insurance_name LIKE '%$serach_keyword' or insurance_name LIKE '$serach_keyword%'";			
            $serach_keywords = array_map("trim", explode(',', $serach_keyword));
            foreach ($serach_keywords as $srch_keyword) {
                if ($srch_keyword != "") {
                    $sub_sql = ($sub_sql <> "") ? $sub_sql . " or " : $sub_sql;
                    $sub_sql .= "insurance_name LIKE '%$srch_keyword%' or insurance_name LIKE '%$srch_keyword' or insurance_name LIKE '$srch_keyword%'";
                }
            }
        }
        $get_Inslist = MedcubicInsurances::with('insurancetype')->whereRaw("($sub_sql)")->orderBy('insurance_name', 'asc')->get();
        //$get_Inslist['insurance_type'] = @$insurance['insurancetype']['type_name'];
        $get_Inslist = json_decode(json_encode($get_Inslist), true);
		return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('get_Inslist')));
	}
	// Get insurance details by id
	public function getInsDetails($insid)
	{
		$insid = Helpers::getEncodeAndDecodeOfId($insid,'decode');
		## Removed for getting insurance details
		$get_Insinfo = MedcubicInsurances::where('id',$insid)->first();	
	//	$get_Insinfo = Insurance::where('id',$insid)->first();			
		$filename = $get_Insinfo->avatar_name . '.' . $get_Insinfo->avatar_ext;
		$img_details = [];
		$img_details['module_name']='insurance';
		$img_details['file_name']=$filename;
		$img_details['practice_name']="admin";
		
		$img_details['class']='';
		$img_details['alt']='insurance-image';
		
		if($get_Insinfo->avatar_name != '')
		{
			$image_tag = Helpers::checkAndGetAvatar($img_details); 	
		}
		else
		{
			$image_tag = 'no-image';
		}
		
		return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('get_Insinfo','image_tag')));
	}
	
	public function insuranceUnique($name='')
	{
		$shortname = Insurance::shortNameUnique($name);
		return $shortname ;
	}
	
	function __destruct() 
	{
    }
}
