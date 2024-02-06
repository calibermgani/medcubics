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
use App\Http\Controllers\Reports\Api\ReportApiController as ReportApiController;
use App\Http\Controllers\ExportPDF\AbbreviationController as AbbreviationController;
use App\Http\Controllers\Reports\ReportController as ReportController;
use App\Http\Controllers\Reports\Practicesettings\ProviderlistController as ProviderlistController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;

$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);

class ProviderSummaryPDFController extends Controller
{
	protected $header;

	public function index()
    {
        try{
    	set_time_limit(300);
        $created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
        $filename = 'Provider_Summary_'.$created_date.'.pdf';
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
        $report_export->report_file_name = 'Downloads/Provider_Summary_'.$created_date.'.pdf';
        $report_export->report_controller_name = 'ProviderlistController';
        $report_export->report_controller_func = 'getProviderFilterResultApi';
        $report_export->status = 'Inprocess';
        $report_export->created_by = $user_id;
        $report_export->save();
        $report_export_id = $report_export->id; 

        $controller = New ProviderlistController;
        $api_response = $controller->getProviderFilterResultApi('summary');
        $api_response_data = $api_response->getData();

        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $practiceopt = $api_response_data->data->practiceopt;
        $header = $api_response_data->data->header;
        $providers = $api_response_data->data->providers;
        $charges = $api_response_data->data->charges;
        $writeoff = $api_response_data->data->writeoff;
        $pat_adj = $api_response_data->data->pat_adj;
        $ins_adj = $api_response_data->data->ins_adj;
        $patient = $api_response_data->data->patient;
        $insurance = $api_response_data->data->insurance;
        $patient_bal = $api_response_data->data->patient_bal;
        $insurance_bal = $api_response_data->data->insurance_bal;
        $units = $api_response_data->data->units;

        $pdf = new Provider_Summary_mypdf("L","mm","A4");
		$pdf->SetMargins(2,6);
		$pdf->SetTextColor(100,100,100);
		$pdf->SetDrawColor(217, 217, 217);
		$pdf->AddPage();
		$pdf->AddFont('Calibri','','calibri.php');
		$pdf->SetFont('Calibri','',7);
        self::BladeContent($practiceopt, $pdf, $providers, $charges, $patient, $insurance, $patient_bal, $insurance_bal, $units, $writeoff, $pat_adj, $ins_adj);
        self::SummaryContent($pdf, $patient, $units, $charges, $writeoff, $pat_adj, $ins_adj, $insurance, $patient_bal, $insurance_bal);
        // $pdf->Output($filename,'F');

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

	public function BladeContent($practiceopt, $pdf, $providers, $charges, $patient, $insurance, $patient_bal, $insurance_bal, $units, $writeoff, $pat_adj, $ins_adj){
		$pdf->SetFillColor(255, 255, 255);
        $pdf->AddFont('Calibri','','calibrib.php');
        $pdf->SetFont('Calibri','',7.5);
        $c_width = 295/count($pdf->header);
        $c_height = 5;

        $req = @$practiceopt;

        if($req == "provider_list"){
        	if(count($filter_group_list) > 0) {
        		$total_adj = 0;
				$patient_total = 0;
				$insurance_total = 0;

				foreach($filter_group_list as $list){
					$x_axis = $pdf->getx();
	                $text = @$list->provider_name;
	                $lengthToSplit = strlen($text);
	                $pdf->Vcell($c_width-1,$c_height,$x_axis,$text,$lengthToSplit,"","L");

	                $x_axis = $pdf->getx();
	                $text = @$list->provider_types->name;
	                $lengthToSplit = strlen($text);
	                $pdf->Vcell($c_width-1,$c_height,$x_axis,$text,$lengthToSplit,"","L");

	                $x_axis = $pdf->getx();
	                $text = date('m/d/y',strtotime(@$list->created_at));
	                $lengthToSplit = strlen($text);
	                $pdf->Vcell($c_width-1,$c_height,$x_axis,$text,$lengthToSplit,"","L");

	                $x_axis = $pdf->getx();
	                $text = @$list->provider_user_details->short_name;
	                $lengthToSplit = strlen($text);
	                $pdf->Vcell($c_width-1,$c_height,$x_axis,$text,$lengthToSplit,"","L");
	                $pdf->Ln();

			    	}
				}
        	}
      	else{
	        if(!empty($providers) && count($providers)> 0){
	        	foreach($providers as $list){
	        		$x_axis = $pdf->getx();
	                $text = @$list->short_name;
	                $lengthToSplit = strlen($text);
	                $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","L");

	                $name = $list->provider_name;
					$prID = $list->id;
					$charge = isset($charges->$prID) ? $charges->$prID : 0;
					$unit = isset($units->$prID) ? $units->$prID : 0;
					$wo = isset($writeoff->$prID) ? $writeoff->$prID : 0;
					$patient_adj = isset($pat_adj->$prID) ? $pat_adj->$prID : 0;
					$insurance_adj = isset($ins_adj->$prID) ? $ins_adj->$prID : 0;
					$pat_pmt = isset($patient->$prID) ? $patient->$prID : 0;
					$ins_pmt = isset($insurance->$prID) ? $insurance->$prID : 0;
					$pat_bal = isset($patient_bal->$prID) ? $patient_bal->$prID : 0;
					$ins_bal = isset($insurance_bal->$prID) ? $insurance_bal->$prID : 0;

					$x_axis = $pdf->getx();
	                $text = $unit;
	                $lengthToSplit = strlen($text);
	                $pdf->Vcell($c_width-2,$c_height,$x_axis,$text,$lengthToSplit,"","L");

	                $x_axis = $pdf->getx();
	                $text = Helpers::priceFormat($charge,'',1);
	                $lengthToSplit = strlen($text);
	                $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");

	                $x_axis = $pdf->getx();
	                $text = Helpers::priceFormat($wo,'',1);
	                $lengthToSplit = strlen($text);
	                $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");

	                $x_axis = $pdf->getx();
	                $text = Helpers::priceFormat($patient_adj,'',1);
	                $lengthToSplit = strlen($text);
	                $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");

	                $x_axis = $pdf->getx();
	                $text = Helpers::priceFormat($insurance_adj,'',1);
	                $lengthToSplit = strlen($text);
	                $pdf->Vcell($c_width-1,$c_height,$x_axis,$text,$lengthToSplit,"","R");

	                $x_axis = $pdf->getx();
	                $text = Helpers::priceFormat($wo+$patient_adj+$insurance_adj,'',1);
	                $lengthToSplit = strlen($text);
	                $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");

	                $x_axis = $pdf->getx();
	                $text = Helpers::priceFormat($pat_pmt,'',1);
	                $lengthToSplit = strlen($text);
	                $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");

	                $x_axis = $pdf->getx();
	                $text = Helpers::priceFormat($ins_pmt,'',1);
	                $lengthToSplit = strlen($text);
	                $pdf->Vcell($c_width-1,$c_height,$x_axis,$text,$lengthToSplit,"","R");

	                $x_axis = $pdf->getx();
	                $text = Helpers::priceFormat($pat_pmt+$ins_pmt,'',1);
	                $lengthToSplit = strlen($text);
	                $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");

	                $x_axis = $pdf->getx();
	                $text = Helpers::priceFormat($pat_bal,'',1);
	                $lengthToSplit = strlen($text);
	                $pdf->Vcell($c_width-1,$c_height,$x_axis,$text,$lengthToSplit,"","R");

	                $x_axis = $pdf->getx();
	                $text = Helpers::priceFormat($ins_bal,'',1);
	                $lengthToSplit = strlen($text);
	                $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");

	                $x_axis = $pdf->getx();
	                $text = Helpers::priceFormat($pat_bal+$ins_bal,'',1);
	                $lengthToSplit = strlen($text);
	                $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");

	                $pdf->Ln();

			    	if($pdf->header[0]=="Billing"){
				    	$abb_billing[] = @$list->short_name." - ".$list->provider_name;
				    	$abb_billing = array_unique($abb_billing);
						foreach (array_keys($abb_billing, ' - ') as $key) {
				        	unset($abb_billing[$key]);
				    	}
			    	}else{
		                $abb_rendering[] = $list->short_name." - ".$list->provider_name;
				    	$abb_rendering = array_unique($abb_rendering);
						foreach (array_keys($abb_rendering, ' - ') as $key) {
				        	unset($abb_rendering[$key]);
				    	}
			    	}

	        	}
		        $pdf->Ln();
				$abbreviation = ['abb_rendering' => @$abb_rendering, 'abb_billing' => @$abb_billing];
				$abb_controller = New AbbreviationController;
				$abb_controller->abbreviation($abbreviation,$pdf);
      		}
        }

	}

    public function SummaryContent($pdf, $patient, $units, $charges, $writeoff, $pat_adj, $ins_adj, $insurance, $patient_bal, $insurance_bal){

        $wallet = isset($patient->wallet) ? $patient->wallet : 0;
        if ($wallet < 0)
            $wallet = 0;

        $x_axis = $pdf->getx();
        $pdf->SetTextColor(240, 125, 8);
        $pdf->SetFont('Calibri-Bold','',8);
        $pdf->Vcell(30,10,$x_axis,"Summary",20,"","L");
        $pdf->Ln();
        $c_height = 5;
        $c_width = 40;
        $lengthToSplit = 30;

        $x_axis=$pdf->getx();
        $pdf->SetTextColor(100,100,100);
        $pdf->SetFont('Calibri-Bold','',7.5);
        $pdf->Vcell($c_width,$c_height,$x_axis,"Wallet Balance",$lengthToSplit,"TL","L");
        $x_axis=$pdf->getx();
        $pdf->SetFont('Calibri','',7.5);
        $pdf->Vcell($c_width,$c_height,$x_axis,'$'.Helpers::priceFormat($wallet,'',1),$lengthToSplit,"TR","R");
        $pdf->Ln();

        $x_axis=$pdf->getx();
        $pdf->SetTextColor(100,100,100);
        $pdf->SetFont('Calibri-Bold','',7.5);
        $pdf->Vcell($c_width,$c_height,$x_axis,"Total Units",$lengthToSplit,"L","L");
        $x_axis=$pdf->getx();
        $pdf->SetFont('Calibri','',7.5);
        $pdf->Vcell($c_width,$c_height,$x_axis,array_sum((array)$units),$lengthToSplit,"R","R");
        $pdf->Ln();

        $x_axis=$pdf->getx();
        $pdf->SetTextColor(100,100,100);
        $pdf->SetFont('Calibri-Bold','',7.5);
        $pdf->Vcell($c_width,$c_height,$x_axis,"Total Charges",$lengthToSplit,"L","L");
        $x_axis=$pdf->getx();
        $pdf->SetFont('Calibri','',7.5);
        $pdf->Vcell($c_width,$c_height,$x_axis,'$'.Helpers::priceFormat(array_sum((array)$charges)),$lengthToSplit,"R","R");
        $pdf->Ln();

        $x_axis=$pdf->getx();
        $pdf->SetTextColor(100,100,100);
        $pdf->SetFont('Calibri-Bold','',7.5);
        $pdf->Vcell($c_width,$c_height,$x_axis,"Total Adjustments",$lengthToSplit,"L","L");
        $x_axis=$pdf->getx();
        $pdf->SetFont('Calibri','',7.5);
        $pdf->Vcell($c_width,$c_height,$x_axis,'$'.Helpers::priceFormat(array_sum((array)$writeoff)+array_sum((array)$pat_adj)+array_sum((array)$ins_adj)),$lengthToSplit,"R","R");
        $pdf->Ln();

        $x_axis=$pdf->getx();
        $pdf->SetTextColor(100,100,100);
        $pdf->SetFont('Calibri-Bold','',7.5);
        $pdf->Vcell($c_width,$c_height,$x_axis,"Total Payments",$lengthToSplit,"L","L");
        $x_axis=$pdf->getx();
        $pdf->SetFont('Calibri','',7.5);
        $pdf->Vcell($c_width,$c_height,$x_axis,'$'.Helpers::priceFormat(array_sum((array)$patient)+array_sum((array)$insurance)),$lengthToSplit,"R","R");
        $pdf->Ln();

        $x_axis=$pdf->getx();
        $pdf->SetTextColor(100,100,100);
        $pdf->SetFont('Calibri-Bold','',7.5);
        $pdf->Vcell($c_width,$c_height,$x_axis,"Total Balance",$lengthToSplit,"BL","L");
        $x_axis=$pdf->getx();
        $pdf->SetFont('Calibri','',7.5);
        $pdf->Vcell($c_width,$c_height,$x_axis,'$'.Helpers::priceFormat(array_sum((array)$patient_bal)+array_sum((array)$insurance_bal)),$lengthToSplit,"BR","R");
        $pdf->Ln();
    }
}

class Provider_Summary_mypdf extends FPDF
{
    public function header(){

        $controller = New ProviderlistController;
        $api_response = $controller->getProviderFilterResultApi('summary');
        $api_response_data = $api_response->getData();

        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $practiceopt = $api_response_data->data->practiceopt;
        $header = $api_response_data->data->header;
        $req = @$practiceopt;

        $this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0, 135, 127);
        $this->AddFont('Calibri-Bold','','calibrib.php');
        $this->SetFont('Calibri-Bold','',10.5);
        $x_axis=$this->getx();
        $c_width = 295;
        $c_height = 0;
        $text = Practice::getPracticeName()." - Provider Summary";
        $lengthToSplit = strlen($text);
        $this->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","C");

        $this->AddFont('Calibri','','calibri.php');
        $this->SetFont('Calibri','',9.5);
        $this->SetTextColor(100, 100, 100);
        $this->Ln();

        $i = 0;
        $text = [];
        foreach ((array)$header as $key => $val) {
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

        if($req == "provider_list"){
 	       $this->header = ['Provider Name', 'Type', 'Created_on', 'User'];
        }else{
        	$this->header = [($header->{'Provider Type'}=='Billing')? 'Billing' : 'Rendering', 'Units', 'Charges($)', 'W/O($)', 'Pat Adj($)', 'Ins Adj($)', 'Total Adj($)', 'Pat Pmts($)', 'Ins Pmts($)', 'Total Pmts($)', 'Pat Balance($)', 'Ins Balance($)', 'Total Balance($)'];
        }

        foreach ($this->header as $key => $value) {
            $this->SetFont('Calibri-Bold','',8);
            $this->SetTextColor(100, 100, 100);
            $x_axis=$this->getx();// now get current pdf x axis value
            $c_height = 6;// cell height
            $c_width = (290/count($this->header));// cell width 
            $lengthToSplit = strlen($value);

            if ($value == "Billing" || $value == "Units" || $value == "Rendering") {
            	$align = "L";
            }else{
            	$align = "R";
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
