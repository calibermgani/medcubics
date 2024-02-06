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
use App\Http\Controllers\Controller;
use App\Models\ReportExport as ReportExport;
use App\Models\Practice as Practice;
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
use App\Http\Controllers\Reports\Api\ReportApiController as ReportApiController;
use App\Http\Controllers\Reports\Financials\Api\FinancialApiController as FinancialApiController;
use App\Http\Controllers\Reports\ReportController as ReportController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;

$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);

class ChargesPaymentsPDFController extends Controller
{
    public function index(){
    	set_time_limit(300);
    	try {
	    	$created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
	        $filename = 'Charges_Payments_Summary_'.$created_date.'.pdf';
	        $url = Request::url();
	    	$request = Request::all();

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
			//$report_export->report_file_name = 'Downloads/Charges_Payments_Summary_'.$created_date.'.pdf';
			$report_export->report_file_name = $filename;
			$report_export->report_controller_name = 'FinancialApiController';
			$report_export->report_controller_func = 'getUnbilledClaimApiSP';
			$report_export->status = 'Inprocess';
			$report_export->created_by = $user_id;
			$report_export->save();
			$report_export_id = $report_export->id;
	    	$payerType = $request['insurance_charge'];
	    	$controller = New ReportController;
	    	$api_response = $controller->chargepaymentsearch('pdf',$request);
	    	$api_response_data = $api_response->getData();
	    	$billingprov = $api_response_data->data->billingprov;
	    	$charges = $api_response_data->data->charges;
	    	$pmt_adj = $api_response_data->data->pmt_adj;
	    	$payments = $api_response_data->data->payments;
	    	$header = (array)$api_response_data->data->header;
	    	
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
	        
	    	$pdf = new charges_payments_mypdf("L","mm","A4");
			$pdf->SetMargins(2,6);
			$pdf->SetTextColor(100,100,100);
			$pdf->SetDrawColor(217, 217, 217);
			$pdf->AddPage();
			$pdf->SetTextColor(100,100,100);
			$pdf->SetFont('Times','',9);
			self::BladeContent($payments, $charges, $billingprov, $pmt_adj, $pdf, $payerType);
			self::SummaryContent();
			//$pdf->Output($filename,'F');

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
	    	\Log::info("Error Occured While export charge payments report. Message:".$e->getMessage() );
	    }
		exit();	
    }

    public function BladeContent($payments, $charges, $billingprov, $pmt_adj, $pdf, $payerType = ''){
    	$pdf->AddFont('Calibri','','calibri.php');
	    $pdf->SetFont('Calibri','',7);
		$pdf->SetTextColor(100, 100, 100);
		$c_width=(290/8);
		$c_height=5;// cell height
    	$patient_total_payment = $insurance_total_payment = $total_billed = $patient_total_adj = $insurance_total_adj = 0;
    	foreach($billingprov as $val){
    		$provider_name = str_replace(',','',$val->provider_name);
			$key = str_replace(' ','_',($provider_name));
            $billed = isset($charges->$key) ? $charges->$key : 0;
            $pat_adj = isset($pmt_adj->$key->Patient) ? $pmt_adj->$key->Patient : 0;
			if($payerType == 'insurance')
				$pat_adj = 0;
            $ins_adj = isset($pmt_adj->$key->Insurance) ? $pmt_adj->$key->Insurance : 0;
			if($payerType == 'self')
				$ins_adj = 0;
            $pat_pmt = isset($payments->$key->Patient) ? $payments->$key->Patient : 0;
			if($payerType == 'insurance')
				$pat_pmt = 0;
            $ins_pmt = isset($payments->$key->Insurance) ? $payments->$key->Insurance : 0;
			if($payerType == 'self')
				$ins_pmt = 0;
            $tot_adj = $pat_adj + $ins_adj;
            $tot_pmt = $pat_pmt + $ins_pmt;
            $total_billed += $billed;
            $patient_total_payment += $pat_pmt;
            $insurance_total_payment += $ins_pmt;
            $patient_total_adj += $pat_adj;
            $insurance_total_adj += $ins_adj;

            if($billed || $pat_adj || $ins_adj || $pat_pmt || $ins_pmt!=0){
				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = str_replace('_', ' ', @$key);
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat($billed,'',1);
				$lengthToSplit = strlen($text);
				if ($text < 0) {
					$pdf->SetTextColor(255, 0, 0);
				}
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
				$pdf->SetTextColor(100, 100, 100);

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat($pat_adj,'',1);
				$lengthToSplit = strlen($text);
				if ($text < 0) {
					$pdf->SetTextColor(255, 0, 0);
				}
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
				$pdf->SetTextColor(100, 100, 100);

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat($ins_adj,'',1);
				$lengthToSplit = strlen($text);
				if ($text < 0) {
					$pdf->SetTextColor(255, 0, 0);
				}
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
				$pdf->SetTextColor(100, 100, 100);

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat($tot_adj,'',1);
				$lengthToSplit = strlen($text);
				if ($text < 0) {
					$pdf->SetTextColor(255, 0, 0);
				}
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
				$pdf->SetTextColor(100, 100, 100);

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat($pat_pmt,'',1);
				$lengthToSplit = strlen($text);
				if ($text < 0) {
					$pdf->SetTextColor(255, 0, 0);
				}
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
				$pdf->SetTextColor(100, 100, 100);

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat($ins_pmt,'',1);
				$lengthToSplit = strlen($text);
				if ($text < 0) {
					$pdf->SetTextColor(255, 0, 0);
				}
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
				$pdf->SetTextColor(100, 100, 100);

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat($tot_pmt,'',1);
				$lengthToSplit = strlen($text);
				if ($text < 0) {
					$pdf->SetTextColor(255, 0, 0);
				}
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
				$pdf->SetTextColor(100, 100, 100);
				$pdf->Ln();
            }
    	}
        $pdf->Ln();

        $x_axis=$pdf->getx();// now get current pdf x axis value
		$text = "Totals($)";
		$pdf->SetFont('Times','B',7.5);
		$pdf->SetTextColor(240, 125, 8);
		$lengthToSplit = strlen($text);
		$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

		$x_axis=$pdf->getx();// now get current pdf x axis value
		$text = Helpers::priceFormat(array_sum((array)$charges),'',1);
		$lengthToSplit = strlen($text);
		$pdf->SetTextColor(0, 135, 127);
		$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

		$x_axis=$pdf->getx();// now get current pdf x axis value
		$text = Helpers::priceFormat($patient_total_adj,'',1);
		$lengthToSplit = strlen($text);
		$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

		$x_axis=$pdf->getx();// now get current pdf x axis value
		$text = Helpers::priceFormat($insurance_total_adj,'',1);
		$lengthToSplit = strlen($text);
		$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

		$x_axis=$pdf->getx();// now get current pdf x axis value
		$text = Helpers::priceFormat($patient_total_adj+$insurance_total_adj,'',1);
		$lengthToSplit = strlen($text);
		$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

		$x_axis=$pdf->getx();// now get current pdf x axis value
		$text = Helpers::priceFormat($patient_total_payment,'',1);
		$lengthToSplit = strlen($text);
		$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

		$x_axis=$pdf->getx();// now get current pdf x axis value
		$text = Helpers::priceFormat($insurance_total_payment,'',1);
		$lengthToSplit = strlen($text);
		$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

		$x_axis=$pdf->getx();// now get current pdf x axis value
		$text = Helpers::priceFormat($patient_total_payment+$insurance_total_payment,'',1);
		$lengthToSplit = strlen($text);
		$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

		$wallet = isset($payments->wallet) ? $payments->wallet : 0;
        if ($wallet < 0){
            $wallet = 0;
        }

		$pdf->Ln();

		$x_axis = $pdf->getx();// now get current pdf x axis value
		$text = "Wallet Balance: ";
		$pdf->SetFont('Calibri','',8.5);
		$pdf->SetTextColor(100, 100, 100);
		$lengthToSplit = strlen($text);
		$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

		$x_axis = $pdf->getx();// now get current pdf x axis value
		$text = "$".Helpers::priceFormat(@$wallet.'',1);
		$lengthToSplit = strlen($text);
		$pdf->SetTextColor(240, 125, 8);
		$pdf->SetFont('Times','B',7.5);
		$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
		$pdf->SetTextColor(100, 100, 100);
    }

    public function SummaryContent(){
    	
    }
}

class charges_payments_mypdf extends FPDF
{
	public function header(){
		$request = Request::all();
		$controller = New ReportController;
    	$api_response = $controller->chargepaymentsearch('pdf',$request);
    	$api_response_data = $api_response->getData();
    	$search_by = $api_response_data->data->header;
	    $this->SetFillColor(255, 255, 255);
		$this->SetTextColor(0, 135, 127);
	    $this->AddFont('Calibri-Bold','','calibrib.php');
	    $this->SetFont('Calibri-Bold','',10.5);
		$x_axis=$this->getx();
		$c_width = 295;
		$c_height = 0;
		$text = Practice::getPracticeName()." - Charges & Payments Summary";
		$lengthToSplit = strlen($text);
		$this->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","C");

		$i = 0;
		$text = [];
		foreach ((array)$search_by as $key => $val) {
			$text[] = $key.":".@$val;                           
            $i++; 
		}
		$text_imp = implode(" | ", $text);

		// $text = 'Transaction Date : 09/20/19 To 09/20/19 | Payer : All';
		$lengthToSplit = strlen($text_imp);
        $this->AddFont('Calibri','','calibri.php');
        $this->SetFont('Calibri','',9.5);
		$this->SetTextColor(100, 100, 100);
		$this->Ln();
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

		$header = ['Billing', 'Total Charges($)', 'Patient Adjustments($)', 'Insurance Adjustments($)', 'Total Adjustments($)', 'Patient Payments($)', 'Insurance Payments($)', 'Total Payments($)'];
		foreach ($header as $key => $value) {
			$this->SetFont('Calibri-Bold','',8);
			$this->SetTextColor(100, 100, 100);
			$x_axis=$this->getx();// now get current pdf x axis value
			$c_height = 6;// cell height
			$c_width = (290/count($header));// cell width 
			$lengthToSplit = strlen($value);
			$align = ($value == "Billing")?"C" : "R"; 
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

	public function vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,$border="",$align="L",$fill=0)
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
			$this->Cell($c_width,$c_height,'',$border,0,$align,$fill);
		}
		else{
		    $this->SetX($x_axis);
		    $this->Cell($c_width,$c_height,$text,$border,0,$align,$fill);
		}
	}
}