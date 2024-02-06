<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use DB;
use Auth;
use Response;
use Request;
use Validator;
use App\Models\Provider as Provider;
use App\Models\Insurance as Insurance;
use App\Models\Facility as Facility;
use App\Models\Insuranceoverride as Insuranceoverride;
use App\Models\AddressFlag as AddressFlag;
use App\Models\IdQualifier as IdQualifier;
use App\Models\Document as Document;
use App\Http\Helpers\Helpers as Helpers;
use Lang;

class InsuranceOverridesApiController extends Controller 
{
	/*** Start to listing the Overrides  ***/
	public function getIndexApi($id, $export = "")
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		// check invalid id 
		if(Insurance::where('id', $id)->count()>0 && is_numeric($id))
		{
			$insurance = Insurance::where('id', $id)->first();
			$overrides = Insuranceoverride::with('facility','insurance','provider.provider_types','provider','provider.degrees','provider.provider_type_details','id_qualifier')->where('insurance_id',$id)->orderBy('id','asc')->get();
			
			 /// Get address for usps ///
			$general_address_flag = AddressFlag::getAddressFlag('insurance',$insurance->id,'general_information');
			$addressFlag['general'] = $general_address_flag;
			//Export
			if($export != "")
			{
				$exportparam 		= 	array(
				'filename'			=>	'insurance_overrides',
				'heading'			=>	'Insurance Overrides',
				'fields' 			=>	array(
				'facilities_id' 	=>	array('table'=>'provider' ,	'column' => 'provider_name' ,	'label' => 'Provider Name'),
				'tax_id' 			=> array('table'=>'provider' ,	'column' => 'etin_type_number' ,	'label' => 'Tax ID'),
				'npi' 				=> array('table'=>'provider' ,	'column' => 'npi' ,	'label' => 'NPI'),
				'provider_id'		=>	'Provider ID',
				'id_qualifiers_id' 	=>	array('table'=>'id_qualifier' ,	'column' => 'id_qualifier_name' ,	'label' => 'ID Type'),
				));
				$callexport = new CommonExportApiController();
				return $callexport->generatemultipleExports($exportparam, $overrides, $export); 
			}
			$insurance->id = Helpers::getEncodeAndDecodeOfId($insurance->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('insurance', 'overrides','addressFlag')));
		}
		else 
		{
			 return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));   
		}
	}
	/*** End to listing the Overrides  ***/
	
	/*** Start to Create the Overrides	 ***/
	public function getCreateApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		// check invalid id 
		if(Insurance::where('id',$id)->count()>0 && is_numeric($id)) 
		{
			$insurance 		= Insurance::where('id',$id)->with('favourite')->first();
			$facilities 	= Facility::where('status', 'Active')->orderBy('facility_name','ASC')->pluck('facility_name','id')->all();
			$facilities_id 	= $insurance_id = $provider_id = $id_qualifiers_id = '';
			$insurances 	= Insurance::pluck('insurance_name','id')->all();
			$providers 		= Provider::getBillingAndREnderingProvider('npi'); 
			
			$providers 		= array_flip($providers);  
			$providers	 	= array_flip(array_map(array($this,'myfunction'),$providers));
				$providers 		= Provider::getBillingAndREnderingProvider('npi'); 
			$id_qualifiers 	= IdQualifier::pluck('id_qualifier_name','id')->all();
			
			 /// Get address for usps ///
			$general_address_flag = AddressFlag::getAddressFlag('insurance',$insurance->id,'general_information');
			$addressFlag['general'] = $general_address_flag;
			$insurance->id = Helpers::getEncodeAndDecodeOfId($insurance->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('facilities','facilities_id','insurances','insurance_id','providers','provider_id','id_qualifiers','id_qualifiers_id','insurance','addressFlag')));
		}
		else 
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Create the Overrides	 ***/
	
	/*** Start to Store the Overrides	 ***/
	public function getStoreApi($id,$request)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		// check invalid id 
		if(Insurance::where('id',$id)->count()>0 && is_numeric($id)) 
		{
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
				$result = Insuranceoverride::create($request);
				if(isset($request['temp_doc_id']))
				{
					if($request['temp_doc_id']!="") 
					{
						Document::where('temp_type_id', '=', $request['temp_doc_id'])->update(['type_id' => $result->id,'temp_type_id' => '']);
					}
				}
				$user = Auth::user ()->id;
				$result->created_by = $user;
				$result->save ();
				$result->id = Helpers::getEncodeAndDecodeOfId($result->id,'encode');
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$result->id));					
			}
		}
		else 
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Store the Overrides	 ***/
	
	/*** Start to Edit the Overrides	 ***/
	public function getEditApi($ids,$id)
	{
		$ids = Helpers::getEncodeAndDecodeOfId($ids,'decode');
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$insurance = Insurance::where('id',$ids)->with('favourite')->first();
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
				$providers = Provider::getBillingAndREnderingProvider('npi'); 
				$providers 		= array_flip($providers);  
				$providers	 	= array_flip(array_map(array($this,'myfunction'),$providers));
				$provider = Provider::where('id',$providers_id)->first();
				@$provider_id = $provider->id.';'.$provider->etin_type_number.';'.$provider->npi; 
				$providers = Provider::getBillingAndREnderingProvider('npi'); 
				$id_qualifiers = IdQualifier::pluck('id_qualifier_name','id')->all();
				$id_qualifiers_id = $overrides->id_qualifiers_id;

				/// Get address for usps ///
				$general_address_flag = AddressFlag::getAddressFlag('insurance',$insurance->id,'general_information');
				$addressFlag['general'] = $general_address_flag;
				$insurance->id = Helpers::getEncodeAndDecodeOfId($insurance->id,'encode');
				$overrides->id = Helpers::getEncodeAndDecodeOfId($overrides->id,'encode');
				return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('overrides','facilities','facilities_id','provider','provider_id','providers','id_qualifiers','id_qualifiers_id','insurance','addressFlag')));
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
	/*** End to Edit the Overrides ***/
	
	/*** Start to Update the Overrides	 ***/
	public function getUpdateApi($insurance_id, $id, $request='')
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$insurance_id = Helpers::getEncodeAndDecodeOfId($insurance_id,'decode');
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
	/*** End to Update the Overrides	 ***/
	
	/*** Start to Destory the Overrides	 ***/
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
	/*** End to Destory the Overrides	 ***/

	/*** Start to Show the Overrides	 ***/
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
				$insurance = Insurance::with('favourite')->where('id',$insid)->first();
				$overrides = Insuranceoverride::with('facility','insurance','provider','id_qualifier','provider.degrees')->where('id',$id)->first();
							
				 /// Get address for usps ///
				$general_address_flag = AddressFlag::getAddressFlag('insurance',$insurance->id,'general_information');
				$addressFlag['general'] = $general_address_flag;
				$insurance->id = Helpers::getEncodeAndDecodeOfId($insurance->id,'encode');
				$overrides->id = Helpers::getEncodeAndDecodeOfId($overrides->id,'encode');
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
	/*** End to Show the Overrides ***/
	function myfunction($num)
	{
	  return(Helpers::getEncodeAndDecodeOfId($num,'encode'));
	}
	function __destruct() 
	{
    }
}