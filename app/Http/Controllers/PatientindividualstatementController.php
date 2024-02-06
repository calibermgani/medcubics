<?php namespace App\Http\Controllers;

use Request;
use Redirect;
use Auth;
use View;
use Config;

class PatientindividualstatementController extends Api\PatientindividualstatementApiController 
{
	public function __construct()
	{ 
		View::share ( 'heading', 'Practice' );  
		View::share ( 'selected_tab', 'patientindividualstatement' ); 
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    } 
	/*** lists page Starts ***/
	public function index()
	{		
		$api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
		$psettings =$api_response_data->data->psettings;
		return view('practice/patientstatementsettings/individualstatement',  compact('psettings'));
	}
	/*** Lists Function Ends ***/
	
	/*** Process patient statement based on type (preview, send statement, email statement) ***/
	public function getindividualtype($patientid,$type)
	{
		// Separate mode and payment message.
		$get_type = explode('::',$type);
		$paymentmessage = '';
		if(count($get_type)>1)
		{
			$type = $get_type[0];
			$paymentmessage = $get_type[1];
		}
		
		$api_response = $this->getTypeApi($patientid,$type,$paymentmessage);
		$api_response_data = $api_response->getData();
		if($api_response_data->status== 'failure')
		{
			$arr = array("status" => "failure", "msg" => $api_response_data->message);
		}
		else
		{
			$arr = array("status" => "success", "msg" => $api_response_data->message, "filename" => $api_response_data->filename);
		}
		echo json_encode($arr);
	}
	
	// Download the patient statement.
	public function getindividualdownload($filename,$id,$existname)
	{
		$api_response = $this->getIndividualDownloadApi($filename,$id,$existname);
	}
	
	// Search patient based on patient name.
	public function getPatientList($patientname)
	{
		$api_response = $this->getPatientListApi($patientname);
		$result = Request::all();
		$api_response_data = $api_response->getData();
		$patients_arr =$api_response_data->data->patients_arr;
		$patient_balance_arr =$api_response_data->data->patient_balance;
		$patient_balance = json_decode(json_encode($patient_balance_arr), True);
		return view('practice/patientstatementsettings/indstatementlist',  compact('patients_arr','patient_balance'));
	}
	
	// Get patient balance details for search results.
	public function getPatientDetails($patientid)
	{
		echo $this->getPatientDetailsApi($patientid);
	}
	
	public function getStatementHistory($patientid='')
	{
		View::share ( 'heading', 'Practice' );  
		View::share ( 'selected_tab', 'patientstatementhistory' ); 
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
		
		$api_response = $this->getStatementHistoryApi($patientid);
		$api_response_data = $api_response->getData();
		$statementlist = $api_response_data->data->statement_history;
		$psettings =$api_response_data->data->psettings;
		//$patient_list =$api_response_data->data->patient_list;
		$patient_list = [];
		$insurance_list = (array) $api_response_data->data->insurances;
		
		if(Request::ajax()) {   
			$pageType = "popup";
            return view('practice/patientstatementsettings/history_list',compact('statementlist','psettings','patient_list','pageType', 'insurance_list'));
        } else {
			$pageType = "page";
			if($patientid == '') {
				return view('practice/patientstatementsettings/historystatementlist',  compact('statementlist','psettings','patient_list','pageType','insurance_list'));
			} else { 
				return view('practice/patientstatementsettings/individualpatientstmthistory',  compact('statementlist','pageType', 'insurance_list'));		
			}
		}
	}
        
    public function patientstatements()
	{		
        View::share ( 'heading', 'Patient' );  
		View::share ( 'selected_tab', 'patientstatements' ); 
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.user'));
		
       
		return view('practice/patientstatementsettings/patientstatements');		
	}
}
