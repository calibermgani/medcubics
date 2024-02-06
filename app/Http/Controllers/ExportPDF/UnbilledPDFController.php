<?php

namespace App\Http\Controllers\ExportPDF;

use DB;
use Carbon;
use Request;
use Auth;
use FPDF;
use Session;
use App\Models\Icd as Icd;
use App\Models\Medcubics\Cpt as Cpt;
use App\Models\Practice as Practice;
use App\Models\Provider as Provider;
use App\Models\Facility as Facility;
use App\Models\Insurance as Insurance;
use App\Http\Controllers\Controller;
use App\Models\ReportExport as ReportExport;
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
use App\Models\ReportExportTask as ReportExportTask;
use App\Http\Controllers\Reports\Api\ReportApiController as ReportApiController;
use App\Http\Controllers\ExportPDF\AbbreviationController as AbbreviationController;
use App\Http\Controllers\Reports\Financials\Api\FinancialApiController as FinancialApiController;
use App\Http\Controllers\Reports\ReportController as ReportController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;

$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);

class UnbilledPDFController extends Controller
{
    public function index()
    {
    	set_time_limit(300);
    	try {
	    	$created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
			$filename = 'Unbilled_Claims_Analysis_'.$created_date.'.pdf';
			$url = Request::url();
	        $request = Request::All();

			$user_id = Auth()->user()->id;
			$report_export = new ReportExport;
			$practice_id = Session::get('practice_dbid');
			$counts = ReportExport::selectRaw('count(report_name) as counts')->where('report_name',$request['report_name'])->groupby('report_name')->where('practice_id',$practice_id)->value('counts');
	        $report_count = (isset($counts) && $counts != null && !empty($counts))?($counts+1):1;
	        $report_export->report_count = $report_count;
			$report_export->practice_id = $practice_id;
			$report_export->report_name = $request['report_name'];
			$report_export->report_url = $url;
			$report_export->report_type = $request['export'];
			$report_export->report_file_name = $filename;
			$report_export->report_controller_name = 'FinancialApiController';
			$report_export->report_controller_func = 'getUnbilledClaimApiSP';
			$report_export->status = 'Inprocess';
			$report_export->created_by = $user_id;
			$report_export->save();
			$report_export_id = $report_export->id;
	    	
	        $controller = New FinancialApiController;
	        $api_response = $controller->getUnbilledClaimApiSP('pdf');
	        $api_response_data = $api_response->getData();
	        $unbilled_claim_details = $api_response_data->data->unbilled_claim_details;
	        $header = $search_by = (array)$api_response_data->data->search_by; 

	        // Parameters for filter
	        $text = $headers = [];
			$i = 0;
	        if(!empty($header)) {
	            foreach ((array)$header as $key => $val) {
	                $text[] = $key."=".(is_array($val)? $val[0] : $val);                           
	                $i++; 
	            }
	        }    
	        $headers = implode('&', $text);

	        $pdf = new unbilled_mypdf("L","mm","A4");
			$pdf->SetMargins(2,6);
			$pdf->SetTextColor(100,100,100);
			$pdf->SetDrawColor(217, 217, 217);
			$pdf->AddPage();
			$pdf->AddFont('Calibri','','calibri.php');
			$pdf->SetFont('Calibri','',7);
			$grand_total = '';
	        self::BladeContent($unbilled_claim_details, $pdf);        
	        // $pdf->Output($filename,'F');

	        /* google bucket integration */
	        $target_dir = $practice_id.DS.'reports'.DS.$user_id.DS.date('my', strtotime($report_export->created_at));
			$resp = $pdf->Output($filename,'S');
	        $data['filename'] = $filename;
	        $data['contents'] = $resp;
	        $data['target_dir'] = $target_dir;
			// Upload to google bucket code
	        Helpers::uploadResourceFile('reports', $practice_id, $data);	

	        $report_export->update(['parameter' => $headers, 'status' => 'Pending']);
        } catch(Exception $e) {
	    	\Log::info("Error Occured While export Unbilled report. Message:".$e->getMessage() );
	    }
		exit();
	}

	public function BladeContent($unbilled_claim_details, $pdf){
		$grand_total = 0;
		foreach ($unbilled_claim_details as $lists) {
			$pdf->AddFont('Calibri','','calibri.php');
		    $pdf->SetFont('Calibri','',7);
			$pdf->SetTextColor(100, 100, 100);
			$c_width=(290/11);
			$c_height=5;// cell height

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($lists->account_no)? $lists->account_no : '-Nill-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width-10,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($lists->patient_name)? $lists->patient_name : '-Nill-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width+10,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($lists->dos)? $lists->dos : '-Nill-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($lists->claim_number)? $lists->claim_number : '-Nill-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($lists->insurance_short_name)? $lists->insurance_short_name : '-Nill-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($lists->facility_short_name)? $lists->facility_short_name : '-Nill-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($lists->rendering_provider_short_name)? $lists->rendering_provider_short_name : '-Nill-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($lists->billing_provider_short_name)? $lists->billing_provider_short_name : '-Nill-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($lists->created_at)? $lists->created_at :'-Nill-' ;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($lists->daysSinceCreatedCount)? $lists->daysSinceCreatedCount : '-Nill-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($lists->total_charge)? $lists->total_charge : '-Nill-';
			$lengthToSplit = strlen($text);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			else{
				$pdf->SetTextColor(100, 100, 100);	
			}
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

			$grand_total = $grand_total + $lists->total_charge;
			$pdf->Ln();

			$abb_facility[] = @$lists->facility_short_name." - ".@$lists->facility_name;
	    	$abb_facility = array_unique($abb_facility);
			foreach (array_keys($abb_facility, ' - ') as $key) {
	        	unset($abb_facility[$key]);
	    	}

	    	$abb_billing[] = @$lists->billing_provider_short_name." - ".@$lists->billing_provider_name;
			$abb_billing = array_unique($abb_billing);
			foreach (array_keys($abb_billing, ' - ') as $key) {
	        	unset($abb_billing[$key]);
	    	}

	    	$abb_rendering[] = @$lists->rendering_provider_short_name." - ".@$lists->rendering_provider_name;
	    	$abb_rendering = array_unique($abb_rendering);
			foreach (array_keys($abb_rendering, ' - ') as $key) {
	        	unset($abb_rendering[$key]);
	    	}

	    	$abb_insurance[] = @$lists->insurance_short_name." - ".@$lists->insurance_name;
        	$abb_insurance = array_unique($abb_insurance);
    		foreach (array_keys($abb_insurance, ' - ') as $key) {
            	unset($abb_insurance[$key]);
        	}
		}	
		$abbreviation = ['abb_facility' => $abb_facility, 'abb_rendering' => $abb_rendering, 'abb_insurance' => $abb_insurance, 'abb_billing' => $abb_billing];
		$abb_controller = New AbbreviationController;
		$abb_controller->abbreviation($abbreviation,$pdf);
	}

}


class unbilled_mypdf extends FPDF
{
	public function header(){

		$request = Request::All();
    	// dd($request);
        $start_date = $end_date = $dos_start_date = $dos_end_date = $billing_provider_id = $rendering_provider_id = $facility_id = $insurance_id = $user_ids = '';
        $search_by = [];

        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
            if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
            $date = explode('-', $request['select_transaction_date']);
            $start_date = date("Y-m-d", strtotime($date[0]));
            if ($start_date == '1970-01-01') {
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));
        } else {
            $start_date = date('Y-m-01'); // hard-coded '01' for first day
            $end_date = date('Y-m-t');
        }
        if($start_date != ""&& $end_date != ""){
            $search_by['Transaction Date'] = date("m/d/y", strtotime($start_date)) . ' to ' . date("m/d/y", strtotime($end_date));
        }

        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='DOS')) {
            if(isset($request['select_date_of_service']) && !empty($request['select_date_of_service'])){
                $date = explode('-', $request['select_date_of_service']);
                $dos_start_date = date("Y-m-d", strtotime($date[0]));
                if ($dos_start_date == '1970-01-01') {
                    $dos_start_date = '0000-00-00';
                }
                $dos_end_date = date("Y-m-d", strtotime($date[1]));
                $search_by['DOS Date'] = date("m/d/y", strtotime($dos_start_date)) . ' to ' . date("m/d/y", strtotime($dos_end_date));
            }
        }

        if (isset($request['billing_provider_id']) && $request['billing_provider_id'] != '') {
            $billing_provider_id = $request['billing_provider_id'];
            if (strpos($request['billing_provider_id'], ',') !== false){
                $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name")->whereIn('id', explode(',', $request['billing_provider_id']))->get()->toArray();
            }else{
                $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name")->where('id', $request['billing_provider_id'])->get()->toArray();
            }
            $search_by["Billing Provider"] =  @array_flatten($provider)[0];
        }

        if (isset($request['rendering_provider_id']) && $request['rendering_provider_id'] != '') {
            $rendering_provider_id = $request['rendering_provider_id'];
            if (strpos($request['rendering_provider_id'], ',') !== false){
                $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name")->whereIn('id', explode(',', $request['rendering_provider_id']))->get()->toArray();
            }else{
                $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name")->where('id', $request['rendering_provider_id'])->get()->toArray();
            }
            $search_by["Rendering Provider"] =  @array_flatten($provider)[0];
        }
        if (isset($request['facility_id']) && $request['facility_id'] != '') {
            $facility_id = $request['facility_id'];
            if (strpos($request['facility_id'], ',') !== false){
                $facility = Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ', ') as facility_name")->whereIn('id', explode(',', $request['facility_id']))->get()->toArray();
            }else{
                $facility = Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ', ') as facility_name")->where('id', $request['facility_id'])->get()->toArray();
            }
            $search_by["Facility Name"] =  @array_flatten($facility)[0];
        }
        if (isset($request['insurance_id']) && $request['insurance_id'] != '') {
            $insurance_id = $request['insurance_id'];
            if (strpos($request['insurance_id'], ',') !== false) {
                $insurance = Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ',  ') as short_name")->whereIn('id', explode(',', $request['insurance_id']))->get()->toArray();
            } else {
                $insurance = Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ', ') as short_name")->where('id', $request['insurance_id'])->get()->toArray();
            }
            $search_by["Insurance"] =  @array_flatten($insurance)[0];
        }
        if (isset($request['user']) && !empty($request['user'])) {
            $req_user = explode(',', $request['user']);
            $user_ids = $request['user'];
            foreach ($req_user as $key => $value) {
                $short_name[] = DB::connection('responsive')->table('users')
                    ->whereIn('id',explode(',', $value))
                    ->pluck('short_name')->first();
            }
            $search_by["User"] = implode(',',$short_name);
        }

		$this->SetFillColor(255, 255, 255);
		$this->SetTextColor(0, 135, 127);
	    $this->AddFont('Calibri-Bold','','calibrib.php');
	    $this->SetFont('Calibri-Bold','',10.5);
		$x_axis=$this->getx();
		$c_width = 295;
		$c_height = 0;
		$text = Practice::getPracticeName()." - Unbilled Claims Analysis";
		$lengthToSplit = strlen($text);
		$this->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","C");

        $this->AddFont('Calibri','','calibri.php');
        $this->SetFont('Calibri','',9.5);
		$this->SetTextColor(100, 100, 100);
		$this->Ln();

		$i = 0;
		$text = [];
		foreach ((array)$search_by as $key => $val) {
			$text[] = $key.":".@$val;                           
            $i++; 
		}
		$text_imp = implode(" | ", $text);
		$this->Vcell(295,12,$x_axis,$text_imp,160,"","C");

		$text = "User :";
		$lengthToSplit = strlen($text);
    	$this->SetFont('Calibri-Bold','',8.5);
		$this->SetTextColor(100, 100, 100);
		$this->Ln();
		$this->Vcell(10,10,$x_axis,$text,$lengthToSplit,"");

		$x_axis=$this->getx();
		$text = Auth::user()->short_name.' - '.Auth::user()->name;
		$lengthToSplit = strlen($text);
		$this->SetFont('Calibri-Bold','',8.5);
		$this->SetTextColor(240, 125, 8);
		$this->Vcell(30,10,$x_axis,$text,$lengthToSplit,"","");

		$x_axis=$this->getx();
		$text = "Created Date : ";
		$lengthToSplit = strlen($text);
        $this->SetFont('Calibri-Bold','',8.5);
		$this->SetTextColor(100, 100, 100);
		$this->Vcell(240,10,$x_axis,$text,$lengthToSplit,"","R");

		$x_axis=$this->getx();
		$text = Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y');
		$lengthToSplit = strlen($text);
        $this->SetFont('Times','B',7.5);
		$this->SetTextColor(0, 135, 127);
		$this->Vcell(10,10,$x_axis,$text,$lengthToSplit,"","L");

		$this->Ln();

		$header = ['Acc No', 'Patient Name', 'DOS', 'Claim No', 'Payer', 'Facility', 'Rendering', 'Billing', 'Created Date', 'Days Since Created', 'Charges($)'];

		foreach ($header as $key => $value) {
			$this->SetFont('Calibri-Bold','',8);
			$this->SetTextColor(100, 100, 100);
			$x_axis=$this->getx();// now get current pdf x axis value
			$c_height = 6;// cell height
			$c_width = (290/count($header));// cell width 
			$lengthToSplit = strlen($value);
			if ($value == "Charges($)") {
				$align = "R";
			}else{
				$align = "L";
			}
			if ($value == "Acc No") {
				$c_width = $c_width-10;
			}
			if ($value == "Patient Name") {
				$c_width = $c_width+10;
			}
			$this->vcell($c_width,$c_height,$x_axis,$value,$lengthToSplit,"B",@$align);// pass all values inside the cell 
		}

		$this->Ln();
	}


	public function footer()
	{
	    $this->AddFont('Calibri','','calibri.php');
	    $this->SetFont('Calibri','',9);
	    $this->SetY(200);
	    $x_axis=$this->getx();
	    $c_width = 200;
	    $c_height = 0;
	    $year = date('Y');
	    $this->SetTextColor(82,82,82);
	    //$this->SetFont('Times','',7);
	    $text =  "Copyright ". chr(169) ." ".$year." Medcubics. All Rights Reserved.";
	    $lengthToSplit = strlen($text);
	    $this->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,'', "L");
	    $c_width = 290;
	    $text =  "Page No :".$this->PageNo();
	    $this->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,'', "R");
	}

	public function vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,$border="",$align="L")
	{
		$w_w=$c_height/3;
		$w_w_1=$w_w+2;
		$w_w1=$w_w+$w_w+$w_w+3;
		$len=strlen($text);// check the length of the cell and splits the text into 7 character each and saves in a array 

		if($len>$lengthToSplit && $lengthToSplit > 0){
		$w_text=str_split($text,$lengthToSplit);
		$this->SetX($x_axis);
		$this->Cell($c_width,$w_w_1,$w_text[0],'','',$align,'');
		if(isset($w_text[1])){
		    $this->SetX($x_axis);
		    $this->Cell($c_width,$w_w1,$w_text[1],'','',$align,'');
		}
		$this->SetX($x_axis);
		$this->Cell($c_width,$c_height,'',$border,0,$align,0);
		}
		else{
		    $this->SetX($x_axis);
		    $this->Cell($c_width,$c_height,$text,$border,0,$align,0);
			}
	}
    
}