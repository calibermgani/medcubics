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
use App\Http\Controllers\ExportPDF\AbbreviationController as AbbreviationController;
use App\Http\Controllers\Reports\Financials\Api\FinancialApiController as FinancialApiController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;

$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);

class ArWorkbenchReportPDFController extends Controller
{
    public function index()
    {
    	try {
	        $request = Request::All();
	        $controller = New FinancialApiController;
	        $api_response = $controller->getWorkbenchListApi('pdf');
	        $api_response_data = $api_response->getData();
	        $workbench_list = $api_response_data->data->workbench_list;

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
	        $created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
			$filename = 'AR_Workbench_Report_'.$created_date.'.pdf';
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
			$report_export->report_file_name = $filename;
			$report_export->report_type = $request['export'];
			$report_export->parameter = $headers;
			$report_export->report_controller_name = 'FinancialApiController';
			$report_export->report_controller_func = 'getWorkbenchListApi';
			$report_export->status = 'Inprocess';
			$report_export->created_by = $user_id;
			$report_export->save();
			$report_export_id = $report_export->id;


	        $pdf = new ar_workbench_mypdf("L","mm",array(350,100));
			$pdf->SetMargins(2,6);
			$pdf->SetTextColor(100,100,100);
			$pdf->SetDrawColor(217, 217, 217);
			$pdf->AddPage();
			$pdf->AddFont('Calibri','','calibri.php');
			$pdf->SetFont('Calibri','',7);
			$grand_total = '';
	        self::BladeContent($workbench_list, $pdf);
	        $created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y');
			$created_date = str_replace('/', '-', $created_date);
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
	    	\Log::info("Error Occured While export AR Workbench report. Message:".$e->getMessage() );
	    }
		exit();
	}

	public function BladeContent($workbench_list, $pdf){
		foreach ($workbench_list as $key => $result) {
			$pdf->AddFont('Calibri','','calibri.php');
		    $pdf->SetFont('Calibri','',7);
			$pdf->SetTextColor(100, 100, 100);
			$c_width=(350/20);
			$c_height=5;// cell height

			if(isset($result->claim_number) && $result->claim_number != ''){

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$result->claim_number;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$result->dos;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$result->patient_name;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$result->rendering_provider_short_name;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");


				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$result->billing_provider_short_name;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$result->facility_short_name;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$result->responsibility;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$result->insurance_category;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat(@$result->total_charge,'',1);
				if ($text < 0) {
						$pdf->SetTextColor(255, 0, 0);
					}
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
				$pdf->SetTextColor(100, 100, 100);

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat(@$result->tot_paid,'',1);
				if ($text < 0) {
						$pdf->SetTextColor(255, 0, 0);
					}
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
				$pdf->SetTextColor(100, 100, 100);

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat(@$result->tot_adj,'',1);
				if ($text < 0) {
						$pdf->SetTextColor(255, 0, 0);
					}
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
				$pdf->SetTextColor(100, 100, 100);

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat(@$result->pat_due,'',1);
				if ($text < 0) {
						$pdf->SetTextColor(255, 0, 0);
					}
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
				$pdf->SetTextColor(100, 100, 100);

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat(@$result->ins_due,'',1);
				if ($text < 0) {
						$pdf->SetTextColor(255, 0, 0);
					}
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
				$pdf->SetTextColor(100, 100, 100);

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat(@$result->ar_due,'',1);
				if ($text < 0) {
						$pdf->SetTextColor(255, 0, 0);
					}
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
				$pdf->SetTextColor(100, 100, 100);

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$result->claim_age_days;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$result->claim_status;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				if(isset($result->sub_status_desc) && $result->sub_status_desc !== null){
					$text = @$result->sub_status_desc;
				}else{
					$text = '-Nil-';
				}
				$lengthToSplit = 10;
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$result->workbench_status;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				if(date("m/d/y") == $result->fllowup_date){
					$pdf->SetTextColor(240, 125, 8);
				}
				elseif(date("m/d/y") >= $result->fllowup_date){
					$pdf->SetTextColor(255, 0, 0);
				}
				else{
					$pdf->SetTextColor(187, 194, 211);
				}
				$text = date("m/d/y", strtotime(@$result->fllowup_date));
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
				$pdf->SetTextColor(100, 100, 100);

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::shortname($result->assign_user_id);
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			}
			else{
				$last_name = @$result->patient->last_name;
                $first_name = @$result->patient->first_name;
                $middle_name = @$result->patient->middle_name;
                $patient_name =Helpers::getNameformat($last_name, $first_name, $middle_name);
                $fin_details = @$result->claim->pmt_claim_fin_data;
                $pat_due = ($result->claim->insurance_id == 0)?@$fin_details->total_charge-(@$fin_details->patient_paid + @$fin_details->patient_adj+ @$fin_details->insurance_paid+ @$fin_details->insurance_adj+ @$fin_details->withheld):0;
                $ins_due = ($result->claim->insurance_id != 0) ? @$fin_details->total_charge-(@$fin_details->patient_paid+ @$fin_details->patient_adj+ @$fin_details->insurance_paid+ @$fin_details->insurance_adj+ @$fin_details->withheld):0;
                $tot_adj = @$fin_details->patient_adj + @$fin_details->insurance_adj+ @$fin_details->withheld;
                $tot_paid = @$fin_details->patient_paid + @$fin_details->insurance_paid;
                $ar_due = @$fin_details->total_charge-(@$fin_details->patient_paid+@$fin_details->patient_adj + @$fin_details->insurance_paid+ @$fin_details->insurance_adj+ @$fin_details->withheld);
                $fllowup_date = date("m/d/y", strtotime(@$result->fllowup_date));
                $fllowup_date = date("m/d/y", strtotime(@$result->fllowup_date));
                $responsibility = 'Patient';
                $ins_category = 'Patient';
                if($result->claim->insurance_details){
                    $responsibility =Helpers::getInsuranceName(@$result->claim->insurance_details->id);
                    $ins_category= @$result->insurance_category;	 
                }

                $x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$result->claim->claim_number;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::dateFormat(@$result->claim->date_of_service,'claimdate');
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = $patient_name;
				$lengthToSplit = 15;
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				if(@$result->claim->rendering_provider->short_name !=''){
					$text = str_limit(@$result->claim->rendering_provider->short_name,25,'...');
				}else{
					$text = '-Nil-';
				}
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				if(@$result->claim->billing_provider->short_name != ''){
					$text = str_limit(@$result->claim->billing_provider->short_name,25,'...');
				}else{
					$text = '-Nil-';
				}
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				if(@$result->claim->facility_detail->short_name != ''){
					$text = str_limit(@$result->claim->facility_detail->short_name,25,'...');
				}else{
					$text = '-Nil-';
				}
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = $responsibility;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = $ins_category;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat(@$result->claim->total_charge,'',1);
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat(@$tot_paid,'',1);
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat(@$tot_adj,'',1);
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat(@$pat_due,'',1);
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat(@$ins_due,'',1);
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat(@$ar_due,'',1);
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$result->claim->claim_age_days;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$result->claim->status;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				if(isset($result->sub_status_desc) && $result->sub_status_desc !== null){
					$text = $result->sub_status_desc;
				}
				else{
					$text = '-Nil-';
				}
				$lengthToSplit = 10;
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$result->status;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				if(date("m/d/y") == $fllowup_date){
					$pdf->SetTextColor(240, 125, 8);
				}
				elseif(date("m/d/y") >= $fllowup_date){
					$pdf->SetTextColor(255, 0, 0);
				}
				else{
					$pdf->SetTextColor(187, 194, 211);
				}
				$text = date("m/d/y", strtotime(@$result->fllowup_date));
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
				$pdf->SetTextColor(100, 100, 100);

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::shortname($result->assign_user_id);
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
			}
			$pdf->Ln();
			$abb_facility[] = @$result->claim->facility_detail->short_name." - ".@$result->claim->facility_detail->facility_name;
	    	$abb_facility = array_unique($abb_facility);
			foreach (array_keys($abb_facility, ' - ') as $key) {
	        	unset($abb_facility[$key]);
	    	}

	    	$abb_billing[] = @$result->claim->billing_provider->short_name." - ".@$result->claim->billing_provider->provider_name;
	    	$abb_billing = array_unique($abb_billing);
			foreach (array_keys($abb_billing, ' - ') as $key) {
	        	unset($abb_billing[$key]);
	    	}

	    	$abb_user[] = Helpers::user_names(@$result->assign_user_id)." - ".Helpers::getUserFullName(@$result->assign_user_id);
	    	$abb_user = array_unique($abb_user);
			foreach (array_keys($abb_user, ' - ') as $key) {
	        	unset($abb_user[$key]);
	    	}

	    	$abb_rendering[] = @$result->claim->rendering_provider->short_name." - ".@$result->claim->rendering_provider->provider_name;
	    	$abb_rendering = array_unique($abb_rendering);
			foreach (array_keys($abb_rendering, ' - ') as $key) {
	        	unset($abb_rendering[$key]);
	    	}
		}
		$abbreviation = ['abb_facility' => $abb_facility, 'abb_rendering' => $abb_rendering, 'abb_billing' => $abb_billing, 'abb_user' => $abb_user];
		$abb_controller = New AbbreviationController;
		$abb_controller->abbreviation($abbreviation,$pdf);
	}
}

class ar_workbench_mypdf extends FPDF
{
	public function header(){

		$request = Request::All();
    	// dd($request);
        $controller = New FinancialApiController;
        $api_response = $controller->getWorkbenchListApiSP('pdf');
        $api_response_data = $api_response->getData();
        $search_by = $api_response_data->data->search_by;

		$this->SetFillColor(255, 255, 255);
		$this->SetTextColor(0, 135, 127);
	    $this->AddFont('Calibri-Bold','','calibrib.php');
	    $this->SetFont('Calibri-Bold','',10.5);
		$x_axis=$this->getx();
		$c_width = 350;
		$c_height = 0;
		$text = Practice::getPracticeName()." - AR Workbench Report";
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
		$this->Vcell(350,12,$x_axis,$text_imp,160,"","C");

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

		$header = ['Claim No', 'DOS', 'Patient Name', 'Rendering Provider', 'Billing Provider', 'Facility', 'Responsibility', 'Category', 'Charge Amt($)', 'Paid($)','Adj($)','Pat  AR($)','Ins  AR($)','AR    Due($)','Claim Age','Claim Status','Sub Status' ,'Workbench Status','Followup Date','Assigned To'];

		foreach ($header as $key => $value) {
			$this->SetFont('Calibri-Bold','',8);
			$this->SetTextColor(100, 100, 100);
			$x_axis=$this->getx();// now get current pdf x axis value
			$c_height = 6;// cell height
			$c_width = (350/count($header));// cell width 
			$lengthToSplit = strlen($value);
			$align = "L";
			if ($value == "Claim No") {
				$c_width = $c_width;
			}if ($value == "Patient Name") {
				$c_width = $c_width;	
			}if ($value == 'Rendering Provider') {
				$c_width = $c_width;
				$lengthToSplit = 10;	
			}if ($value == 'Billing Provider') {
				$lengthToSplit = 8;
				$c_width = $c_width;	
			}if ($value == "Facility") {
				$c_width = $c_width;
			}if ($value == "Responsibility") {
				$c_width = $c_width;
			}if ($value == 'Charge Amt($)') {
				$align = "R";
			}if ($value == "Paid($)") {
				$align = "R";
			}if ($value == "Adj($)") {
				$align = "R";
			}if ($value == 'Pat  AR($)') {
				$align = "R";
			}if ($value == 'Ins  AR($)') {
				$align = "R";
			}if ($value == 'AR    Due($)') {
				$align = "R";
			}
			if ($value == 'Workbench Status') {
				$lengthToSplit = 10;	
			}if ($value=='Followup Date') {
				$lengthToSplit = 9;	
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