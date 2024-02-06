<?php

namespace App\Http\Controllers\exportpdf;

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
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Models\Payments\PMTInfoV1 as PMTInfoV1;
use App\Models\Payments\ClaimCPTInfoV1 as ClaimCPTInfoV1;
use App\Http\Controllers\ExportPDF\AbbreviationController as AbbreviationController;
use App\Http\Controllers\Reports\ReportController as ReportController;
use App\Http\Controllers\Reports\Practicesettings\ProviderlistController as ProviderlistController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;

$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);

class EremittancePDFController extends Controller
{
    public function index()
    {
    	set_time_limit(300);
    	try {
	    	$created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
			$filename = 'Downloads/Eremittance_'.$created_date.'.pdf';
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
			$report_export->report_controller_func = 'getEraApi';
			$report_export->status = 'Inprocess';
			$report_export->created_by = $user_id;
			$report_export->save();
			$report_export_id = $report_export->id;

	        $controller = New PaymentApiController;
	        $api_response = $controller->getEraApi('','pdf');
	        $api_response_data = $api_response->getData();
	        $e_remittance = (!empty($api_response_data->data->e_remittance))? (array)$api_response_data->data->e_remittance:[];

	        $pdf = new ElectronicClaimsMypdf("L","mm","A4");
			$pdf->SetMargins(2,6);
			$pdf->SetTextColor(100,100,100);
			$pdf->SetDrawColor(217, 217, 217);
			$pdf->AddPage();
			$pdf->AddFont('Calibri','','calibri.php');
			$pdf->SetFont('Calibri','',7);
	        self::BladeContent($e_remittance, $pdf);
	        $pdf->Output($filename,'F');

	        $report_export->update(['status' => 'Pending']);
        } catch(Exception $e) {
	    	\Log::info("Error Occured While export Payments report. Message:".$e->getMessage() );
	    }
		exit();
	}

	public function BladeContent($e_remittance, $pdf){
		$pdf->AddFont('Calibri','','calibri.php');
	    $pdf->SetFont('Calibri','',7);
		$pdf->SetTextColor(100, 100, 100);
		$c_width=(290/7);
		$c_height=5;// cell height

		foreach($e_remittance as $list){
			
			if(isset($list->check_details->pmt_details)){
				$posted = $list->check_details->pmt_details->amt_used;
			}elseif(isset($list->eft_details->pmt_details)){
				$posted = $list->eft_details->pmt_details->amt_used;
			}else{
				$posted = '0.00';
			}
			if(isset($list->check_details->pmt_details)){
				$un_posted = $list->check_details->pmt_details->pmt_amt - $list->check_details->pmt_details->amt_used;
			}elseif(isset($list->eft_details->pmt_details)){
				$un_posted = $list->eft_details->pmt_details->pmt_amt - $list->eft_details->pmt_details->amt_used;
			}else{
				$un_posted = $list->check_paid_amount;
			}

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::dateFormat($list->receive_date);
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");	

			$x_axis=$pdf->getx();// now get current pdf x axis value
			if(!empty($list->insurance_details)){
				$text = Helpers::getInsuranceName(@$list->insurance_details->id);
			}else{
				$text = $list->insurance_name;
			}
			$lengthToSplit = 30;
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");	

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = $list->check_no;
			$lengthToSplit = 30;
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");	

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::dateFormat($list->check_date);
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");	

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat($list->check_paid_amount,'',1);
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");	

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat($posted,'',1);
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");	

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat($un_posted,'',1);
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");	
			$pdf->Ln();
		}
	}
}

class ElectronicClaimsMypdf extends FPDF
{
    public function header(){

    	$controller = New PaymentApiController;
        $api_response = $controller->getEraApi('','pdf');
        $api_response_data = $api_response->getData();

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
		$text = "Payments E-Remittance";
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

        $this->header = ['Received Date', 'Insurance', 'Check No', 'Check Date', 'Check Amount($)', 'Posted($)', 'Un Posted($)'];

        foreach ($this->header as $key => $value) {
            $this->SetFont('Calibri-Bold','',8);
            $this->SetTextColor(100, 100, 100);
            $x_axis=$this->getx();// now get current pdf x axis value
            $c_height = 6;// cell height
            $c_width = (290/count($this->header));// cell width 
            $lengthToSplit = strlen($value);

            if ($value == "Check Amount($)" || $value == "Posted($)" || $value == "Un Posted($)") {
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
