<?php namespace App\Http\Controllers\Claims;

use View;
use Request;
use Redirect;
use File;
use Response;
use App\Http\Helpers\Helpers as Helpers;

class ClaimController extends Api\ClaimApiController
{
    public function __construct()
    {
        View::share('heading', 'Claims');
        View::share('selected_tab', 'claims');
        View::share('heading_icon', 'shopping-cart');
    }

    public function index($type='tosubmit')
    {   
        if($type == 'hold')
        {
            $heading = 'Hold Claims';
            $heading_icon = 'fa-lock';
        }
        elseif($type == 'submitted')
        {
            $heading = 'Submitted';
            $heading_icon = 'fa-check';
        } 
		elseif($type == 'pending')
        {
            $heading = 'Pending Claim';
            $heading_icon = 'fa-exclamation-triangle';
        }
        elseif($type == 'rejected')
        {
            $heading = 'Rejections';
            $heading_icon = 'fa-ban';
        }        
        else
        {
            $heading = 'To submit';
            $heading_icon = 'fa-cart-plus';
        }
            
		View::share('heading', $heading);
		View::share('heading_icon', $heading_icon);
        View::share('selected_tab', $type);
        $api_response = $this->getIndexApi($type);
        $api_response_data = $api_response->getData();
        $claims = $api_response_data->data->claims;  
		//dd($claims);
        //$hold_options = $api_response_data->data->hold_options;
        if(Request::ajax())
        {            
            return view('claims/claims/claims_listing',compact('claims','type'));
        }
        else
        {
            $patients = $api_response_data->data->patients; 
            $billing_provider = $api_response_data->data->billing_provider;
            $rendering_provider = $api_response_data->data->rendering_provider;
            $referring_provider = $api_response_data->data->referring_provider;
            $insurances = $api_response_data->data->insurances;
            $facility = $api_response_data->data->facility; 
            return view('claims/claims/claims',compact('claims','type','patients','billing_provider','rendering_provider','referring_provider','insurances','facility'));
        }
    }

    public function indexTableData($type='tosubmit') {  
        if($type == 'hold') {
            $heading = 'Hold Claims';
            $heading_icon = 'fa-lock';
        } elseif($type == 'submitted') {
            $heading = 'Submitted';
            $heading_icon = 'fa-check';
        } elseif($type == 'pending') {
            $heading = 'Pending Claim';
            $heading_icon = 'fa-exclamation-triangle';
        } elseif($type == 'rejected') {
            $heading = 'Rejections';
            $heading_icon = 'fa-ban';
        } else {
            $heading = 'To submit';
            $heading_icon = 'fa-cart-plus';
        }
            
        View::share('heading', $heading);
        View::share('heading_icon', $heading_icon);
        View::share('selected_tab', $type);
        $api_response = $this->getIndexApi($type);
        $api_response_data = $api_response->getData();

        $claims     = (array)$api_response_data->data->claims;  
        $patients   = (array)$api_response_data->data->patients; 

        $billing_provider = (array)$api_response_data->data->billing_provider;
        $rendering_provider = (array)$api_response_data->data->rendering_provider;
        $referring_provider = (array)$api_response_data->data->referring_provider;
        $insurances = (array)$api_response_data->data->insurances;
        $facility = (array)$api_response_data->data->facility; 
        if($type == "rejected"){
            $view_html      = Response::view('claims/claims/rejection_listing_ajax', compact('claims','type','patients','billing_provider','rendering_provider','referring_provider','insurances','facility'));

        } else {
            $view_html      = Response::view('claims/claims/claim_list_ajax', compact('claims','type','patients','billing_provider','rendering_provider','referring_provider','insurances','facility'));
        }
        //$view_html      = Response::view('claims/claims/claim_list_ajax', compact('claims','type','patients','billing_provider','rendering_provider','referring_provider','insurances','facility'));

        $content_html   = htmlspecialchars_decode($view_html->getContent());
        $content        = array_filter(explode("</tr>",trim($content_html)));
        $request = Request::all();  
        if(!empty($request['draw']))
        $data['draw'] = $request['draw'];   
        $data['data'] = $content;   
        $data['datas'] = ['id'=>2 ];    
        $data['recordsTotal'] = $api_response_data->data->count;
        $data['recordsFiltered'] = $api_response_data->data->count;     
        
        return Response::json($data);
    }
    
    public function GetHtml($claims, $type, $patients, $billing_provider, $rendering_provider, $referring_provider, $insurances, $facility)
    {
        if($type == 'rejected'){
            return Response::view('claims/claims/rejection_listing_ajax', compact('claims','type','patients','billing_provider','rendering_provider','referring_provider','insurances','facility'));

        } else {
            return Response::view('claims/claims/claim_list_ajax', compact('claims','type','patients','billing_provider','rendering_provider','referring_provider','insurances','facility'));
        }

       //return Response::view('claims/claims/claim_list_ajax', compact('claims','type','patients','billing_provider','rendering_provider','referring_provider','insurances','facility'));
    }

    public function getHoldReason()
    {
        $api_response = $this->getHoldReasonApi();
        $api_response_data = $api_response->getData();
        $hold_options = (array)$api_response_data->data->hold_options;
        return view('claims/claims/hold_option',compact('hold_options'));
    }
    public function initialScrubbing()
    {
        $api_response = $this->initialScrubbingApi();
        $api_response_data = $api_response->getData();
       // $claims = (array) $api_response_data->data->claims;     
        //return view('claims/claims/claims',compact('claims'));
    }
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
    public function getEdiReports()
    {
        View::share('selected_tab', 'edireports');
        View::share('heading', 'EDI Reports');
        View::share('heading_icon', 'fa-file-text-o');
        $api_response = $this->getEdiReportsApi();
        $api_response_data = $api_response->getData();
        $edi_reports = $api_response_data->data->edi_reports;
		return view('claims/claims/edi_reports',compact('edi_reports'));
    }
	public function getStatusEdiReports()
    {
        View::share('selected_tab', 'edireports');
        $api_response = $this->getStatusEdiReportsApi();
        $api_response_data = $api_response->getData();
        $edi_reports = $api_response_data->data->edi_reports;
		$list_page	= $api_response_data->data->list_page;
        return view('claims/claims/edi_reports_list',compact('edi_reports','list_page'));
    }
	
	/**
	 * View EDI file contents
	 *
	 * @param  $id - EDI report ID
	 * @return view if response status is equal to 'success' or else redirect to claims/edireports with the error message
	 */
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
     
    public function summary() {        
        
        View::share('selected_tab', 'claimsummary');
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();       	
        return view('claims/claims/claimsummary');
    }	
}