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
use App\Models\Payments\PMTInfoV1 as PMTInfoV1;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Http\Controllers\Charges\Api\ChargeApiController as ChargeApiController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;


$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);

class ChargesPDFController extends Controller
{
	public function index()
    {
    	ini_set('max_execution_time', '0');
        ini_set('memory_limit', -1);
    	try {
	    	$created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
			$filename = 'Downloads/Charges'.$created_date.'.pdf';
			$url = Request::url();
	        $request = Request::All();
	        $req = $request['dataArr']['data'];
			$user_id = Auth()->user()->id;
			$report_export = new ReportExport;
			$practice_id = Session::get('practice_dbid');
			$counts = ReportExport::selectRaw('count(report_name) as counts')->where('report_name',$req['report_name'])->groupby('report_name')->where('practice_id',$practice_id)->value('counts');
	        $report_count = (isset($counts) && $counts != null && !empty($counts))?($counts+1):1;
	        $report_export->report_count = $report_count;
			$report_export->practice_id = $practice_id;
			$report_export->report_name = $req['report_name'];
			$report_export->report_url = $url;
			$report_export->report_type = $req['export'];
			$report_export->report_file_name = $filename;
			$report_export->report_controller_name = 'FinancialApiController';
			$report_export->report_controller_func = 'getUnbilledClaimApiSP';
			$report_export->status = 'Inprocess';
			$report_export->created_by = $user_id;
			$report_export->save();
			$report_export_id = $report_export->id;

	    	// dd($request);
	        $controller = New ChargeApiController;
	        $api_response = $controller->getListIndexApi('pdf');
	        $api_response_data = $api_response->getData();
	        $charges = $api_response_data->data->charges;
	        $facilities = $api_response_data->data->facilities;
	        $rendering_providers = $api_response_data->data->rendering_providers;
	        $billing_providers = $api_response_data->data->billing_providers;

	        $pdf = new charges_mypdf("L","mm","A4");
			$pdf->SetMargins(2,6);
			$pdf->SetTextColor(100,100,100);
			$pdf->SetDrawColor(217, 217, 217);
			$pdf->AddPage();
			$pdf->AddFont('Calibri','','calibri.php');
			$pdf->SetFont('Calibri','',7);
			$grand_total = '';
	        self::BladeContent($charges, $pdf);	        
	        $pdf->Output($filename,'F');

	        $report_export->update(['status'=>'Pending']);
        } catch(Exception $e) {
	    	\Log::info("Error Occured While export Charges report. Message:".$e->getMessage() );
	    }
		exit();
	}

	public function BladeContent($charges, $pdf){
		if(!empty($charges)){
			$count = 1;   
            $insurances = Helpers::getInsuranceNameLists();
            $insurance_fullName = Helpers::getInsuranceFullNameLists();
            $patient_insurances = PatientInsurance::getAllPatientInsuranceList();

            foreach($charges as $charge){
                $patient_name = Helpers::getNameformat(@$charge->last_name, @$charge->first_name, @$charge->middle_name);
                $insurance_name = "";
                if(empty($charge->insurance_id)) {
                    $insurance_name = "Self";
                } else {
                    $insurance_name = !empty($insurances[$charge->insurance_id]) ? $insurances[$charge->insurance_id] : Helpers::getInsuranceName(@$charge->insurance_id);
                    $insurance_full_name = !empty($insurance_fullName[$charge->insurance_id]) ? $insurance_fullName[$charge->insurance_id] : Helpers::getInsuranceName(@$charge->insurance_id);
                }
                $patient_ins_name = '';
                if(isset($patient_insurances['all'][@$patient->patient_id])) {
                    $patient_ins_name = $patient_insurances['all'][@$patient->patient_id];
                }
                // When billed amount comes unbilled amount should not come
                $charge_amt = Helpers::BilledUnbilled($charge);
                $billed = isset($charge_amt['billed'])?$charge_amt['billed']:0.00;
                $unbilled = isset($charge_amt['unbilled'])?$charge_amt['unbilled']:0.00;

                if(isset($charge) &&!empty($charge)){
                	$dos = Helpers::checkAndDisplayDateInInput(@$charge->date_of_service, '','-');

                	$pdf->AddFont('Calibri','','calibri.php');
				    $pdf->SetFont('Calibri','',7);
					$pdf->SetTextColor(100, 100, 100);
					$c_width=(290/16);
					$c_height=5;// cell height

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = !empty($charge->claim_number)? @$charge->claim_number:'-Nill-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = !empty($charge->account_no)? @$charge->account_no:'-Nill-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = !empty($patient_name)? @$patient_name:'-Nill-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width+15,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = ($dos!='')?@$dos:'-Nil-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = (isset($charge->facility_short_name) && !empty($charge->facility_short_name))?@$charge->facility_short_name:'-Nil-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width-5,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = (isset($charge->rendering_short_name) && !empty($charge->rendering_short_name))?@$charge->rendering_short_name:'-Nil-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width-5,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = (isset($charge->billing_short_name) && !empty($charge->billing_short_name))?@$charge->billing_short_name:'-Nil-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width-5,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = (isset($insurance_name) && !empty($insurance_name))?@$insurance_name:'-Nil-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = !empty($unbilled)? Helpers::priceFormat(@$unbilled,'',1):'-Nill-';
					$lengthToSplit = strlen($text);
					if ($text < 0) {
						$pdf->SetTextColor(255, 0, 0);
					}
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
					$pdf->SetTextColor(100, 100, 100);

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = !empty($billed)? Helpers::priceFormat(@$billed,'',1):'-Nill-';
					$lengthToSplit = strlen($text);
					if ($text < 0) {
						$pdf->SetTextColor(255, 0, 0);
					}
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
					$pdf->SetTextColor(100, 100, 100);

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = !empty($charge->total_paid)? Helpers::priceFormat(@$charge->total_paid,'',1):'-Nill-';
					$lengthToSplit = strlen($text);
					if ($text < 0) {
						$pdf->SetTextColor(255, 0, 0);
					}
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
					$pdf->SetTextColor(100, 100, 100);

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = !empty($charge->patient_due)? Helpers::priceFormat(@$charge->patient_due,'',1):'-Nill-';
					$lengthToSplit = strlen($text);
					if ($text < 0) {
						$pdf->SetTextColor(255, 0, 0);
					}
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
					$pdf->SetTextColor(100, 100, 100);

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = !empty($charge->insurance_due)? Helpers::priceFormat(@$charge->insurance_due,'',1):'-Nill-';
					$lengthToSplit = strlen($text);
					if ($text < 0) {
						$pdf->SetTextColor(255, 0, 0);
					}
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
					$pdf->SetTextColor(100, 100, 100);

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = !empty($charge->balance_amt)? Helpers::priceFormat($charge->balance_amt,'',1):'-Nill-';
					$lengthToSplit = strlen($text);
					if ($text < 0) {
						$pdf->SetTextColor(255, 0, 0);
					}
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
					$pdf->SetTextColor(100, 100, 100);

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = (isset($charge->status) && !empty($charge->status)) ? $charge->status : '-Nil-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = (isset($charge->sub_status_desc) && !empty($charge->sub_status_desc)) ? $charge->sub_status_desc : '-Nil-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$pdf->Ln();
                }

                $abb_billing[] = @$charge->billing_short_name." - ".@$charge->billing_full_name;
	    		$abb_billing = array_unique($abb_billing);
	    		foreach (array_keys($abb_billing, ' - ') as $key) {
	            	unset($abb_billing[$key]);
	        	}

	        	$abb_rendering[] = @$charge->rendering_short_name." - ".@$charge->rendering_full_name;
	        	$abb_rendering = array_unique($abb_rendering);
	    		foreach (array_keys($abb_rendering, ' - ') as $key) {
	            	unset($abb_rendering[$key]);
	        	}

	        	$abb_facility[] = @$charge->facility_short_name." - ".@$charge->facility_name;
	        	$abb_facility = array_unique($abb_facility);
	    		foreach (array_keys($abb_facility, ' - ') as $key) {
	            	unset($abb_facility[$key]);
	        	}

	        	if ($insurance_name !== "Self") {
		        	$abb_insurance[] = @$insurance_name." - ".@$insurance_full_name;
		        	$abb_insurance = array_unique($abb_insurance);
		    		foreach (array_keys($abb_insurance, ' - ') as $key) {
		            	unset($abb_insurance[$key]);
		        	}
	        	}
            }
            $pdf->Ln();
            $abbreviation = ['abb_facility' => $abb_facility, 'abb_rendering' => $abb_rendering, 'abb_billing' => $abb_billing, 'abb_insurance' => $abb_insurance];
			$abb_controller = New AbbreviationController;
			$abb_controller->abbreviation($abbreviation,$pdf);                
		}
	}
}

class charges_mypdf extends FPDF
{
	public function header(){

		$request = Request::All();
		
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
		$text = "Charges List";
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

		$header = ['Claim No', 'Acc No', 'Patient Name', 'DOS', 'Facility', 'Rendering', 'Billing', 'Payer', 'Unbilled($)', 'Billed($)', 'Paid($)', 'Pat Bal($)', 'Ins Bal($)', 'AR Bal($)', 'Status','Sub Status'];

		foreach ($header as $key => $value) {
			$this->SetFont('Calibri-Bold','',8);
			$this->SetTextColor(100, 100, 100);
			$x_axis=$this->getx();// now get current pdf x axis value
			$c_height = 6;// cell height
			$c_width = (290/count($header));// cell width 
			$lengthToSplit = strlen($value);
			if ($value == "Unbilled($)" || $value == "Billed($)" || $value == "Paid($)" || $value == "Pat Bal($)" || $value == "Ins Bal($)" || $value == "AR Bal($)") {
				$align = "R";
			}else{
				$align = "L";
			}
			if ($value == "Facility" || $value == "Rendering" || $value == "Billing") {
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
		}else{
		    $this->SetX($x_axis);
		    $this->Cell($c_width,$c_height,$text,$border,0,$align,0);
		}
	}
    
}