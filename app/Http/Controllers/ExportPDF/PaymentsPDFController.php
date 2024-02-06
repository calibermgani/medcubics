<?php

namespace App\Http\Controllers\ExportPDF;

use DB;
use Carbon;
use Request;
use Auth;
use FPDF;
use Session;
use App\Models\Practice as Practice;
use App\Models\Icd as Icd;
use App\Http\Controllers\Controller;
use App\Models\ReportExport as ReportExport;
use App\Http\Controllers\Payments\Api\PaymentApiController as PaymentApiController;
use App\Http\Controllers\ExportPDF\AbbreviationController as AbbreviationController;
use App\Http\Controllers\Reports\ReportController as ReportController;
use App\Http\Controllers\Reports\Practicesettings\ProviderlistController as ProviderlistController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;

$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);

class PaymentsPDFController extends Controller
{
    public function index()
    {
    	set_time_limit(300);
    	try {
	    	$created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
			$filename = 'Downloads/Payments_'.$created_date.'.pdf';
			$url = Request::url();
	        $request = Request::All();
	        $request = $request['dataArr']['data'];

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
			$report_export->report_controller_name = 'PaymentApiController';
			$report_export->report_controller_func = 'getListIndexApi';
			$report_export->status = 'Inprocess';
			$report_export->created_by = $user_id;
			$report_export->save();
			$report_export_id = $report_export->id;

	        $controller = New PaymentApiController;
	        $api_response = $controller->getListIndexApi('pdf');
	        $api_response_data = $api_response->getData();
	        $payment_details = (!empty($api_response_data->data->payment_list)) ? $api_response_data->data->payment_list : [];
	        
	        $pdf = new Payments_mypdf("L","mm","A4");
			$pdf->SetMargins(2,6);
			$pdf->SetTextColor(100,100,100);
			$pdf->SetDrawColor(217, 217, 217);
			$pdf->AddPage();
			$pdf->AddFont('Calibri','','calibri.php');
			$pdf->SetFont('Calibri','',7);
	        self::BladeContent($payment_details, $pdf);
	        $pdf->Output($filename,'F');

	        $report_export->update(['status' => 'Pending']);
        } catch(Exception $e) {
	    	\Log::info("Error Occured While export Payments report. Message:".$e->getMessage() );
	    }
		exit();
	}

	public function BladeContent($payment_details, $pdf){
        if(!empty($payment_details)){
            foreach($payment_details as $payment_detail){

            	$type = @$payment_detail->pmt_type;
	            if ($payment_detail->pmt_method == "Patient") {
	                $insurance = $payment_detail->pmt_method;
	            } else {
	                if($payment_detail->insurance_name!='') {
	                    //$insurance = !empty($insurances[@$payment_detail->insurancedetail->id]) ? $insurances[@$payment_detail->insurancedetail->id] : App\Http\Helpers\Helpers::getInsuranceName(@$payment_detail->insurancedetail->id);
	                    $insurance = !empty($payment_detail->insurance_name) ? $payment_detail->insurance_name : "";
	                } else {
	                    $insurance = "-Nil-";
	                }
	            }
	            $check_mode = $payment_detail->pmt_mode;
	            $check_date = '';
	            if ($check_mode == "Check" && $payment_detail->check_no!='') {
	                $check_no = $payment_detail->check_no;
	                $check_date = $payment_detail->check_date;
	            } elseif ($check_mode == "EFT" && $payment_detail->eft_no!='') {
	                $check_no = $payment_detail->eft_no;
	                $check_date = $payment_detail->eft_date;
	            } elseif ($check_mode == "Cash") {
	                $check_no = "-Nil-";
	            } elseif($check_mode == "Credit" && $payment_detail->card_last_4!=''){
	                $check_no = isset($payment_detail->card_last_4) ? @$payment_detail->card_last_4 : '';
					$check_date = isset($payment_detail->expiry_date) ? @$payment_detail->expiry_date : '';
	            } elseif($check_mode == "Money Order"){
	                $check_no = isset($payment_detail->check_no) ? str_replace("MO-", "",@$payment_detail->check_no) : '';
	                $check_date = isset($payment_detail->check_date) ?$payment_detail->check_date : '';
	            } else {
	                $check_no = '-Nil-';//$payment_detail->card_no;
	            }
	            if ($payment_detail->pmt_type == "Refund") {
	                $check_no = $check_no." - Refund";                
	            }
	            $check_date = (!empty($check_date) && $check_date != '1970-01-01' && $check_date != '0000-00-00') ? Helpers::dateFormat($check_date) : "-Nil-";
	            $payment_detail_id = Helpers::getEncodeAndDecodeOfId($payment_detail->pmt_id, 'encode');
	            $bal_amt = @$payment_detail->pmt_amt - @$payment_detail->amt_used;

		        $pdf->AddFont('Calibri','','calibri.php');
			    $pdf->SetFont('Calibri','',7);
				$pdf->SetTextColor(100, 100, 100);
				$c_width=(290/10);
				$c_height=5;// cell height

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = (!empty($payment_detail->pmt_no))? $payment_detail->pmt_no:'-Nill-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = (!empty($insurance))? $insurance:'-Nill-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = (!empty($check_no))? $check_no:'-Nill-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = (!empty($check_mode))? $check_mode:'-Nill-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = (!empty($check_date))? $check_date:'-Nill-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = (!empty($payment_detail->pmt_amt))? Helpers::priceFormat(@$payment_detail->pmt_amt,'',1):'-Nill-';
				$lengthToSplit = strlen($text);
				if ($text < 0) {
					$pdf->SetTextColor(255, 0, 0);
				}
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
				$pdf->SetTextColor(100, 100, 100);

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = (!empty($payment_detail->amt_used))? Helpers::priceFormat(@$payment_detail->amt_used,'',1):'-Nill-';
				$lengthToSplit = strlen($text);
				if ($text < 0) {
					$pdf->SetTextColor(255, 0, 0);
				}
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
				$pdf->SetTextColor(100, 100, 100);

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = (!empty($bal_amt))? Helpers::priceFormat(@$bal_amt,'',1):'-Nill-';
				$lengthToSplit = strlen($text);
				if ($text < 0) {
					$pdf->SetTextColor(255, 0, 0);
				}
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
				$pdf->SetTextColor(100, 100, 100);

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = (!empty($payment_detail->created_date))? Helpers::timezone($payment_detail->created_date, 'm/d/y'):'-Nill-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = (!empty($payment_detail->user_name))? @$payment_detail->user_name:'-Nill-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
				$pdf->Ln();

				// $abb_facility = $abb_rendering = $abb_billing = $abb_insurance = [];
                $abb_user[] = @$payment_detail->user_name." - ".@$payment_detail->name;
	    		$abb_user = array_unique($abb_user);
	    		foreach (array_keys($abb_user, ' - ') as $key) {
	            	unset($abb_user[$key]);
	        	}
			}
			$abbreviation = ['abb_user' => $abb_user];
			$abb_controller = New AbbreviationController;
			$abb_controller->abbreviation($abbreviation,$pdf); 
        }
	}
}

class Payments_mypdf extends FPDF
{
    public function header(){
        $this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0, 135, 127);
        $this->AddFont('Calibri-Bold','','calibrib.php');
        $this->SetFont('Calibri-Bold','',10.5);
        $x_axis=$this->getx();
        $c_width = 295;
        $c_height = 0;
        $text = Practice::getPracticeName();
        $lengthToSplit = strlen($text);
        $this->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","C");

        $this->AddFont('Calibri','','calibri.php');
        $this->SetFont('Calibri','',9.5);
		$this->SetTextColor(100, 100, 100);
		$this->Ln();
		$text = "Payments List";
		$this->Vcell(295,12,$x_axis,$text,160,"","C");

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

        $this->header = ['Payment ID', 'Payer', 'Check/EFT No', 'Mode', 'Check Date', 'Check Amt($)', 'Posted($)', 'Un Posted($)', 'Created On', 'User'];

        foreach ($this->header as $key => $value) {
            $this->SetFont('Calibri-Bold','',8);
            $this->SetTextColor(100, 100, 100);
            $x_axis=$this->getx();// now get current pdf x axis value
            $c_height = 6;// cell height
            $c_width = (290/count($this->header));// cell width 
            $lengthToSplit = strlen($value);

            if ($value == "Check Amt($)" || $value == "Posted($)" || $value == "Un Posted($)") {
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
