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
use App\Http\Controllers\Claims\ClaimControllerV1 as ClaimControllerV1;
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

class ClaimEditsPDFController extends Controller
{
    public function index()
    {
    	set_time_limit(300);
    	try {
	    	$created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
			$filename = 'Downloads/ClaimEdits_'.$created_date.'.pdf';
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
			$report_export->report_controller_name = 'ClaimControllerV1';
			$report_export->report_controller_func = 'getClaimsDataSearchApi';
			$report_export->status = 'Inprocess';
			$report_export->created_by = $user_id;
			$report_export->save();
			$report_export_id = $report_export->id;

	        $controller = New ClaimControllerV1;
	        $api_response = $controller->getClaimsDataSearchApi('error');
	        $api_response_data = $api_response->getData();
	        $claims = $api_response_data->data->claims;
			$count = $api_response_data->data->counts;
			$search_by = $api_response_data->data->get_list_header;
			// $encodeClaimIds = $api_response_data->data->encodeClaimIds;
			$type = $api_response_data->data->type;

	        $pdf = new ClaimEditsMypdf("L","mm","A4");
			$pdf->SetMargins(2,6);
			$pdf->SetTextColor(100,100,100);
			$pdf->SetDrawColor(217, 217, 217);
			$pdf->AddPage();
			$pdf->AddFont('Calibri','','calibri.php');
			$pdf->SetFont('Calibri','',7);
	        self::BladeContent($claims, $pdf, $type);
	        $pdf->Output($filename,'F');

	        $report_export->update(['status' => 'Pending']);
        } catch(Exception $e) {
	    	\Log::info("Error Occured While export Payments report. Message:".$e->getMessage() );
	    }
		exit();
	}

	public function BladeContent($claims, $pdf, $type){
		$count = 1;   
		$insurances = Helpers::getInsuranceNameLists(); 
		$patient_insurances = PatientInsurance::getAllPatientInsuranceList();  
		$payment_claimed_det = PMTInfoV1::getAllpaymentClaimDetails('payment');  
		$billed_amounts_list = ClaimCPTInfoV1::getAllBilledAmountByActiveLineItem();

		foreach($claims as $key => $claim){
			$facility = @$claim->facility_detail;
            $provider = @$claim->rendering_provider;
            $patient = @$claim->patient;
            $patient_name = Helpers::getNameformat(@$claim->patient->last_name,@$claim->patient->first_name,@$claim->patient->middle_name);
            if (@$claim->insurance_details->payerid != '' && @$claim->self_pay == 'No')
            $class_name = 'cls-electronic';
            else if (@$claim->status == 'Patient' || $claim->self_pay == 'Yes')
            $class_name = 'cls-patient';
            else
            $class_name = 'cls-paper';
            $claim_id = Helpers::getEncodeAndDecodeOfId($claim->id);
            $billed_amount = (!empty($billed_amounts_list[$claim->id])) ? $billed_amounts_list[$claim->id] : 0;
            $insurance_payment_count = (!empty($payment_claimed_det[$claim->id])) ? $payment_claimed_det[$claim->id] : 0;
            
            $insurance_name = "";
            if ($claim->self_pay == 'Yes')
            $insurance_name = "Self";
            else
            $insurance_name = !empty($insurances[@$claim->insurance_details->id]) ? $insurances[@$claim->insurance_details->id] : Helpers::getInsuranceFullName(@$claim->insurance_details->id);
            $detArr = ['patient_id' => @$claim->patient->id, 'status' => @$claim->status, 'charge_add_type' => @$claim->charge_add_type, 'claim_submit_count' => @$claim->claim_submit_count];
            $edit_link = Helpers::getChargeEditLinkByDetails(@$claim->id, @$insurance_payment_count, "Charge", $detArr);
            
            $patient_ins_name = '';
            if (isset($patient_insurances['all'][@$claim->patient->id])) {
                $patient_ins_name = $patient_insurances['all'][@$claim->patient->id];
            }
            if($type == 'submitted' || $type == 'rejected'){
                $filled_date = $claim->submited_date;
            }else{
                $filled_date = $claim->filed_date;
            }

            if($type == 'rejected'){

            }
            else{

				$pdf->AddFont('Calibri','','calibri.php');
			    $pdf->SetFont('Calibri','',7);
				$pdf->SetTextColor(100, 100, 100);
				$c_width=(290/15);
				$c_height=5;// cell height

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($claim->claim_number)? $claim->claim_number:'-Nill-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($claim->date_of_service)? Helpers::dateFormat($claim->date_of_service,'claimdate'):'-Nill-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($patient_name)? $patient_name:'-Nill-';
				$lengthToSplit = 17;
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($insurance_name)? $insurance_name:'-Nill-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($claim->insurance_category)? $claim->insurance_category:'-Nill-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				if($claim->self_pay == 'No'){
					$text = !empty($claim->insurance_details->payerid)? $claim->insurance_details->payerid:'-Nill-';
				}else{
					$text = '';
				}
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				if($claim->rendering_provider->provider_name !=''){
					$text = @$claim->rendering_provider->short_name;
				}else{
					$text = '-Nill-';
				}
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
				$x_axis=$pdf->getx();// now get current pdf x axis value
				if($claim->billing_provider !=''){
					$text = @$claim->billing_provider->short_name;
				}else{
					$text = '-Nill-';
				}
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				if($claim->facility_detail->facility_name !=''){
					$text = @$claim->facility_detail->short_name;
				}else{
					$text = '-Nill-';
				}
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($billed_amount)? $billed_amount:'-Nill-';
				$lengthToSplit = strlen($text);
				if ($text < 0) {
					$pdf->SetTextColor(255, 0, 0);
				}
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
				$pdf->SetTextColor(100, 100, 100);

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($claim->arbal)? $claim->arbal:'-Nill-';
				$lengthToSplit = strlen($text);
				if ($text < 0) {
					$pdf->SetTextColor(255, 0, 0);
				}
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
				$pdf->SetTextColor(100, 100, 100);

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($claim->created_at)? Helpers::dateFormat(@$claim->created_at, 'date'):'-Nill-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($filled_date)? Helpers::dateFormat(@$filled_date,'date'):'-Nill-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				if($claim->no_of_issues > 0 && $claim->status == 'Ready'){
					$text = 'Error';
				}else{
					$text = !empty($claim->status)? $claim->status:'-Nill-';
				}
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				if(isset($claim->claim_sub_status->sub_status_desc)){
					$text = !empty($claim->claim_sub_status->sub_status_desc)? $claim->claim_sub_status->sub_status_desc:'-Nill-';
				}else{
					$text = '-Nil-';
				}
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
            }

			$pdf->Ln();
			$pdf->Ln();

			$abb_billing[] = @$claim->billing_provider->short_name." - ".@$claim->billing_provider->provider_name;
    		$abb_billing = array_unique($abb_billing);
    		foreach (array_keys($abb_billing, ' - ') as $key) {
            	unset($abb_billing[$key]);
        	}

        	$abb_rendering[] = @$claim->rendering_provider->short_name." - ".@$claim->rendering_provider->provider_name;
    		$abb_rendering = array_unique($abb_rendering);
    		foreach (array_keys($abb_rendering, ' - ') as $key) {
            	unset($abb_rendering[$key]);
        	}

        	$abb_facility[] = @$claim->facility_detail->short_name." - ".@$claim->facility_detail->facility_name;
    		$abb_facility = array_unique($abb_facility);
    		foreach (array_keys($abb_facility, ' - ') as $key) {
            	unset($abb_facility[$key]);
        	}

        	$abb_insurance[] = @$claim->insurance_details->short_name." - ".@$claim->insurance_details->insurance_name;
    		$abb_insurance = array_unique($abb_insurance);
    		foreach (array_keys($abb_insurance, ' - ') as $key) {
            	unset($abb_insurance[$key]);
        	}
		}
		$abbreviation = ['abb_billing' => $abb_billing, 'abb_rendering' => $abb_rendering, 'abb_facility' => $abb_facility, 'abb_insurance' => $abb_insurance];
		$abb_controller = New AbbreviationController;
		$abb_controller->abbreviation($abbreviation,$pdf); 	
	}
}

class ClaimEditsMypdf extends FPDF
{
    public function header(){

    	$controller = New ClaimControllerV1;
        $api_response = $controller->getClaimsDataSearchApi('electronic');
        $api_response_data = $api_response->getData();
		$search_by = $api_response_data->data->get_list_header;

        $this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0, 135, 127);
        $this->AddFont('Calibri-Bold','','calibrib.php');
        $this->SetFont('Calibri-Bold','',10.5);
        $x_axis=$this->getx();
        $c_width = 295;
        $c_height = 0;
        $text = Practice::getPracticeName().' - Claim Edits';
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

        $this->header = ['Claim No', 'Dos', 'Patient Name', 'Billed To', 'Category', 'Payer ID', 'Rendering', 'Billing', 'Facility', 'Charge Amt($)', 'AR Bal($)', 'Created Date', 'Filed Date', 'Status', 'Sub Status'];

        foreach ($this->header as $key => $value) {
            $this->SetFont('Calibri-Bold','',8);
            $this->SetTextColor(100, 100, 100);
            $x_axis=$this->getx();// now get current pdf x axis value
            $c_height = 6;// cell height
            $c_width = (290/count($this->header));// cell width 
            $lengthToSplit = strlen($value);

            if ($value == "Charge Amt($)" || $value == "AR Bal($)") {
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
