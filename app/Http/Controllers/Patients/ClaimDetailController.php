<?php namespace App\Http\Controllers\Patients;

use App\Http\Controllers\Controller;
use View;
use Request;
use Redirect;
use App\Models\Facility as Facility;
use App\Http\Controllers\Charges\Api\ChargeV1ApiController;

class ClaimDetailController extends Api\ClaimDetailApiController {

    public function __construct() {
        View::share('heading', 'Patient');
        View::share('selected_tab', 'patients');
        View::share('heading_icon', 'users');
    }
	public function create($patient_id)
	{
        // Update by baskar - 27/02/19 - Start(last update 05/03/19)
        // Get Clia_number
        $exp = explode('&&&', $patient_id);
        if(!empty($exp)){
            $patient_id = $exp[0];
            $facility_cpt = explode('_', $exp[1]);
            $facility_id = $facility_cpt[0];
            unset($facility_cpt[0]);
            $cpt = array_filter($facility_cpt);
            if($facility_id!='' && !empty($cpt)){
                        $cpts = \App\Models\Cpt::whereIn('cpt_hcpcs',$cpt)->where('required_clia_id','Yes')->get();
                        if(count($cpts)>0){
                            $clia_no = Facility::where('id',$facility_id)->pluck('clia_number')->first();
                        }else{
                            $clia_no = '';
                        }
            }else{
                $clia_no = '';
            }
        }else{
            $clia_no = '';
        }
        // Update by baskar - 27/02/19 - End
		$api_response = $this->getCreateApi($patient_id);
        $api_response_data = $api_response->getData();       
        $provider = $api_response_data->data->providers;
        $patient_lists = $api_response_data->data->patient_lists;
        $patient_attorney = $api_response_data->data->patient_attorney;
        $facilities = $api_response_data->data->facilities;
        $claimdetail = $api_response_data->data->claimdetail;
        $state = $api_response_data->data->state;
		return view('patients/claimdetail/create', compact('provider', 'patient_lists', 'patient_attorney', 'facilities', 'claimdetail', 'state','clia_no'));
	}
	public function store(Request $request)
	{
        $request = $request::all();
        /*dd($request);
    	$api_response = $this->getStoreApi($request);*/
        $chargeV1 = new  ChargeV1ApiController();
        $api_response = $chargeV1->createClaimAddDetails($request);
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'success') 
        {
           if(Request::ajax()){
            return json_encode($api_response_data);
           }
           return $api_response_data->data;
        } 
        else 
        {
			return 'failiur';
        }
	}
	public function edit($id,$val='')
	{
        $api_response = $this->getEditApi($id);
        $api_response_data = $api_response->getData();
        $provider = $api_response_data->data->providers;
        $patient_lists = $api_response_data->data->patient_lists;
        $patient_attorney = $api_response_data->data->patient_attorney;
        $facilities = $api_response_data->data->facilities;
        $claimdetail = $api_response_data->data->claimdetail;
        $state = $api_response_data->data->state;
        // Update by baskar - 05/03/19 - Start
        // Get Clia_number
        if(!empty($_GET['val']) && empty($claimdetail->box23_type)){
            $exp = explode('_', $_GET['val']);
            if(!empty($exp)){
                $facility_id = $exp[0];
                unset($exp[0]);
                $cpt = array_filter($exp);
                if($facility_id!='' && !empty($cpt)){
                            $cpts = \App\Models\Cpt::whereIn('cpt_hcpcs',$cpt)->where('required_clia_id','Yes')->get();
                            if(count($cpts)>0){
                                $clia_no = Facility::where('id',$facility_id)->pluck('clia_number')->first();
                            }else{
                                $clia_no = '';
                            }
                }else{
                    $clia_no = '';
                }
            }
        }else{
            $clia_no = '';
        }
        // Update by baskar - 05/03/19 - End
		return view('patients/claimdetail/edit', compact('provider', 'patient_lists', 'patient_attorney', 'facilities', 'claimdetail', 'state','clia_no'));
	}
	public function update($id, Request $request)
	{
		$request = $request::all();
		$api_response = $this->getUpdateApi($id, $request);
		$api_response_data 	= 	$api_response->getData();		
		if ($api_response_data->status == 'success') 
		{
           	if(Request::ajax()){
            	return json_encode($api_response_data);
           	}
           	return $api_response_data->data;
        } 
        else 
        {
			if(Request::ajax()){
            	return json_encode($api_response_data);
           	}
           	return $api_response_data->data;
        }
	}
}
