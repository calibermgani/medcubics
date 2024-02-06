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

class YearEndFinancialsPDFController extends Controller
{
    public function index()
    {
    	set_time_limit(300);
    	try {
	    	$created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
			$filename = 'Year_End_financials'.$created_date.'.pdf';
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
			// $report_export->parameter = $headers;
			$report_export->report_type = $request['export'];
			$report_export->report_file_name = $filename;
			$report_export->report_controller_name = 'ReportController';
			$report_export->report_controller_func = 'financialSearchExport';
			$report_export->status = 'Inprocess';
			$report_export->created_by = $user_id;
			$report_export->save();
			$report_export_id = $report_export->id;
	    	// dd($request);
	        $controller = New ReportController;
	        $api_response = $controller->financialSearchExport('pdf');
	        $claims = $api_response['claims'];

	        // Parameters for filter
	        $header = $search_by = (array)$api_response['search_by'];        
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
	        
	        $pdf = new yearEnd_mypdf("L","mm","A4");
			$pdf->SetMargins(2,6);
			$pdf->SetTextColor(100,100,100);
			$pdf->SetDrawColor(217, 217, 217);
			$pdf->AddPage();
			$pdf->AddFont('Calibri','','calibri.php');
			$pdf->SetFont('Calibri','',7);
			$grand_total = '';
			$pdf->Ln();
			$pdf->Ln();
			$pdf->Ln();
			$pdf->Ln();
			$pdf->Ln();
	        self::BladeContent($claims, $pdf);
			//$pdf->Output($filename,'F');

	        /* google bucket integration */
			$resp = $pdf->Output($filename,'S');
	        $data['filename'] = $filename;
	        $data['contents'] = $resp;
	        $target_dir = $practice_id.DS.'reports'.DS.$user_id.DS.date('my', strtotime($report_export->created_at));
	        $data['target_dir'] = $target_dir;
			// Upload to google bucket code
	        Helpers::uploadResourceFile('reports', $practice_id, $data);	

	        $report_export->update(['parameter' => $headers, 'status' => 'Pending']);
        } catch(Exception $e) {
	    	\Log::info("Error Occured While export Work RVU report. Message:".$e->getMessage() );
	    } 
		exit();
	}

	// public function index(){
	// 	set_time_limit(300);
 //        $request = Request::All();
 //        $req = $request['dataArr']['data'];
 //        $created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
	// 	$filename = 'Downloads/Year_End_financials_'.$created_date.'.pdf';
	// 	$url = Request::url();
	// 	$user_id = Auth()->user()->id;
	// 	$report_export = new ReportExport;
	// 	$practice_id = Session::get('practice_dbid');
	// 	$counts = ReportExport::selectRaw('count(report_name) as counts')->where('report_name',$request['report_name'])->groupby('report_name')->where('practice_id',$practice_id)->value('counts');
 //        $report_count = (isset($counts) && $counts != null && !empty($counts))?($counts+1):1;
 //        $report_export->report_count = $report_count;
	// 	$report_export->practice_id = $practice_id;
	// 	$report_export->report_name = $request['report_name'];
	// 	$report_export->report_url = $url;
	// 	$report_export->report_file_name = 'Downloads/Year_End_financials_'.$created_date.'.pdf';
	// 	$report_export->report_type = $request['export'];
	// 	// $report_export->parameter = $headers;
	// 	$report_export->report_controller_name = 'ReportController';
	// 	$report_export->report_controller_func = 'financialSearchExport';
	// 	$report_export->status = 'Inprocess';
	// 	$report_export->created_by = $user_id;
	// 	$report_export->save();
	// 	$report_export_id = $report_export->id;

 //        $controller = New ReportController;
 //        $api_response = $controller->financialSearchExport('pdf');
 //        $claims = $api_response['claims'];
 //        // $api_response_data = $api_response->getData();
 //        // $patients = (array) $api_response_data->data->patients;
 //        // $insurance_list = (array) $api_response_data->data->insurances;

 //        $pdf = new yearEnd_mypdf("L","mm","A4");
	// 	$pdf->SetMargins(2,6);
	// 	$pdf->SetTextColor(100,100,100);
	// 	$pdf->SetDrawColor(217, 217, 217);
	// 	$pdf->AddPage();
	// 	$pdf->AddFont('Calibri','','calibri.php');
	// 	$pdf->SetFont('Calibri','',7);
	// 	$pdf->Ln();
	// 	$pdf->Ln();
	// 	$pdf->Ln();
	// 	$pdf->Ln();
	// 	$pdf->Ln();
 //        self::BladeContent($claims, $pdf);
 //        $pdf->Output();
 //        // $report_export->update(['status'=>'Pending']);
	// 	exit();
	// }

	public function BladeContent($claims, $pdf){

		$count = 1;   
		$last_visit = [];

		$charges = 0;
		$total_adjustments = 0;
		$patient_payments = 0;
		$insurance_payments = 0;
		$patient_ar_due = 0;
		$insurance_ar_due = 0;
		$total_patient_adj =0;
		$total_ins_adj = $total_ref_patient = $total_ref_ins =0;
        $claims_count = 0;
		foreach ((array)$claims as $key => $claim_list) {
			$ins_adj = @$claim_list->insurance_adj;
            $claims_count += @$claim_list->claims_visits;

            $pdf->AddFont('Calibri','','calibri.php');
		    $pdf->SetFont('Calibri','',7);
			$pdf->SetTextColor(100, 100, 100);
			$c_width=(290/15);
			$c_height=5;// cell height

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = @$key."-".@$claim_list->year_key;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = @$claim_list->claims_visits;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$claim_list->value,'',1);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			else{
				$pdf->SetTextColor(100, 100, 100);	
			}
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$claim_list->patient_adjusted,'',1);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			else{
				$pdf->SetTextColor(100, 100, 100);	
			}
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$claim_list->insurance_adj,'',1);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			else{
				$pdf->SetTextColor(100, 100, 100);	
			}
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$claim_list->total_adjusted,'',1);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			else{
				$pdf->SetTextColor(100, 100, 100);	
			}
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis-1,@$text,$lengthToSplit,'', "R");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$claim_list->patient_refund,'',1);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			else{
				$pdf->SetTextColor(100, 100, 100);	
			}
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(-(@$claim_list->ins_refund),'',1);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			else{
				$pdf->SetTextColor(100, 100, 100);	
			}
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(-(@$claim_list->ins_refund) + @$claim_list->patient_refund,'',1);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			else{
				$pdf->SetTextColor(100, 100, 100);	
			}
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$claim_list->patient_payment,'',1);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			else{
				$pdf->SetTextColor(100, 100, 100);	
			}
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$claim_list->insurance_payment,'',1);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			else{
				$pdf->SetTextColor(100, 100, 100);	
			}
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis-1,@$text,$lengthToSplit,'', "R");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$claim_list->patient_payment + @$claim_list->insurance_payment,'',1);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			else{
				$pdf->SetTextColor(100, 100, 100);	
			}
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$claim_list->patient_due,'',1);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			else{
				$pdf->SetTextColor(100, 100, 100);	
			}
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$claim_list->insurance_due,'',1);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			else{
				$pdf->SetTextColor(100, 100, 100);	
			}
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis-1,@$text,$lengthToSplit,'', "R");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$claim_list->insurance_due + @$claim_list->patient_due,'',1);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			else{
				$pdf->SetTextColor(100, 100, 100);	
			}
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis+2,@$text,$lengthToSplit,'', "R");

			$pdf->Ln();

			$abb_facility[] = @$claim_list->facility_short_name." - ".@$claim_list->facility_name;
	    	$abb_facility = array_unique($abb_facility);
			foreach (array_keys($abb_facility, ' - ') as $key) {
	        	unset($abb_facility[$key]);
	    	}

	    	$abb_rendering[] = @$claim_list->rendering_short_name." - ".@$claim_list->rendering_name;
	    	$abb_rendering = array_unique($abb_rendering);
			foreach (array_keys($abb_rendering, ' - ') as $key) {
	        	unset($abb_rendering[$key]);
	    	}

	    	$count++;  
            $charges += @$claim_list->value;                                 
			$total_adjustments += @$claim_list->total_adjusted;
			$total_patient_adj += @$claim_list->patient_adjusted;
			$total_ins_adj += @$ins_adj;
			$patient_payments += @$claim_list->patient_payment;
			$total_ref_patient += @$claim_list->patient_refund;
			$total_ref_ins += @$claim_list->ins_refund;
			$insurance_payments += @$claim_list->insurance_payment;
			$patient_ar_due += @$claim_list->patient_due;
			$insurance_ar_due += @$claim_list->insurance_due;
			
		}

		$c_width=(290/15);
		$c_height=5;

		$x_axis=$pdf->getx();// now get current pdf x axis value
		$pdf->SetFont('Calibri-Bold','',7);
		$text = "Total";
		$lengthToSplit = strlen($text);
		$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

		$pdf->SetFont('Calibri','',7);
		$x_axis=$pdf->getx();// now get current pdf x axis value
		$text = $claims_count;
		$lengthToSplit = strlen($text);
		$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

		$x_axis=$pdf->getx();// now get current pdf x axis value
		$text = Helpers::priceFormat($charges,'',1);
		if ($text < 0) {
			$pdf->SetTextColor(255, 0, 0);
		}
		else{
			$pdf->SetTextColor(100, 100, 100);	
		}
		$lengthToSplit = strlen($text);
		$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

		$x_axis=$pdf->getx();// now get current pdf x axis value
		$text = Helpers::priceFormat($total_patient_adj,'',1);
		if ($text < 0) {
			$pdf->SetTextColor(255, 0, 0);
		}
		else{
			$pdf->SetTextColor(100, 100, 100);	
		}
		$lengthToSplit = strlen($text);
		$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

		$x_axis=$pdf->getx();// now get current pdf x axis value
		$text = Helpers::priceFormat(@$total_ins_adj,'',1);
		if ($text < 0) {
			$pdf->SetTextColor(255, 0, 0);
		}
		else{
			$pdf->SetTextColor(100, 100, 100);	
		}
		$lengthToSplit = strlen($text);
		$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

		$x_axis=$pdf->getx();// now get current pdf x axis value
		$text = Helpers::priceFormat(@$total_adjustments,'',1);
		if ($text < 0) {
			$pdf->SetTextColor(255, 0, 0);
		}
		else{
			$pdf->SetTextColor(100, 100, 100);	
		}
		$lengthToSplit = strlen($text);
		$pdf->Vcell($c_width,$c_height,$x_axis-1,@$text,$lengthToSplit,'', "R");

		$x_axis=$pdf->getx();// now get current pdf x axis value
		$text = Helpers::priceFormat(@$total_ref_patient,'',1);
    	if ($text < 0) {
			$pdf->SetTextColor(255, 0, 0);
		}
		else{
			$pdf->SetTextColor(100, 100, 100);	
		}
		$lengthToSplit = strlen($text);
		$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

		$x_axis=$pdf->getx();// now get current pdf x axis value
		$text = Helpers::priceFormat(-($total_ref_ins),'',1);
		if ($text < 0) {
			$pdf->SetTextColor(255, 0, 0);
		}
		else{
			$pdf->SetTextColor(100, 100, 100);	
		}
		$lengthToSplit = strlen($text);
		$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

		$x_axis=$pdf->getx();// now get current pdf x axis value
		$text = Helpers::priceFormat((-($total_ref_ins)) + (($total_ref_patient)),'',1);
		if ($text < 0) {
			$pdf->SetTextColor(255, 0, 0);
		}
		else{
			$pdf->SetTextColor(100, 100, 100);	
		}
		$lengthToSplit = strlen($text);
		$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

		$x_axis=$pdf->getx();// now get current pdf x axis value
		$text = Helpers::priceFormat(@$patient_payments,'',1);
		if ($text < 0) {
			$pdf->SetTextColor(255, 0, 0);
		}
		else{
			$pdf->SetTextColor(100, 100, 100);	
		}
		$lengthToSplit = strlen($text);
		$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

		$x_axis=$pdf->getx();// now get current pdf x axis value
		$text = Helpers::priceFormat(@$insurance_payments,'',1);
		if ($text < 0) {
			$pdf->SetTextColor(255, 0, 0);
		}
		else{
			$pdf->SetTextColor(100, 100, 100);	
		}
		$lengthToSplit = strlen($text);
		$pdf->Vcell($c_width,$c_height,$x_axis-1,@$text,$lengthToSplit,'', "R");

		$x_axis=$pdf->getx();// now get current pdf x axis value
		$text = Helpers::priceFormat($insurance_payments+$patient_payments,'',1);
		if ($text < 0) {
			$pdf->SetTextColor(255, 0, 0);
		}
		else{
			$pdf->SetTextColor(100, 100, 100);	
		}
		$lengthToSplit = strlen($text);
		$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

		$x_axis=$pdf->getx();// now get current pdf x axis value
		$text = Helpers::priceFormat(@$patient_ar_due,'',1);
		if ($text < 0) {
			$pdf->SetTextColor(255, 0, 0);
		}
		else{
			$pdf->SetTextColor(100, 100, 100);	
		}
		$lengthToSplit = strlen($text);
		$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

		$x_axis=$pdf->getx();// now get current pdf x axis value
		$text = Helpers::priceFormat(@$insurance_ar_due,'',1);
		if ($text < 0) {
			$pdf->SetTextColor(255, 0, 0);
		}
		else{
			$pdf->SetTextColor(100, 100, 100);	
		}
		$lengthToSplit = strlen($text);
		$pdf->Vcell($c_width,$c_height,$x_axis-1,@$text,$lengthToSplit,'','R');

		$x_axis=$pdf->getx();// now get current pdf x axis value
		$text = Helpers::priceFormat(($insurance_ar_due+$patient_ar_due),'',1);
		if ($text < 0) {
			$pdf->SetTextColor(255, 0, 0);
		}
		else{
			$pdf->SetTextColor(100, 100, 100);	
		}
		$lengthToSplit = strlen($text);
		$pdf->Vcell($c_width,$c_height,$x_axis+2,@$text,$lengthToSplit,'','R');

		$abbreviation = ['abb_facility' => $abb_facility, 'abb_rendering' => $abb_rendering];
		$abb_controller = New AbbreviationController;
		$abb_controller->abbreviation($abbreviation,$pdf);
		
	}
}

class yearEnd_mypdf extends FPDF{

	public function header(){

		$request = Request::All();
    	// dd($request);
		$controller = New ReportController;
        $api_response = $controller->financialSearchExport('pdf');
        $search_by = (array)$api_response['search_by']; 

		$this->SetFillColor(255, 255, 255);
		$this->SetTextColor(0, 135, 127);
	    $this->AddFont('Calibri-Bold','','calibrib.php');
	    $this->SetFont('Calibri-Bold','',10.5);
		$x_axis=$this->getx();
		$c_width = 295;
		$c_height = 0;
		$text = Practice::getPracticeName()." - Year End Financials";
		$lengthToSplit = strlen($text);
		$this->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","C");

        $this->AddFont('Calibri','','calibri.php');
        $this->SetFont('Calibri','',9.5);
		$this->SetTextColor(100, 100, 100);
		$this->Ln();

		$i = 0;
		$text = [];
		foreach ((array)$search_by as $key => $val) {
			$text[] = $key.":".@$val[0];                           
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
		$this->Headers();
	}

	public function Headers(){
		$this->SetFont('Calibri-Bold','',8);
		$this->SetTextColor(100, 100, 100);

		$this->SetY(33);
	    $this->SetX(2);
	    $this->MultiCell(290,12,"" ,'TBL', "L");
	    $this->SetY(39);
	    $this->SetX(3);
	    $this->MultiCell(0, 0.5,"Months" ,'', "L");
	    $this->SetY(33);
	    $this->SetX(21);
	    $this->MultiCell(290,12,"" ,'L', "L");
	    $this->SetY(39);
	    $this->SetX(22);
	    $this->MultiCell(0, 0.5,"Claims" ,'L', "L");
	    $this->SetY(33);
	    $this->SetX(40);
	    $this->MultiCell(290,12,"" ,'L', "L");
	    $this->SetY(39);
	    $this->SetX(41);
	    $this->MultiCell(0, 0.5,"Charges($)" ,'L', "L");
	    $this->SetY(33);
	    $this->SetX(60);
	    $this->MultiCell(290,12,"" ,'L', "L");
	    $this->SetY(38);
	    $this->SetX(60);
	    $this->MultiCell(232,1,"" ,'B', "L");
	    $this->SetY(36);	
	    $this->SetX(79);
	    $this->MultiCell(0, 0.5,"Adjustments($)" ,'', "L");
	    $this->SetY(42);	
	    $this->SetX(59);
	    $this->MultiCell(19, 0.5,"Patient" ,'', "R");
	    $this->SetY(39);	
	    $this->SetX(79);
	    $this->MultiCell(290,6,"" ,'L', "L");
	    $this->SetY(42);	
	    $this->SetX(78);
	    $this->MultiCell(19, 0.5,"Insurance" ,'', "R");
	    $this->SetY(39);	
	    $this->SetX(98);
	    $this->MultiCell(290,6,"" ,'L', "L");
	    $this->SetY(42);	
	    $this->SetX(97);
	    $this->MultiCell(19, 0.5,"Total" ,'', "R");
	    $this->SetY(33);	
	    $this->SetX(117);
	    $this->MultiCell(290,12,"" ,'L', "L");
	    $this->SetY(36);	
	    $this->SetX(136);
	    $this->MultiCell(0, 0.5,"Refunds($)" ,'', "L");
	    $this->SetY(42);	
	    $this->SetX(116);
	    $this->MultiCell(19, 0.5,"Patient" ,'', "R");
	    $this->SetY(39);	
	    $this->SetX(136);
	    $this->MultiCell(290,6,"" ,'L', "L");
	    $this->SetY(42);	
	    $this->SetX(136);
	    $this->MultiCell(19, 0.5,"Insurance" ,'', "R");
	    $this->SetY(39);	
	    $this->SetX(155);
	    $this->MultiCell(290,6,"" ,'L', "L");
	    $this->SetY(42);	
	    $this->SetX(155);
	    $this->MultiCell(19, 0.5,"Total" ,'', "R");
	    $this->SetY(33);	
	    $this->SetX(174);
	    $this->MultiCell(290,12,"" ,'L', "L");
	    $this->SetY(36);	
	    $this->SetX(193);
	    $this->MultiCell(0, 0.5,"Payments($)" ,'', "L");
	    $this->SetY(39);	
	    $this->SetX(174);
	    $this->MultiCell(290,6,"" ,'L', "L");
	    $this->SetY(42);	
	    $this->SetX(174);
	    $this->MultiCell(19, 0.5,"Patient" ,'', "R");
	    $this->SetY(39);	
	    $this->SetX(193);
	    $this->MultiCell(290,6,"" ,'L', "L");
	    $this->SetY(42);	
	    $this->SetX(193);
	    $this->MultiCell(19, 0.5,"Insurance" ,'', "R");
	    $this->SetY(39);	
	    $this->SetX(212);
	    $this->MultiCell(290,6,"" ,'L', "L");
	    $this->SetY(42);	
	    $this->SetX(212);
	    $this->MultiCell(19, 0.5,"Total" ,'', "R");
	    $this->SetY(33);	
	    $this->SetX(231);
	    $this->MultiCell(290,12,"" ,'L', "L");
	    $this->SetY(36);	
	    $this->SetX(250);
	    $this->MultiCell(0, 0.5,"AR Bal($)" ,'', "L");
	    $this->SetY(39);	
	    $this->SetX(250);
	    $this->MultiCell(290,6,"" ,'L', "L");
	    $this->SetY(42);	
	    $this->SetX(231);
	    $this->MultiCell(19, 0.5,"Patient" ,'', "R");
	    $this->SetY(39);	
	    $this->SetX(269);
	    $this->MultiCell(290,6,"" ,'L', "L");
	    $this->SetY(42);	
	    $this->SetX(250);
	    $this->MultiCell(19, 0.5,"Insurance" ,'', "R");
	    $this->SetY(33);	
	    $this->SetX(292);
	    $this->MultiCell(290,12,"" ,'L', "L");
	    $this->SetY(42);	
	    $this->SetX(271);
	    $this->MultiCell(19, 0.5,"Total" ,'', "R");
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
