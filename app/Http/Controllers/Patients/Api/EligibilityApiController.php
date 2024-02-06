<?php namespace App\Http\Controllers\Patients\Api;

use App\Http\Controllers\Controller;
use App\Models\Provider as Provider; 
use App\Models\Patients\Patient as Patient;
use App\Models\Facility as Facility;
use App\Models\Insurance as Insurance;
use App\Models\Insuranceclass as Insuranceclass;
use App\Models\Insurancetype as Insurancetype;
use App\Models\Language as Language;
use App\Models\Ethnicity as Ethnicity;
use App\Models\Religion as Religion;
use App\Http\Controllers\EligibilityReportController as EligibilityReportController;
use App\Models\AddressFlag as AddressFlag;
use App\Models\State as State;
use App\Models\Zipcode as Zipcode;
use App\Models\Pos as Pos;
use Input;
use File;
use Auth;
use Response;
use Request;
use Validator;
use Schema;
use DB;
use Lang;
class EligibilityApiController extends Controller {
	public function getIndexApi()
	{
        $patients = Patient::with('provider_details','facility_details','ethnicity_details','insurance_details')->get();
        return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('patients')));
	}
		
	public function getCreateApi()
	{
		
	}
	public function getStoreApi($request='')
	{
		
	}
	public function getEditApi($id)
	{
		
	}

	public function getUpdateApi($id, $request='')
	{
		
	}

	public function getDeleteApi($id)
	{
		
	}

	public function getShowApi($id)
	{
        if(Patient::where('id', $id )->count())
        {
            $patients = Patient::with('provider_details','facility_details','languages_details','ethnicity_details','religion_details','insurance_details','insurance_class','insurance_type')->where('id', $id )->first();  
			
			$general_address_flag = AddressFlag::getAddressFlag('patients',$patients->id,'general_information');
            $addressFlag['general'] = $general_address_flag; 
		                                    
			$language = Language::orderBy('language', 'ASC')->pluck('language', 'id')->all();
			$language_id = $patients->language_id;
			$ethnicity = Ethnicity::orderBy('name', 'ASC')->pluck('name', 'id')->all();
			$ethnicity_id = $patients->ethnicity_id;
			$religion = Ethnicity::orderBy('name', 'ASC')->pluck('name', 'id')->all();
			$religion_id = $patients->religion_id;
			$facility = Facility::orderBy('facility_name', 'ASC')->pluck('facility_name', 'id')->all();
			$facility_id = $patients->facility_id;
			$facility = Facility::orderBy('facility_name', 'ASC')->pluck('facility_name', 'id')->all();
			$facility_id = $patients->facility_id;
			$insurance = Insurance::orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();
			$insurance_id = $patients->insurance_id;
			
			$insuranceclass = Insuranceclass::orderBy('insurance_class', 'ASC')->pluck('insurance_class', 'id')->all();
			$insuranceclass_id = $patients->insurance_class_id;
			$insurancetype = Insurancetype::orderBy('type_name', 'ASC')->pluck('type_name', 'id')->all();
			$insurancetype_id = $patients->insurance_type_id;
			
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('patients','addressFlag','language','language_id','ethnicity','ethnicity_id','religion','religion_id','facility','facility_id','insurance_id','insurance','insuranceclass','insuranceclass_id','insurancetype','insurancetype_id')));
        }
        else
        {
         	return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
        }
	}
        
    public function getShow1Api($id)
	{
        if(Patient::where('id', $id )->count())
        {
            $patients = Patient::with('provider_details','facility_details','languages_details','ethnicity_details','religion_details','insurance_details','insurance_class','insurance_type')->where('id', $id )->first();  
			
			$general_address_flag = AddressFlag::getAddressFlag('patients',$patients->id,'general_information');
            $addressFlag['general'] = $general_address_flag; 
		                                    
			$language = Language::orderBy('language', 'ASC')->pluck('language', 'id')->all();
			$language_id = $patients->language_id;
			$ethnicity = Ethnicity::orderBy('name', 'ASC')->pluck('name', 'id')->all();
			$ethnicity_id = $patients->ethnicity_id;
			$religion = Ethnicity::orderBy('name', 'ASC')->pluck('name', 'id')->all();
			$religion_id = $patients->religion_id;
			$facility = Facility::orderBy('facility_name', 'ASC')->pluck('facility_name', 'id')->all();
			$facility_id = $patients->facility_id;
			$facility = Facility::orderBy('facility_name', 'ASC')->pluck('facility_name', 'id')->all();
			$facility_id = $patients->facility_id;
			$insurance = Insurance::orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();
			$insurance_id = $patients->insurance_id;
			
			$insuranceclass = Insuranceclass::orderBy('insurance_class', 'ASC')->pluck('insurance_class', 'id')->all();
			$insuranceclass_id = $patients->insurance_class_id;
			$insurancetype = Insurancetype::orderBy('type_name', 'ASC')->pluck('type_name', 'id')->all();
			$insurancetype_id = $patients->insurance_type_id;
			
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('patients','addressFlag','language','language_id','ethnicity','ethnicity_id','religion','religion_id','facility','facility_id','insurance_id','insurance','insuranceclass','insuranceclass_id','insurancetype','insurancetype_id')));
        }
        else
        {
         	return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
        }
	}
        
        
    public function getShow2Api($id)
	{
        if(Patient::where('id', $id )->count())
        {
            $patients = Patient::with('provider_details','facility_details','languages_details','ethnicity_details','religion_details','insurance_details','insurance_class','insurance_type')->where('id', $id )->first();  
			
			$general_address_flag = AddressFlag::getAddressFlag('patients',$patients->id,'general_information');
            $addressFlag['general'] = $general_address_flag; 
		                                    
			$language = Language::orderBy('language', 'ASC')->pluck('language', 'id')->all();
			$language_id = $patients->language_id;
			$ethnicity = Ethnicity::orderBy('name', 'ASC')->pluck('name', 'id')->all();
			$ethnicity_id = $patients->ethnicity_id;
			$religion = Ethnicity::orderBy('name', 'ASC')->pluck('name', 'id')->all();
			$religion_id = $patients->religion_id;
			$facility = Facility::orderBy('facility_name', 'ASC')->pluck('facility_name', 'id')->all();
			$facility_id = $patients->facility_id;			
			$insurance = Insurance::orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();
			$insurance_id = $patients->insurance_id;
			
			$insuranceclass = Insuranceclass::orderBy('insurance_class', 'ASC')->pluck('insurance_class', 'id')->all();
			$insuranceclass_id = $patients->insurance_class_id;
			$insurancetype = Insurancetype::orderBy('type_name', 'ASC')->pluck('type_name', 'id')->all();
			$insurancetype_id = $patients->insurance_type_id;
			
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('patients','addressFlag','language','language_id','ethnicity','ethnicity_id','religion','religion_id','facility','facility_id','insurance_id','insurance','insuranceclass','insuranceclass_id','insurancetype','insurancetype_id')));
        }
        else
        {
         	return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
        }
	}        
        
 	public function getPatientsprofile($id, $request='')
	{ 
		
		if($request == '')
			$request = Request::all();

		$validator = Validator::make($request, Patient::$rules, Patient::$messages);

		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			if($id == '')
			{
				$patients->create($request);
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>''));
			}
			else
			{	
				$patients = Patient::findOrFail($id);
				$patients->update($request);
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));
			}
		}
	}
}
