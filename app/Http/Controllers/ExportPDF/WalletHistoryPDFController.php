<?php

namespace App\Http\Controllers\ExportPDF;

use DB;
use Carbon;
use Request;
use Auth;
use FPDF;
use Session;
use App\Models\Icd as Icd;
use App\Http\Controllers\Controller;
use App\Models\Medcubics\Cpt as Cpt;
use App\Models\Practice as Practice;
use App\Models\Provider as Provider;
use App\Models\Facility as Facility;
use App\Models\Insurance as Insurance;
use App\Models\Medcubics\Users as Users;
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
use App\Models\ReportExport as ReportExport;
use App\Http\Controllers\Reports\Api\ReportApiController as ReportApiController;
use App\Http\Controllers\ExportPDF\AbbreviationController as AbbreviationController;
use App\Http\Controllers\Reports\Financials\Api\FinancialApiController as FinancialApiController;
use App\Http\Controllers\Reports\ReportController as ReportController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;

$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);

class WalletHistoryPDFController extends Controller
{
    public function index()
    {
    	set_time_limit(300);
    	try {
	    	$created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
			$filename = 'Wallet_History_Detailed_'.$created_date.'.pdf';
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
			$report_export->report_controller_func = 'getPatientWalletHistoryFilterApiSP';
			$report_export->status = 'Inprocess';
			$report_export->created_by = $user_id;
			$report_export->save();
			$report_export_id = $report_export->id;

	        $controller = New ReportApiController;
	        $api_response = $controller->getPatientWalletHistoryFilterApiSP('pdf');
	        $api_response_data = $api_response->getData();
	        $patient_wallethistory_filter = $api_response_data->data->filter_result;

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
	        
	        $pdf = new wallet_history_mypdf("L","mm","A4");
			$pdf->SetMargins(2,6);
			$pdf->SetTextColor(100,100,100);
			$pdf->SetDrawColor(217, 217, 217);
			$pdf->AddPage();
			$pdf->AddFont('Calibri','','calibri.php');
			$pdf->SetFont('Calibri','',7);
			$grand_total = '';
	        self::BladeContent($patient_wallethistory_filter, $pdf);
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
	    	\Log::info("Error Occured While export Wallet History report. Message:".$e->getMessage() );
	    }
		exit();
	}

	public function BladeContent($patient_wallethistory_filter, $pdf){
		foreach($patient_wallethistory_filter as $list){
			$patient_name = Helpers::getNameformat(@$list->last_name, @$list->first_name, @$list->middle_name);
			$bal_amt = @$list->pmt_amt - @$list->amt_used;

			$pdf->AddFont('Calibri','','calibri.php');
		    $pdf->SetFont('Calibri','',7);
			$pdf->SetTextColor(100, 100, 100);
			$c_width=(290/8);
			$c_height=5;// cell height

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($list->account_no)? @$list->account_no : '-Nill-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($list->patient_name)? @$list->patient_name : '-Nill-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($list->pmt_no)? @$list->pmt_no : '-Nill-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($list->pmt_mode)? @$list->pmt_mode : '-Nill-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($list->created_at)? Helpers::timezone(@$list->created_at, 'm/d/y') : '-Nill-' ;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$list->pmt_amt,'',1);
			$lengthToSplit = strlen($text);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
			$pdf->SetTextColor(100, 100, 100);	

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$list->amt_used,'',1);
			$lengthToSplit = strlen($text);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
			$pdf->SetTextColor(100, 100, 100);	

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$bal_amt,'',1);
			$lengthToSplit = strlen($text);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
			$pdf->SetTextColor(100, 100, 100);	

			$pdf->Ln();
		}
	}
}

class wallet_history_mypdf extends FPDF
{
	public function header(){

		$request = Request::All();
    	$search_by = array();
       if(!empty($request['created_at'])) {
            $date = explode('-',$request['created_at']);
            $start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));
        } else {
            $start_date = date('Y-m-01'); // hard-coded '01' for first day
            $end_date  = date('Y-m-t');
        }

        $search_by['Transaction Date'][]= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));
        $start_date = Helpers::utcTimezoneStartDate($start_date);
        $end_date = Helpers::utcTimezoneEndDate($end_date);
        if(!empty($request['pmt_mode'])){
            $search_by['Payment Mode'][] =  $request['pmt_mode'];
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
		$c_width = 295;
		$c_height = 0;
		$text = Practice::getPracticeName()." - Wallet History - Detailed";
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
		// $text_imp = 'Transaction Date : 01/01/19 to 12/02/19';
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

		$header = ['Acc No', 'Patient Name', 'Payment ID', 'Mode of Pmt', 'Payment Date', 'Total Payment($)', 'Posted($)', 'UnPosted($)'];

		foreach ($header as $key => $value) {
			$this->SetFont('Calibri-Bold','',8);
			$this->SetTextColor(100, 100, 100);
			$x_axis=$this->getx();// now get current pdf x axis value
			$c_height = 6;// cell height
			$c_width = (290/count($header));// cell width 
			$lengthToSplit = strlen($value);
			if ($value == "Total Payment($)" || $value == "Posted($)" || $value == "UnPosted($)") {
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