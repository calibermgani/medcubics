<?php namespace App\Http\Controllers\Dashboard;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patients\Claims; 
use App\Models\Patients\Payment;
use App\Http\Helpers\Helpers as Helpers;
use Response;
use View;
use Auth;
use App;
use Input;

class DashboardChargeController extends  Api\DashboardChargeApiController {
	public function __construct() {		
        View::share('heading', 'Dashboard');
        View::share('selected_tab', 'charge-analysis');
        View::share('heading_icon', 'dashboard');
    }

	 function getChargeAnalytics()
	 {		
		$api_response = $this->getChargeAnalyticsApi();
		$api_response_data = $api_response->getData();	
		$data = $api_response_data->data;		
		$topCPT = $api_response_data->topCPT;		
		$performanceData = $api_response_data->performanceData;	
		$insuranceData = $api_response_data->insuranceData;
		$insuranceData = json_decode(json_encode($api_response_data->insuranceData),true);	
		$patient_paid = array_values($insuranceData['Patient']);
		$insurance_paid = array_values($insuranceData['Insurance']);
		$total_charge_percentage = $api_response_data->total_charge_percentage;	
		$clean_claim = $api_response_data->clean_claim;			
		return view('dashboard/dashboard1', compact('data','performanceData', 'insuranceData', 'topCPT', 'total_charge_percentage', 'clean_claim'))
		->with('patient_paid', json_encode(@$patient_paid)) 
		->with('insurance_paid', json_encode(@$insurance_paid)) ; 
    }
	 function getChargeAnalyticsAjax($facility_id =null){
		
		$api_response = $this->getChargeAnalyticsApi($facility_id);
		$api_response_data = $api_response->getData();	
		$data = $api_response_data->data;		
		return view('dashboard/charges/facility_filter', compact('data'));
    }
    function postCreatedDateApi($type, $id, $date){
        $claims = [];
      
	}
  function getPerformanceManagement($type){  	
  	$performanceData = $this->getPerformanceManagementApi($type);  
  	$performanceData = json_decode(json_encode($performanceData), FALSE);
  	return view('dashboard/dashboard_performance', compact('performanceData'));
  }
	
}
