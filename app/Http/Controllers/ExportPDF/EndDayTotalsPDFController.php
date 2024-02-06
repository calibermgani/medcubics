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
use App\Models\ReportExport as ReportExport;
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
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


class EndDayTotalsPDFController extends Controller
{
    public function index()
    {
    	set_time_limit(300);
    	try {
	    	$created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
			$filename = 'End_of_the_Day_Totals_'.$created_date.'.pdf';
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
			// $report_export->parameter = $headers;
			$report_export->report_type = $request['export'];
			$report_export->report_file_name = $filename;
			$report_export->report_controller_name = 'FinancialApiController';
			$report_export->report_controller_func = 'getUnbilledClaimApiSP';
			$report_export->status = 'Inprocess';
			$report_export->created_by = $user_id;
			$report_export->save();
			$report_export_id = $report_export->id;

	        $controller = New FinancialApiController;
	        $api_response = $controller->getFilterResultApiSP('pdf');
	        $api_response_data = $api_response->getData();
	        $result = $api_response_data->data->export_array;

	        // Parameters for filter
	        $header = (array)$api_response_data->data->search_by;
	        $text = $headers = [];
			$i = 0;
	        if(!empty($header)) {
	            foreach ((array)$header as $key => $val) {
	                $text[] = $key."=".(is_array($val)? $val[0] : $val);
	                $i++; 
	            }
	        }    
	        $headers = implode('&', $text);
	        
	        $pdf = new end_day_mypdf("L","mm","A4");
			$pdf->SetMargins(2,6);
			$pdf->SetTextColor(100,100,100);
			$pdf->SetDrawColor(217, 217, 217);
			$pdf->AddPage();
			$pdf->AddFont('Calibri','','calibri.php');
			$pdf->SetFont('Calibri','',7);
			$grand_total = '';
	        self::BladeContent($result, $pdf);
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
	    	\Log::info("Error Occured While export End Day Totals report. Message:".$e->getMessage() );
	    }
		exit();
	}

	public function BladeContent($result, $pdf){

		$total_adj = 0;
        $patient_total = 0;
        $insurance_total = 0;
		foreach ($result as $key => $dates) {
			$insurance_payment[] = isset($dates->insurance_payment) ? $dates->insurance_payment : 0;
            $writeoff_total[] = isset($dates->writeoff_total) ? $dates->writeoff_total : 0;
            $patient_payment[] = isset($dates->patient_payment) ? $dates->patient_payment : 0;
            $patient_adjustment[] = isset($dates->patient_adjustment) ? $dates->patient_adjustment : 0;
            $insurance_adjustment[] = isset($dates->insurance_adjustment) ? $dates->insurance_adjustment : 0;

			$pdf->AddFont('Calibri','','calibri.php');
		    $pdf->SetFont('Calibri','',7);
			$pdf->SetTextColor(100, 100, 100);
			$c_width=(290/11);
			$c_height=5;// cell height

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = $key.'-'.date('D', strtotime($key));
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width-3,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$dates->total_charge, '',1);
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width-4,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
			
			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = (isset($dates->claims_count) && @$dates->claims_count != '') ? @$dates->claims_count : "0";
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width-7,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$dates->writeoff_total,'',1);
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width+1,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$dates->insurance_adjustment,'',1);
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width+1,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$dates->patient_adjustment,'',1);
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width+1,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$dates->insurance_refund,'',1);
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width+2,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$dates->patient_refund,'',1);
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width+1,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$dates->insurance_payment,'',1);
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width+2,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$dates->patient_payment,'',1);
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width+2,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$dates->total_payment,'',1);
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width+5,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
			$pdf->Ln();
		}
	}
}

class end_day_mypdf extends FPDF
{
	public function header(){

		$request = Request::All();
    	// dd($request);
        $search_by = [];
        $start_date = $end_date = $billing_provider = $rendering_provider = $facility = $user_ids = '';
        if (!empty($request['created_at'])) {
            $date = explode('-', $request['created_at']);
            $start_date = Helpers::utcTimezoneStartDate($date[0]);
            if ($start_date == '1970-01-01') {
                $start_date = '0000-00-00';
            }
            $end_date = Helpers::utcTimezoneStartDate($date[1]);
        } else {
            $start_date = date('Y-m-01'); // hard-coded '01' for first day
            $end_date = date('Y-m-d');
            $start_date = Helpers::utcTimezoneStartDate($start_date);
            $end_date = Helpers::utcTimezoneStartDate($end_date);
        }
        $search_by['Transaction Date'][] = date("m/d/y", strtotime($start_date)) . ' to ' . date("m/d/y", strtotime($end_date));

        if (isset($request['facility']) && $request['facility'] != '') {
            $facility = $request['facility'];
            if (strpos($request['facility'], ',') !== false) {
                $search_name = Facility::select('facility_name');
                $facility_names = $search_name->whereIn('id', explode(',', $request['facility']))->get();
                foreach ($facility_names as $name) {
                    $value_names[] = $name['facility_name'];
                }
                $search_filter = implode(", ", array_unique($value_names));
            } else {
                $facility_names = Facility::select('facility_name')->where('id', $request['facility'])->get();
                foreach ($facility_names as $facility_na) {
                    $search_filter = $facility_na['facility_name'];
                }
            }
            $search_by['Facility'][] = isset($search_filter) ? $search_filter : [];
        }

        if (!empty($request['rendering_provider'])) {
            $rendering_provider = $request['rendering_provider'];
            $renders_id = explode(',', $request['rendering_provider']);
            foreach ($renders_id as $id) {
                $value_name[] = Provider::getProviderFullName($id);
            }
            $search_render = implode(", ", array_unique($value_name));
            $search_by['Rendering Provider'][] = $search_render;
        }

        if (!empty($request['billing_provider'])) {
            $billing_provider = $request['billing_provider'];            
            $providers_id = explode(',', $request['billing_provider']);
            foreach ($providers_id as $id) {
                $value_name[] = Provider::getProviderFullName($id);
            }
            $search_provider = implode(", ", array_unique($value_name));
            $search_by['Billing Provider'][] = $search_provider;
        }
        
        if (isset($request['user']) && !empty($request['user'])) {
            $req_user = explode(',', $request['user']);
            $user_ids = $request['user'];
            foreach ($req_user as $key => $value) {
                $short_name[] = DB::connection('responsive')->table('users')
                    ->whereIn('id',explode(',', $value))
                    ->pluck('short_name')->first();
            }
            $search_by["User"][] = implode(',',$short_name);
        }

		$this->SetFillColor(255, 255, 255);
		$this->SetTextColor(0, 135, 127);
	    $this->AddFont('Calibri-Bold','','calibrib.php');
	    $this->SetFont('Calibri-Bold','',10.5);
		$x_axis=$this->getx();
		$c_width = 295;
		$c_height = 0;
		$text = Practice::getPracticeName()." - End of the Day Totals";
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
		$this->Headers();
	}

	public function Headers(){
		$this->SetFont('Calibri-Bold','',8);
		$this->SetTextColor(100, 100, 100);

		$this->SetY(33);
	    $this->SetX(2);
	    $this->MultiCell(290,12,"" ,'TBL', "L");
	    $this->SetY(39);
	    $this->SetX(3);
	    $this->MultiCell(0, 0.5,"Date-Day" ,'', "L");
	    $this->SetY(33);
	    $this->SetX(23);
	    $this->MultiCell(0,12,"" ,'L', "L");
	    $this->SetY(39);
	    $this->SetX(25);
	    $this->MultiCell(0, 0.5,"Charges($)" ,'', "L");
	    $this->SetY(33);
	    $this->SetX(48);
	    $this->MultiCell(0,12,"" ,'L', "L");
	    $this->SetY(39);
	    $this->SetX(50);
	    $this->MultiCell(0, 0.5,"Claims" ,'', "L");
	    $this->SetY(33);
	    $this->SetX(73);
	    $this->MultiCell(0,12,"" ,'L', "L");
	    $this->SetY(39);
	    $this->SetX(75);
	    $this->MultiCell(0, 0.5,"Writeoff($)" ,'', "L");
	    $this->SetY(38);
	    $this->SetX(94);
	    $this->MultiCell(168,1,"" ,'B', "L");
	    $this->SetY(36);
	    $this->SetX(110);
	    $this->MultiCell(0, 0.5,"Adjustments($)" ,'', "L");
	    $this->SetY(33);
	    $this->SetX(94);
	    $this->MultiCell(0,12,"" ,'L', "L");
	    $this->SetY(42);
	    $this->SetX(96);
	    $this->MultiCell(0, 0.5,"Insurance" ,'', "L");
	    $this->SetY(39);
	    $this->SetX(122);
	    $this->MultiCell(0,6,"" ,'L', "L");
	    $this->SetY(42);
	    $this->SetX(124);
	    $this->MultiCell(0, 0.5,"Patients" ,'', "L");
	    $this->SetY(36);
	    $this->SetX(166);
	    $this->MultiCell(0, 0.5,"Refund($)" ,'', "L");
	    $this->SetY(33);
	    $this->SetX(150);
	    $this->MultiCell(0,12,"" ,'L', "L");
	    $this->SetY(42);
	    $this->SetX(152);
	    $this->MultiCell(0, 0.5,"Insurance" ,'', "L");
	    $this->SetY(39);
	    $this->SetX(178);
	    $this->MultiCell(0,6,"" ,'L', "L");
	    $this->SetY(42);
	    $this->SetX(180);
	    $this->MultiCell(0, 0.5,"Patients" ,'', "L");
	    $this->SetY(36);
	    $this->SetX(222);
	    $this->MultiCell(0, 0.5,"Payments($)" ,'', "L");
	    $this->SetY(33);
	    $this->SetX(206);
	    $this->MultiCell(0,12,"" ,'L', "L");
	    $this->SetY(42);
	    $this->SetX(208);
	    $this->MultiCell(0, 0.5,"Insurance" ,'', "L");
	    $this->SetY(39);
	    $this->SetX(234);
	    $this->MultiCell(0,6,"" ,'L', "L");
	    $this->SetY(42);
	    $this->SetX(236);
	    $this->MultiCell(0, 0.5,"Patients" ,'', "L");

	    $this->SetY(33);
	    $this->SetX(262);
	    $this->MultiCell(0,12,"" ,'L', "L");
	    $this->SetY(36);
	    $this->SetX(262);
	    $this->MultiCell(0, 0.5,"Total" ,'', "L");
	    $this->SetY(39);
	    $this->SetX(262);
	    $this->MultiCell(0, 0.5,"Payments($)" ,'', "L");
	    $this->SetY(33);
	    $this->SetX(292);
	    $this->MultiCell(0,12,"" ,'L', "L");
	    
	    // $this->SetY(33);
	    // $this->SetX(253+26-12-5);
	    // $this->MultiCell(10,12,"" ,'L', "L");
	    // $this->SetY(39);
	    // $this->SetX(255+26-6);
	    // $this->MultiCell(0, 0.5,"Total Payments($)" ,'', "L");
	    // $this->SetY(33);
	    // $this->SetX(292+26-6);
	    // $this->MultiCell(0,12,"" ,'L', "L");

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
