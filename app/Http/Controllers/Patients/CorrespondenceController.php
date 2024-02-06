<?php namespace App\Http\Controllers\Patients;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use View;
use Request;
use Redirect;
use App\Models\Template as Template;
use App\Http\Controllers\Patients\Api\PatientApiController as PatientApiController;

class CorrespondenceController extends Api\CorrespondenceApiController {
	
	 public function __construct() {
        View::share('heading', 'Patient');
        View::share('selected_tab', 'correspondence');
        View::share('heading_icon', 'fa-user');
    }
	 
	/*** Correspondence list page start ***/
	public function index($patient_id)
	{	
		$api_response = $this->getindexApi($patient_id);
        $api_response_data = $api_response->getData();
	    if($api_response_data->status == 'success') 
		{
			$patient_correspondence = $api_response_data->data->patient_correspondence;
			return view('patients/correspondence/correspondence', compact( 'patient_correspondence', 'patient_id'));
		}	
		else{
			return Redirect::to('patients')->with('message', $api_response_data->message);
		}
	}
	/*** Correspondence list page end ***/
	
	/*** Correspondence create page start ***/
	public function create($patient_id,$template_id)
	{
		$api_response = $this->getCreateApi($patient_id,$template_id);
        $api_response_data = $api_response->getData();
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('patients')->with('message', $api_response_data->message);
		}	
        if($api_response_data->status == 'success') 
		{
			$set_input_col = [];
			$templates = $api_response_data->data->template_details;
			$set_input_col = $api_response_data->data->set_input_col;
			$input_arr = $api_response_data->data->get_array_list;
			$temp_pair = $api_response_data->data->temp_pair_values;
			$total_pair = $api_response_data->data->all_pair_variable;
			return view('patients/correspondence/create', compact('templates','set_input_col','patient_id','input_arr','temp_pair','total_pair'));
		}	
		else
		{
			return Redirect::to('patients/'.$patient_id.'/correspondence')->with('error', $api_response_data->message);
		}
	}
	/*** Correspondence create page end ***/
	
	/*** Correspondence message update start ***/
	public function update(Request $request){
		$request = Request::all();
		$temp_id = $request['template_id'];
		$temp = Template::where('id',$temp_id)->first();
		$temp->content = $request['message'];
		$temp->save();

		$url = \URL::current();
		return Redirect::to($url);
	}
	/*** Correspondence message update start ***/

	/*** Correspondence mail send page start ***/
	public function send($id,Request $request)
	{
		$api_response = $this->getSendApi($id,Request::all());   
        $api_response_data = $api_response->getData();	
		if ($api_response_data->status == 'success') 
		{
			$corr_id = $api_response_data->data;	
			$api_response_data->data = 'patients/'.$id.'/correspondencehistory/'.$corr_id;	
			$data = json_encode($api_response_data);
			print_r($data);exit;
        } 
		else 
		{
			$data = json_encode($api_response_data);
			print_r($data);exit;
        }
	}
	/*** Correspondence mail send page end ***/
	
	/*** Correspondence mail show page start ***/
	public function show($patient_id,$id)
	{
		$api_response = $this->getshowApi($patient_id,$id);
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status == 'failure') {
			return Redirect::to('patients')->with('message', $api_response_data->message);
		}	
		
		if ($api_response_data->status == 'success') 
		{
			$content = $api_response_data->data->content;
			return view('patients/correspondence/show', compact('content','patient_id'));
		}	
		else
		{
			return Redirect::to('patients/'.$patient_id.'/correspondencehistories')->with('error', $api_response_data->message);
		}
	}
	/*** Correspondence mail show page end ***/
	
	/*** Template list page start ***/
	public function templateList($patient_id)
	{
            
		$api_response = $this->gettemplateListApi($patient_id);
		$api_response_data = $api_response->getData();
		if ($api_response_data->status == 'success') 
		{
			$templates = $api_response_data->data->templates;
			return view('patients/correspondence/template', compact('templates',"patient_id"));
		}	
		else
		{
			return Redirect::to('patients')->with('message', $api_response_data->message);
		}
	}
	/*** Templatelist page end ***/
}
