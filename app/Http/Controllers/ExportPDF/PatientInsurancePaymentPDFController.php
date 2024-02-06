<?php

namespace App\Http\Controllers\ExportPDF;

use DB;
use Carbon;
use Request;
use Auth;
use Session;
use FPDF;
use App\Models\Icd as Icd;
use App\Models\Medcubics\Cpt as Cpt;
use App\Models\Practice as Practice;
use App\Http\Controllers\Controller;
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
use App\Models\ReportExport as ReportExport;
use App\Models\Insurance;
use App\Models\Medcubics\Users;
use App\Http\Controllers\Reports\CollectionController as CollectionController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;

$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);

class PatientInsurancePaymentPDFController extends Controller
{
    public function index(){
    	set_time_limit(300);
    	try {
	        $request = Request::All();
	        $controller = New CollectionController;
	        $api_response = $controller->patientInsurancePaymentSearch('pdf');
	        $payments = $api_response['payment'];
	        $patient_total = $api_response['patient_total'];
	        $insurance_total = $api_response['insurance_total'];
	        $header = $api_response['header'];
			
	        $text = $headers = [];
	        $i = 0;
	        if(!empty($header)) {
	            foreach ((array)$header as $key => $val) {
	                $text[] = $key."=".(is_array($val)? $val[0] : $val);                           
	                $i++; 
	            }
	        }
	        $headers = implode('&', $text);
		
	        $created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
	        $filename = 'Patient_Insurance_Payment_'.$created_date.'.pdf';
	        $url = Request::url();
	        $user_id = Auth()->user()->id;
	        $report_export = new ReportExport;
	        $practice_id = Session::get('practice_dbid');
	        $counts = ReportExport::selectRaw('count(report_name) as counts')->where('report_name',$request['report_name'])->groupby('report_name')->where('practice_id',$practice_id)->value('counts');
	        $report_count = (isset($counts) && $counts != null && !empty($counts))?($counts+1):1;
	        $report_export->report_count = $report_count;
	        $report_export->practice_id = $practice_id;
	        $report_export->report_name = $request['report_name'];
	        $report_export->report_url = $url;
	        $report_export->parameter = $headers;
	        $report_export->report_type = $request['export'];
	        $report_export->report_file_name = $filename;
	        $report_export->report_controller_name = 'CollectionController';
	        $report_export->report_controller_func = 'patientInsurancePaymentSearchexport';
	        $report_export->status = 'Inprocess';
	        $report_export->created_by = $user_id;
	        $report_export->save();
	        $report_export_id = $report_export->id;
	        
	        $pdf = new patient_insurance_mypdf("L","mm","A4");
	        $pdf->SetMargins(2,6);
	        $pdf->SetTextColor(100,100,100);
	        $pdf->SetDrawColor(217, 217, 217);
	        $pdf->AddPage();
	        $pdf->AddFont('Calibri','','calibri.php');
	        $pdf->SetFont('Calibri','',7);
	        $grand_total = '';
				
	        self::BladeContent($payments, $pdf, $header, $patient_total, $insurance_total);
	        //$pdf->Output($filename,'F');

	        /* google bucket integration */
			$resp = $pdf->Output($filename,'S');
	        $data['filename'] = $filename;
	        $data['contents'] = $resp;
	        $target_dir = $practice_id.DS.'reports'.DS.$user_id.DS.date('my', strtotime($report_export->created_at));
        	$data['target_dir'] = $target_dir;
			// Upload to google bucket code
	        Helpers::uploadResourceFile('reports', $practice_id, $data);	

	        $report_export->update(['status' => 'Pending']);
	    } catch(Exception $e) {
	    	\Log::info("Error Occured While export Patient Insurance Payment report. Message:".$e->getMessage() );
	    }
        exit();
    }

	public function BladeContent($payments, $pdf, $header, $patient_total, $insurance_total){
		$abb_user = [];
		
		foreach ($payments as $key => $value) {
			$pdf->AddFont('Calibri','','calibri.php');
		    $pdf->SetFont('Calibri','',7);
			$pdf->SetTextColor(100, 100, 100);
                        if(isset($header['Payer']) && $header['Payer']!="Patient Payments"){
                            $c_width=(290/12);
                        }else{
                            $c_width=(290/10);
                        }
			$c_height=5;// cell height

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($value->transaction_date)? Helpers::timezone(@$value->transaction_date,'m/d/y') : '-Nil-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($value->account_no)? @$value->account_no : '-Nil-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width-5,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$title = !empty($value->title)?$value->title.'. ':'';
			$text = $title.@$value->last_name.', '.@$value->first_name.' '.@$value->middle_name;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width+5,$c_height,$x_axis,(!empty($text)? $text : '-Nil-' ),$lengthToSplit,'', "L");

            if(isset($header['Payer'])){
                $x_axis=$pdf->getx();// now get current pdf x axis value
                $text = (isset($value->payer))? @$value->dos:'-Nil-';
                $lengthToSplit = strlen($text);
                $pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

                $x_axis=$pdf->getx();// now get current pdf x axis value
                $text = (isset($value->payer))? @$value->claim_number:'-Nil-';
                $lengthToSplit = strlen($text);
                $pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");                            
            }
			
			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($value->payer)? @$value->payer : '-Nil-' ;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($value->pmt_mode)? @$value->pmt_mode : '-Nil-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($value->pmt_mode_no)? @$value->pmt_mode_no : '-Nil-';
			$lengthToSplit = 18;
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($value->pmt_mode_date)? @$value->pmt_mode_date : '-Nil-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			if(isset($r->payer) && $r->payer=="Patient"){
				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat(@$value->pmt_amt,'',1);
			} else {
				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat(@$value->total_paid,'',1);
			}
			$lengthToSplit = strlen($text);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			} else {
				$pdf->SetTextColor(100, 100, 100);	
			}
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");	
			$pdf->SetTextColor(100, 100, 100);
			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($value->reference)? $value->reference : "-Nil-";
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width+10,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($value->created_by)? Helpers::user_names(@$value->created_by) : '-Nil-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$pdf->Ln();
			
			$abb_user[] = Helpers::user_names(@$value->created_by)." - ".Helpers::getUserFullName(@$value->created_by);
	    	$abb_user = array_unique($abb_user);
			foreach (array_keys($abb_user, ' - ') as $key) {
	        	unset($abb_user[$key]);
	    	}
		}
		self::SummaryContent($pdf, $header, $patient_total, $insurance_total);
		$abbreviation = ['abb_user' => $abb_user];
		$abb_controller = New AbbreviationController;
		$abb_controller->abbreviation($abbreviation,$pdf);
	}

	public function SummaryContent($pdf, $header, $patient_total, $insurance_total){
		$x_axis = $pdf->getx();
    	$pdf->SetTextColor(240, 125, 8);
    	$pdf->SetFont('Calibri-Bold','',8);
    	$pdf->Vcell(30,10,$x_axis,"Summary",20,"","L");
    	$pdf->Ln();

    	$pdf->MultiCell(80, 0, "",'T', "L");
    	
    	if(isset($header['Transaction Date'])){
	    	$x_axis=$pdf->getx();
	    	$pdf->SetTextColor(100,100,100);
	    	$pdf->SetFont('Calibri','',7.5);
	 		$pdf->Vcell(40,5,$x_axis,"Transaction Date",30,"L","L");
	 		$x_axis=$pdf->getx();
	 		$pdf->Vcell(40,5,$x_axis,$header['Transaction Date'],30,"R","R");
	 		$pdf->Ln();
    	}
    	if(isset($header['Payer']) && $header['Payer']=="Patient Payments" || $header['Payer']=="All Payments"){
    		$x_axis=$pdf->getx();
	    	$pdf->SetTextColor(100,100,100);
	    	$pdf->SetFont('Calibri','',7.5);
	 		$pdf->Vcell(40,5,$x_axis,"Total Patient Payments",30,"L","L");
	 		$x_axis=$pdf->getx();
	 		$pdf->Vcell(40,5,$x_axis,"$".Helpers::priceFormat($patient_total,'',1),30,"R","R");
	 		$pdf->Ln();
    	}
    	if(isset($header['Payer']) && $header['Payer']=="Insurance Payments" || $header['Payer']=="All Payments"){
    		$x_axis=$pdf->getx();
	    	$pdf->SetTextColor(100,100,100);
	    	$pdf->SetFont('Calibri','',7.5);
	 		$pdf->Vcell(40,5,$x_axis,"Total Insurance Payments",30,"L","L");
	 		$x_axis=$pdf->getx();
	 		$pdf->Vcell(40,5,$x_axis,"$".Helpers::priceFormat($insurance_total,'',1),30,"R","R");
	 		$pdf->Ln();	
    	}
    	if(isset($header['Payer']) && $header['Payer']=="All Payments"){
    		$x_axis=$pdf->getx();
	    	$pdf->SetTextColor(100,100,100);
	    	$pdf->SetFont('Times','B',7);
	 		$pdf->Vcell(40,5,$x_axis,"Total Payments",30,"L","L");
	 		$x_axis=$pdf->getx();
	 		$pdf->SetTextColor(240, 125, 8);
	 		$pdf->Vcell(40,5,$x_axis,"$".Helpers::priceFormat($patient_total+$insurance_total,'',1),30,"R","R");
	 		$pdf->SetTextColor(100,100,100);
	 		$pdf->Ln();	
    	}
    	$pdf->MultiCell(80, 0, "",'B', "L");
    	$pdf->Ln();
	}
}

class patient_insurance_mypdf extends FPDF
{ 
	public function header(){
            $request = Request::All();
        $search_by = [];
        $start_date = $end_date = $dos_start_date =  $dos_end_date = $payer = $insurance_id = $option_zero_payments = $user_ids = '';
        if(isset($request['include_refund'])){
        	$search_by['Include Refund'] = $request['include_refund'];
    	}
        // Filter by Transaction Date
        if(isset($request['choose_date']) && !empty($request['choose_date']) && 
            ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
            if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
                    $exp = explode("-",$request['select_transaction_date']);
                    $start_date = $exp[0];
                    $end_date = $exp[1];
                $search_by['Transaction Date'] = date("m/d/y", strtotime($start_date)) . "  To " . date("m/d/y", strtotime($end_date));
            }
            
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
            
        // Filter by User
        if (isset($request["user"]) && !empty($request["user"])) {
            $user_ids = (isset($request['export']) || is_string($request['user'])) ? $request['user']:implode(',',$request['user']);
            $user = (isset($request['export']) || is_string($request['user'])) ? explode(',',$request['user']):$request['user'];
            $User_name =  Users::whereIn('id', $user)->where('status', 'Active')->pluck('short_name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            $search_by['User'] = $User_name;
        }

        // Except zero payments 
        if (isset($request['options'])) {
            $option_zero_payments = $request['options'];
            $search_by["Include"] = "Zero Payments";
        }

        if (isset($request['payer'])){
            $payer = $request['payer'];
            if ($request['payer'] == 'all') {
                $search_by["Payer"] = "All Payments";
            } elseif ($request['payer'] == 'self') {
                $search_by["Payer"] = "Patient Payments";
            } else {
                $search_by["Payer"] = "Insurance Payments";
                if (isset($request['insurance_id']) && $request['insurance_id']!='') {
                    $insurance_id = (isset($request['export']) || is_string($request['insurance_id'])) ? $request['insurance_id']:implode(",", $request['insurance_id']);
                    $insurance = (isset($request['export']) || is_string($request['insurance_id'])) ? explode(",", $request['insurance_id']):$request['insurance_id'];
		        	$search_by["Insurance"] = @array_flatten(Insurance::selectRaw("GROUP_CONCAT(insurance_name SEPARATOR ' , ') as insurance_name")->whereIn('id',$insurance)->get()->toArray())[0];
		        }
            }
        }
		$this->SetFillColor(255, 255, 255);
		$this->SetTextColor(0, 135, 127);
	    $this->AddFont('Calibri-Bold','','calibrib.php');
	    $this->SetFont('Calibri-Bold','',10.5);
		$x_axis=$this->getx();
		$c_width = 295;
		$c_height = 0;
		$text = Practice::getPracticeName()." - Patient and Insurance Payment";
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
                
                if($search_by["Payer"] != "Patient Payments"){
                    $header = ['Transaction Date', 'Acc No', 'Patient Name', 'DOS', 'Claim No', 'Payer', 'Payment Type', 'Check/EFT/CC/MO No', 'Check/EFT/CC/MO Date', 'Paid($)', 'Reference','User'];
                }else{
                    $header = ['Transaction Date', 'Acc No', 'Patient Name', 'Payer', 'Payment Type', 'Check/EFT/CC/MO No', 'Check/EFT/CC/MO Date', 'Paid($)', 'Reference','User'];
                }
		

		foreach ($header as $key => $value) {
			$this->SetFont('Calibri-Bold','',8);
			$this->SetTextColor(100, 100, 100);
			$x_axis=$this->getx();// now get current pdf x axis value
			$c_height = 6;// cell height
			$c_width = (290/count($header));// cell width 
			$lengthToSplit = strlen($value);
			if ($value == "Paid($)") {
				$align = "R";
			}else{
				$align = "L";
			}
			if ($value == "Check/EFT/CC/MO No" || $value == "Check/EFT/CC/MO Date") {
				$lengthToSplit = 13;
			}
			if ($value == "Acc No") {
				$c_width = $c_width-5;
			}
			if ($value == "Patient Name") {
				$c_width = $c_width+5;
			}
			if ($value == "Reference") {
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
		} else {
		    $this->SetX($x_axis);
		    $this->Cell($c_width,$c_height,$text,$border,0,$align,0);
		}
	}
    
}