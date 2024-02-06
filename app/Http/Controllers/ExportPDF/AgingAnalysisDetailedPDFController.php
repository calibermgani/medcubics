<?php

namespace App\Http\Controllers\ExportPDF;

use DB;
use Carbon;
use Request;
use Auth;
use FPDF;
use Session;
use App\Models\Practice as Practice;
use App\Http\Controllers\Controller;
use App\Models\ReportExport as ReportExport;
use App\Http\Controllers\Reports\Financials\Api\FinancialApiController as FinancialApiController;
use App\Http\Controllers\ExportPDF\AbbreviationController as AbbreviationController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;

$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);

class AgingAnalysisDetailedPDFController extends Controller
{
    public function index()
    {
    	set_time_limit(300);
    	try {
	    	$created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
			$filename = 'Aging_Analysis_Detailed_'.$created_date.'.pdf';
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
			$report_export->report_controller_func = 'getAgingReportSearchApiSP';
			$report_export->status = 'Inprocess';
			$report_export->created_by = $user_id;
			$report_export->save();
			$report_export_id = $report_export->id;

	        $controller = New FinancialApiController;
	        $api_response = $controller->getAgingReportSearchApiSP('pdf');
	        $api_response_data = $api_response->getData();
	        $aging_report_list = $api_response_data->data->aging_report_list;
	        $search_lable = $api_response_data->data->search_lable;
	        $search_by = $api_response_data->data->search_by;
	        $summaries = $api_response_data->data->summaries;

	        // Parameters for filter
	        $search_by = (array)$api_response_data->data->search_by;        
	        $text = $headers = [];
	        $i = 0;
	        if(!empty($search_by)) {
	            foreach ((array)$search_by as $key => $val) {
	                $text[] = $key."=".(is_array($val)? $val[0] : $val);                           
	                $i++; 
	            }
	        }    
	        $headers = implode('&', $text);
	        
	        $pdf = new aging_analysis_mypdf("L","mm","A4");
			$pdf->SetMargins(2,6);
			$pdf->SetTextColor(100,100,100);
			$pdf->SetDrawColor(217, 217, 217);
			$pdf->AddPage();
			$pdf->AddFont('Calibri','','calibri.php');
			$pdf->SetFont('Calibri','',7);
	        self::BladeContent($aging_report_list, $pdf, $search_lable, $summaries);
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
	    	\Log::info("Error Occured While export Aging Analysis Detailed report. Message:".$e->getMessage() );
	    }
		exit();
	}

	public function BladeContent($aging_report_list, $pdf, $search_lable, $summaries){
		$temp_id = $cnt = 0; $label = $search_lable.'_id';
		foreach($aging_report_list as  $result){
			if(($search_lable == 'billing_provider' || $search_lable == 'rendering_provider' || $search_lable == 'facility')){
				$cnt++;
				if($temp_id!=0 && $temp_id != $result->$label){
					$pdf->AddFont('Calibri','','calibri.php');
				    $pdf->SetFont('Calibri','',7);
					$pdf->SetTextColor(100, 100, 100);
					$c_width=(290/12);
					$c_height=5;// cell height

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = "Totals";
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					for ($i=0; $i < 10; $i++) { 
						$x_axis=$pdf->getx();// now get current pdf x axis value
						$text = "";
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
					}
					$id = $result->billing_provider_id;

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = Helpers::priceFormat($summaries->$temp_id->total_charge);
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					if($show_flag == "All" || $show_flag == "Unbilled"){
						$x_axis=$pdf->getx();// now get current pdf x axis value
						$text = Helpers::priceFormat($summaries->$temp_id->unbilled);
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
					}

					if($show_flag == "All" || $show_flag == "0-30"){
						$x_axis=$pdf->getx();// now get current pdf x axis value
						$text = Helpers::priceFormat($summaries->$temp_id->days30);
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");	
					}

					if($show_flag == "All" || $show_flag == "31-60"){
						$x_axis=$pdf->getx();// now get current pdf x axis value
						$text = Helpers::priceFormat($summaries->$temp_id->days60);
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");	
					}

					if($show_flag == "All" || $show_flag == "61-90"){
						$x_axis=$pdf->getx();// now get current pdf x axis value
						$text = Helpers::priceFormat($summaries->$temp_id->days90);
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");	
					}

					if($show_flag == "All" || $show_flag == "91-120"){
						$x_axis=$pdf->getx();// now get current pdf x axis value
						$text = Helpers::priceFormat($summaries->$temp_id->days120);
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");	
					}

					if($show_flag == "All" || $show_flag == "121-150"){
						$x_axis=$pdf->getx();// now get current pdf x axis value
						$text = Helpers::priceFormat($summaries->$temp_id->days150);
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");	
					}

					if($show_flag == "All" || $show_flag == "150-above"){
						$x_axis=$pdf->getx();// now get current pdf x axis value
						$text = Helpers::priceFormat($summaries->$temp_id->daysabove);
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");	
					}

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = Helpers::priceFormat($summaries->$temp_id->total_pat);
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = Helpers::priceFormat($summaries->$temp_id->total_ins);
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = Helpers::priceFormat($summaries->$temp_id->total);
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					for ($i=0; $i < 2; $i++) { 
						$x_axis=$pdf->getx();// now get current pdf x axis value
						$text = "";
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
					}
					$pdf->Ln();
				}
			}
		}
	}
}

class aging_analysis_mypdf extends FPDF
{
	public function header(){

		$request = Request::All();
    	// dd($request);
        $controller = New FinancialApiController;
        $api_response = $controller->getAgingReportSearchApiSP('pdf');
        $api_response_data = $api_response->getData();
        $show_flag = $api_response_data->data->show_flag;
        $search_by = $api_response_data->data->search_by;

		$this->SetFillColor(255, 255, 255);
		$this->SetTextColor(0, 135, 127);
	    $this->AddFont('Calibri-Bold','','calibrib.php');
	    $this->SetFont('Calibri-Bold','',10.5);
		$x_axis=$this->getx();
		$c_width = 295;
		$c_height = 0;
		$text = Practice::getPracticeName()." - Aging Analysis - Detailed";
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

		$header = ['Acc No', 'Patient Name', 'Claim No', 'DOS', 'Responsibility', 'Policy ID', 'Billing', 'Rendering', 'Facility', 'First Submission Date', 'Last Submission Date','Charges ($)',(($show_flag == "All" || $show_flag == "Unbilled")?'Unbilled($)':''), (($show_flag == "All" || $show_flag == "0-30")?'0-30 ($)':''), (($show_flag == "All" || $show_flag == "31-60")?'31-60 ($)':''), (($show_flag == "All" || $show_flag == "61-90")?'61-90 ($)':''), (($show_flag == "All" || $show_flag == "91-120")?'91-120 ($)':''), (($show_flag == "All" || $show_flag == "121-150")?'121-150 ($)':''), (($show_flag == "All" || $show_flag == "150-above")?'>150 ($)':''), 'Pat AR ($)', 'Ins AR ($)', 'Tot AR ($)', 'AR Days', 'Claim Status'];

		foreach ($header as $key => $value) {
			if ($value == '') {
				unset($header[$key]);
			}
		}

		foreach ($header as $key => $value) {
			$this->SetFont('Calibri-Bold','',8);
			$this->SetTextColor(100, 100, 100);
			$x_axis=$this->getx();// now get current pdf x axis value
			$c_height = 6;// cell height
			
			$c_width = (290/count($header));// cell width 
			$lengthToSplit = strlen($value);
			$align = "L";

			if ($value == "Patient Name" || $value == "Responsibility") {
				$c_width = $c_width+5;
			}
			if ($value == "First Submission Date" || $value == "Last Submission Date") {
				$lengthToSplit = 11;
				$c_width = $c_width+2;
			}
			if ($value == "Charges ($)") {
				$lengthToSplit = 8;
				$align = "R";
			}
			if ($value == "Unbilled($)") {
				$lengthToSplit = 8;
				$align = "R";
			}
			if ($value == "0-30 ($)") {
				$lengthToSplit = 5;
				$align = "R";
			}
			if ($value == "31-60 ($)" || $value == "61-90 ($)") {
				$lengthToSplit = 6;
				$align = "R";
			}
			if ($value == "91-120 ($)") {
				$lengthToSplit = 7;
				$align = "R";
			}
			if ($value == "121-150 ($)") {
				$lengthToSplit = 8;
				$align = "R";
			}
			if ($value == ">150 ($)") {
				$lengthToSplit = 5;
				$align = "R";
			}
			if ($value == "Pat AR ($)") {
				$lengthToSplit = 7;
				$align = "R";
			}
			if ($value == "Ins AR ($)") {
				$lengthToSplit = 7;
				$align = "R";
			}
			if ($value == "Tot AR ($)") {
				$lengthToSplit = 7;
				$align = "R";
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
		} else {
		    $this->SetX($x_axis);
		    $this->Cell($c_width,$c_height,$text,$border,0,$align,0);
		}
	}
    
}