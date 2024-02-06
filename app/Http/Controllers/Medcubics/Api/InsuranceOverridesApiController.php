<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use DB;
use Auth;
use Response;
use Request;
use Validator;
use App\Models\Medcubics\Provider as Provider;
use App\Models\Medcubics\Insurance as Insurance;
use App\Models\Medcubics\Facility as Facility;
use App\Models\Medcubics\Insuranceoverride as Insuranceoverride;
use App\Models\Medcubics\AddressFlag as AddressFlag;
use App\Models\Medcubics\IdQualifier as IdQualifier;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use Lang;

class InsuranceOverridesApiController extends Controller
{
	/*** Start to Listing & Export the Insurance Overrides	 ***/
	public function getIndexApi($id, $export = "")
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		// check invalid id 
		if(Insurance::where('id', $id)->count()>0 && is_numeric($id))
		{
			$insurance = Insurance::where('id', $id)->first();
			$overrides = Insuranceoverride::with('insurance','facility','provider','provider.provider_types','provider.degrees','provider.provider_type_details','id_qualifier')->where('insurance_id',$id)->orderBy('id','asc')->get();
			
			 /*** Start to Get address for usps ***/
			$general_address_flag = AddressFlag::getAddressFlag('insurance',$insurance->id,'general_information');
			$addressFlag['general'] = $general_address_flag;
			/*** End to Get address for usps ***/
			
			if($export != "")
			{
				$exportparam 	= 	array(
						'filename'	=>	'insurance_overrides',
						'heading'	=>	'Insurance Overrides',
						'fields' 	=>	array(
						'provider' 	=>	array('table'=>'provider' ,	'column' => 'provider_name' ,	'label' => 'Provider'),
						'tax_id' 	=> array('table'=>'provider' ,	'column' => 'etin_type_number' ,	'label' => 'Tax ID'),
							'npi' 	=> array('table'=>'provider' , 'column' => 'npi' ,	'label' => 'NPI'),
					'provider_id'	=>	'Provider ID',
				'id_qualifiers_id' 	=>	array('table'=>'id_qualifier' ,	'column' => 'id_qualifier_name' ,	'label' => 'ID Type'),
					));
				$callexport = new CommonExportApiController();
				return $callexport->generatemultipleExports($exportparam, $overrides, $export); 
			}
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('insurance', 'overrides','addressFlag')));
		}
		else 
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));   
		}
	}
	/*** End to Listing & Export the Insurance Overrides	***/
	
	/*** Start to Create the Insurance Overrides	 ***/		
	public function getCreateApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Insurance::where('id',$id)->count()>0 && is_numeric($id)) 
		{
			$insurance = Insurance::where('id',$id)->first();
			$facilities = Facility::where('status', 'Active')->orderBy('facility_name','ASC')->pluck('facility_name','id')->all();
			$facilities_id='';
			$insurances = Insurance::pluck('insurance_name','id')->all();
			$insurance_id ='';
			$providers = Provider::select(DB::raw("CONCAT(id,';',etin_type_number,';', npi) AS id, CONCAT(last_name,' ',first_name) AS provider_name"))->where('status', '=', 'Active')->orderBy('provider_name','ASC')->pluck('provider_name', 'id')->all();
			$provider_id = '';
			$id_qualifiers = IdQualifier::pluck('id_qualifier_name','id')->all();
			$id_qualifiers_id = '';
		
			/*** Start to Get address for usps ***/
			$general_address_flag = AddressFlag::getAddressFlag('insurance',$insurance->id,'general_information');
			$addressFlag['general'] = $general_address_flag;
			/*** End to Get address for usps ***/
			
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('facilities','facilities_id','insurances','insurance_id','providers','provider_id','id_qualifiers','id_qualifiers_id','insurance','addressFlag')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Create the Insurance Overrides ***/
	
	/*** Start to Store the Insurance Overrides  ***/	
	public function getStoreApi($id, $request='')
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		// check invalid id 
		if(Insurance::where('id',$id)->count()>0 && is_numeric($id)) 
		{
			$request = Request::all();
			$request['insurance_id']  = Helpers::getEncodeAndDecodeOfId($request['insurance_id'],'decode');  
			$validator = Validator::make($request, Insuranceoverride::$rules, Insuranceoverride::messages());
			if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{	
				$result = Insuranceoverride::create($request);
				$user = Auth::user ()->id;
				$result->created_by = $user;
				$result->save ();
				$result->id  = Helpers::getEncodeAndDecodeOfId($result->id,'encode');
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$result->id));					
			}
		}
		else 
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Store the Insurance Overrides	 ***/

	/*** Start to Edit the Insurance Overrides	 ***/
	public function getEditApi($ids,$id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$ids = Helpers::getEncodeAndDecodeOfId($ids,'decode');
		$insurance = Insurance::where('id',$ids)->first();
		// check invalid id for override 
		if(Insuranceoverride::where('id',$id)->count()>0 && is_numeric($id))
        {
			// check invalid id for insurance.
			if(Insurance::where('id',$ids)->count()>0 && is_numeric($ids)) 
			{
				$overrides = Insuranceoverride::find($id);
                $providers_id = $overrides->providers_id;
				$facilities = Facility::where('status', 'Active')->orderBy('facility_name','ASC')->pluck('facility_name','id')->all();
				$facilities_id=$overrides->facility_id;
				$providers = Provider::select(DB::raw("CONCAT(id,';',etin_type_number,';', npi) AS id, CONCAT(last_name,' ',first_name) AS provider_name"))->where('status', '=', 'Active')->orderBy('provider_name','ASC')->pluck('provider_name', 'id')->all();
                $provider = Provider::where('id',$providers_id)->first();
				$provider_id = $provider->id.';'.$provider->etin_type_number.';'.$provider->npi; 
				$id_qualifiers = IdQualifier::pluck('id_qualifier_name','id')->all();
				$id_qualifiers_id = $overrides->id_qualifiers_id;
		
				 /*** Start to Get address for usps ***/
				$general_address_flag = AddressFlag::getAddressFlag('insurance',$insurance->id,'general_information');
				$addressFlag['general'] = $general_address_flag;
				/*** End to Get address for usps ***/
			
				return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('overrides','facilities','facilities_id','provider_id','providers','id_qualifiers','id_qualifiers_id','insurance','addressFlag')));
			}
			else
			{
				 return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
			}
		}
		else 
		{ 
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Edit the Insurance Overrides	 ***/
	
	/*** Start to Update the Insurance Overrides	 ***/
	public function getUpdateApi($insurance_id, $id, $request='')
	{
		$insurance_id = Helpers::getEncodeAndDecodeOfId($insurance_id,'decode');
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		// check invalid id for insurance.
		if(Insurance::where('id',$insurance_id)->count()>0 && is_numeric($insurance_id)) 
		{
			// check invalid id for override 
			if(Insuranceoverride::where('id',$id)->count()>0 && is_numeric($id))
			{
				if($request == '')
					$request = Request::all();
				$request['insurance_id'] = Helpers::getEncodeAndDecodeOfId($request['insurance_id'],'decode');
				$validator = Validator::make($request, Insuranceoverride::$rules, Insuranceoverride::messages());
				if ($validator->fails())
				{
					$errors = $validator->errors();
					return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
				}
				else
				{	
					$override = Insuranceoverride::findOrFail($id); 
					$override->update($request);
					$user = Auth::user ()->id;
					$override->updated_by = $user;
					$override->save ();
					return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));					
				}
			}
			else
			{
				return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
			}
		}
		else 
		{ 
			return Response::json(array('status'=>'failure_ins', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Update the Insurance Overrides	 ***/
	
	/*** Start to Destory the Insurance Overrides	 ***/
	public function getDeleteApi($type,$id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$insurance_id = Helpers::getEncodeAndDecodeOfId($type,'decode');
		// check invalid id for insurance.
		if(Insurance::where('id',$insurance_id)->count()>0 && is_numeric($insurance_id)) 
		{
			// check invalid id for override 
			if(Insuranceoverride::where('id',$id)->count()>0 && is_numeric($id))
			{
				Insuranceoverride::where('id',$id)->delete();
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
			}
			else
			{
				return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
			}
		}
		else 
		{ 
			return Response::json(array('status'=>'failure_ins', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Destory the Insurance Overrides	 ***/

	/*** Start to Show the Insurance Overrides	 ***/
	public function getShowApi($insid,$id)
	{
		$insid = Helpers::getEncodeAndDecodeOfId($insid,'decode');
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		// check invalid id for override 
		if(Insuranceoverride::where('id', $id )->count()>0 && is_numeric($id))
		{
			// check invalid id for insurance.
			if(Insurance::where('id', $insid)->count()>0 && is_numeric($insid)) 
			{
				$insurance = Insurance::where('id', $insid)->first();
				$overrides = Insuranceoverride::with('facility','insurance','provider','provider.degrees','id_qualifier')->where('id',$id)->first();
                        
				 /*** Start to Get address for usps ***/
				$general_address_flag = AddressFlag::getAddressFlag('insurance',$insurance->id,'general_information');
				$addressFlag['general'] = $general_address_flag;
				/*** End to Get address for usps ***/
				
				return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('overrides','insurance','addressFlag')));
            }
			else
			{
				return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
			}
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Show the Insurance Overrides	 ***/
	
	function __destruct() 
	{
    }
}