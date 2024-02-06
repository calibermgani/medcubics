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
use App\Http\Controllers\Reports\Api\ReportApiController as ReportApiController;
use App\Http\Controllers\ExportPDF\AbbreviationController as AbbreviationController;
use App\Http\Controllers\Reports\ReportController as ReportController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;

$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);

class DemographicSheetPDFController extends Controller
{
    public function index()
    {
    	set_time_limit(300);
    	try {
	    	$created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
			$filename = 'Downloads/Demographic_Sheet_'.$created_date.'.pdf';
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
			$report_export->report_file_name = 'Downloads/Demographic_Sheet_'.$created_date.'.pdf';
			$report_export->report_controller_name = 'ReportApiController';
			$report_export->report_controller_func = 'getPatientDemographicsSPFilterApi';
			$report_export->status = 'Inprocess';
			$report_export->created_by = $user_id;
			$report_export->save();
			$report_export_id = $report_export->id;

	        $controller = New ReportApiController;
	        $api_response = $controller->getPatientDemographicsSPFilterApi('pdf');
	        $api_response_data = $api_response->getData();
	        $patient_demographics_filter = $api_response_data->data->filter_result;
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

	        $pdf = new demographics_mypdf("L","mm",array(500,100));
			$pdf->SetMargins(2,6);
			$pdf->SetTextColor(100,100,100);
			$pdf->SetDrawColor(217, 217, 217);
			$pdf->AddPage();
			$pdf->AddFont('Calibri','','calibri.php');
			$pdf->SetFont('Calibri','',7);
	        self::BladeContent($patient_demographics_filter, $pdf);
	       // $pdf->Output($filename,'F');

			/* google bucket integration */
			$resp = $pdf->Output($filename,'S');
	        $data['filename'] = 'Demographic_Sheet_'.$created_date.'.pdf';;
	        $data['contents'] = $resp;
	        $target_dir = $practice_id.DS.'reports'.DS.$user_id.DS.date('my', strtotime($report_export->created_at));
	        $data['target_dir'] = $target_dir;
			// Upload to google bucket code
	        Helpers::uploadResourceFile('reports', $practice_id, $data);
			
	        $report_export->update(['parameter' => $headers, 'status'=>'Pending']);
        } catch(Exception $e) {
	    	\Log::info("Error Occured While export Patient Demographic report. Message:".$e->getMessage() );
	    }
		exit();
	}

	public function BladeContent($patient_demographics_filter, $pdf){
		foreach ($patient_demographics_filter as $key => $result) {
			
            $total_adj = 0;
            $patient_total = 0;
            $insurance_total = 0;  
            $set_title = (@$result->title)? @$result->title.". ":'';
            $patient_name =    $set_title."". Helpers::getNameformat(@$result->last_name,@$result->first_name,@$result->middle_name);                     
             $primary_ins = $secondary_ins = $tertiary_ins = $primary_policy_id = $secondary_policy_id = $tertiary_policy_id = $primary_ins_short_name = $secondary_ins_short_name = $tertiary_ins_short_name = '';
            if(isset($result->ins_category) && $result->ins_category != '') {
                $insurance = explode("^^", $result->ins_category);
                foreach($insurance as $ins_val) {
                    if($ins_val != ''){
                        $det = explode("$$", @$ins_val);
                        if(($det[0]) == 'Primary'){
                            $primary_ins = @$det[1];
                            $primary_policy_id = @$det[2];
                            $primary_ins_short_name = @$det[3];
                        }elseif(($det[0]) == 'Secondary'){
                            $secondary_ins = @$det[1];
                            $secondary_policy_id = @$det[2];
                            $secondary_ins_short_name = @$det[3];
                        }else {
                            $tertiary_ins = @$det[1];
                            $tertiary_policy_id = @$det[2];
                            $tertiary_ins_short_name = @$det[3];
                        }
                    }
                }
            }
			
			$pat_category = $guar_l_name = $guar_f_name = $guar_m_name = $emrg_l_name = $emrg_f_name = $emrg_m_name = $emrg_hm_phone = $emrg_cl_phone = $emp_name = '';
			if(isset($result->pat_contact_category) && $result->pat_contact_category != '') {
				$patient_contacts = explode("^^", $result->pat_contact_category);
				foreach($patient_contacts as $patient_contacts_val){
					if($patient_contacts_val != ''){
						$contact_list = explode("$$", $patient_contacts_val);//dd($contact_list);
						$pat_category = $contact_list[0];
						if(($contact_list[0]) == 'Guarantor'){
							$guar_l_name = @$contact_list[1];
							$guar_f_name = @$contact_list[2];
							$guar_m_name = @$contact_list[3];
						} elseif(($contact_list[0]) == 'Emergency Contact'){
							$emrg_l_name = @$contact_list[4];
							$emrg_f_name = @$contact_list[5];
							$emrg_m_name = @$contact_list[6];
							$emrg_hm_phone = @$contact_list[7];
							$emrg_cl_phone = @$contact_list[8];
						} elseif(($contact_list[0]) == 'Employer') {
							$emp_name = @$contact_list[9];
						}
					}
				}
			}
			
			$pdf->AddFont('Calibri','','calibri.php');
		    $pdf->SetFont('Calibri','',7);
			$pdf->SetTextColor(100, 100, 100);
			$c_width=(500/25);
			$c_height=5;// cell height

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($result->last_name)? @$result->last_name : '-Nil-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($result->first_name)? @$result->first_name : '-Nil-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($result->middle_name)? @$result->middle_name : '-Nil-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($result->gender)? @$result->gender : '-Nil-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($result->dob)? @$result->dob : '-Nil-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($result->ssn)? @$result->ssn : '-Nil-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($result->account_no)? @$result->account_no : '-Nil-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			if($result->is_self_pay == "No")
                $text ='Insurance';
            else
                $text = 'Self Pay';
			$lengthToSplit = 13;
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis = $pdf->getx();// now get current pdf x axis value
			$text = !empty($result->phone)? @$result->phone : '-Nil-';
			$lengthToSplit = 16;
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis = $pdf->getx();// now get current pdf x axis value
			$text = !empty($result->email)? @$result->email : '-Nil-';
			$lengthToSplit = 16;
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$guarantor = $guar_l_name.', '.$guar_f_name.' '.$guar_m_name;
			$guarantor_name = (isset($guarantor) && $guarantor != ",  ") ? $guarantor : '-Nil-';
			$emergency = $emrg_l_name.', '.$emrg_f_name.' '.$emrg_m_name;
			$emergency_contact_name = (isset($emergency) && $emergency != ",  ") ? $emergency : '-Nil-';

			$x_axis = $pdf->getx();// now get current pdf x axis value
			$text = !empty($guarantor_name)? @$guarantor_name : '-Nil-';
			$lengthToSplit = 16;
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis = $pdf->getx();// now get current pdf x axis value
			$text = @$emergency_contact_name;
			$lengthToSplit = 16;
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis = $pdf->getx();// now get current pdf x axis value
			$text = !empty($emrg_hm_phone)? @$emrg_hm_phone : '-Nil-';;
			$lengthToSplit = 16;
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis = $pdf->getx();// now get current pdf x axis value
			$text = @$emrg_cl_phone;
			$lengthToSplit = 16;
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
                        
            $emp_name = (isset($emp_name) && $emp_name != "") ? $emp_name : '-Nil-';
			$x_axis = $pdf->getx();// now get current pdf x axis value
			$text = @$emp_name;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,15,'', "L");


			$x_axis = $pdf->getx();// now get current pdf x axis value
			$text = !empty($primary_ins) ? @$primary_ins ."/". @$primary_policy_id : " -Nil- ";
			$lengthToSplit = 10;
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis = $pdf->getx();// now get current pdf x axis value
			$text = !empty($secondary_ins) ? @$secondary_ins ."/". @$secondary_policy_id : " -Nil- ";
			$lengthToSplit = 10;
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis = $pdf->getx();// now get current pdf x axis value
			$text = !empty($tertiary_ins) ? @$tertiary_ins ."/". @$tertiary_policy_id : " -Nil- ";
			$lengthToSplit = 10;
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");


			$x_axis = $pdf->getx();// now get current pdf x axis value
			$text = @$result->address1;
			$lengthToSplit = 10;
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis = $pdf->getx();// now get current pdf x axis value
			$text = @$result->address2;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis = $pdf->getx();// now get current pdf x axis value
			$text = @$result->city;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis = $pdf->getx();// now get current pdf x axis value
			$text = @$result->state;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis = $pdf->getx();// now get current pdf x axis value
			$zip4 = !empty($result->zip4)?' - '.$result->zip4:'';
			$text = @$result->zip5.$zip4;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis = $pdf->getx();// now get current pdf x axis value
			$text = !empty($result->created_at)? Helpers::timezone(@$result->created_at, 'm/d/y') :'-Nil-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis = $pdf->getx();// now get current pdf x axis value
			$text = !empty($result->created_by)? Helpers::user_names(@$result->created_by) :'-Nil-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$pdf->Ln();

			$abb_user[] = Helpers::user_names(@$result->created_by)." - ".Helpers::getUserFullName(@$result->created_by);
	    	$abb_user = array_unique($abb_user);
			foreach (array_keys($abb_user, ' - ') as $key) {
	        	unset($abb_user[$key]);
	    	}

	    	$abb_pri_insurance[] = @$primary_ins_short_name." - ".@$primary_ins;
	    	$abb_sec_insurance[] = @$secondary_ins_short_name." - ".@$secondary_ins;
	  		$abb_ter_insurance[] = @$tertiary_ins_short_name." - ".@$tertiary_ins;

		}
	    $abb_insurance_merge = array_merge($abb_pri_insurance, $abb_sec_insurance, $abb_ter_insurance);
	    $abb_insurance = array_unique($abb_insurance_merge);
		foreach (array_keys($abb_insurance, ' - ') as $key) {
        	unset($abb_insurance[$key]);
    	}
		
		$abbreviation = ['abb_user' => @$abb_user, 'abb_insurance' => @$abb_insurance];
		$abb_controller = New AbbreviationController;
		$abb_controller->abbreviation($abbreviation,$pdf);
	}
}

class demographics_mypdf extends FPDF
{
	public function header(){

		$request = Request::All();
    	// dd($request);
        $controller = New ReportApiController;
        $api_response = $controller->getPatientDemographicsSPFilterApi('pdf');
        $api_response_data = $api_response->getData();
        $search_by = $api_response_data->data->search_by;

		$this->SetFillColor(255, 255, 255);
		$this->SetTextColor(0, 135, 127);
	    $this->AddFont('Calibri-Bold','','calibrib.php');
	    $this->SetFont('Calibri-Bold','',10.5);
		$x_axis=$this->getx();
		$c_width = 500;
		$c_height = 0;
		$text = Practice::getPracticeName()." - Demographic Sheet";
		$lengthToSplit = strlen($text);
		$this->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","C");

        $this->AddFont('Calibri','','calibri.php');
        $this->SetFont('Calibri','',9.5);
		$this->SetTextColor(100, 100, 100);
		$this->Ln();

		$i = 0;
		$text = [];
		foreach ((array)$search_by as $key => $val) {
			$text[] = $key.":".@$val[0];                           
            $i++; 
		}
		$text_imp = implode(" | ", $text);
		$this->Vcell(500,12,$x_axis,$text_imp,160,"","C");

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

		$header = ['Last Name','First Name','MI','Gender','DOB','SSN','Acc No','Responsibility','Home Phone','Email ID','Guarantor Name','ER Contact Person','ER HomePhone','ER CellPhone','Employer Name','Primary Insurance/Policy ID','Secondary Insurance/Policy ID','Tertiary Insurance/Policy ID','Address Line 1','Address Line 2','City','State','Zip Code','Created Date','User'];
		
		foreach ($header as $key => $value) {

			$this->SetFont('Calibri-Bold','',8);
			$this->SetTextColor(100, 100, 100);
			$x_axis=$this->getx();// now get current pdf x axis value
			$c_height = 6;// cell height
			$c_width = (500/count($header));// cell width 
			$lengthToSplit = strlen($value);
			if ($value == "ER Contact Person" || $value == "Primary Insurance/Policy ID" || $value=='Secondary Insurance/Policy ID' || $value=='Tertiary Insurance/Policy ID' || $value=='Address Line 1') {
				$lengthToSplit = 10;
			}
			if($value=='MI')
				$lengthToSplit = 5;
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
	    $c_width = 500;
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