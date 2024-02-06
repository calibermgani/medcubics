<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Auth;
use Input;
use Route;
use Response;
use Request;
use Validator;
use App\Models\Facilityoverride as Facilityoverride;
use App\Models\Facility as Facility;

use App\Insurance as Insurance;
use App\Practice as Practice; 
use App\Provider as Provider; 
use App\IdQualifier as IdQualifier;
use DB;


class FacilityoverridesApiController extends Controller {
	
	public function getIndexApi($id, $export = "")
	{	
		$facility = Facility::with('facility_address')->where('id',$id)->first();
		$overrides = Facilityoverride::with('insurance','provider','id_qualifier')->where('facilities_id', $id)->get();		
		//Export
			if($export != "")
				{
					$exportparam 	= 	array(
					'filename'=>	'facility_overrides',
					'heading'=>	$facility->facility_name,
					'fields' =>	array(
					'insurances_id' =>	array('table'=>'insurance' ,	'column' => 'insurance_name' ,	'label' => 'Insurance Name'),
					'providers_id' =>	array('table'=>'provider'	,	'column' => ['first_name','last_name']		,	'label' => 'Provider Name'),
					'tax_id' => 'Tax Id',
					'npi' => 'NPI',
					'provider_id'	=>	'Provider ID',
					'id_qualifiers_id' =>	array('table'=>'id_qualifier' ,	'column' => 'id_qualifier_name' ,	'label' => 'ID Type'),
					));
					$export = new CommonExportApiController();
					return $export->generateExports($exportparam, $overrides);
				}
		    ////
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('overrides','facility')));
	}
	
	public function getCreateApi($id)
	{			
		$facility = Facility::with('facility_address')->where('id',$id)->first();
		$facilities = Facility::where('status', 'Active')->orderBy('facility_name','ASC')->pluck('facility_name','id')->all();
		$facility_id='';
		$insurances = Insurance::pluck('insurance_name','id')->all();
		$insurance_id ='';
		$practices = Practice::all();
		$providers = Provider::select(DB::raw("CONCAT(id,';',etin_type_number,';', npi) AS id, CONCAT(last_name,' ',first_name) AS provider_name"))->where('status', '=', 'Active')->orderBy('provider_name','ASC')->pluck('provider_name', 'id')->all();
		$provider_id = '';
		$id_qualifiers = IdQualifier::pluck('id_qualifier_name','id')->all();
		$id_qualifiers_id = '';
				
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('facilities','facility_id','insurances','insurance_id','practices','providers','provider_id','id_qualifiers','id_qualifiers_id','facility')));
	}
	
	
	public function getStoreApi()
	{
	
		$request = Request::all();
		
		$validator = Validator::make($request, Facilityoverride::$rules, Facilityoverride::$messages);
		if ($validator->fails())
			{

				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
		else
			{	
				Facilityoverride::create($request);
				
				return Response::json(array('status'=>'success', 'message'=>'Facility overrides added successfully','data'=>''));					
			}
	}
	public function getEditApi($ids,$id)
	{
		$facility = Facility::with('facility_address')->where('id',$ids)->first();
		$practices = Practice::all();
		$overrides = FacilityOverride::find($id);
		$facilities = Facility::where('status', 'Active')->orderBy('facility_name','ASC')->pluck('facility_name','id')->all();
		$facility_id=$overrides->facilities_id;
		$insurances = Insurance::pluck('insurance_name','id')->all();
		$insurance_id =$overrides->insurances_id;
		$provider_id = $overrides->providers_id.';'.$overrides->tax_id.';'.$overrides->npi;       
		$providers = Provider::select(DB::raw("CONCAT(id,';',etin_type_number,';', npi) AS id, CONCAT(last_name,' ',first_name) AS provider_name"))->where('status', '=', 'Active')->orderBy('provider_name','ASC')->pluck('provider_name', 'id')->all();
		$id_qualifiers = IdQualifier::pluck('id_qualifier_name','id')->all();
		$id_qualifiers_id = $overrides->id_qualifiers_id;
				
	
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('practices','overrides','facilities','facility_id','insurances','insurance_id','provider_id','providers','id_qualifiers','id_qualifiers_id','facility')));
	}
	
	
public function getUpdateApi($facility_id, $id, $request='')
	{
		if($request == '')
			$request = Request::all();

		$validator = Validator::make($request, Facilityoverride::$rules, Facilityoverride::$messages);

		if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
		else
			{	
				$override = FacilityOverride::findOrFail($id); 
        		$override->update(Request::all());
				return Response::json(array('status'=>'success', 'message'=>'Facility overrrides updated successfully','data'=>''));					
			}
	}
	
	public function getDeleteApi($type,$id)
	{
		FacilityOverride::where('id',$id)->delete();
		return Response::json(array('status'=>'success', 'message'=>'Facility overrides deleted successfully','data'=>''));	
	}
	
	public function getShowApi($ids, $id)
	{
		if(FacilityOverride::where('id', $id )->count())
		{		
			$facility = Facility::with('facility_address')->where('id',$ids)->first();
			
			$overrides = FacilityOverride::with('insurance','provider','id_qualifier')->where('id',$id)->first();
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('overrides','facility')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>'No facility overrides details found.','data'=>null));
		}
	}
	
	function __destruct() 
	{
    }
}
