<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Provider as Provider;
use App\Models\Providermanagecare;
use App\Models\Insurance;
use Auth;
use Response;
use Request;
use Validator;
use DB;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Document as Document;
use Lang;
use Illuminate\Support\Collection;

class ProviderManagedcareApiController extends Controller 
{

	public function getIndexApi($providerid, $export = "")
	{
		$providerid = Helpers::getEncodeAndDecodeOfId($providerid,'decode');
		if((isset($providerid) && is_numeric($providerid)) && Provider::where('id', $providerid)->count())
		{
			$provider 			= 	Provider::with('degrees')->where('id',$providerid)->first();
			$managecare 		= 	Providermanagecare::with('insurance')->where( 'providers_id', $providerid)->get ();
			if($export != "")
			{
				$exportparam 	= 	array(
									'filename'	=>	'Provider_Managecare',
									'heading'	=>	$provider->last_name . $provider->first_name,
									'fields' 	=>	array(
												'insurance_id' 	=>	array('table'=>'insurance' ,	'column' => 'insurance_name' ,	'label' 		=> 'Insurance'),
												'provider_id' 	=> 'Provider ID',
												'enrollment' 	=> 'Credential',
												'entitytype'	=>	'Entity Type',
												'effectivedate' =>	'Effective Date',
												'terminationdate' =>	'Termination Date',
												'feeschedule' 	=>	'Fee Schedule',
												)
									);
                                        
                $callexport = new CommonExportApiController();
                return $callexport->generatemultipleExports($exportparam, $managecare, $export); 
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
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('provider', 'managecare')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
		
	public function getCreateApi($providerid)
	{
		$providerid = Helpers::getEncodeAndDecodeOfId($providerid,'decode');
		if((isset($providerid) && is_numeric($providerid)) && Provider::where('id', $providerid)->count())
		{
			$insurances 	= 	Insurance::pluck( 'insurance_name', 'id' )->all(); 	
			$provider		=	Provider::with('degrees')->where('id', $providerid)->first();
			//Encode ID for provider
			$temp = new Collection($provider);
			$temp_id = $temp['id'];
			$temp->pull('id');
			$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
			$temp->prepend($temp_encode_id, 'id');
			$data = $temp->all();
			$provider = json_decode(json_encode($data), FALSE);
			//Encode ID for provider
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('provider', 'insurances')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}		
	}
	
	public function getStoreApi($providerid)
	{
		$request 	= Request::all();
		$providerid = Helpers::getEncodeAndDecodeOfId($providerid,'decode'); 
		if((isset($providerid) && is_numeric($providerid)) && Provider::where('id', $providerid)->count())
		{
			$request['providers_id'] = Helpers::getEncodeAndDecodeOfId($request['providers_id'],'decode');
			$validator 	= Validator::make($request, Providermanagecare::$rules, Providermanagecare::messages());
			if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{			
				if($request['effectivedate'] != '')
					$request['effectivedate'] = date('Y-m-d',strtotime($request['effectivedate']));
				if($request['terminationdate'] != '')
					$request['terminationdate'] = date('Y-m-d',strtotime($request['terminationdate']));
							
				$result = Providermanagecare::create($request);
				if(isset($request['temp_doc_id']))
				{
					if($request['temp_doc_id']!="") Document::where('temp_type_id', '=', $request['temp_doc_id'])->update(['type_id' => $result->id,'temp_type_id' => '']);
				}
				$user = Auth::user ()->id;
				$result->created_by = $user;
				$result->save ();
				$resultid = Helpers::getEncodeAndDecodeOfId($result->id,'encode');
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.managecare_create_msg"),'data'=>$resultid));
			}
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	
	public function getShowApi($ids,$id)
	{
		$ids = Helpers::getEncodeAndDecodeOfId($ids,'decode');
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Providermanagecare::where('id', $id )->count())
		{
            if(Provider::where('id', $ids)->count()) 
			{
				$provider = Provider::with('degrees')->where('id', $ids)->first();
				//Encode ID for provider
				$temp = new Collection($provider);
				$temp_id = $temp['id'];
				$temp->pull('id');
				$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
				$temp->prepend($temp_encode_id, 'id');
				$data = $temp->all();
				$provider = json_decode(json_encode($data), FALSE);
				//Encode ID for provider
				$managedcare = Providermanagecare::with('Insurance')->where('id',$id)->first();
				//Encode ID for managedcare
				$temp = new Collection($managedcare);
				$temp_id = $temp['id'];
				$temp->pull('id');
				$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
				$temp->prepend($temp_encode_id, 'id');
				$data = $temp->all();
				$managedcare = json_decode(json_encode($data), FALSE);
				//Encode ID for managedcare
				
				return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('managedcare','provider')));
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
	
	public function getEditApi($providerid,$id)
	{
		$providerid = Helpers::getEncodeAndDecodeOfId($providerid,'decode');
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if((isset($providerid) && is_numeric($providerid)) && Provider::where('id', $providerid)->count())
		{
			if(Providermanagecare::where('id', $id)->where('providers_id', $providerid)->count() && (isset($id) && is_numeric($id)))
			{
				$managecare 			= 	Providermanagecare::findOrFail($id);
                $insurances 			= 	Insurance::pluck( 'insurance_name', 'id' )->all();
				$provider 				= 	Provider::with('degrees')->where('id', $providerid)->first();				
                $insurance_id 			= 	$managecare->insurance_id;
				//Encode ID for provider
				$temp = new Collection($provider);
				$temp_id = $temp['id'];
				$temp->pull('id');
				$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
				$temp->prepend($temp_encode_id, 'id');
				$data = $temp->all();
				$provider = json_decode(json_encode($data), FALSE);
				//Encode ID for provider
				
				return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('managecare','insurances', 'provider', 'insurance_id')));
			}
			else
			{
				return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
			}
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}

	public function getUpdateApi($providerid, $id)
	{
		$providerid = Helpers::getEncodeAndDecodeOfId($providerid,'decode');
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		
		if((isset($providerid) && is_numeric($providerid)) && Provider::where('id', $providerid)->count())
		{
			if(Providermanagecare::where('id', $id)->where('providers_id', $providerid)->count() && (isset($id) && is_numeric($id)))
			{
				$request 	= Request::all();
				$request['providers_id'] = Helpers::getEncodeAndDecodeOfId($request['providers_id'],'decode');
				//dd($request);
				$validator 	= Validator::make($request, Providermanagecare::$rules, Providermanagecare::messages());
				if($validator->fails())
				{
					$errors = $validator->errors();
					return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
				}
				else
				{		
					$providermanagecare = Providermanagecare::findOrFail($id);			
					if($request['effectivedate'] != '')   
						$request['effectivedate'] = date('Y-m-d',strtotime($request['effectivedate']));
					if($request['terminationdate'] != '') 
						$request['terminationdate'] = date('Y-m-d',strtotime($request['terminationdate']));
					$providermanagecare->update($request);
					$user = Auth::user ()->id;
					$providermanagecare->updated_by = $user;
					$providermanagecare->save ();
					return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.managecare_update_msg"),'data'=>''));
				}
			}
			else
			{
				return Response::json(array('status'=>'failure_care', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
			}
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}

	public function getDeleteApi($providerid,$id)
	{
		$providerid = Helpers::getEncodeAndDecodeOfId($providerid,'decode');
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if((isset($providerid) && is_numeric($providerid)) && Provider::where('id', $providerid)->count())
		{
			if(Providermanagecare::where('id', $id)->where('providers_id', $providerid)->count() && (isset($id) && is_numeric($id)))
			{
				Providermanagecare::where('id',$id)->delete();
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.managecare_delete_msg"),'data'=>''));
			}
			else
			{
				return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
			}
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	
	function __destruct() 
	{
    }
	
}