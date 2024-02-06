<?php

namespace App\Http\Controllers\Armanagement;

use Auth;
use App;
use View;
use Input;
use Session;
use Request;
use Redirect;
use Validator;
use Response;
use Config;
use Excel;
use PDF;
use App\Http\Controllers\Documents\Api\DocumentApiController;

class ArmanagementController extends Api\ArmanagementApiController {

    public function __construct() {
        View::share('heading', 'AR');
        View::share('selected_tab', 'summary');
        View::share('heading_icon', 'fa-laptop');
        View::share('selected_tab', 'aranalytics-dashboard');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $claim_status_count = $api_response_data->data->claim_status_count;
        $api_response = $this->getAR_SummaryPageDataApi();
        $api_response_data = $api_response->getData();        
        $claim_count = $api_response_data->data->claim_status_wise_count;
        $collection_chart = $api_response_data->data->collection_chart; 
        $aging_data = $api_response_data->data->aging_data; 
        $insurance_aging_data = $api_response_data->data->insurance_aging;
        $patient_aging_data = $api_response_data->data->PatientAging;       
        $claims_status_balances = $api_response_data->data->patient_claims_status; 
        $ar_days = $api_response_data->data->ar_days; 
        $total_patient_aging_chart = $api_response_data->data->patientAgingChart;
        $total_insurance_aging_chart = $api_response_data->data->insuranceAgingChart;
        $insuranceLineChart = $api_response_data->data->insuranceLineChart;		
		/*$ins_category_value = $api_response_data->data->ins_category_value;	
		$datavaluefinal = json_decode($ins_category_value);
		$insurance_chart_data = json_encode($datavaluefinal->datavaluefinal, true);	
		$insurance_chart_label = json_encode($datavaluefinal->date_label_final, true);*/ //Category related payment info hidded if need we can enable from here
        $insurance_chart_label = json_encode($collection_chart->insurance_chart_label, true);
       return view('armanagement/armanagement/armanagement',
           compact('claim_count', 'aging_data', 'insurance_aging_data','patient_aging_data',
               'claims_status_balances', 'ar_days', 'total_patient_aging_chart', 'total_insurance_aging_chart',
               'insuranceLineChart', 'claim_status_count'))
       ->with('charge', json_encode(@$collection_chart->Charge))                        
       ->with('payment', json_encode(@$collection_chart->Collections))
       ->with('balance', json_encode(@$collection_chart->Balance))
	  // ->with('insurance_chart_data', $insurance_chart_data) //Category related payment info hidded if need we can enable from here
	   ->with('insurance_chart_label', $insurance_chart_label); //'insurance_chart_data', 'insurance_chart_label'
    }

    public function arAnalytics() {
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $claim_status_count = $api_response_data->data->claim_status_count;
        $api_response = $this->getAR_SummaryPageDataApi();
        $api_response_data = $api_response->getData();        
        $claim_count = $api_response_data->data->claim_status_wise_count;
        $collection_chart = $api_response_data->data->collection_chart; 
        $aging_data = $api_response_data->data->aging_data; 
        $insurance_aging_data = $api_response_data->data->insurance_aging;
        $patient_aging_data = $api_response_data->data->PatientAging;       
        $claims_status_balances = $api_response_data->data->patient_claims_status; 
        $ar_days = $api_response_data->data->ar_days; 
        $total_patient_aging_chart = $api_response_data->data->patientAgingChart;
        $total_insurance_aging_chart = $api_response_data->data->insuranceAgingChart;
        $insuranceLineChart = $api_response_data->data->insuranceLineChart;     
        /*$ins_category_value = $api_response_data->data->ins_category_value;   
        $datavaluefinal = json_decode($ins_category_value);
        $insurance_chart_data = json_encode($datavaluefinal->datavaluefinal, true); 
        $insurance_chart_label = json_encode($datavaluefinal->date_label_final, true);*/ //Category related payment info hidded if need we can enable from here
        $insurance_chart_label = json_encode($collection_chart->insurance_chart_label, true);
       return view('dashboard/aranalytics',
           compact('claim_count', 'aging_data', 'insurance_aging_data','patient_aging_data',
               'claims_status_balances', 'ar_days', 'total_patient_aging_chart', 'total_insurance_aging_chart',
               'insuranceLineChart', 'claim_status_count'))
       ->with('charge', json_encode(@$collection_chart->Charge))                        
       ->with('payment', json_encode(@$collection_chart->Collections))
       ->with('balance', json_encode(@$collection_chart->Balance))
      // ->with('insurance_chart_data', $insurance_chart_data) //Category related payment info hidded if need we can enable from here
       ->with('insurance_chart_label', $insurance_chart_label); //'insurance_chart_data', 'insurance_chart_label'
    }

    public function index1() {

        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        return view('armanagement/armanagement/armanagement1');
    }

    public function insurance() {
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();

        return view('armanagement/armanagement/insurance_claim');
    }

    public function insurancewise() {
        $insurance_aging_data = $this->InsuranceWiseAging(Request::all()['option']);
        return view('armanagement/armanagement/insurance_wise',compact('insurance_aging_data'));
    }

    public function insclaims() {
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();

        return view('armanagement/armanagement/ins_claims');
    }

    public function statuswise() {
        $claims_status_balances = $this->getBalanceByStatus('',Request::all()['option']);

        return view('armanagement/armanagement/status_wise',compact('claims_status_balances'));
    }

    public function insurance1() {
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();

        return view('armanagement/armanagement/insurance_claim1');
    }

    public function armanagementlist() {
        $api_response = $this->getarmanagementlistApi();
        $api_response_data = $api_response->getData();

        $claims_lists = $api_response_data->data->claims_list;
		// Added hold reason for bulk hold option in armanagement
		// Revision 1 : MR-2786 : 4 Sep 2019
		$hold_option = $api_response_data->data->hold_options;
		$claims_count = count($claims_lists);
        if (Request::ajax()) {
            return view('armanagement/armanagement/claimslist', compact('claims_lists','claims_count','hold_option'));
        } else {
            $user_list = $api_response_data->data->user_list;
            $patients = $api_response_data->data->patients;
            $billing_provider = $api_response_data->data->billing_provider;
            $rendering_provider = $api_response_data->data->rendering_provider;
            $referring_provider = $api_response_data->data->referring_provider;
            $claim_status_count = $api_response_data->data->claim_status_count;
            $insurances = $api_response_data->data->insurances;
            $facility = $api_response_data->data->facility;
            $category = $api_response_data->data->category;
            $question = $api_response_data->data->question;
            $pagination_count = $api_response_data->data->pagination_count;
            $search_fields = $api_response_data->data->search_fields;
            $searchUserData = $api_response_data->data->searchUserData;
			View::share('heading', 'AR');
			View::share('selected_tab', 'armanagementlist');
			View::share('heading_icon', 'fa-laptop');
            return view('armanagement/armanagement/armanagementlist', compact('claims_lists', 'user_list', 'patients', 'billing_provider', 'rendering_provider', 'referring_provider', 'insurances', 'facility', 'category', 'question','claims_count', 'claim_status_count','pagination_count','search_fields','searchUserData','hold_option'));
        }
    }

    public function armanagementDenialList() {
        $heading = "AR";
        $heading_icon = "fa-laptop";
        $api_response = $this->getARDenialListApi();
        $api_response_data = $api_response->getData();
        $last_addin_problemlist = $api_response_data->data;
        $search_fields = $api_response_data->data->search_fields;
        $searchUserData = $api_response_data->data->searchUserData;
        $summary = $this->getARDenialListSummaryApi();
        View::share('heading', 'Patient');        
        $selected_tab = "deniallist";
        View::share('selected_tab', $selected_tab);
        View::share('heading_icon', \Config::get('cssconfigs.Practicesmaster.user'));
        $workbench_status = 'Include';
        return view('armanagement/denials/deniallist', compact('heading', 'heading_icon','search_fields','searchUserData', 'summary', 'workbench_status'));
    } 

    public function armanagementDenialSummary() {
        $heading = "AR";
        $heading_icon = "fa-laptop";
        $api_response = $this->getARDenialListApi();
        $api_response_data = $api_response->getData();
        $last_addin_problemlist = $api_response_data->data;
        $search_fields = $api_response_data->data->search_fields;
        $searchUserData = $api_response_data->data->searchUserData;
        $summary = $this->getARDenialListSummaryApi();
        View::share('heading', 'Patient');        
        $selected_tab = "deniallist";
        View::share('selected_tab', $selected_tab);
        View::share('heading_icon', \Config::get('cssconfigs.Practicesmaster.user'));
        $workbench_status = 'Include';
        return view('armanagement/denials/denials_summary', compact('heading', 'heading_icon','search_fields','searchUserData', 'summary', 'workbench_status'));
    }    

    public function getARDenialListAjax() {
        $api_response = $this->getARDenialListFilterApi();
        $api_response_data = $api_response->getData();
        $denial_cpt_list = $api_response_data->data->denial_cpt_list;
        $workbench_status = $api_response_data->data->workbench_status;
        $claim_nos = $api_response_data->data->claim_nos;

        $view_html = Response::view('armanagement/denials/claimslist_ajax', compact('denial_cpt_list', 'workbench_status','claim_nos'));
        $content_html = htmlspecialchars_decode($view_html->getContent());
        $content = array_filter(explode("</tr>", trim($content_html)));
        $request = Request::all();
        if (!empty($request['draw']))
            $data['draw'] = $request['draw'];
        $data['data'] = $content;
        $data['datas'] = ['id' => 2];
        $data['recordsTotal'] = $api_response_data->data->count;
        $data['recordsFiltered'] = $api_response_data->data->count;
        return Response::json($data);
    }
    
    /*For AR Denial List Export*/
    public function arDenialListExport($export = '', $data = '') {
        $api_response = $this->getARDenialListFilterApi($export);
        $api_response_data = $api_response->getData();
        $denial_cpt_list = $api_response_data->data->denial_cpt_list_export;
        $workbench_status = $api_response_data->data->workbench_status;
        $search_by = $api_response_data->data->search_by;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'AR_Denial_List_' . $date;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';
        if ($export == 'pdf') {
            $html = view('armanagement/denials/denial_list_export_pdf', compact('denial_cpt_list','workbench_status', 'export'));
            //return PDF::load($html, 'A4', 'landscape')->filename($name.".pdf")->download();
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            /*Excel::create($name, function($excel) use ($denial_cpt_list, $workbench_status, $export) {
                $excel->sheet('New Sheet', function($sheet) use ($denial_cpt_list, $workbench_status, $export) {
                    $sheet->loadView('armanagement/denials/denial_list_export')->setColumnFormat(array('N:O'=>'#,#0.00'))->with("denial_cpt_list", $denial_cpt_list)->with("workbench_status", $workbench_status)->with("export", $export);
                });
            })->export('xls');*/
            $filePath = 'armanagement/denials/denial_list_export';
            $data['denial_cpt_list'] = $denial_cpt_list;
            $data['workbench_status'] = $workbench_status;
            $data['header'] = $search_by;
            $data['export'] = $export;
            $data['practice_id'] = $practice_id;
            $data['file_path'] = $filePath;
            return $data;
        } elseif ($export == 'csv') {
            Excel::create($name, function($excel) use ($denial_cpt_list, $workbench_status, $export) {
                $excel->sheet('New Sheet', function($sheet) use ($denial_cpt_list, $workbench_status, $export) {
                    $sheet->loadView('armanagement/denials/denial_list_export')->setColumnFormat(array('N:O'=>'#,#0.00'))->with("denial_cpt_list", $denial_cpt_list)->with("workbench_status", $workbench_status)->with("export", $export);
                });
            })->export('csv');
        }
    }

    public function indexTableData() {

        $api_response = $this->getarmanagementAjaxlistApi();
        $api_response_data = $api_response->getData();
        $claims_lists = $api_response_data->data->claims_list;
		$encodeClaimIds = $api_response_data->data->encodeClaimIds;
        $view_html = Response::view('armanagement/armanagement/claimslist_ajax', compact('claims_lists', 'encodeClaimIds'));
        $content_html = htmlspecialchars_decode($view_html->getContent());
        $content = array_filter(explode("</tr>", trim($content_html)));
        $request = Request::all();
        if (!empty($request['draw']))
            $data['draw'] = $request['draw'];
        $data['data'] = $content;
        $data['datas'] = ['id' => 2];
        $data['recordsTotal'] = $api_response_data->data->count;
        $data['recordsFiltered'] = $api_response_data->data->count;
        return Response::json($data);
    }
    
    /*For AR Management List Export*/
    public function arManagementListExport($export = '') {
        $api_response = $this->getarmanagementAjaxlistApi($export);
        $api_response_data = $api_response->getData();
        $claims_lists = $api_response_data->data->claims_list;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'AR_Management_List_' . $date;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';
        
        if ($export == 'xlsx' || $export == 'csv') {
            $filePath = 'armanagement/armanagement/armanagement_list_export';
            $data['claims_lists'] = $claims_lists;
            $data['export'] = $export;
            $data['practice_id'] = $practice_id;
            $data['file_path'] = $filePath;
            return $data;
        }
    }

    public function myfollowup() {
        $api_response = $this->getarmanagementfollowupApi();
        $api_response_data = $api_response->getData();
        $user_list = $api_response_data->data->user_list;
        $claims_lists = $api_response_data->data->claims_list;
        $patients = $api_response_data->data->patients;
        $billing_provider = $api_response_data->data->billing_provider;
        $rendering_provider = $api_response_data->data->rendering_provider;
        $referring_provider = $api_response_data->data->referring_provider;
        $insurances = $api_response_data->data->insurances;
        $facility = $api_response_data->data->facility;
        $myfollowup_list = $api_response_data->data->myfollowup_list;
        $category = $api_response_data->data->category;
        $question = $api_response_data->data->question;
        return view('armanagement/followuplist/armanagementlist', compact('claims_lists', 'user_list', 'patients', 'billing_provider', 'rendering_provider', 'referring_provider', 'insurances', 'facility', 'myfollowup_list', 'category', 'question'));
    }

    public function otherfollowup() {
        $api_response = $this->getarmanagementfollowupApi();
        $api_response_data = $api_response->getData();
        $user_list = $api_response_data->data->user_list;
        $claims_lists = $api_response_data->data->claims_list;
        $patients = $api_response_data->data->patients;
        $billing_provider = $api_response_data->data->billing_provider;
        $rendering_provider = $api_response_data->data->rendering_provider;
        $referring_provider = $api_response_data->data->referring_provider;
        $insurances = $api_response_data->data->insurances;
        $facility = $api_response_data->data->facility;
        $myfollowup_list = $api_response_data->data->otherfollowup_list;
        $category = $api_response_data->data->category;
        $question = $api_response_data->data->question;
        return view('armanagement/followuplist/armanagementlist', compact('claims_lists', 'user_list', 'patients', 'billing_provider', 'rendering_provider', 'referring_provider', 'insurances', 'facility', 'myfollowup_list', 'category', 'question'));
    }

    public function getdynamicdocument(){
        $documentApi =  new DocumentApiController();
        $api_response       = $documentApi->getDynamicDocumentApi();
        $api_response_data = $api_response->getData();
        $document_data      =   $api_response_data->data->document_data;
        $users      =     $api_response_data->data->users;
        $categories     =   $api_response_data->data->categories; 
        $patients       =   $api_response_data->data->patients; 
        $insurances     =   $api_response_data->data->insurances;

        $heading = "AR";
        $heading_icon = "fa-laptop";
        $api_response = $this->getARDenialListApi();
        $api_response_data = $api_response->getData();
        $last_addin_problemlist = $api_response_data->data;
        $search_fields = $api_response_data->data->search_fields;
        $searchUserData = $api_response_data->data->searchUserData;
        $summary = $this->getARDenialListSummaryApi();
        View::share('heading', 'Patient');        
        $selected_tab = "deniallist";
        View::share('selected_tab', $selected_tab);
        View::share('heading_icon', \Config::get('cssconfigs.Practicesmaster.user'));
        $workbench_status = 'Include';

        return view('armanagement/denials/denials_ajax', compact('document_data', 'users', 'categories', 'patients', 'insurances', 'heading', 'heading_icon','search_fields','searchUserData', 'summary', 'workbench_status'));
         
    }

}
