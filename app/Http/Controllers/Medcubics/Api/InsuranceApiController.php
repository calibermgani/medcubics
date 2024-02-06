<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use Intervention\Image\ImageManagerStatic as Image;
use App\Models\Medcubics\Insurance;
use App\Models\Medcubics\Claimformat as ClaimFormat;
use App\Models\Medcubics\Insurancetype as InsuranceType;
use App\Models\Medcubics\Insuranceclass as InsuranceClass;
use App\Models\Medcubics\AddressFlag as AddressFlag;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Models\Document as Document;
use App\Http\Helpers\Helpers as Helpers;
use Auth;
use Request;
use Response;
use Validator;
use Input;
use File;
use DB;
use Config;
use Lang;

class InsuranceApiController extends Controller 
{
    public function __construct()
	{
	}
	
	/*** Start to Export the Insurance	 ***/
	public function getIndexApi($export = "")
	{			
		if($export == '') 
			$insurances = Insurance::with('insurancetype','insuranceclass')->get();
		if($export != "")
		{
			$table      = "insurances";
			$columns    = DB::raw('short_name, insurance_name, CONCAT(address_1, ", ", city, ", ", state, ", ",zipcode5,"-",zipcode4) AS Address,phone1,payerid');
			// IMP : If u any changes in above columns need to change in columns_index also
			$columns_index = ['short_name', 'insurance_name', 'Address','phone1','payerid'];
			$filename   = "Insurance";
			$columnheading = array('Short Name','Insurance Name','Address','Phone','Payer Id');
			
			$callexport = new CommonExportApiController();
			return $callexport->generatebulkexport($table,$columns, $filename,$columnheading,$export,$with_table = '',$con_response = 'No', $pcon = '',$columns_index);
		}
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
		return Response::json(array('status'=>'success', 'message' => null, 'data' => compact('claimformats','insurancetypes', 'insuranceclasses', 'insurancetype_id','insuranceclass_id','addressFlag', 'inscmstypes')));	
	}
	/*** End to Create the Insurance	 ***/

	/*** Start to Store the Insurance	 ***/
	public function getStoreApi($request='')
	{
		if($request == '')
			$request = Request::all();
		if(!empty($request['website'])){
			if(substr($request['website'],0,7) != 'http://' && substr($request['website'],0,8) != 'https://')	
				$request['website'] = "http://".$request['website'];
		}
		Validator::extend('insuniqueaddress', function($attribute, $value, $parameters) use($request)
		{
			$numberofrecord = Insurance::where('insurance_name',$request['insurance_name'])->where('address_1',$request['address_1'])->where('city',$request['city'])->where('state',$request['state'])->where('zipcode5',$request['zipcode5'])->where('zipcode4',$request['zipcode4'])->count();
			if($numberofrecord > 0)	return false;
			else			return true;
		});

		$validate_insurance_rules = Insurance::$rules+array('address_1' => 'required|regex:/^[A-Za-z0-9 \t]*$/i|insuniqueaddress')+array('image'=>Config::get('siteconfigs.customer_image.defult_image_size'));
		$validator = Validator::make($request, $validate_insurance_rules, Insurance::messages());
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{		
			$data = Insurance::create($request);
			$user = Auth::user ()->id;
			$data->created_by = $user;
			$insurance_id = $data->id;
			if(isset($request['temp_doc_id']))
			{
				if($request['temp_doc_id']!="")
				{
					Document::where('temp_type_id', '=', $request['temp_doc_id'])->update(['type_id' => $insurance_id,'temp_type_id' => '']);
				} 
			}
			$data->save ();
			
			if (Input::hasFile('avatar_url'))
			{
				$image = Input::file('avatar_url');
				$filename  = rand(11111,99999);
				$extension = $image->getClientOriginalExtension();
				$filestoreName = $filename .'.'.$extension;
				$resize = array('150','150');
				//Helpers::mediauploadpath('admin','insurance',$data->id,$image,$resize,$filestoreName);
				Helpers::mediauploadpath('admin','insurance',$image,$resize,$filestoreName);
				$data->avatar_name = $filename;
				$data->avatar_ext = $extension;
				$data->save();
			}
                        
			/* Starts - address flag update */				
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
			/* Ends - address flag update */		
             $data->id = Helpers::getEncodeAndDecodeOfId($data->id,'encode');           
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$data->id));					
		}
	}
	/*** End to Store the Insurance	 ***/

	/*** Start to Edit the Insurance	 ***/
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if((isset($id) && is_numeric($id))  && (isset($id) && is_numeric($id)) && Insurance::where('id', $id)->count())
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
			$inscmstypes = array_combine(Config::get('siteconfigs.cms_insurance_types'), Config::get('siteconfigs.cms_insurance_types'));
			
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('insurance', 'insurancetypes', 'insuranceclasses', 'claimformats', 'claimtypes','claimtype_id','claimformat_id','insurancetype_id','insuranceclass_id','addressFlag', 'inscmstypes')));
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
				$numberofrecord = Insurance::where('insurance_name',$request['insurance_name'])->where('id','!=',$id)->where('address_1',$value)->where('city',$request['city'])->where('state',$request['state'])->where('zipcode5',$request['zipcode5'])->where('zipcode4',$request['zipcode4'])->count();
				
				if($numberofrecord > 0)	return false;
				else			return true;
			});
			
			// Check address is unique and deleted at is null
			$validate_insurance_rules = Insurance::$rules+array('address_1' => 'required|regex:/^[A-Za-z0-9 ]+$/i|insuniqueaddress')+array('image'=>Config::get('siteconfigs.customer_image.defult_image_size'));

			$validator = Validator::make($request, $validate_insurance_rules, Insurance::messages());
			if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{
				$insurances = Insurance::findOrFail ( $id );
				if (Input::hasFile('avatar_url'))
				{
					$image = Input::file('avatar_url');
					$filename  = rand(11111,99999);
					$old_filename  = $insurances->avatar_name;
					$old_extension  = $insurances->avatar_ext;
					$extension = $image->getClientOriginalExtension();
					$filestoreName = $filename .'.'.$extension;
					$filestoreoldName = $old_filename .'.'.$old_extension;
					$resize = array('150','150');
					//Helpers::mediauploadpath('admin','insurance',$id,$image,$resize,$filestoreName,$filestoreoldName);
					Helpers::mediauploadpath('admin','insurance',$image,$resize,$filestoreName,$filestoreoldName);
					$insurances->avatar_name = $filename;
					$insurances->avatar_ext = $extension;
				}
				
				$insurances->update ( $request);
				$user = Auth::user ()->id;
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
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));					
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
			Insurance::where('id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
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

			/*** Start to Get address for usps ***/
			$general_address_flag = AddressFlag::getAddressFlag('insurance',$insurance->id,'general_information');
			$addressFlag['general'] = $general_address_flag;
			$claimformats = ClaimFormat::pluck('claim_format', 'id')->all();
			
			$appeal_address_flag = AddressFlag::getAddressFlag('insurance',$insurance->id,'appeal_address');
			$addressFlag['appeal'] = $appeal_address_flag;
			/*** End to Get address for usps ***/
               
			return Response::json(array('status'=>'success', 'message'=>'Insurance details found.','data'=>compact('claimformats','insurance','addressFlag')));
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
		if(DB::table($tablename)->where($fieldname, $addedvalue)->where("deleted_at",null)->count() == 0)
		{			
			$data[$fieldname] = $addedvalue;
			DB::table($tablename)->insert($data);			
			return "1";
		}
		else
		{			
			return "2";
		}		
	}
	/*** End to New Select the Insurance	 ***/	
	
	/*** Start to Listing the Insurance	 ***/
	public function getinsurancevaluesAdmin()
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
							
		$insurance_arr_details = array();
		foreach($insurances as $insurance)
		{
			$admin_insurances_details = $insurance;
			$admin_insurances_details['id'] = Helpers::getEncodeAndDecodeOfId($insurance['id']);
			$admin_insurances_details['insurance_type'] = @$insurance['insurancetype']['type_name'];
			$insurance_arr_details[] = $admin_insurances_details;
		}      
		$data['data'] = $insurance_arr_details;
		$data = array_merge($data,$request);
		$data['recordsTotal'] = $total_rec_count;
		$data['recordsFiltered'] = $total_rec_count;	
		
		return Response::json($data);
	}
	/*** End to Listing the Insurance	 ***/
	
	public function avatarapipicture($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$delete_avr = Insurance::where('id',$id)->first();
		$delete_avr->avatar_name = "";
		$delete_avr->avatar_ext = "";
		$delete_avr->save();
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
	}
	
	function __destruct() 
	{
    }
}