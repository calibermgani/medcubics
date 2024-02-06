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
use App\Http\Controllers\Controller;
use App\Models\ReportExport as ReportExport;
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
use App\Models\ReportExportTask as ReportExportTask;
use App\Http\Controllers\Reports\CollectionController as CollectionController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Provider as Provider;
use App\Models\Facility as Facility;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;

$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);
class InsuranceOverPaymentPDFController extends Controller
{
    public function index()
    {
    	set_time_limit(300);
	    	try{
	    	$created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
			$filename = 'Insurance_Over_Payment'.$created_date.'.pdf';
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
			$report_export->report_controller_name = 'CollectionController';
			$report_export->report_controller_func = 'insuranceOverpaymentSearchSP';
			$report_export->status = 'Inprocess';
			$report_export->created_by = $user_id;
			$report_export->save();
			$report_export_id = $report_export->id;

	        $controller = New CollectionController;
	        $api_response = $controller->insuranceOverpaymentSearchSP('pdf');

	        $overpayments = $api_response['overpayment'];
	        // Parameters for filter
	        $search_by = (array)$api_response['header'];
	        $text = $headers = [];
	        $i = 0;
	        if(!empty($search_by)) {
	            foreach ((array)$search_by as $key => $val) {
	                $text[] = $key."=".(is_array($val)? $val[0] : $val);
	                $i++; 
	            }
	        }    
	        $headers = implode('&', $text);
	        
	        $pdf = new insurance_over_payments_mypdf("L","mm","A4");
			$pdf->SetMargins(2,6);
			$pdf->SetTextColor(100,100,100);
			$pdf->SetDrawColor(217, 217, 217);
			$pdf->AddPage();
			$pdf->AddFont('Calibri','','calibri.php');
			$pdf->SetFont('Calibri','',7);
			$grand_total = '';
	        self::BladeContent($overpayments, $pdf);
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
	    	\Log::info("Error Occured While export Insurance_Over_Payment report. Message:".$e->getMessage() );
	    } 
		exit();
	}

	public function BladeContent($overpayments, $pdf){
		foreach ($overpayments as $key => $value) {
			$pdf->AddFont('Calibri','','calibri.php');
		    $pdf->SetFont('Calibri','',7);
			$pdf->SetTextColor(100, 100, 100);
			$c_width=(290/11);
			$c_height=5;// cell height

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = @$value->claim_number;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width-5,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = @$value->dos;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width-5,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = @$value->account_no;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width-5,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = @$value->patient_name;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width+15,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = @$value->provider_short_name;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = @$value->facility_short_name;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = @$value->transaction_date;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = @$value->total_charge;
			$lengthToSplit = strlen($text);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
			$pdf->SetTextColor(100, 100, 100);

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = @$value->adjustment;
			$lengthToSplit = strlen($text);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
			$pdf->SetTextColor(100, 100, 100);

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = @$value->insurance_paid;
			$lengthToSplit = strlen($text);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
			$pdf->SetTextColor(100, 100, 100);

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = @$value->ar_due;
			$lengthToSplit = strlen($text);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
			$pdf->SetTextColor(100, 100, 100);

			$pdf->Ln();

			$abb_billing[] = @$value->provider_short_name." - ".@$value->provider_name;
	    	$abb_billing = array_unique($abb_billing);
			foreach (array_keys($abb_billing, ' - ') as $key) {
	        	unset($abb_billing[$key]);
	    	}

	    	$abb_facility[] = @$value->facility_short_name." - ".@$value->facility_name;
	    	$abb_facility = array_unique($abb_facility);
			foreach (array_keys($abb_facility, ' - ') as $key) {
	        	unset($abb_facility[$key]);
	    	}
		}
		$abbreviation = ['abb_billing' => @$abb_billing, 'abb_facility' => @$abb_facility];
		$abb_controller = New AbbreviationController;
		$abb_controller->abbreviation($abbreviation,$pdf);
	}	
}

class insurance_over_payments_mypdf extends FPDF
{
	public function header(){

		$request = Request::All();
    	//dd($request);
        $search_by = [];
        $start_date = $end_date = $dos_start_date = $dos_end_date = $billing_provider_id = $facility_id = '';

        // Filter by Transaction Date
        if(isset($request['choose_date']) && !empty($request['choose_date']) && 
            ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
        if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
            $exp = explode("-",$request['select_transaction_date']);
            $start_date = date("Y-m-d", strtotime($exp[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }           
            $end_date = date("Y-m-d", strtotime($exp[1]));
            $search_by['Transaction Date'] = date("m/d/y", strtotime($start_date)) . "  To " . date("m/d/y", strtotime($end_date));
        }
        
        // Filter by DOS
        if(isset($request['choose_date']) && !empty($request['choose_date']) && 
            ($request['choose_date']=='all' || $request['choose_date']=='DOS'))
        if(isset($request['select_date_of_service']) && !empty($request['select_date_of_service'])){
            $exp = explode("-",$request['select_date_of_service']);
            $dos_start_date = $exp[0];
            $dos_end_date = $exp[1];
            $dos_start_date = Helpers::dateFormat($dos_start_date, 'datedb');
            $dos_end_date = Helpers::dateFormat($dos_end_date, 'datedb');
            $search_by['DOS'] = date("m/d/Y",strtotime($dos_start_date)) . "  To " . date("m/d/Y",strtotime($dos_end_date));
        }
        // Filter by Billing provider
        if (isset($request['billing_provider_id']) && !empty($request['billing_provider_id'])) {
            $explode = explode(',', $request['billing_provider_id']);
            $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ' | ') as provider_name")->whereIn('id', $explode)->get()->toArray();
            $search_by['Billing Provider'] = @array_flatten($provider)[0];
        }

        // Filter by facility
        if (isset($request['facility']) && !empty($request['facility'])) {
            $explode_facility = explode(',', $request['facility']);
            $facility = Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ' | ') as facility_name")->whereIn('id', $explode_facility)->get()->toArray();
            $search_by['Facility'] = @array_flatten($facility)[0];
        }

		$this->SetFillColor(255, 255, 255);
		$this->SetTextColor(0, 135, 127);
	    $this->AddFont('Calibri-Bold','','calibrib.php');
	    $this->SetFont('Calibri-Bold','',10.5);
		$x_axis=$this->getx();
		$c_width = 295;
		$c_height = 0;
		$text = Practice::getPracticeName()." - Insurance Over Payment";
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

		$header = ['Claim No', 'DOS', 'Acc No', 'Patient Name', 'Billing Provider', 'Facility', 'Transaction Date', 'Charge Amt($)', 'Adjustments($)', 'Payments($)', 'AR Due($)'];

		foreach ($header as $key => $value) {
			$this->SetFont('Calibri-Bold','',8);
			$this->SetTextColor(100, 100, 100);
			$x_axis=$this->getx();// now get current pdf x axis value
			$c_height = 6;// cell height
			$c_width = (290/count($header));// cell width 
			$lengthToSplit = strlen($value);
			if ($value == "Charge Amt($)" || $value == "Adjustments($)" || $value == "Payments($)" || $value == "AR Due($)") {
				$align = "R";
			}else{
				$align = "L";
			}
			if ($value == "Claim No" || $value == "Acc No" || $value == "DOS") {
				$c_width = $c_width-5;
			}
			if ($value == "Patient Name") {
				$c_width = $c_width+15;
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

