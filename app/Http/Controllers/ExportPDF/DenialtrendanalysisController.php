<?php

namespace App\Http\Controllers\ExportPDF;

use DB;
use Carbon;
use Request;
use Auth;
use FPDF;
use Session;
use App\Models\Icd as Icd;
use App\Models\Practice as Practice;
use App\Models\Medcubics\Cpt as Cpt;
use App\Http\Controllers\Controller;
use App\Models\ReportExport as ReportExport;
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
use App\Http\Controllers\Reports\Api\ReportApiController as ReportApiController;
use App\Http\Controllers\Reports\Financials\Api\FinancialApiController as FinancialApiController;
use App\Http\Controllers\Reports\ReportController as ReportController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;

$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);

class DenialtrendanalysisController extends Controller
{	
	public function index(){
		try {
	    	$created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
			$filename = 'Denial_Trend_Analysis_'.$created_date.'.pdf';
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
			$report_export->report_controller_func = 'getDenialAnalysisListApiSP';
			$report_export->status = 'Inprocess';
			$report_export->created_by = $user_id;
			$report_export->save();
			$report_export_id = $report_export->id;

		    $controller = New FinancialApiController;
		    $api_response = $controller->getDenialAnalysisListApiSP('pdf');
		    $api_response_data = $api_response->getData();
		    $denial_cpt_list = $api_response_data->data->export_array;
		    $workbench_status = $api_response_data->data->workbench_status;
		    $search_by = $api_response_data->data->search_by;

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
		    
		    $date = date('m-d-Y');
		    $pdf = new denial_trend_mypdf("L","mm",array(400,100));
			$pdf->SetMargins(2,6,2,true);
			$pdf->SetDrawColor(217, 217, 217);
			$pdf->SetTextColor(100,100,100);
			$pdf->AddPage();
			$pdf->AddFont('Calibri','','calibrib.php');
			$pdf->SetFont('Calibri','',7);

		    self::BladeContent($denial_cpt_list, $pdf, $request);
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
	    	\Log::info("Error Occured While export Denial Trend Analysis report. Message:".$e->getMessage() );
	    }
		exit();
	}

	public function BladeContent($denial_cpt_list, $pdf, $request){
		foreach ($denial_cpt_list as $result) {
			// dd($result);
			$pdf->AddFont('Calibri','','calibri.php');
		    $pdf->SetFont('Calibri','',7);
			$pdf->SetTextColor(100, 100, 100);
			$x_axis=$pdf->getx();// now get current pdf x axis value
			$c_height=5;// cell height
			if ($request['workbench_status'] == "Include") {
				$c_width = (400/16);
			}
			else{
				$c_width=(400/15);
			}


			// if(isset($result->claim_number) && $result->claim_number != ''){

			// }

            $responsibility = 'Patient';
            $ins_category = 'Patient';
            $responsibility = Helpers::getInsuranceName(@$result->lastcptdenialdesc->claimcpt_txn->claimtxdetails->payer_insurance_id);
            $ins_category = @$result->lastcptdenialdesc->claimcpt_txn->claimtxdetails->ins_category;
            $cpt_info_id = @$result->claim_cpt_info_id;

            $text = @$result->claim_number;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();
			$text = Helpers::checkAndDisplayDateInInput(@$result->dos,'','Nil');
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();
			$text =  @$result->account_no;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();
			$text = @$result->patient_name;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();
			$text = @$result->responsibility;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();
			$text = @$result->ins_category;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();
			$text = @$result->rendering_short_name;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();
			$text = @$result->facility_short_name;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();
			$text = @$result->cpt_code;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();
			$text = @$result->denial_date;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();
			if(@$result->denial_code != ''){
				$text = rtrim(@$result->denial_code,',');
			}
			else{
				$text = '-Nil-';
			}
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();
			$text = !empty($result->claim_age_days)?@$result->claim_age_days:'-Nil-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			if($request['workbench_status'] == "Include"){
				$x_axis=$pdf->getx();
				if(isset($result->last_workbench_status) && $result->last_workbench_status != null){
					$text = @$result->last_workbench_status;
					$lengthToSplit = 10;
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
				}
				else{
					$text = "N/A";
					$lengthToSplit = 10;
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");		
				}
			}

			$x_axis=$pdf->getx();
			if(isset($result->sub_status_desc) && $result->sub_status_desc != null){
				$text = $result->sub_status_desc;
			}
			else{
				$text = '-Nil-';
			}
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width-5,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();
			$text = Helpers::priceFormat(@$result->charge,'',1);
			$lengthToSplit = strlen($text);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			else{
				$pdf->SetTextColor(100, 100, 100);	
			}
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

			$x_axis=$pdf->getx();
			$text = Helpers::priceFormat(@$result->total_ar_due,'',1);
			$lengthToSplit = strlen($text);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			else{
				$pdf->SetTextColor(100, 100, 100);	
			}
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
			$pdf->Ln();
		}
	}
}

class denial_trend_mypdf extends FPDF
{
	public function header($search_by = ''){
	    $this->SetFillColor(255, 255, 255);
		$this->SetTextColor(0, 135, 127);
	    $this->AddFont('Calibri-Bold','','calibrib.php');
	    $this->SetFont('Calibri-Bold','',10.5);
		$x_axis=$this->getx();
		$c_width = 400;
		$c_height = 0;
		$text = Practice::getPracticeName()." - Denial Trend Analysis";
		$lengthToSplit = strlen($text);
		$this->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","C");

        $this->AddFont('Calibri','','calibri.php');
        $this->SetFont('Calibri','',9.5);
		$this->SetTextColor(100, 100, 100);
		$this->Ln();

		$request = Request::All();
	    $controller = New FinancialApiController;
	    $api_response = $controller->getDenialAnalysisListApiSP('pdf');
	    $api_response_data = $api_response->getData();
	    $search_by = $api_response_data->data->search_by;
		$i = 0;
		$text = [];
		foreach ((array)$search_by as $key => $val) {
			$text[] = $key.":".@$val[0];                           
            $i++; 
		}
		$text_imp = implode(" | ", $text);
		$this->Vcell(400,12,$x_axis,$text_imp,160,"","C");

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
		$this->Vcell(300,10,$x_axis,$text,$lengthToSplit,"","R");

		$x_axis=$this->getx();
		$text = Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y');
		$lengthToSplit = strlen($text);
        $this->SetFont('Times','B',7.5);
		$this->SetTextColor(0, 135, 127);
		$this->Vcell(10,10,$x_axis,$text,$lengthToSplit,"","L");

		$this->Ln();

		$header = ['Claim No', 'DOS', 'Acc No', 'Patient Name', 'Insurance', 'Category', 'Rendering', 'Facility', 'Denied CPT', 'Denied Date', 'Denial Reason Code', 'Claim Age', 'Claim Sub Status' ,'Charge Amt($)', 'Outstanding AR($)'];
		if ($request['workbench_status'] == "Include") {
			array_splice($header, 12, 0, 'Workbench Status');
		}
		foreach ($header as $key => $value) {
			$this->SetFont('Calibri-Bold','',8);
			$this->SetTextColor(100, 100, 100);
			$x_axis=$this->getx();// now get current pdf x axis value
			$c_height = 6;// cell height
			if ($request['workbench_status'] == "Include")
			$c_width = (400/16);// cell width 
			else
			$c_width = (400/15);// cell width 
			$lengthToSplit = strlen($value);
			if ($value == "Charge Amt($)" || $value == "Outstanding AR($)") {
				$align = 'R';
			}else{
				$align = 'L';
			}
			if ($value == "Denial Reason Code") {
				$lengthToSplit = 7;
			}
			if ($value == 'Workbench Status') {
				$lengthToSplit = 10;
			}
			if ($value == 'Claim Sub Status') {
				$c_width = $c_width-5;
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
	    $c_width = 400;
	    $c_height = 0;
	    $year = date('Y');
	    $this->SetTextColor(82,82,82);
	    //$this->SetFont('Times','',7);
	    $text =  "Copyright ". chr(169) ." ".$year." Medcubics. All Rights Reserved.";
	    $lengthToSplit = strlen($text);
	    $this->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,'', "L");
	    $c_width = 400;
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