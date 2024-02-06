<?php namespace App\Http\Controllers\Patients;
use View;
use Request;
use Response;
use Redirect;
use Config;

class SuperBillController extends Api\SuperBillApiController {

	public function __construct()
	{
		View::share('heading', 'Patient');
		View::share('selected_tab', 'billing');
		View::share('heading_icon', Config::get('cssconfigs.Practicesmaster.user'));
	}
	
	public function create($patient_id){
		
		$api_response 			= $this->getCreateApi($patient_id);
		$api_response_data 		= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$providers				= $api_response_data->data->providers;
			$existing_icds_arr		= json_decode(json_encode($api_response_data->data->existing_icds_arr), true);
			$existing_cpts_arr		= json_decode(json_encode($api_response_data->data->existing_cpts_arr), true);
			$claims_list			= $api_response_data->data->claims_list;
			$insurance_arr			= $api_response_data->data->insurance_arr;
			$patients 				= $api_response_data->data->patient_detail;
			return view ( 'patients/superbill/create', compact ( 'patient_detail','patient_id','providers','existing_icds_arr','existing_cpts_arr','claims_list','insurance_arr','patients') );
		}
		else
		{
			return Redirect::to('patients')->with('message',$api_response_data->message);
		} 	
	}
	
	public function store(Request $request){
		
		$api_response 		= $this->getStoreApi();
		$api_response_data 	= $api_response->getData();
		$patient_id			= $api_response_data->patient_id;
		return Response::json(array('status'=>'success','message'=>'Superbill claim added successfully','patient_id'=>$patient_id));
		//return Redirect::to('patients/'.$patient_id.'/superbill/create')->with('success', $api_response_data->message);
		
	}
	

}
