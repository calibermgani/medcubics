<?php namespace App\Http\Controllers\Reports\Billing;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Request;
use Input;
//use Illuminate\Http\Request;
use View;
use Auth;
use Config;
use App\Http\Controllers\Charges\Api\ChargeApiController as ChargeApiController;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Models\Practice;
use App\Http\Helpers\Helpers as Helpers;
use Session;
use Response;
class BillingController extends Api\BillingApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	 
	public function __construct() {
        View::share('heading', 'Reports');
        View::share('selected_tab', 'billing-report');
        View::share('heading_icon', 'fa-line-chart');
    } 
	 

	public function enddaytotal(){
		
		$groupby = array();
		$groupby['enter_date'] = 'Enter Date';
		$groupby['today'] = 'Today';
		$groupby['current_month'] = 'Current Month';
		$groupby['last_month'] = 'Last Month';
		$groupby['current_year'] = date('Y');
		
		$api_response = $this->getEnddaytotalApi();
        $api_response_data = $api_response->getData();
		$cliam_date_details = $api_response_data->data->cliam_date_details;
		foreach($cliam_date_details as $year_list){
			$groupby[$year_list->year] = $year_list->year;
		}
		$groupby = array_unique($groupby);
		
		return view('reports/billing/enddaytotal/list',compact('groupby'));
	}
	
	public function filter_result(){
		$api_response = $this->getFilterResultApi();
        $api_response_data = $api_response->getData();
		$start_date = $api_response_data->data->start_date;
		$end_date = $api_response_data->data->end_date;
		$name = Auth::user()->name;
		$filter_result = $api_response_data->data->filter_result;
		return view('reports/billing/enddaytotal/report_list',compact('start_date','end_date','name','filter_result'));
	}
	
	public function getUnbilledreports(){
		$api_response = $this->getUnbilledClaimApi();
        $api_response_data = $api_response->getData();
		$unbilled_claim_details = $api_response_data->data->unbilled_claim_details;
		return view('reports/billing/unbilled/report_list',compact('unbilled_claim_details'));	
	}
	
	public function getUnbilledexport($export=''){
		$api_response = $this->getUnbilledClaimApi();
        $api_response_data = $api_response->getData();
		$unbilled_claim_details = $api_response_data->data->unbilled_claim_details;
		$total_charge_count = 0;
		$grand_total = 0;
		foreach($unbilled_claim_details as $list){
			$data['claim_number'] = $list->claim_number;
			$data['first_name'] = $list->patient->first_name;
			$data['account_no'] = $list->patient->account_no;
			$data['insurance_name'] = $list->insurance_details->insurance_name;
			$data['provider_name'] = $list->rendering_provider->provider_name;
			$data['billing_provider_name'] = $list->billing_provider->provider_name;
			$data['date_of_service'] = date('m/d/Y',strtotime($list->date_of_service));
			$data['created_at'] = date('m/d/Y',strtotime($list->created_at));
			$data['total_charge'] = $list->total_charge;
			$grand_total = $grand_total + $list->total_charge; 
			$get_list[$total_charge_count] = $data;
			$total_charge_count++;
		}
		$get_result = $get_list;
		$total = '100';
		$get_result[$total_charge_count] = ['claim_number'=>'','first_name'=>'','account_no'=>'','insurance_name'=>'','provider_name'=>'','billing_provider_name'=>'','date_of_service'=>'','created_at'=>'','total_charge'=>''];
		
		$total_charge_count = $total_charge_count + 1;
		
		$get_result[$total_charge_count] = ['claim_number'=>'','first_name'=>'','account_no'=>'','insurance_name'=>'','provider_name'=>'','billing_provider_name'=>'','date_of_service'=>'','created_at'=>'','total_charge'=>'Grand Total : '.Helpers::priceFormat($grand_total,'no')];
		
		$result["value"] = json_decode(json_encode($get_result));
		$result["exportparam"] = array(
				'filename'	=>	'Unbilled Claim Reports',
				'heading'	=>	'',
				'fields' 	=>	array(
					'claim_number'	=>	'Claim#',
					'first_name'	=>	'Patient Name',
					'account_no'	=>	'Acct#',
					'insurance_name'=>	'Responsibility',
					'provider_name'	=>	'Rendering Provider',
					'billing_provider_name'	=>	'Billing Provider',
					'date_of_service'=>  'DOS',
					'created_at'	=>	'Transaction Date',
					'total_charge'	=>	'Fee',
					));
		
			$callexport = new CommonExportApiController();
			return $callexport->generatemultipleExports($result["exportparam"], $result["value"], $export); 
		
	}
	
	
	public function getEnddayexport($export=''){
		
		$api_response = $this->getExportResultApi();
        $api_response_data = $api_response->getData();
		$export_result = $api_response_data->data->export_result; 
		$heading_name = Practice::getPracticeName();
		$total_charge_count = 0;
		$total_adj = 0;
		$patient_total = 0;
		$insurance_total = 0;
		foreach($export_result as $list){
			$data['created_at'] = date("m/d/Y",strtotime($list->created_at));
			$data['total_charge'] = $list->claim->total_charge;
			$data['payment_type'] = $list->payment_type;
			$data['total_adjusted'] = $list->total_adjusted;
			$data['patient_paid_amt'] = $list->patient_paid_amt;
			$data['insurance_paid_amt'] = $list->insurance_paid_amt;
			$get_list[$total_charge_count] = $data;
			$total_charge_count++;
			$total_adj = $total_adj + $list->total_adjusted;
			$patient_total = $patient_total + $list->patient_paid_amt;
			$insurance_total = $insurance_total + $list->insurance_paid_amt;
		}
		
		
		$get_result = $get_list;
		
		$get_result[$total_charge_count] = ['created_at'=>'','total_charge'=>'','payment_type'=>'','total_adjusted'=>'','patient_paid_amt'=>'','insurance_paid_amt'=>''];
		
		$total_charge_count = $total_charge_count + 1;
		
		$get_result[$total_charge_count] = ['created_at'=>'','total_charge'=>'','payment_type'=>'','total_adjusted'=>'','patient_paid_amt'=>'','insurance_paid_amt'=>''];
		
		$total_charge_count = $total_charge_count + 1;
		$pratices_name = "Practice ".$heading_name.' Total :';
		$get_result[$total_charge_count] = ['created_at'=>'','total_charge'=>'','payment_type'=>$pratices_name,'total_adjusted'=>Helpers::priceFormat($total_adj,'no'),'patient_paid_amt'=>Helpers::priceFormat($patient_total,'no'),'insurance_paid_amt'=>Helpers::priceFormat($insurance_total,'no')];
		
		$total_charge_count = $total_charge_count + 1;
		
		$get_result[$total_charge_count] = ['created_at'=>'','total_charge'=>'','payment_type'=>'Grand Total : ','total_adjusted'=>Helpers::priceFormat($total_adj,'no'),'patient_paid_amt'=>Helpers::priceFormat($patient_total,'no'),'insurance_paid_amt'=>Helpers::priceFormat($insurance_total,'no')];
		
		$total_charge_count = $total_charge_count + 1;
		
		$get_result[$total_charge_count] = ['created_at'=>'','total_charge'=>'','payment_type'=>'','total_adjusted'=>'','patient_paid_amt'=>'','insurance_paid_amt'=>''];
		
		$result["value"] = json_decode(json_encode($get_result));
		$result["exportparam"] = array(
				'filename'	=>	'End of the Day Total Reports',
				'heading'	=>	'',
				'fields' 	=>	array(
					'created_at'	=>	'Date',
					'total_charge'	=>	'Charges',
					'payment_type'	=>	'Payments',
					'total_adjusted'=>	'Adjustments',
					'patient_paid_amt'	=>	'Patient Payments',
					'insurance_paid_amt'	=>	'Insurance Payments',
					));
		
		$callexport = new CommonExportApiController();
		return $callexport->generatemultipleExports($result["exportparam"], $result["value"], $export); 
	}
	### Aging Report module start ###
	/**
	 * Display a listing of the dropdown.
	 *
	 * @return Response
	 */
	public function aginganalysislist() {
        $api_response = $this->getAgingReportApi();
        $api_response_data = $api_response->getData();
        $insurance = $api_response_data->data->insurance;
        $facilities = $api_response_data->data->facilities;
        $selected_tab = "billing-report";
        $heading = "Reports";
        $heading_icon = "fa-line-chart";
        return view('reports/billing/aginganalysisdetails/aginganalysis', compact('insurance', 'selected_tab', 'heading', 'heading_icon', 'facilities', 'adjustment_reason'));
    }

	/**
	 * Search base on aging.
	 *
	 * @return Response
	 */
	public function aginganalysissearch() {
        $api_response = $this->getAgingReportSearchApi();
        $api_response_data = $api_response->getData();
        $header = $api_response_data->data->header;
        $aging_report_list = $api_response_data->data->aging_report_list;
        $title = $api_response_data->data->title;
        return view('reports/billing/aginganalysisdetails/normalreport', compact('aging_report_list', 'title', 'header'));
    }
	### Agingn report module start ###

	/**
	 *
	 * @return Response
	 */
	public function chargelist() {
        $api_response = $this->getChargeList();
        $api_response_data = $api_response->getData();
        $insurance = $api_response_data->data->insurance;
        $facilities = $api_response_data->data->facilities;
        $search_fields = $api_response_data->data->search_fields;
        $searchUserData = $api_response_data->data->searchUserData;
        $selected_tab = "financial-report";
        $heading = "Reports";
        $heading_icon = "fa-line-chart";
        $report_data = Session::get('report_data');
        return view('reports/billing/chargeList/report', compact('insurance', 'selected_tab', 'heading', 'heading_icon', 'facilities', 'adjustment_reason','report_data','search_fields','searchUserData'));
    }

	/**
	 *
	 * @return Response
	 */
	public function chargelistreport() {
		$header = Request::all();
        $api_response = $this->getChargesSearchApi();
        $api_response_data = $api_response->getData();
        $result = $api_response_data->data->claim_lists;
        $search_by = $api_response_data->data->search_by;
        $pagination = $api_response_data->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        return view('reports/billing/chargeList/reportListing', compact('result','header','pagination','pagination_prt','search_by'));
    }

    public function chargeListSearchExport($export = '',$data='') {
		$request = Request::all();
        $api_response = $this->getChargesSearchApi();
        $api_response_data = $api_response->getData();
        $result = $api_response_data->data->claim_lists;
        $search_by = $api_response_data->data->search_by;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : ''; 

        if ($request['export'] == 'xlsx' || $request['export'] == 'csv') {
            $filePath = 'reports/billing/chargeList/export';
            $data['result'] = $result;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['export'] = $request['export'];
            $data['search_by'] = $search_by;
            $data['file_path'] = $filePath;
            if(Request::ajax()) {
                return Response::json(array('value' => $data));
            } else {
                return $data;
            }
            $type = '.xls';
        }
    }
	
	public function chargelistV2(){
		$api_response = $this->getChargesSearchApiV2();
	}
}
