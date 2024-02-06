<?php namespace App\Http\Controllers\Claims;

use View;
use Request;
use Redirect;
use File;
use Response;
use Session;
use App;
use Config;
use SSH;
use PDF;
use Excel;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Claims\TransmissionClaimDetails as TransmissionClaimDetails;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\ClaimTXDESCV1;
use App\Models\Patients\PatientInsurance;
use App\Exports\BladeExport;

class ClaimControllerV1 extends Api\ClaimApiControllerV1
{
    public function __construct($type = null)
    {
		if(empty($type)) {
				View::share('heading', 'Claims');
				View::share('selected_tab', 'claims');
				View::share('heading_icon', 'shopping-cart');
		}
       
    }
	
	#======================================================================================#
	#				 This function used to showing the claim Dashboard   			   	   #
	#				 Claim Dashboard Start Point                                           # 
	#======================================================================================#
	
	public function summary() {        
        View::share('selected_tab', 'claimsummary');
		View::share('heading', 'Dashboard');
		View::share('heading_icon', 'dashboard');
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();  
        $dataArr = $api_response_data->data->dataArr;  
        $responseArr = $api_response_data->data->responseArr;  
        return view('claims/claims/claimsummary',compact('dataArr','responseArr'));
    }
	
	#======================================================================================#
	#								End Claim dashboard Page							   #
	#======================================================================================#
	
	
	#======================================================================================#
	#				 This function used to showing the claim ReadytoSubmit			   	   #
	#				 Claim ReadytoSubmit Start Point                                       # 
	#======================================================================================#
	
	
	public function claims_data($type){
		View::share('heading', ucfirst($type).' Claims');
		View::share('heading_icon', 'fa-cart-plus');
        View::share('selected_tab', $type);
        $claims = [];  
        $pagination_count = 0;  
		$page_name = 'claim_'.$type.'_listing';
		if($type == 'rejected')
			$page_name = 'claim_rejection_listing';
		$search_fields_data  = $this->generateSearchPageLoad($page_name);
		$search_fields = $search_fields_data['search_fields'];
		$searchUserData = $search_fields_data['searchUserData'];
		return view('claims/claims/claimsSubmissionV1',compact('type','claims','pagination_count','search_fields','searchUserData'));
	}
	
	public function ClaimsDataSearch($type){
		$request = Request::all();
		$request['type'] = $type;
		View::share('heading', ucfirst($request['type']).' Claims');
		View::share('heading_icon', 'fa-cart-plus');
        View::share('selected_tab', $request['type']);
		
		$api_response = $this->getClaimsDataSearchApi($type);
		$api_response_data = $api_response->getData();
        $claims = $api_response_data->data->claims;
        $pagination_count = $api_response_data->data->pagination_count;
		$count = $api_response_data->data->counts;
		$encodeClaimIds = $api_response_data->data->encodeClaimIds;
		$type = $api_response_data->data->type;
		
		if($type == 'electronic')
			$view_html      = Response::view('claims/claims/claims_electronic_listing_ajax', compact('claims','pagination_count','encodeClaimIds'));
		elseif($type == 'paper')
			$view_html      = Response::view('claims/claims/claims_paper_listing_ajax', compact('claims','pagination_count','encodeClaimIds'));
		elseif($type == 'error')
			$view_html      = Response::view('claims/claims/claims_error_listing_ajax', compact('claims','pagination_count'));
		elseif($type == 'submitted')
			$view_html      = Response::view('claims/claims/claims_submitted_listing_ajax', compact('claims','pagination_count'));
		elseif($type == 'rejected')
			$view_html      = Response::view('claims/claims/rejection_listing_ajax', compact('claims','pagination_count'));
		
		$content_html   = htmlspecialchars_decode($view_html->getContent());
        $content        = array_filter(explode("</tr>",trim($content_html)));  
        if(!empty($request['draw']))
			$data['draw'] = $request['draw'];   
        $data['data'] = $content;   
        $data['datas'] = ['id'=>2 ];    
        $data['recordsTotal'] = $count;
        $data['recordsFiltered'] = $count;     
        
        return Response::json($data);
	}
        
    public function ClaimsDataSearchExport($type,$export = ''){
        $api_response = $this->getClaimsDataSearchApi($type,$export);
        $api_response_data = $api_response->getData();
        $claims = $api_response_data->data->claims;  
        $type = $api_response_data->data->type;
        $search_by = $api_response_data->data->get_list_header;
        $date = Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');

        if($type == 'electronic'){
            $header = 'Electronic Claims List';
            $name = 'Electronic_Claims_list_' . $date;
            $column = 'J';
        }elseif($type == 'paper'){
            $header = 'Paper Claims List';
            $name = 'Paper_Claims_list_' . $date;
            $column = 'J';
        }elseif($type == 'error'){
            $header = 'Error Claims List';
            $name = 'Error_Claims_list_' . $date;
            $column = 'J';
        }elseif($type == 'submitted'){
            $header = 'Submitted Claims List';
            $name = 'Submitted_Claims_list_' . $date;
            $column = 'J';
        }elseif($type == 'rejected'){
            $header = 'Rejected Claims List';
            $name = 'Rejected_Claims_list_' . $date;
            $column = 'E';
        }
        $view_html = 'claims/claims/claims_electronic_listing_export';

        if ($export == 'pdf') {
            $html = view('claims/claims/claims_electronic_listing_pdf', compact('claims', 'export','header', 'type'));
            return PDF::loadHTML($html, 'A4', 'landscape')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = $view_html;
            $data['column'] = $column;
            $data['claims'] = $claims;
            $data['type'] = $type;
            $data['header'] = $header;
            $data['search_by'] = $search_by;
            $data['view_html'] = $view_html;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return $data;
        } elseif ($export == 'csv') {
            $filePath = $view_html;
            $data['column'] = $column;
            $data['claims'] = $claims;
            $data['type'] = $type;
            $data['view_html'] = $view_html;
            $data['header'] = $header;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');            
        }        
    }
	
	#======================================================================================#
	#								End Claim ReadytoSubmit Page						   #
	#======================================================================================#
	
	
	
	#======================================================================================#
	#				 This function used to get the claim getHoldReason				   	   #
	#				 Claim getHoldReason Start Point                                       # 
	#======================================================================================#
	
	public function getHoldReason()
    {
        $api_response = $this->getHoldReasonApi();
        $api_response_data = $api_response->getData();
        $hold_options = (array)$api_response_data->data->hold_options;
        return view('claims/claims/hold_option',compact('hold_options'));
    }
	
	
	#======================================================================================#
	#								End Claim GetHoldReason								   #
	#======================================================================================#
	
	#======================================================================================#
	#				 This function used to get the claim listClaimTransmission		   	   #
	#				 Claim getHoldReason Start Point                                       # 
	#======================================================================================#
	
	public function listClaimTransmission()
    {
        View::share('selected_tab', 'transmission');
        View::share('heading', 'Transmission');
        View::share('heading_icon', 'fa-exchange');
        $api_response = $this->listClaimTransmissionApi();
        $api_response_data = $api_response->getData();
        $claim_transmission = $api_response_data->data->claim_transmission;
        return view('claims/claims/transmission',compact('claim_transmission'));
    }
	
	#======================================================================================#
	#								End Claim listClaimTransmission						   #
	#======================================================================================#
	
	#======================================================================================#
	#				 This function used to get the viewClaimTransmission			   	   #
	#				 Claim viewClaimTransmission Start Point                               # 
	#======================================================================================#
	
	public function viewClaimTransmission($id)
    {
        View::share('selected_tab', 'transmission');
        View::share('heading', 'Transmission');
        View::share('heading_icon', 'fa-exchange');
        $api_response = $this->viewClaimTransmissionApi($id);
        $api_response_data = $api_response->getData();
        $transmission = $api_response_data->data->transmission;
        return view('claims/claims/transmission_details',compact('transmission'));
    }
	
	#======================================================================================#
	#								End Claim viewClaimTransmission						   #
	#======================================================================================#
	
	#======================================================================================#
	#				 This function used to getEdiReports					   	   		   #
	#				 Claim getEdiReports Start Point 			                           # 
	#======================================================================================#
	
	public function getEdiReports()
    {
        View::share('selected_tab', 'edireports');
        View::share('heading', 'EDI Reports');
        View::share('heading_icon', 'fa-file-text-o');
        $search_fields_data = $this->generateSearchPageLoad('edi_reports');
        $search_fields = $search_fields_data['search_fields'];
		$searchUserData = $search_fields_data['searchUserData'];
		if(Request::ajax()) {
            $api_response = $this->getEdiReportsApi();
            $api_response_data = $api_response->getData();
	        $edi_reports = (!empty($api_response_data->data->edi_reports))? (array)$api_response_data->data->edi_reports:[];
	        $list_page = $api_response_data->data->list_page;
	        $view_html = Response::view('claims/claims/edi_reports_ajax', compact('edi_reports','list_page'));
	        $content_html = htmlspecialchars_decode($view_html->getContent());
	        $content = array_filter(explode("</tr>", trim($content_html)));
	        
	        if (!empty($request['draw']))
	           $data['draw'] = $request['draw'];
	        $data['data'] = $content;
	        $data['recordsTotal'] = $api_response_data->data->count;
	        $data['recordsFiltered'] = $api_response_data->data->count;
            return Response::json($data);
        }
		return view('claims/claims/edi_reports',compact('search_fields','searchUserData'));
    }
	
	#======================================================================================#
	#								End Claim getEdiReports								   #
	#======================================================================================#
	
	
	#======================================================================================#
	#				 This function used to viewEdiReport					   	   		   #
	#				 Claim viewEdiReport Start Point 			                           # 
	#======================================================================================#
	
	public function viewEdiReport($id)  
    {
        $api_response = $this->viewEdiReportApi($id);
        $api_response_data = $api_response->getData();
        if($api_response_data->status == 'success')
        {
            $file_content = $api_response_data->data->file_content;
            return view('claims/claims/view_edi_report', compact('file_content'));
        }
        else
        {
            return Redirect::to('claims/edireports')->with('error',$api_response_data->message);
        }
    }
	
	
	#======================================================================================#
	#								End Claim viewEdiReport								   #
	#======================================================================================#
	
	
	#======================================================================================#
	#				 This function used to getStatusEdiReports				   	   		   #
	#				 Claim getStatusEdiReports Start Point 		                           # 
	#======================================================================================#
	
	public function getStatusEdiReports()
    {
        View::share('selected_tab', 'edireports');
        $search_fields_data = $this->generateSearchPageLoad('edi_reports');
        $search_fields = $search_fields_data['search_fields'];
		$searchUserData = $search_fields_data['searchUserData'];
        $api_response = $this->getEdiReportsApi();
        $api_response_data = $api_response->getData();
	    $edi_reports = (!empty($api_response_data->data->edi_reports))? (array)$api_response_data->data->edi_reports:[];
	    $view_html = Response::view('claims/claims/edi_reports_ajax', compact('edi_reports'));
	    $content_html = htmlspecialchars_decode($view_html->getContent());
	    $content = array_filter(explode("</tr>", trim($content_html)));

	    if (!empty($request['draw']))
	       $data['draw'] = $request['draw'];
	    $data['data'] = $content;
	    $data['recordsTotal'] = $api_response_data->data->count;
	    $data['recordsFiltered'] = $api_response_data->data->count;
	    return Response::json($data);
    }
	
	#======================================================================================#
	#								End Claim getStatusEdiReports						   #
	#======================================================================================#
	
	#======================================================================================#
	#				 This function used to getedireporttabdetails			   	   		   #
	#				 Claim getedireporttabdetails Start Point 	                           # 
	#======================================================================================#
	
	public function getedireporttabdetails()
	{	
		$api_response = $this->getedireporttabdetailsApi();
        $api_response_data = $api_response->getData();
        $added_edireport_tabs = $api_response_data->added_edireport_tabs;
		$remove_edireport_tabs = $api_response_data->remove_edireport_tabs;
		$edireport_detail = $api_response_data->edireport_detail;
		$edireport_tab_list = $api_response_data->edireport_tab_list;
		$added_edireport_tab_details = view('claims/claims/added_edireport_tab_details',compact('edireport_detail'));
		return $added_edireport_tabs."^^::^^".$remove_edireport_tabs."^^::^^".$edireport_tab_list."^^::^^".$added_edireport_tab_details;
	}
	
	#======================================================================================#
	#								End Claim getedireporttabdetails					   #
	#======================================================================================#
	
	
	#======================================================================================#
	#				 This function used to getresponsefile					   	   		   #
	#				 Claim getresponsefile Start Point		 	                           # 
	#======================================================================================#
	
	public function getresponsefile($claim_id){
		$claim_id = Helpers::getEncodeAndDecodeOfId($claim_id,'decode');
		if(App::environment() == "production")
			$path_medcubic = $_SERVER['DOCUMENT_ROOT'].'/medcubic/';
		else
			$path_medcubic = public_path().'/';
		$file_path = $path_medcubic.'media/clearingHouse_response/'.Session::get('practice_dbid').'/FS_HCFA_443130028_IN_C.txt';
		//echo $file_path = public_path('media/clearingHouse_response/FS_HCFA_443130028_IN_C.txt');
		$file_content = file($file_path);
		$contents = File::get($file_path);
		print_r($contents);
	}
	
	#======================================================================================#
	#								End Claim getresponsefile							   #
	#======================================================================================#
	
	public function generateSearch($type){
		$api_response = $this->generateSearchApi($type);
        $api_response_data = $api_response->getData();
		$search_fields = $api_response_data->data->search_details;
		$searchUserData = $api_response_data->data->searchUserData;
		echo view('layouts/search_fields',compact('search_fields','type','searchUserData'));
	}
	
	public function searchSavedData($type,$search_id){
		$api_response = $this->searchSavedDataApi($type,$search_id);
        $api_response_data = $api_response->getData();
		$data = $api_response_data->data->data;
		$data = rtrim($data,'&');
		return $data;
	}
	
	public function generateSearchPageLoad($type){ 
		$api_response = $this->generateSearchApi($type,'');
        $api_response_data = $api_response->getData();
        if($api_response_data->status == 'error'){
        	\Log::info("ERROR: Search fields not defined for ".$type." not found in master settings");
        	// If search fields not defined in master settings redirect to dashboard.	
			\Session::put('error','Search not defined. Please contact administrator');			
			header("Location: ".URL('analytics/practice'));
			exit;
        } 
		$data['search_fields'] = $api_response_data->data->search_details;
		$data['searchUserData'] = $api_response_data->data->searchUserData;
		return $data;
	}
	
	public function searchData(){
		$api_response = $this->searchDataApi();
	}
	
	public function searchDataRemove(){
		$api_response = $this->searchDataRemoveApi();
	}
	
	public function updateInsuranceCategory(){
		$api_response = $this->updateInsuranceCategoryApi();
		return "success";
	}
	
	
	
	public function errorClaimSubmission($id){
		$data['claim_ids'] = TransmissionClaimDetails::where('edi_transmission_id',$id)->pluck('claim_id')->all();
		ClaimInfoV1::whereIn('id',$data['claim_ids'])->update(['status'=>'Ready']);
		$data['submission'] = "ErrorSubmission";
		$this->checkAndSubmitEdiErrorClaim($data);
		
	}

	#======================================================================================#
	#				 This function used to change the claims status			   	   		   #
	#				 Claim status change Start Point		 	                           # 
	#======================================================================================#
	
	public function changeClaimStatus(){
		$api_response = $this->changeClaimStatusApi();
		if(isset($api_response) && !empty($api_response)){
			$api_response_data = $api_response->getData();
			$data = $api_response_data->data->dataArr;
			return Response::json($data);
		}
		return 'Success';
	}
	
	#======================================================================================#
	#								End Claim claims status change						   #
	#======================================================================================#
	
	
	// Generating missing files in era folder
	// Author: Selvakumar Date: 10/17/2019 
	
	public function erafiles(){
		$path_medcubic = public_path() . '/';
		$local_path = $path_medcubic . 'media/era_files/40/';
        if (!file_exists($local_path)) {
            mkdir($local_path, 0777, true);
        }
		/* Original zip storage folder*/
		$zipFilePath = $local_path.'EraZipFile/';
		if(!file_exists($zipFilePath))
			mkdir($zipFilePath, 0777, true);
		 foreach(glob($zipFilePath.'*.*') as $filename){
			$folderName = basename($filename,'.835');
			if(!is_dir($local_path.$folderName)){
				$tempPath = $local_path.$folderName;
				$tempName = str_replace('STATUS','835',$folderName).".835";
				if (!file_exists($tempPath)) {
					mkdir($tempPath, 0777, true);
				}
				@fopen($tempPath ."/". $tempName, 'w');
				$file_content = file_get_contents($filename);
				$myera835file = fopen($tempPath ."/". $tempName, "w+");
				fwrite($myera835file, $file_content);	
			}
		 }
	}
	
	
	/* checking clearing house file data */
	
	public function clearingHouseData(){
		$path_medcubic = public_path() . '/';
        $local_path = $path_medcubic . 'media/clearing_house/' . Session::get('practice_dbid') . '/';
		$read_claim_item = 0;
		$claims_count = 0;
		$claim_count = 1;
		$dataArr = array();
		$file_name = 'FS_HCFA_848303600_IN_C.txt';
		$file_content = file($local_path . $file_name);
		foreach ($file_content as $key => $file_line) {
			if (substr($file_line, 0, 6) == 'CLAIM#') {
				$read_claim_item = 1; echo substr($file_line, 0, 2).$claim_count;
			}
			if ($read_claim_item == 1 && (substr($file_line, 0, 2) == $claim_count . ')' || substr($file_line, 0, 3) == $claim_count . ')' || substr($file_line, 0, 4) == $claim_count . ')')) {
				
				$claims_count ++;
				
				$dataArr[$claim_count]['CLAIM#'] = substr($file_line, 0, 6);
				$dataArr[$claim_count]['OA_Claim_ID'] = substr($file_line, 7, 10);
				$dataArr[$claim_count]['PATIENT_ID'] = substr($file_line, 19, 18);
				$dataArr[$claim_count]['LAST_FIRST'] = substr($file_line, 37, 20);
				$dataArr[$claim_count]['DOB'] = substr($file_line, 57, 10);
				$dataArr[$claim_count]['FROM_DOS'] = substr($file_line, 70, 10);
				$dataArr[$claim_count]['TO_DOS'] = substr($file_line, 81, 10);
				$dataArr[$claim_count]['CPT'] = substr($file_line, 92, 5);
				$dataArr[$claim_count]['DIAG'] = substr($file_line, 100, 7);
				$dataArr[$claim_count]['TAX_ID'] = substr($file_line, 109, 10);
				$dataArr[$claim_count]['ACCNT#'] = substr($file_line, 121, 14);
				$dataArr[$claim_count]['PHYS_ID'] = substr($file_line, 135, 10);
				$dataArr[$claim_count]['PAYER'] = substr($file_line, 146, 5);
				$dataArr[$claim_count]['ERROR'] = substr($file_line, 153, 30);
		
				$claim_details = ClaimInfoV1::where('claim_number', trim(@$dataArr[$claim_count]['ACCNT#']));
											$claimsInfo = $claim_details->get()->first();
						
				$insuranceID = '';
				if(empty($insuranceID)){
					$claimsTXDetails = ClaimTXDESCV1::where('claim_id',$claimsInfo->id)->where('transaction_type','Insurance Payment')->orderBy('id','desc')->get()->first();
					if(isset($claimsTXDetails->responsibility) && !empty($claimsTXDetails->responsibility)){
						$insuranceID = $claimsTXDetails->responsibility;
					}else{
						$claimsTXDetails = ClaimTXDESCV1::where('claim_id',$claimsInfo->id)->where('transaction_type','Submitted')->orderBy('id','desc')->get()->first();
					}
					$patientInsurance = PatientInsurance::where('insurance_id',$insuranceID)->where('patient_id',$claimsInfo->patient_id)->get()->first();
					$insuranceID = @$patientInsurance->category.'-'.@$claimsTXDetails->responsibility;	
				}
				echo "inaurance id";
				echo $insuranceID;  
				$claim_count++;
			}
			
		}echo "<pre>";print_r($dataArr);
	}
	

}