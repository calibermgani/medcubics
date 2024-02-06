<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Facilitymanagecare as Facilitymanagecare;
use App\Models\Insurance as Insurance;
use App\Models\Provider as Provider;
use App\Models\Facility as Facility;
use Illuminate\Support\Collection;
use Auth;
use Input;
use Route;
use Response;
use Request;
use Validator;
use DB;
use App\Models\Document as Document;
use App\Http\Helpers\Helpers as Helpers;
use Lang;

class FacilitymanagecareApiController extends Controller 
{
	/*** Start Listing of the Facility Managecare ***/ 
    public function getIndexApi($facility_id, $export = "") 
	{
		$facility_id = Helpers::getEncodeAndDecodeOfId($facility_id,'decode');
		if(Facility::where('id', $facility_id)->count()>0 && is_numeric($facility_id)) 
		{
			$facility 		= Facility::with('facility_address')->where('id', $facility_id)->first();
			$managecare 	= Facilitymanagecare::with('insurance', 'provider','provider.degrees','provider.provider_types')->where('facilities_id', $facility_id)->get();
			if ($export != "") 
			{
				$exportparam = array(
					'filename' 	=> 'Facility_Managecare',
					'heading' 	=> $facility->facility_name,
					'fields'	=> array(
						'insurances_id' 	=> array('table' => 'insurance', 'column' => 'insurance_name', 'label' => 'Insurance'),
						'providers_id' 		=> array('table' => 'provider', 'column' => 'provider_name', 'label' => 'Provider'),
						'enrollment' 		=> 'Credential',
						'entitytype'		=> 'Entity Type',
						'effectivedate' 	=> 'Effective Date',
						'terminationdate' 	=> 'Termination Date',
						'feeschedule' 		=> 'Fee Schedule',
				));
				$callexport = new CommonExportApiController();
				return $callexport->generatemultipleExports($exportparam, $managecare, $export); 
			}
			return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('managecare', 'facility')));
		} 
		else 
		{
			return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
    }
	/*** End Listing of the Facility Managecare ***/ 

	/*** Start Create of the Facility Managecare ***/ 
    public function getCreateApi($facility_id) 
	{
		$facility_id = Helpers::getEncodeAndDecodeOfId($facility_id,'decode');
		if(Facility::where('id', $facility_id)->count()>0 && is_numeric($facility_id)) 
		{
			$facility 		= Facility::with('facility_address')->where('id', $facility_id)->first();
			$insurances 	= Insurance::orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();
			$insurance_id 	= '';
			$providers 		= Provider::getBillingAndREnderingProvider('yes'); 
			$provider_id 	= '';

			return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('insurances', 'insurance_id', 'providers', 'provider_id', 'facility')));
		} 
		else 
		{
			return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
    }
	/*** End Create of the Facility Managecare ***/
	
	/*** Start Store of the Facility Managecare ***/ 
    public function getStoreApi($facility_id, $request = '') 
	{
		$facility_id = Helpers::getEncodeAndDecodeOfId($facility_id,'decode');
		if(Facility::where('id', $facility_id)->count()>0 && is_numeric($facility_id)) 
		{
			if ($request == '')
				$request = Request::all();
				$validator 	= Validator::make($request, Facilitymanagecare::$rules, Facilitymanagecare::messages());
			if ($validator->fails()) 
			{
				$errors = $validator->errors();
				return Response::json(array('status' => 'error', 'message' => $errors, 'data' => ''));
			}
			else 
			{
			   if($request['effectivedate'] != '') 
					$request['effectivedate']	= date("Y-m-d",strtotime($request['effectivedate'])); 
			   
			   if($request['terminationdate'] != '')
					$request['terminationdate'] 	= date("Y-m-d",strtotime($request['terminationdate'])); 
				$request['facilities_id'] = $facility_id;
				// dd($request);

				$data = Facilitymanagecare::create($request);
				// dd($data);
				if(isset($request['temp_doc_id']))
				{
						if($request['temp_doc_id']!="") Document::where('temp_type_id', '=', $request['temp_doc_id'])->update(['type_id' => $data->id,'temp_type_id' => '']);
				}
				$user = Auth::user ()->id;
				$data->created_by = $user;
				$data->save ();
				// dd($data);
				//Encode ID for data
				$temp = new Collection($data);
				$temp_id = $temp['id'];
				$temp->pull('id');
				$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
				$temp->prepend($temp_encode_id, 'id');
				$data = $temp->all();
				$data = json_decode(json_encode($data), FALSE);
				//Encode ID for data
				return Response::json(array('status' => 'success', 'message' =>Lang::get("common.validation.create_msg"), 'data' => $data->id));
			}
		} 
		else 
		{
			return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
    }
	/*** End Store of the Facility Managecare ***/ 

	/*** Start Edit of the Facility Managecare ***/	
    public function getEditApi($facility_id, $id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$facility_id = Helpers::getEncodeAndDecodeOfId($facility_id,'decode');
		
		if(Facility::where('id', $facility_id)->count()>0 && is_numeric($facility_id)) 
		{
			if(Facilitymanagecare::where('id', $id)->count()>0 && is_numeric($id)) 
			{
			$facility 		= Facility::with('facility_address')->where('id', $facility_id)->first();
			$managecare 	= Facilitymanagecare::find($id);
			$insurances 	= Insurance::orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();
			$insurance_id 	= $managecare->insurances_id;
			$providers 		= Provider::getBillingAndREnderingProvider('yes'); 
			$provider_id 	= $managecare->providers_id;
			return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('managecare', 'insurances', 'insurance_id', 'providers', 'provider_id', 'facility')));
			} 
			else 
			{
				return Response::json(array('status' => 'failure_care', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
			}
		} 
		else 
		{
			return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
    }
	/*** End Edit of the Facility Managecare ***/ 

	/*** Start Update of the Facility Managecare ***/ 	
    public function getUpdateApi($facility_id, $id, $request = '') 
	{
        $id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$facility_id = Helpers::getEncodeAndDecodeOfId($facility_id,'decode');
		
		if(Facility::where('id', $facility_id)->count()>0 && is_numeric($facility_id)) 
		{
			if(Facilitymanagecare::where('id', $id)->count()>0 && is_numeric($id)) 
			{
				if ($request == '')
					$request = Request::all();
				$request['facilities_id'] = Helpers::getEncodeAndDecodeOfId($request['facilities_id'],'decode');
				$validator = Validator::make($request, Facilitymanagecare::$rules, Facilitymanagecare::messages());

				if ($validator->fails()) 
				{
					$errors = $validator->errors();
					return Response::json(array('status' => 'error', 'message' => $errors, 'data' => ''));
				}
				else 
				{
					$managecare = Facilitymanagecare::find($id);
					
					if($request['effectivedate'] != '')
						$request['effectivedate'] 		= date("Y-m-d",strtotime($request['effectivedate'])); 

					if($request['terminationdate'] != '') 
						$request['terminationdate'] 	= date("Y-m-d",strtotime($request['terminationdate'])); 
							
					$managecare->update($request);
					$user = Auth::user ()->id;
					$managecare->updated_by = $user;
					$managecare->save ();
					return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.managecare_update_msg"), 'data' => ''));
				}
			} 
			else 
			{
				return Response::json(array('status' => 'failure_care', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
			}
		} 
		else 
		{
			return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
    }
	/*** End Update of the Facility Managecare ***/ 

	/*** Start Delete of the Facility Managecare ***/
    public function getDeleteApi($facility_id, $id) 
	{
		 $id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		 $facility_id = Helpers::getEncodeAndDecodeOfId($facility_id,'decode');
		if(Facility::where('id', $facility_id)->count()>0 && is_numeric($facility_id)) 
		{
			if(Facilitymanagecare::where('id', $id)->count()>0 && is_numeric($id)) 
			{ 
				Facilitymanagecare::where('id', $id)->delete();
				return Response::json(array('status' => 'success', 'message' =>Lang::get("common.validation.managecare_delete_msg"), 'data' => ''));
			} 
			else 
			{
				return Response::json(array('status' => 'failure_care', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
			}
		} 
		else 
		{
			return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
    }
	/*** End Delete of the Facility Managecare ***/ 
	
	/*** Start View of the Facility Managecare ***/	
    public function getShowApi($ids, $id) 
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$ids = Helpers::getEncodeAndDecodeOfId($ids,'decode');
		if(Facility::where('id', $ids)->count()>0 && is_numeric($ids)) 
		{
			if (Facilitymanagecare::where('id', $id)->count()>0 && is_numeric($id)) 
			{
				$facility 		= Facility::with('facility_address')->where('id', $ids)->first();
				$managedcare 	= Facilitymanagecare::with('insurance', 'provider','provider.degrees')->where('id', $id)->first();
				$insurances 	= Insurance::orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();
			$insurance_id 	= '';
			$providers 		= Provider::getBillingAndREnderingProvider('yes'); 
			$provider_id 	= '';
                                return Response::json(array('status' => 'success', 'message' => '', 'data' => compact('managedcare', 'facility','insurances','insurance_id','providers','provider_id')));
			}
			else 
			{
				return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => null));
			}
		} 
		else 
		{
			return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
    }
	/*** End View of the Facility Managecare ***/ 
	
	function __destruct() 
	{
    }
}
