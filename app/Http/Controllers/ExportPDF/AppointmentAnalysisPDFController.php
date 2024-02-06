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
use App\Http\Controllers\Reports\Appointment\Api\AppointmentApiController as AppointmentApiController;
use App\Http\Controllers\ExportPDF\AbbreviationController as AbbreviationController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Medcubics\Users as Users;
use App\Models\Provider as Provider;
use App\Models\Facility as Facility;
use App\Models\ReasonForVisit as ReasonForVisit;

$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);

class AppointmentAnalysisPDFController extends Controller
{
    public function index()
    {
    	set_time_limit(300);
    	try {
	    	$created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
			$filename = 'Appointment_Analysis_Report_'.$created_date.'.pdf';
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
			$report_export->report_controller_name = 'AppointmentApiController';
			$report_export->report_controller_func = 'analysissearchApi';
			$report_export->status = 'Inprocess';
			$report_export->created_by = $user_id;
			$report_export->save();
			$report_export_id = $report_export->id;

	        $controller = New AppointmentApiController;
	        $api_response = $controller->analysissearchApi('pdf');
	        $api_response_data = $api_response->getData();
	        $appt_result = $api_response_data->data->appt_result;

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

	        $pdf = new appointment_mypdf("L","mm",array(450,100));
			$pdf->SetMargins(2,6);
			$pdf->SetTextColor(100,100,100);
			$pdf->SetDrawColor(217, 217, 217);
			$pdf->AddPage();
			$pdf->AddFont('Calibri','','calibri.php');
			$pdf->SetFont('Calibri','',7);
	        self::BladeContent($appt_result, $pdf);
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
	    	\Log::info("Error Occured While export Appointment Analysis report. Message:".$e->getMessage() );
	    }    
		exit();
	}

	public function BladeContent($appt_result, $pdf){
		$pdf->AddFont('Calibri','','calibri.php');
	    $pdf->SetFont('Calibri','',7);
		$pdf->SetTextColor(100, 100, 100);
		$c_width=(450/18);
		$c_height=5;// cell height

		foreach ($appt_result as $key => $result) {

			if(isset($result->account_no) && $result->account_no != ''){
				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->scheduled_on)? Helpers::timezone(@$result->scheduled_on, 'm/d/y') : '-Nil-' ;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->appointment_time)? @$result->appointment_time : '-Nil-' ;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->status)? @$result->status : '-Nil-' ;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->cancel_delete_reason)? @$result->cancel_delete_reason : '-Nil-' ;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->account_no)? @$result->account_no : '-Nil-' ;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->patient_name)? @$result->patient_name : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->rendering_short_name)? @$result->rendering_short_name : '-Nil-' ;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->facility_short_name)? @$result->facility_short_name : '-Nil-' ;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->reason)? @$result->reason : '-Nil-' ;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->responsibility)? @$result->responsibility : '-Nil-' ;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->eligibility)? @$result->eligibility : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat(@$result->copay,'',1);
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->copay_option)? @$result->copay_option :'-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->copay_date)? @$result->copay_date : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->next_appt)? @$result->next_appt : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat(@$result->total_pat_due,'',1);
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				if($result->created_by != 0 && isset($user_names[@$result->created_by]) ){
					$text = !empty($result->created_by)? $user_names[@$result->created_by] : '-Nil-';
				}else{
					$text = '-Nil-';
				}
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->created_at)? @$result->created_at : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
			}
			else{
				$last_name = $result->patient->last_name;
	          	$first_name = $result->patient->first_name;
	          	$middle_name = $result->patient->middle_name;
	         	$patient_name = Helpers::getNameformat($last_name, $first_name, $middle_name);
				if($result->patient->eligibility_verification == 'None'){
	                $eligibility = 'Unverified';
	              }else if($result->patient->eligibility_verification == 'Active'){
	                $eligibility = 'Eligible';
	              }else if($result->patient->eligibility_verification == 'Inactive'){
	                $eligibility = 'Ineligible';
	            }else{
	                $eligibility = 'Error';
	            }
	            if($result->patient->is_self_pay == 'No'){
	                @$responsibility = $result->patient->patient_insurance[0]->insurance_details->short_name;
	            } else{
	                $responsibility = 'Self Pay';
	            } 
	            if($result->copay != ''){
	                $co_pay = Helpers::priceFormat(@$result->copay);
	            }else{ 
	                $co_pay = '0.00'; 
	            }
	            if(@$result->copay_date != '0000-00-00'){
	                $result->copay_date = Helpers::dateFormat(@@$result->copay_date);
	            }else{
	                 $result->copay_date = '-Nil-';
	            } 
	            if($result->copay_option != ''){
	                $result->copay_option = @$result->copay_option;
	            }else{
	                $result->copay_option = '-Nil-';
	            } 

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->scheduled_on)? Helpers::timezone(@$result->scheduled_on, 'm/d/y') : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->appointment_time)? @$result->appointment_time : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->status)? @$result->status : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = (!empty(@$result->cancel_delete_reason))? @$result->cancel_delete_reason:'-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->patient->account_no)? @$result->patient->account_no : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($patient_name)? @$patient_name : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->provider->short_name)? @$result->provider->short_name : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->facility->short_name)? @$result->facility->short_name : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->reasonforvisit->reason)? @$result->reasonforvisit->reason : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($responsibility)? @$responsibility : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($eligibility)? @$eligibility : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat(@$co_pay,'',1);
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$result->copay_option;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$result->copay_date;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->next_appt)?Helpers::dateFormat(@$result->next_appt):'-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat(@$result->patient->patient_claim_fin[0]->total_pat_due,'',1);
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->created_user->short_name)? @$result->created_user->short_name : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($result->created_at)? Helpers::timezone(@$result->created_at, 'm/d/y') : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
			}


			$pdf->Ln();
			$abb_user[] = @$result->created_user->short_name." - ".@$result->created_user->name;
	    	$abb_user = array_unique($abb_user);
			foreach (array_keys($abb_user, ' - ') as $key) {
	        	unset($abb_user[$key]);
	        }

	        $abb_rendering[] = @$result->provider->short_name." - ".@$result->provider->provider_name;
	    	$abb_rendering = array_unique($abb_rendering);
			foreach (array_keys($abb_rendering, ' - ') as $key) {
	        	unset($abb_rendering[$key]);
	        }

	        $abb_facility[] = @$result->facility->short_name." - ".@$result->facility->facility_name;
	    	$abb_facility = array_unique($abb_facility);
			foreach (array_keys($abb_facility, ' - ') as $key) {
	        	unset($abb_facility[$key]);
	        }
		}
		$pdf->Ln();
		$abbreviation = ['abb_user' => $abb_user, 'abb_facility' => $abb_facility, 'abb_rendering' => $abb_rendering];
		$abb_controller = New AbbreviationController;
		$abb_controller->abbreviation($abbreviation,$pdf);
	}
}

class appointment_mypdf extends FPDF
{
	public function header(){

		$request = Request::All();
    	// dd($request);
        if(!empty($request['created_at'])){
            $date = explode('-',$request['created_at']);          
            $start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }           
            $end_date = date("Y-m-d", strtotime($date[1]));
            $search_by['Appointment Date'][]= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));
        }
        if(!empty($request['status'])) {
            $status = explode(',',$request['status']);
            if(is_array($request['status']))
            $search_by['Appointment Status'][] = $request['status'];
         }
         if(!empty($request['rendering_provider_id'])){
            $renders_id = explode(',',$request['rendering_provider_id']);
            foreach ($renders_id as $id) {
                $value_name[] = Provider::getProviderFullName($id);
            }
            $search_render = implode(", ", array_unique($value_name)); 
            $search_by['Rendering Provider'][] = $search_render;  
        }

       if(!empty($request['facility_id'])){
            $facility = Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ', ') as facility_name")->whereIn('id', explode(',',$request['facility_id']))->get()->toArray();
            $search_by["Facility Name"][] =  @array_flatten($facility)[0];
        }
        if(!empty($request['reason_for_visits'])){
            $reason = ReasonForVisit::selectRaw("GROUP_CONCAT(reason SEPARATOR ', ') as facility_name")->whereIn('id', explode(',',$request['reason_for_visits']))->get()->toArray();
            $search_by["Reason for Visit"][] =  @array_flatten($reason)[0];
        }
        if(!empty($request['reason_for_visits'])){
            $reason = ReasonForVisit::selectRaw("GROUP_CONCAT(reason SEPARATOR ', ') as facility_name")->whereIn('id', explode(',',$request['reason_for_visits']))->get()->toArray();
            $search_by["Reason for Visit"][] =  @array_flatten($reason)[0];
        }
        if(isset($request['eligible'])) {
            $eligible = $request['eligible'];
            if($request['eligible'] == 'Active'){
                $eligible = 'Eligible';
            }else if($request['eligible'] == 'Inactive'){
                $eligible = 'Ineligible';
            }else if($request['eligible'] == 'None'){
                $eligible = 'Unverified';
            }else{
                $eligible = 'All';
            }
            $search_by['Eligibility'][] = $eligible;
         }
        if (isset($request['user']) && $request['user'] != '') {      
            $User_name =  Users::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')->pluck('short_name', 'id')->all(); 
            $User_name = implode(", ", array_unique($User_name));
            $search_by['User'][] = $User_name;
        }

		$this->SetFillColor(255, 255, 255);
		$this->SetTextColor(0, 135, 127);
	    $this->AddFont('Calibri-Bold','','calibrib.php');
	    $this->SetFont('Calibri-Bold','',10.5);
		$x_axis=$this->getx();
		$c_width = 450;
		$c_height = 0;
		$text = Practice::getPracticeName()." - Appointment Analysis Report";
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
		$this->Vcell(450,12,$x_axis,$text_imp,160,"","C");

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

		$header = ['Appt Date', 'Appt Time', 'Appt Status', 'Canceled Reason', 'Acc No', 'Patient Name', 'Rendering Provider', 'Facility', 'Reason for Visit', 'Responsibility', 'Eligibility','Co-Pay Amt($)', 'Mode of Pmt', 'Paid Date', 'Future Appt', 'Pat Bal($)', 'User', 'Created Date'];

		foreach ($header as $key => $value) {
			$this->SetFont('Calibri-Bold','',8);
			$this->SetTextColor(100, 100, 100);
			$x_axis=$this->getx();// now get current pdf x axis value
			$c_height = 6;// cell height
			$c_width = (450/count($header));// cell width 
			$lengthToSplit = strlen($value);
			$align = 'L';
			if ($value == "Rendering Provider") {
				$lengthToSplit = strlen($value);
			}if ($value == "Reason for Visit") {
				$c_width = $c_width;
			}if ($value == "Co-Pay Amt($)") {
				$lengthToSplit = strlen($value);
				$align = "R";
			}if ($value == "Mode of Pmt") {
				$lengthToSplit = strlen($value);
			}if ($value == "User" || $value == "Acc No") {
				$c_width = $c_width;
			}if ($value == "Appt Date") {
				$c_width = $c_width;
			}if ($value == "Canceled Reason") {
				$c_width = $c_width;
			}if ($value == "Appt Time") {
				$c_width = $c_width;
			}if ($value == "Patient Name") {
				$c_width = $c_width;
			}if ($value == "Facility") {
				$c_width = $c_width;
			}if ($value == "Pat Bal($)") {
				$align = "R";
			}if ($value == "Created Date") {
				$lengthToSplit = strlen($value);
			}if ($value == "Responsibility") {
				$c_width = $c_width;
			}if ($value == "Eligibility") {
				$c_width = $c_width;
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