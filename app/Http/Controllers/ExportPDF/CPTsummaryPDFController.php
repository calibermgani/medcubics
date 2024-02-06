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
use App\Http\Controllers\Reports\Practicesettings\Api\CptlistApiController as CptlistApiController;
use App\Http\Controllers\Reports\ReportController as ReportController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;

$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);

class CPTsummaryPDFController extends Controller
{
    public function index(){
		set_time_limit(300);
    	$created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
		$filename = 'Downloads/CPT_HCPCS_Summary_'.$created_date.'.pdf';
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
		$report_export->report_file_name = 'Downloads/CPT_HCPCS_Summary_'.$created_date.'.pdf';
		$report_export->report_controller_name = 'CptlistApiController';
		$report_export->report_controller_func = 'getFilterResultApiSP';
		$report_export->status = 'Inprocess';
		$report_export->created_by = $user_id;
		$report_export->save();
		$report_export_id = $report_export->id;

	    $controller = New CptlistApiController;
	    $api_response = $controller->getFilterResultApiSP('pdf');
	    $api_response_data = $api_response->getData();
        $cpts = $api_response_data->data->cpts;
        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $search_by = $api_response_data->data->search_by;
        $patient = $api_response_data->data->patient;
        $cpt_summary = json_decode(json_encode($api_response_data->data->cpt_summary));
       
        $summary_det = array('units' => 0, 'charges' => 0, 'adj' => 0, 'pmt' => 0, 'bal' => 0);        
        foreach ($cpt_summary as $item) {
            $cpt_code = $item->cpt_code;
            $summary_det['units'] += @$item->unit;
            $summary_det['charges'] += @$item->total_charge;
            $summary_det['adj'] += (@$item->ins_adj+@$item->pat_adj);
            $summary_det['pmt'] += (@$item->total_paid);
            $summary_det['bal'] += (@$item->total_ar_due);//(@$item->patient_bal+@$item->insurance_bal);
        }                
        
        // $patient = isset($api_response_data->data->patient) ? $api_response_data->data->patient : [];
        $cptDesc = isset($api_response_data->data->cptDesc) ? $api_response_data->data->cptDesc : [];
	    
	    $date = date('m-d-Y');
	    $pdf = new denial_trend_mypdf("L","mm","A4");
		$pdf->SetMargins(2,6);
		$pdf->SetDrawColor(217, 217, 217);
		$pdf->SetTextColor(100,100,100);
		$pdf->AddPage();
		$pdf->AddFont('Calibri','','calibrib.php');
		$pdf->SetFont('Calibri','',7);

	    self::BladeContent($cpts, $pdf, $patient, $cptDesc, $summary_det);
	    $pdf->Output($filename,'F');
        $report_export->update(['status'=>'Pending']);
	    // $pdf->Output('D','Denial_Trend_Analysis_'.$created_date.'.pdf');
		exit();
	}

	public function BladeContent($cpts, $pdf, $patient, $cptDesc, $summary_det){
		foreach($cpts as $list){
			$cpt_code = @$list->cpt_code;
            $total_charge = isset($list->total_charge) ? $list->total_charge : 0;
            $desc = isset($cptDesc->$cpt_code) ? $cptDesc->$cpt_code : '';
            $patient_adj = isset($list->pat_adj)?$list->pat_adj:0;
            $insurance_adj = isset($list->ins_adj)?$list->ins_adj:0;
            $adjustment = isset($list->tot_adj)?$list->tot_adj:0;
            $pat_pmt = isset($patient->$cpt_code)?$patient->$cpt_code:0;
            $ins_pmt = isset($insurance->$cpt_code)?$insurance->$cpt_code:0;
            $pat_bal = isset($list->patient_bal)?$list->patient_bal:0;
            $ins_bal = isset($list->insurance_bal)?$list->insurance_bal:0;

            $pdf->AddFont('Calibri','','calibri.php');
		    $pdf->SetFont('Calibri','',7);
			$pdf->SetTextColor(100, 100, 100);
			$x_axis=$pdf->getx();// now get current pdf x axis value
			$c_height=5;// cell height
			$c_width = (290/9);

			$text = @$cpt_code;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width-15,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis = $pdf->getx();
			$text = @$list->description;
			$lengthToSplit = 50;
			$pdf->Vcell($c_width+35,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis = $pdf->getx();
			$text = @$list->unit;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width-20,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis = $pdf->getx();
			$text = Helpers::priceFormat($total_charge,'',1);
			$lengthToSplit = strlen($text);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
			$pdf->SetTextColor(100, 100, 100);

			$x_axis = $pdf->getx();
			$text = Helpers::priceFormat(@$list->patient_paid,'',1);
			$lengthToSplit = strlen($text);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
			$pdf->SetTextColor(100, 100, 100);

			$x_axis=$pdf->getx();
			$text = Helpers::priceFormat(@$list->insurance_paid,'',1);
			$lengthToSplit = strlen($text);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
			$pdf->SetTextColor(100, 100, 100);

			$x_axis=$pdf->getx();
			$text = Helpers::priceFormat($patient_adj,'',1);
			$lengthToSplit = strlen($text);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
			$pdf->SetTextColor(100, 100, 100);

			$x_axis=$pdf->getx();
			$text = Helpers::priceFormat($insurance_adj,'',1);
			$lengthToSplit = strlen($text);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
			$pdf->SetTextColor(100, 100, 100);

			$x_axis=$pdf->getx();
			$text = Helpers::priceFormat(@$list->total_ar_due,'',1);
			$lengthToSplit = strlen($text);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
			$pdf->SetTextColor(100, 100, 100);

			$pdf->Ln();
		}
		self::SummaryContent($pdf, $summary_det, $patient);
	}

	public function SummaryContent($pdf, $summary_det, $patient){

		$wallet = isset($patient->wallet) ? @$patient->wallet : 0;
		if ($wallet < 0){
            $wallet = 0;
		}
		$x_axis = $pdf->getx();
    	$pdf->SetTextColor(240, 125, 8);
    	$pdf->SetFont('Calibri-Bold','',8);
    	$pdf->Vcell(30,10,$x_axis,"Summary",20,"","L");
    	$pdf->Ln();
    	$c_height = 5;
    	$c_width = 40;
    	$lengthToSplit = 30;

 		$x_axis=$pdf->getx();
    	$pdf->SetTextColor(100,100,100);
    	$pdf->SetFont('Calibri-Bold','',7.5);
 		$pdf->Vcell($c_width,$c_height,$x_axis,"Wallet Balance",$lengthToSplit,"TL","L");
 		$x_axis=$pdf->getx();
    	$pdf->SetFont('Calibri','',7.5);
 		$pdf->Vcell($c_width,$c_height,$x_axis,'$'.Helpers::priceFormat(@$wallet,'',1),$lengthToSplit,"TR","R");
 		$pdf->Ln();

 		$x_axis=$pdf->getx();
    	$pdf->SetTextColor(100,100,100);
    	$pdf->SetFont('Calibri-Bold','',7.5);
 		$pdf->Vcell($c_width,$c_height,$x_axis,"Total Units",$lengthToSplit,"L","L");
 		$x_axis=$pdf->getx();
    	$pdf->SetFont('Calibri','',7.5);
 		$pdf->Vcell($c_width,$c_height,$x_axis,@$summary_det['units'],$lengthToSplit,"R","R");
 		$pdf->Ln();

		$x_axis=$pdf->getx();
    	$pdf->SetTextColor(100,100,100);
    	$pdf->SetFont('Calibri-Bold','',7.5);
 		$pdf->Vcell($c_width,$c_height,$x_axis,"Total Charges",$lengthToSplit,"L","L");
 		$x_axis=$pdf->getx();
    	$pdf->SetFont('Calibri','',7.5);
 		$pdf->Vcell($c_width,$c_height,$x_axis,'$'.Helpers::priceFormat(@$summary_det['charges'],'',1),$lengthToSplit,"R","R");
 		$pdf->Ln();

 		$x_axis=$pdf->getx();
    	$pdf->SetTextColor(100,100,100);
    	$pdf->SetFont('Calibri-Bold','',7.5);
 		$pdf->Vcell($c_width,$c_height,$x_axis,"Total Adjustments",$lengthToSplit,"L","L");
 		$x_axis=$pdf->getx();
    	$pdf->SetFont('Calibri','',7.5);
 		$pdf->Vcell($c_width,$c_height,$x_axis,'$'.Helpers::priceFormat(@$summary_det['adj'],'',1),$lengthToSplit,"R","R");
 		$pdf->Ln();

 		$x_axis=$pdf->getx();
    	$pdf->SetTextColor(100,100,100);
    	$pdf->SetFont('Calibri-Bold','',7.5);
 		$pdf->Vcell($c_width,$c_height,$x_axis,"Total Payments",$lengthToSplit,"L","L");
 		$x_axis=$pdf->getx();
    	$pdf->SetFont('Calibri','',7.5);
 		$pdf->Vcell($c_width,$c_height,$x_axis,'$'.Helpers::priceFormat(@$summary_det['pmt'],'',1),$lengthToSplit,"R","R");
 		$pdf->Ln();

 		$x_axis=$pdf->getx();
    	$pdf->SetTextColor(100,100,100);
    	$pdf->SetFont('Calibri-Bold','',7.5);
 		$pdf->Vcell($c_width,$c_height,$x_axis,"Total Balance",$lengthToSplit,"BL","L");
 		$x_axis=$pdf->getx();
    	$pdf->SetFont('Calibri','',7.5);
 		$pdf->Vcell($c_width,$c_height,$x_axis,'$'.Helpers::priceFormat(@$summary_det['bal'],'',1),$lengthToSplit,"BR","R");
 		$pdf->Ln();

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
		$c_width = 295;
		$c_height = 0;
		$text = Practice::getPracticeName()." - CPT/HCPCS Summary";
		$lengthToSplit = strlen($text);
		$this->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","C");

        $this->AddFont('Calibri','','calibri.php');
        $this->SetFont('Calibri','',9.5);
		$this->SetTextColor(100, 100, 100);
		$this->Ln();

		$request = Request::All();
	    $controller = New CptlistApiController;
	    $api_response = $controller->getFilterResultApiSP('pdf');
	    $api_response_data = $api_response->getData();
	    $search_by = isset($api_response_data->data->search_by) ? $api_response_data->data->search_by : '';

	    if ($search_by != '') {
			$i = 0;
			$text = [];
			foreach ((array)$search_by as $key => $val) {
				$text[] = $key.":".@$val[0];                           
	            $i++; 
			}
			$text_imp = implode(" | ", $text);
			$this->Vcell(295,12,$x_axis,$text_imp,160,"","C");
	    }

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

		$header = ['CPT Code', 'Description', 'Units', 'Charges($)', 'Pat Paid($)', 'Ins Paid($)', 'Pat Adj($)', 'Ins Adj($)', 'AR Due($)'];
		
		foreach ($header as $key => $value) {
			$this->SetFont('Calibri-Bold','',8);
			$this->SetTextColor(100, 100, 100);
			$x_axis=$this->getx();// now get current pdf x axis value
			$c_height = 6;// cell height
			$c_width = (290/count($header));// cell width 
			$lengthToSplit = strlen($value);

			if ($value == "CPT Code") {
				$c_width = $c_width - 15;
			}
			if ($value == "Description") {
				$c_width = $c_width + 35;
			}
			if ($value == "Units") {
				$c_width = $c_width - 20;
			}
			if ($value == "Charges($)" || $value == "Pat Paid($)" || $value == "Ins Paid($)" || $value == "Pat Adj($)" || $value == "Ins Adj($)" || $value == "AR Due($)") {
				$align = "R";
			}else{
				$align = "L";
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