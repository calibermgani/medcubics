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
use App\Models\Provider as Provider;
use App\Models\Facility as Facility;
use App\Models\Insurance as Insurance;
use App\Models\ReportExport as ReportExport;
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
use App\Models\Medcubics\Users as Users;
use App\Models\ReportExportTask as ReportExportTask;
use App\Http\Controllers\Reports\Api\ReportApiController as ReportApiController;
use App\Http\Controllers\ExportPDF\AbbreviationController as AbbreviationController;
use App\Http\Controllers\Reports\Financials\Api\FinancialApiController as FinancialApiController;
use App\Http\Controllers\Reports\ReportController as ReportController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;

$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);

class RefundAnalysisPDFController extends Controller
{
    public function index()
    {
    	set_time_limit(300);
    	try {
	    	$created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
			$filename = 'Refund_Analysis_Detailed_'.$created_date.'.pdf';
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
			$report_export->report_controller_name = 'ReportApiController';
			$report_export->report_controller_func = 'getRefundsearchApiSP';
			$report_export->status = 'Inprocess';
			$report_export->created_by = $user_id;
			$report_export->save();
			$report_export_id = $report_export->id;

	        $controller = New ReportApiController;
	        $api_response = $controller->getRefundsearchApi('pdf');
	        $api_response_data = $api_response->getData();
	        $total_refund = $api_response_data->data->refund_value;
	        $get_refund_datas = $api_response_data->data->get_refund_data;
	        $refund_type = $api_response_data->data->refund_type;
	        $unposted = $api_response_data->data->unposted;
	        $wallet = $api_response_data->data->wallet;

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

	        $pdf = new refund_analysis_mypdf("L","mm",array(400,100));
			$pdf->SetMargins(2,6);
			$pdf->SetTextColor(100,100,100);
			$pdf->SetDrawColor(217, 217, 217);
			$pdf->AddPage();
			$pdf->AddFont('Calibri','','calibri.php');
			$pdf->SetFont('Calibri','',7);
			$grand_total = '';
	        self::BladeContent($get_refund_datas, $total_refund, $refund_type, $pdf, $unposted, $wallet);
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
	    	\Log::info("Error Occured While export Refund Analysis report. Message:".$e->getMessage() );
	    }
		exit();
	}

	public function BladeContent($get_refund_datas, $total_refund, $refund_type, $pdf, $unposted, $wallet){
		if($refund_type == 'insurance'){
			foreach(@$get_refund_datas as $refund_value){
				$pdf->AddFont('Calibri','','calibri.php');
			    $pdf->SetFont('Calibri','',7);
				$pdf->SetTextColor(100, 100, 100);
				$c_width=(400/12);
				$c_height=5;// cell height

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($refund_value->claim->claim_number)? $refund_value->claim->claim_number : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($refund_value->claim->date_of_service)? date('m/d/Y',strtotime($refund_value->claim->date_of_service)) : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($refund_value->claim->patient->account_no)? $refund_value->claim->patient->account_no : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::getNameformat(@$refund_value->claim->patient->last_name,@$refund_value->claim->patient->first_name,@$refund_value->claim->patient->middle_name);
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($refund_value->claim->rendering_provider->short_name)? $refund_value->claim->rendering_provider->short_name : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($refund_value->claim->billing_provider->short_name)? $refund_value->claim->billing_provider->short_name : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($refund_value->claim->facility_detail->short_name)? $refund_value->claim->facility_detail->short_name : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
				$insurance_name = Insurance::where('id', @$refund_value->insurance_id)->value("insurance_name");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = $insurance_name;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($refund_value->latest_payment_check->check_details->check_date)? Helpers::dateFormat(@$refund_value->latest_payment_check->check_details->check_date, 'dob') : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($refund_value->latest_payment_check->check_details->check_no)? @ucwords($refund_value->latest_payment_check->check_details->check_no) : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat(abs(@$refund_value->total_paid));
				$lengthToSplit = strlen($text);
				if ($text < 0) {
					$pdf->SetTextColor(255, 0, 0);
				} else {
					$pdf->SetTextColor(100, 100, 100);	
				}
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = !empty($refund_value->created_by)? @$refund_value->user->short_name : '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$pdf->Ln();

				/*$abb_billing[] = @$refund_value->claim->billing_provider_short_name." - ".@$refund_value->claim->billing_provider_name;
	    		$abb_billing = array_unique($abb_billing);
	    		foreach (array_keys($abb_billing, ' - ') as $key) {
	            	unset($abb_billing[$key]);
	        	}

	        	$abb_rendering[] = @$refund_value->claim->rendering_provider_short_name." - ".@$refund_value->claim->rendering_provider_name;
	        	$abb_rendering = array_unique($abb_rendering);
	    		foreach (array_keys($abb_rendering, ' - ') as $key) {
	            	unset($abb_rendering[$key]);
	        	}

	        	$abb_facility[] = @$refund_value->claim->facility_short_name." - ".@$refund_value->claim->facility_name;
	        	$abb_facility = array_unique($abb_facility);
	    		foreach (array_keys($abb_facility, ' - ') as $key) {
	            	unset($abb_facility[$key]);
	        	}

	        	$abb_insurance[] = @$refund_value->claim->insurance_short_name." - ".@$refund_value->claim->insurance_name;
	        	$abb_insurance = array_unique($abb_insurance);
	    		foreach (array_keys($abb_insurance, ' - ') as $key) {
	            	unset($abb_insurance[$key]);
	        	}

				$abb_user[] = Helpers::user_names(@$refund_value->claim->created_by)." - ".Helpers::getUserFullName(@$refund_value->claim->created_by);
	        	$abb_user = array_unique($abb_user);
	    		foreach (array_keys($abb_user, ' - ') as $key) {
	            	unset($abb_user[$key]);
	        	}*/
			}
			$this->SummaryContent($total_refund,$pdf,$var='insurance',$title='Total Insurance Refunds');
		}	
		elseif($refund_type == 'patient' && empty($unposted) && empty($wallet)){
			foreach($get_refund_datas as $refund_value->claim){
				
				$patient_name = Helpers::getNameformat(@$refund_value->claim->claim_patient_det->last_name,@$refund_value->claim->claim_patient_det->first_name,@$refund_value->claim->claim_patient_det->middle_name);

				$pdf->AddFont('Calibri','','calibri.php');
			    $pdf->SetFont('Calibri','',7);
				$pdf->SetTextColor(100, 100, 100);
				$c_width=(290/4);
				$c_height=5;// cell height

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$refund_value->claim->patient_name;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
				
				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$refund_value->claim->account_no;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat(@$refund_value->claim->refund_amt);
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::user_names(@$refund_value->claim->created_by);
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");	

				$pdf->Ln();

				$abb_user[] = Helpers::user_names(@$refund_value->claim->created_by)." - ".Helpers::getUserFullName(@$refund_value->claim->created_by);
	        	$abb_user = array_unique($abb_user);
	    		foreach (array_keys($abb_user, ' - ') as $key) {
	            	unset($abb_user[$key]);
	        	}
			}
			$this->SummaryContent($total_refund,$pdf,$var='patient',$title='Total Patient Refunds');
		}
		elseif(!is_null($unposted) && !empty($unposted) || !is_null($wallet) && !empty($wallet)){
			foreach($get_refund_datas as $refund_value->claim){
				$refund_amt = $refund_value->claim->pmt_amt;
                $patient_name = App\Http\Helpers\Helpers::getNameformat(@$refund_value->claim->patient->last_name,@$refund_value->claim->patient->first_name,@$refund_value->claim->patient->middle_name);

                $pdf->AddFont('Calibri','','calibri.php');
			    $pdf->SetFont('Calibri','',7);
				$pdf->SetTextColor(100, 100, 100);
				$c_width=(290/4);
				$c_height=5;// cell height

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$refund_value->claim->patient_name;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::dateFormat(@$refund_value->claim->check_date);
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$refund_value->claim->check_no;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$refund_value->claim->refund_amt;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");	

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::user_names(@$refund_value->claim->created_by);
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");	

				$pdf->Ln();

				$abb_user[] = Helpers::user_names(@$refund_value->claim->created_by)." - ".Helpers::getUserFullName(@$refund_value->claim->created_by);
	        	$abb_user = array_unique($abb_user);
	    		foreach (array_keys($abb_user, ' - ') as $key) {
	            	unset($abb_user[$key]);
	        	}
			}
		}
		$abbreviation = ['abb_facility' => @$abb_facility, 'abb_rendering' => @$abb_rendering, 'abb_billing' => @$abb_billing, 'abb_insurance' => @$abb_insurance, 'abb_user' => @$abb_user];
		$abb_controller = New AbbreviationController;
		$abb_controller->abbreviation($abbreviation,$pdf);
	}

	public function SummaryContent($total_refund,$pdf,$var,$title)
    {
    	$x_axis = $pdf->getx();
    	$pdf->SetTextColor(240, 125, 8);
    	$pdf->SetFont('Calibri-Bold','',8);
    	$pdf->Vcell(30,10,$x_axis,"Summary",20,"","L");
    	$pdf->Ln();

 		$x_axis=$pdf->getx();
    	$pdf->SetTextColor(100,100,100);
    	$pdf->SetFont('Calibri-Bold','',7.5);
 		$pdf->Vcell(50,10,$x_axis,$title,30,"TLB","L");
 		$x_axis=$pdf->getx();
    	$pdf->SetFont('Calibri','',7.5);
 		$pdf->Vcell(30,10,$x_axis,'$'.Helpers::priceFormat(@$total_refund->$var),30,"TRB","R");
 		$pdf->Ln();
    }
}

class refund_analysis_mypdf extends FPDF
{
	public function header(){
		$request = Request::all();
        $start_date = $end_date = $billing_provider = $rendering_provider = $facility = $refund_type = $insurance_id = $include = $reference = $user = '';
        $pagination = $insrefunds = $unposted =  $wallet = '';
        // Get refund result
        ### Start Search Fields filering data select ###
        $search_by = array();
        
        if (!empty($request['created_at'])) {
            $createdAt = isset($request['created_at']) ? trim($request['created_at']) : "";
            $date = explode('-', $createdAt);
            $start_date = date("Y-m-d", strtotime(@$date[0]));
            if ($start_date == '1970-01-01') {
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime(@$date[1]));
            $search_by['Transaction Date'][] = date("m/d/y", strtotime(@$start_date)) . ' to ' . date("m/d/y", strtotime(@$end_date));
            $start_date = Helpers::utcTimezoneStartDate($start_date);
            $end_date = Helpers::utcTimezoneEndDate($end_date);
        }
        if(!empty($request['billing_provider_id'])){
            $billing_provider = $request['billing_provider_id'];
            $providers_id = explode(',', $request['billing_provider_id']);
            foreach ($providers_id as $id) {
                $value_name[] = Provider::getProviderFullName($id);
            }
            $search_provider = implode(", ", array_unique($value_name));
            $search_by['Billing Provider'][] = $search_provider;
        }
        if(!empty($request['rendering_provider_id'])){
            $rendering_provider = $request['rendering_provider_id'];
            $renders_id = explode(',', $request['rendering_provider_id']);
            foreach ($renders_id as $id) {
                $value_name[] = Provider::getProviderFullName($id);
            }
            $search_render = implode(", ", array_unique($value_name));
            $search_by['Rendering Provider'][] = $search_render;
        }
        if(!empty($request['facility_id'])){
            $facility = $request['facility_id'];
            if (strpos($request['facility_id'], ',') !== false) {
                $search_name = Facility::select('facility_name');
                $facility_names = $search_name->whereIn('id', explode(',', $request['facility_id']))->get();
                foreach ($facility_names as $name) {
                    $value_names[] = $name['facility_name'];
                }
                $search_filter = implode(", ", array_unique($value_names));
            } else {
                $facility_names = Facility::select('facility_name')->where('id', $request['facility_id'])->get();
                foreach ($facility_names as $facility_na) {
                    $search_filter = $facility_na['facility_name'];
                }
            }
            $search_by['Facility'][] = isset($search_filter) ? $search_filter : [];
        }
        if(!empty($request['refund_type'])){
            $refund_type = $request['refund_type'];
            $search_by['Refund Type'][] = ($request['refund_type'] == 'patient') ? 'Patient Refund' :'Insurance Refund';
        }
        if(!empty($request["insurance_id"]) && $request['refund_type'] == 'insurance' && $request["insurance_id"] != "all"){
            $insurance_id = $request["insurance_id"];
            if (strpos($request['insurance_id'], ',') !== false) {
                $insurance = Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ',  ') as short_name")->whereIn('id', explode(',', $request['insurance_id']))->get()->toArray();
            } else {
                $insurance = Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ', ') as short_name")->where('id', $request['insurance_id'])->get()->toArray();
            }
            $search_by["Payer"][] = @array_flatten($insurance)[0];
        }
        
        if(!empty($request['include'])){
            $include = $request['include'];
            $search_by['Include'][] = $request['include'] ;
        } else {
            $include = '';
        }

        if(!empty($request['reference'])){
            $reference = $request['reference'];
            $search_by['Reference'][] = $request['reference'];
        }
         ## Search By User
        if(!empty($request['user'])){
            $user = $request['user'];
            $User_name =  Users::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')
                         ->pluck('short_name', 'id')->all();
            $User_name = implode(",", array_unique($User_name));
            $search_by['User'][] = $User_name;
        }

        if(!empty($request['include']) && $request['include'] == 'unposted'){
            $unposted ="unposted";
            $wallet = "";
        }
        if(!empty($request['include']) && $request['include'] == 'wallet'){
            $wallet ="wallet";
            $unposted ="";
        }
        $refund_type = $request['refund_type'];

		$this->SetFillColor(255, 255, 255);
		$this->SetTextColor(0, 135, 127);
	    $this->AddFont('Calibri-Bold','','calibrib.php');
	    $this->SetFont('Calibri-Bold','',10.5);
		$x_axis=$this->getx();
		$c_width = 295;
		$c_height = 0;
		$text = Practice::getPracticeName()." - Refund Analysis - Detailed";
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

		if($refund_type == 'insurance'){
			$header = ['Claim No', 'DOS', 'Acc No', 'Patient Name', 'Rendering', 'Billing', 'Facility', 'Insurance', 'Check Date', 'Check No', 'Refund Amt($)','User'];
		} elseif($refund_type == 'patient' && empty($unposted) && empty($wallet)){
			$header = ['Patient Name', 'Acc No', 'Refund Amt($)', 'User'];
		} elseif(!is_null($unposted) && !empty($unposted) || !is_null($wallet) && !empty($wallet)){
			$header = ['Patient Name', 'Check Date', 'Check No', 'Refund Amt($)', 'User'];	
		}
		foreach ($header as $key => $value) {
			$this->SetFont('Calibri-Bold','',8);
			$this->SetTextColor(100, 100, 100);
			$x_axis=$this->getx();// now get current pdf x axis value
			$c_height = 6;// cell height
			$c_width = (400/count($header));// cell width 
			$lengthToSplit = strlen($value);
			if ($value == "Refund Amt($)") {
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
		} else {
		    $this->SetX($x_axis);
		    $this->Cell($c_width,$c_height,$text,$border,0,$align,0);
		}
	}
    
}