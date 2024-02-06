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
use App\Http\Controllers\Patients\Api\PatientApiController as PatientApiController;
use App\Http\Controllers\ExportPDF\AbbreviationController as AbbreviationController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;

$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);

class PatientsListPDFController extends Controller
{
    public function index()
    {
    	set_time_limit(300);
        $request = Request::All();
        $req = $request['dataArr']['data'];
        $created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
		$filename = 'Downloads/Patients_list_'.$created_date.'.pdf';
		$url = Request::url();
		$user_id = Auth()->user()->id;
		$report_export = new ReportExport;
		$practice_id = Session::get('practice_dbid');
		$counts = ReportExport::selectRaw('count(report_name) as counts')->where('report_name',$req['report_name'])->groupby('report_name')->where('practice_id',$practice_id)->value('counts');
        $report_count = (isset($counts) && $counts != null && !empty($counts))?($counts+1):1;
        $report_export->report_count = $report_count;
		$report_export->practice_id = $practice_id;
		$report_export->report_name = $req['report_name'];
		$report_export->report_url = $url;
		$report_export->report_file_name = 'Downloads/Patients_list_'.$created_date.'.pdf';
		$report_export->report_type = $req['export'];
		// $report_export->parameter = $headers;
		$report_export->report_controller_name = 'PatientsController';
		$report_export->report_controller_func = 'getPatientExport';
		$report_export->status = 'Inprocess';
		$report_export->created_by = $user_id;
		$report_export->save();
		$report_export_id = $report_export->id;

        $controller = New PatientApiController;
        $api_response = $controller->getIndexApi('pdf');
        $api_response_data = $api_response->getData();
        $patients = (array) $api_response_data->data->patients;
        $insurance_list = (array) $api_response_data->data->insurances;

        $pdf = new patinet_list_mypdf("L","mm","A4");
		$pdf->SetMargins(2,6);
		$pdf->SetTextColor(100,100,100);
		$pdf->SetDrawColor(217, 217, 217);
		$pdf->AddPage();
		$pdf->AddFont('Calibri','','calibri.php');
		$pdf->SetFont('Calibri','',7);
        self::BladeContent($patients, $pdf, $insurance_list);
        $pdf->Output($filename,'F');
        $report_export->update(['status'=>'Pending']);
		exit();
	}

	public function BladeContent($patients, $pdf, $insurance_list){
		if(!empty($patients)){
			$insurances = json_decode(json_encode($insurance_list), TRUE);
			foreach($patients as $patient){
			 	
			 	if($patient !=''){
			 		$getReachEndday = '';
					$patient_id = Helpers::getEncodeAndDecodeOfId(@$patient->id);
					$plan_end_date = ''; //App\Http\Helpers\Helpers::getPatientPlanEndDate(@$patient->id,'','','Primary'); 
					if ($plan_end_date == '0000-00-00' || $plan_end_date == '') {
						$getReachEndday = 0;
					} else {
						$now = strtotime(date('Y-m-d')); // or your date as well
						$your_date = strtotime($plan_end_date);
						$datediff = $now - $your_date;
						$getReachEndday = floor($datediff / (60 * 60 * 24));
					}
					$insurance_name = "";
					if ($patient->is_self_pay == 'Yes') {
						$insurance_name = "Self Pay";
					} else {
						$insurance_name = (isset($insurances['all'][$patient->id])) ? ($insurances['all'][$patient->id]) : '';
					}
					$patient_ins_name = $insurance_name;
					$open_new_window = 0; // open patient view in same page. 		

					$fin_details = @$patient->patient_claim_fin[0];
					
					$patient_due = (!empty($patient->total_pat_due)) ? Helpers::priceFormat(@$patient->total_pat_due,'',1) : '0.00';
					 
					$ins_due = (!empty($patient->total_ins_due)) ? Helpers::priceFormat(@$patient->total_ins_due,'',1) : '0.00';
					  
					$ar_due = (!empty($patient->total_ar)) ? Helpers::priceFormat(@$patient->total_ar,'',1) : '0.00';
					$patient_name = Helpers::getNameformat($patient->last_name, $patient->first_name, $patient->middle_name);

					$pdf->AddFont('Calibri','','calibri.php');
				    $pdf->SetFont('Calibri','',7);
					$pdf->SetTextColor(100, 100, 100);
					$c_width=(295/12);
					$c_height=5;// cell height

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = @$patient->account_no;
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width-10,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = @$patient_name;
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width+10,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
                                        
                    if($patient->mobile!=''){ $mobile=@$patient->mobile; }else{ $mobile='-Nil-'; }
                                        
					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = $mobile;
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = @$patient->gender;
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = Helpers::dateFormat(@$patient->dob,'claimdate');
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width-5,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
                                        
                    if($patient->ssn!=''){ $ssn=@$patient->ssn; }else{ $ssn='-Nil-'; }
                                        
					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = @$ssn;
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width-5,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");                                        
                    if($insurance_name !=''){ $insurance_name=$insurance_name; }else{ $insurance_name='-Nil-'; }

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = @$insurance_name;
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width+10,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = @$patient_due;
					$lengthToSplit = strlen($text);
					if ($text < 0) {
						$pdf->SetTextColor(255, 0, 0);
					}
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
					$pdf->SetTextColor(100, 100, 100);

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = @$ins_due;
					$lengthToSplit = strlen($text);
					if ($text < 0) {
						$pdf->SetTextColor(255, 0, 0);
					}
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
					$pdf->SetTextColor(100, 100, 100);

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = @$ar_due;
					$lengthToSplit = strlen($text);
					if ($text < 0) {
						$pdf->SetTextColor(255, 0, 0);
					}
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
					$pdf->SetTextColor(100, 100, 100);

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = Helpers::timezone(@$patient->created_at, 'm/d/y');
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = @$patient->percentage;
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$pdf->Ln();
			 	}
			 }
		}
	}
}

class patinet_list_mypdf extends FPDF
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
		$this->Vcell(295,12,$x_axis,'Patients List',160,"","C");

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

		$header = ['Acc No', 'Patient Name', 'Cell Phone', 'Gender', 'DOB', 'SSN', 'Payer', 'Pat Due($)', 'Ins Due($)', 'AR Due($)','Created On', ' %'];

		foreach ($header as $key => $value) {
			$this->SetFont('Calibri-Bold','',8);
			$this->SetTextColor(100, 100, 100);
			$x_axis=$this->getx();// now get current pdf x axis value
			$c_height = 6;// cell height
			$c_width = (295/count($header));// cell width 
			$lengthToSplit = strlen($value);
			$align = 'L';
			if ($value == "Acc No") {
				$c_width = $c_width-10;
			}
			if ($value == "Patient Name" || $value == "Payer") {
				$c_width = $c_width+10;
			}
			if ($value == "Pat Due($)" || $value == "Ins Due($)" || $value == "AR Due($)") {
				$align = "R";
			}
			if ($value == "DOB" || $value == "SSN") {
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