<?php namespace App\Http\Controllers\Patients;

use Auth;
use View;
use Input;
use Session;
use Request;
use Redirect;
use Validator;
use App\Http\Controllers\Api\BillingApiController as BillingApiController;
use App\Models\Patient;
use App\Models\Charges\BatchCharge as BatchCharge;
use App\Models\Payments\ClaimInfoV1;
use Route;
use App\Http\Controllers\Patients\Api\PatientApiController as PatientApiController;
use App\Http\Helpers\Helpers as Helpers;

class WalletController extends Api\WalletApiController {

	public function __construct()
	{		
    	View::share('heading', 'Wallet');
        View::share('selected_tab', 'wallet');
        View::share('heading_icon', 'fa-money');        	
	}

	public function index($patient_id)
	{		
		$api_response 		= 	$this->getIndexApi($patient_id);
		$api_response_data 	= 	$api_response->getData();		
		if($api_response_data->status == 'success')	
		{
		    $claims_lists 		= 	$api_response_data->data->claims_list;
			
			/*$pati_api_tab_obj				= new PatientApiController();
			$pass_id 						= Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
			$patient_tabs_api_response 		= $pati_api_tab_obj->getPatientTabsDetails($pass_id);
			$patient_tabs_api_res_data 		= $patient_tabs_api_response->getData();
			$patient_tabs_details			= $patient_tabs_api_res_data->data->patients;
			$patient_tabs_insurance_count	= $patient_tabs_api_res_data->data->patient_insurance_count;
			$patient_tabs_insurance_details	= json_decode(json_encode($patient_tabs_api_res_data->data->patient_insurance), true);*/
			
		    return view ( 'patients/wallet/wallet', compact ('claims_lists', 'patient_id','patient_tabs_details','patient_tabs_insurance_details','patient_tabs_insurance_count') );
		}
		else
		{
            return Redirect::to('/patients');
		}				
	}        
        
    public function lists($patient_id)
	{		
		$api_response 		= 	$this->getListsApi($patient_id);
		$api_response_data 	= 	$api_response->getData();		
		if($api_response_data->status == 'success')	
		{
		    $claims_lists 		= 	$api_response_data->data->claims_list;
			
			/*$pati_api_tab_obj				= new PatientApiController();
			$pass_id 						= Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
			$patient_tabs_api_response 		= $pati_api_tab_obj->getPatientTabsDetails($pass_id);
			$patient_tabs_api_res_data 		= $patient_tabs_api_response->getData();
			$patient_tabs_details			= $patient_tabs_api_res_data->data->patients;
			$patient_tabs_insurance_count	= $patient_tabs_api_res_data->data->patient_insurance_count;
			$patient_tabs_insurance_details	= json_decode(json_encode($patient_tabs_api_res_data->data->patient_insurance), true);*/
			
		    return view ( 'patients/wallet/list', compact ('claims_lists', 'patient_id','patient_tabs_details','patient_tabs_insurance_details','patient_tabs_insurance_count') );
		}
		else
		{
            return Redirect::to('/patients');
		}		
	}
        
    public function view($patient_id)
	{		

		$api_response 		= 	$this->getViewApi($patient_id);
		
		$api_response_data 	= 	$api_response->getData();		
		if($api_response_data->status == 'success')	
		{
		    $claims_lists 		= 	$api_response_data->data->claims_list;
			//$patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
			
			/*$pati_api_tab_obj				= new PatientApiController();
			$pass_id 						= Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
			$patient_tabs_api_response 		= $pati_api_tab_obj->getPatientTabsDetails($pass_id);
			$patient_tabs_api_res_data 		= $patient_tabs_api_response->getData();
			$patient_tabs_details			= $patient_tabs_api_res_data->data->patients;
			$patient_tabs_insurance_count	= $patient_tabs_api_res_data->data->patient_insurance_count;
			$patient_tabs_insurance_details	= json_decode(json_encode($patient_tabs_api_res_data->data->patient_insurance), true);*/
			
			$facilities = $api_response_data->data->facilities;
	        $providers = $api_response_data->data->providers;
	        $rendering_providers = $api_response_data->data->rendering_providers;
	        $referring_providers = $api_response_data->data->referring_providers;
	        $billing_providers = $api_response_data->data->billing_providers;
	        $insurances = $api_response_data->data->insurances;
		    return view ( 'patients/wallet/view', compact ('claims_lists', 'patient_id','patient_tabs_details','rendering_providers','billing_providers','facilities','providers','patient_tabs_insurance_details','patient_tabs_insurance_count') );
		}
		else
		{
            return Redirect::to('/patients');
		}		
	}

	public function create($patient_id, $claim_id=null)
	{
		$insurances_list = array();
        $api_response = $this->getCreateApi($patient_id, $claim_id);
        $api_response_data = $api_response->getData();
        $facilities = $api_response_data->data->facilities;
        $providers = $api_response_data->data->providers;
        $rendering_providers = $api_response_data->data->rendering_providers;
        $referring_providers = $api_response_data->data->referring_providers;
        $billing_providers = $api_response_data->data->billing_providers;
        $insurances = $api_response_data->data->insurances;
        //$insurance_data = $api_response_data->data->insurance_data;
        if(!empty($insurances))
        {
        	$insurances_list= array($insurances->id => $insurances->insurance_name);
        } 
        $insurance_data = $api_response_data->data->insurance_data;

        //dd($insurance_data);         
        $patients = $api_response_data->data->patient_detail;
        //dd($patients);
        $modifier = $api_response_data->data->modifier;
        $claims = $api_response_data->data->claims_list;
        $hold_option = $api_response_data->data->hold_options;
        $facility_id = '';
        $provider_id = '';
        $rendering_provider_id = '';
        $referring_provider_id = '';
        $billing_provider_id = '';
        $insurance_id = '';
        $view = 'patients/armanagement/create';
        if(strpos(Route::getCurrentRoute()->uri(), 'charges') !== false)
        {
        	$view = 'charges/charges/create1';
        }
        return view($view, compact('modifier','facilities','facility_id','','providers','rendering_providers','insurance_data','referring_providers','billing_providers','provider_id','rendering_provider_id','referring_provider_id','billing_provider_id','insurances','insurance_id', 'patient_id', 'patients', 'claims','hold_option', 'insurances_list'));
	}

	public function getselectbasedvalues($id, $value)
	{
      $api_response = $this->getApiselectbasedvalue($id, $value);
      $api_response_data = $api_response->getData();
      $data_needed = $api_response_data->data->data_needed;
      $pos_code = $api_response_data->data->code;
      $clai_no = 	isset($api_response_data->data->data_needed->clia_number)?$api_response_data->data->data_needed->clia_number:'';
      if($value == 'Facility')         
      {
	      $val = '<li><span>Facility</span> : '.$data_needed->facility_name.'</li>';                                            
	 	  $val.= '<li>'.$data_needed->facility_address->address1.', '.$data_needed->facility_address->city.', '.$data_needed->facility_address->pay_zip5.' - '.$data_needed->facility_address->pay_zip4.'</li> <li> <a href="'.url("/").'/facility/'.$id.'" target = "_blank" data-title="More Info"><i class="fa fa-info" data-placement="bottom" data-toggle="tooltip" data-original-title="More Details"></i></a></li>';
      }
      elseif($value == 'Attorney')
      {
      	 	$val = '<li><span>Adjuster Name</span> : '.$data_needed->attorney_adjuster_name.'</li>';
      }
      elseif($value == 'Employer')
      {
      	   $val = '<li><span>Name</span> : '.$data_needed->employer_name.'</li>';
      }
      else
      {
	      $val = '<li><span>Insurance</span> : '.$data_needed->insurance_name.'</li>';
	      $val.= '<li><span>Type</span> : '.(isset($data_needed->insurancetype->type_name)?$data_needed->insurancetype->type_name:'').'</li>';
	      
	      $val.= '<li>'.$data_needed->address_1.', '.$data_needed->city.' - '.$data_needed->state.', '.$data_needed->zipcode5.' - '.$data_needed->zipcode4.'</li> <li> <a href="'.url("/").'/insurance/'.$id.'" data-title="More Info" target = "_blank"><i class="fa fa-info" data-placement="bottom"  data-toggle="tooltip" data-original-title="More Details"></i></a></li>';
      }
      $data = $api_response_data->data->data.'|'.$val.'|'.$pos_code.'|'.$clai_no;
      return $data;
	}

	public function store(Request $request)
	{ 
        $request = $request::all();
		$api_response = $this->getStoreApi($request);
        $api_response_data = $api_response->getData();
		//$id=$api_response_data->data;
        if ($api_response_data->status == 'success') {
        	// /dd($request);
        	/*****Redirection for E-superbill batches starts******/
          	if(!empty($request['next_id']))
          	{
      			$patient_id = ClaimInfoV1::where('id', $request['next_id'])->value('patient_id');
      			return Redirect::to('patients/'.$patient_id.'/billing/create/'.$request['next_id'])->with('success', 'Claim details updated successfully');
          	}
          	elseif(isset($request['next_id']) && empty($request['next_id']))
          	{
          		return Redirect::to('charges')->with('success', $api_response_data->message);
          	}
          	/*****Redirection for E-superbill batches Ends******/
            if(isset($request['is_create'])&&$request['is_create'] == 1)
            {
          		return Redirect::to('patients/'.$request['patient_id'].'/billing/create')->with('success', $api_response_data->message);
            }
            elseif(isset($request['is_from_charge']) && $request['is_from_charge'] == 1 && empty($request['batch_id']))
            {
            	return Redirect::to('charges/create')->with('success', $api_response_data->message);
          	}
          	elseif(!empty($request['batch_id']))
          	{
          		$no_of_claims = BatchCharge::where('id', $request['batch_id'])->select('no_of_claims','claim_ids')->first(); 
          		/******** To restrict claims added into that batch once batch finished change its status to closed  Starts******/
          	if(count(explode(',',$no_of_claims['claim_ids'])) == $no_of_claims['no_of_claims'])
          	{
          		$this->ChageBatchStatusApi($request['batch_id']);
          		return Redirect::to('charges')->with('success', "Batch was successfully completed");
          	}  /******** To restrict claims added into that batch once batch finished change its status to closed  ends******/        
          	return Redirect::to('charges/create_batch/'.$request['batch_id'].'/batch')->with('success', $api_response_data->message);          
          }
            return Redirect::to('patients/'.$request['patient_id'].'/billing')->with('success', 'Claim details updated successfully');
        } else {
			return Redirect::to('patients/'.$request['patient_id'].'/billing')->with('error', $api_response_data->message);
        }
    }

    public function getaddmoredos($i)
    {
		$api_response = $this->getmodifier();
        $api_response_data = $api_response->getData();
        $modifier = $api_response_data->data->modifier;
        return view('patients/billing/appendrow', compact('i', 'modifier'));
    }

	public function show($patient_id)
	{		
		return view('patients/billing/show');		
	}

	 // Popup authorization add
	public function popupauthorization($patient_id)
	{
        $api_response = $this->getCreateauthorizationApi($patient_id);
        $api_response_data = $api_response->getData();
        $exist_authorizations = $api_response_data->data->authorization;
        $auth_insurances_detail = $api_response_data->data->auth_insurances_detail;
        $pos = $api_response_data->data->pos;
        $referring_providers =  $api_response_data->data->referring_providers;
        return view('patients/billing/popup_authorization',compact('exist_authorizations', 'auth_insurances_detail', 'pos', 'referring_providers', 'patient_id'));       
	}
	 // Popup authorization edit
	public function storeauthorization(Request $request)
	{		
       $request = $request::all();       
	   $api_response = $this->getStoreAuthApi($request);  	
       $api_response_data = $api_response->getData();
       if($api_response_data->status == 'success')
       {
       	   return $api_response_data->data->patient_id;
       } else
       {
           return $api_response_data->status;
       }
       
	}
	public function getproviderdetail($value, $type)
	{
		$api_response = $this->getProviderApiDetailpopup($value, $type);
		return $api_response;
	}
	 // Popup patient contacts store
	public function storepopupemployer(Request $request)
	{		
       $request = $request::all();
       $api_response = $this->getStoreEmployerApi(Request::all());
       $api_response_data = $api_response->getData();
       return $api_response_data->status;
       
	}
	public function storepopupprovider(Request $request)
	{		
       $request = $request::all();
       $api_response = $this->getStoreReferringProviderApi(Request::all());
       $api_response_data = $api_response->getData();
       return $api_response;
       
	}
	public function edit($id)
	{
        $facility_id = '';
        $provider_id = '';
        $rendering_provider_id = '';
        $referring_provider_id = '';
        $billing_provider_id = '';
        $insurance_id = ''; 
        return view('patients/billing/edit', compact('facilities','facility_id','providers','rendering_providers','referring_providers','billing_providers','provider_id','rendering_provider_id','referring_provider_id','billing_provider_id','insurances','insurance_id'));
	}
	public function paymentdetail($id){
		$api_response = $this->getPaymentDetail($id);
		$api_response_data = $api_response->getData();
		$detail = $api_response_data->value->detail;
		//dd($detail);
		return view('patients/billing/paymentpopup', compact('detail'));
	}
	public function cmsdetail($id){
		return view('patients/billing/cms');
	}
	public function destroy($id)
	{
		$api_response = $this->getDeleteApi($id);
		$api_response_data 	= 	$api_response->getData();
		$patient_id 	= 	$api_response_data->data->patient_id;
		if($api_response_data->status == 'success')
		{
			return Redirect::to('patients/'.$patient_id.'/billing')->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::back()->withErrors(['msg', 'Errorn on deleting records']);
		}
	}
}
