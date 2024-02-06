<?php 
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Provider as Provider;
use App\Models\Insurance as Insurance;
use App\Models\IdQualifier as IDQualifier;
use App\Models\Provideroverride as ProviderOverride;
use DB;
use Auth;
use Response;
use Request;
use Validator;
use App\Models\Document as Document;

class ProviderOverridesApiController extends Controller 
{

	public function getIndexApi($providerid, $export = "")
	{
		if((isset($providerid) && is_numeric($providerid)) && Provider::where('id', $providerid)->count())
		{
			$provider = Provider::where('id', $providerid)->first();
			$overrides = ProviderOverride::with('provider_override','id_qualifier')->where('providers_id', $providerid)->orderBy('id','asc')->get();
			//Export 
			if($export != "")
			{
				$exportparam 	= 	array(
									'filename'=>	'provider_overrides',
									'heading'=>	$provider->last_name.$provider->first_name,
									'fields' =>	array(
												'provider_name' =>array('table'=>'provider_override' ,'column' => 'provider_name' ,'label' => 'Provider Name'),
												'npi' =>array('table'=>'provider_override' ,'column' => 'npi' ,'label' => 'Provider NPI'),
												'tax_id' =>array('table'=>'provider_override' ,'column' => 'etin_type_number' ,'label' => 'Provider Tax ID'),
									'provider_id'	=>	'Provider ID',
									'id_qualifiers_id' =>	array('table'=>'id_qualifier' ,	'column' => 'id_qualifier_name' ,	'label' => 'ID Type'),
									)
								);
				$callexport = new CommonExportApiController();
				return $callexport->generatemultipleExports($exportparam, $overrides, $export);
			}
		    ////
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('provider', 'overrides')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>'Invalid Provider ID.','data'=>null));
		}		
	}
		
	public function getCreateApi($providerid)
	{
        $count = ProviderOverride::checkHasOverrideOrNot($providerid);
        if($count == 0)
        {
			if((isset($providerid) && is_numeric($providerid)) && Provider::where('id', $providerid)->count())
			{
                $provider = Provider::find($providerid);
				$id_qualifiers = IDQualifier::pluck('id_qualifier_name','id')->all();
				$provider_override = Provider::select(DB::raw("CONCAT(id,';',etin_type_number,';', npi) AS id, CONCAT(last_name,' ',first_name) AS provider_name"))->where('status','Active')->where('id','!=',$provider->id)->pluck('provider_name', 'id')->all();
				$provider_override_id = ';;';
				$id_qualifiers_id	= '';
				return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('provider', 'id_qualifiers', 'provider_override','provider_override_id','id_qualifiers_id')));
			}
			else
			{
				return Response::json(array('status'=>'error', 'message'=>'Invalid Provider ID.','data'=>null));
			}
        }
        else 
			return Response::json(array('status'=>'error_create', 'message'=>'Can\'t add more than one overrides','data'=>null));
	}
	
	public function getStoreApi($providerid)
	{
        $count = ProviderOverride::checkHasOverrideOrNot($providerid);
        if($count == 0)
        {
			$request = Request::all();
			$validator = Validator::make($request, ProviderOverride::$rules , ProviderOverride::$messages);
			if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{
				$result = ProviderOverride::create($request);
				if(isset($request['temp_doc_id']))
				{
					if($request['temp_doc_id']!="") Document::where('temp_type_id', '=', $request['temp_doc_id'])->update(['type_id' => $result->id,'temp_type_id' => '']);
				}
				$user = Auth::user ()->id;
				$result->created_by = $user;
				$result->save ();
				return Response::json(array('status'=>'success', 'message'=>'Provider override added successfully','data'=>$result->id));
			}
        }
        else 
			return Response::json(array('status'=>'error_create', 'message'=>'Can\'t add more than one overrides','data'=>null));
	}

	public function getShowApi($ids, $id)
	{ 
		if(ProviderOverride::where('id', $id )->count())
		{		
			if(Provider::where('id', $ids)->count()) 
			{
				$provider	=	Provider::where('id', $ids)->first();		
				$overrides 	= ProviderOverride::with('provider_override','id_qualifier')->where('id',$id)->first();
				return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('overrides','provider')));
			}
			else
			{
                return Response::json(array('status'=>'failure', 'message'=>'No provider details found.','data'=>null));    
            }
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>'No provider overrides details found.','data'=>null));
		}
	}
	
	public function getEditApi($providerid,$id)
	{
		if((isset($id) && is_numeric($id))  && (isset($id) && is_numeric($id)) && ProviderOverride::where('id', $id)->count())
		{
			if(Provider::where('id', $providerid)->count())
			{
				$provider 			=  Provider::where('id', $providerid)->first();
				$overrides 			=  ProviderOverride::findOrFail($id);
                $id_qualifiers_id 	= $overrides->id_type_id;				
				$id_qualifiers 		= IDQualifier::pluck('id_qualifier_name','id')->all();                                
				$provider_override 	= Provider::select(DB::raw("CONCAT(id,';',etin_type_number,';', npi) AS id, CONCAT(last_name,' ',first_name) AS provider_name"))->where('status','Active')->where('id','!=',$provider->id)->pluck('provider_name', 'id')->all();
                $get_provider_override_details = Provider::where('id',$overrides->provider_override_id)->first();
                $provider_override_id 	= $overrides->provider_override_id.';'.$get_provider_override_details->etin_type_number.';'.$get_provider_override_details->npi;
				return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('provider', 'overrides', 'id_qualifiers', 'provider_override', 'id_qualifiers_id', 'provider_override_id')));
			}
			else
			{
				return Response::json(array('status'=>'failure', 'message'=>'Invalid Provider Details.','data'=>null));
			}
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>'Invalid Provider ID.','data'=>null));
		}
	}

	public function getUpdateApi($providerid,$id)
	{ 
		$request 	= Request::all();
		$validator = Validator::make($request, ProviderOverride::$rules, ProviderOverride::$messages);
		if ($validator->fails())
		{ 
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{ 
			$overrides = ProviderOverride::findOrFail($id);
			$overrides->update($request);
			$user = Auth::user ()->id;
			$overrides->updated_by = $user;
			$overrides->save ();
			return Response::json(array('status'=>'success', 'message'=>'Provider override updated successfully','data'=>''));
		}
	}

	public function getDeleteApi($providerid,$id)
	{
		if((isset($providerid) && is_numeric($providerid))  && (isset($id) && is_numeric($id)) && Provider::where('id', $providerid)->count())
		{
			if(ProviderOverride::where('id', $id )->where('providers_id', $providerid)->count())
			{
				$result = ProviderOverride::find($id)->delete();
				if($result == 1)
				{
					return Response::json(array('status'=>'success', 'message'=>'Overrides deleted successfully','data'=>''));
				}	
				else
				{
					return Response::json(array('status'=>'error', 'message'=>'Unable to delete the provider.','data'=>''));
				}
			}
			else
			{
				return Response::json(array('status'=>'error', 'message'=>'Invalid Overrides Details.','data'=>null));
			}
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>'Invalid Provider ID.','data'=>null));
		}
	}
	
    public function checkHasOverrideOrNotApi($provider_id)
    {
        $count = ProviderOverride::checkHasOverrideOrNot($provider_id);
        if($count == 0)
        {
            return Response::json(array('status'=>'success', 'message'=>null,'data'=>null));
        }
        else
		{
			return Response::json(array('status'=>'error', 'message'=>'Can\'t add more than one overrides','data'=>null));
		}
    }
	
	function __destruct() 
	{
    }
	
}