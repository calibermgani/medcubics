<?php

namespace App\Http\Controllers\exportpdf;

use DB;
use Carbon;
use Request;
use Auth;
use FPDF;
use Session;
use App\Models\Practice as Practice;
use App\Models\Icd as Icd;
use App\Http\Controllers\Controller;
use App\Models\Pos as Pos;
use App\Models\ReportExport as ReportExport;
use App\Http\Controllers\Reports\Practicesettings\Api\FacilitylistApiController as FacilitylistApiController;
use App\Http\Controllers\ExportPDF\AbbreviationController as AbbreviationController;
use App\Http\Controllers\Reports\ReportController as ReportController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;

$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);

class FacilitySummaryPDFController extends Controller
{
    public function index()
    {
        try{
    	set_time_limit(300);
        $created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
        $filename = 'facility_summary_'.$created_date.'.pdf';
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
        $report_export->report_file_name = 'Downloads/facility_summary_'.$created_date.'.pdf';
        $report_export->report_controller_name = 'ReportApiController';
        $report_export->report_controller_func = 'getPatientIcdWorksheetFilterApiSP';
        $report_export->status = 'Inprocess';
        $report_export->created_by = $user_id;
        $report_export->save();
        $report_export_id = $report_export->id; 

        $controller = New FacilitylistApiController;
        $api_response = $controller->getFilterResultSummaryApi('pdf');
        $api_response_data = $api_response->getData();

        $facilities = $api_response_data->data->facilities;
        $charges = $api_response_data->data->charges;
        $adjustments = $api_response_data->data->adjustments;
        $patient = $api_response_data->data->patient;
        $insurance = $api_response_data->data->insurance;
        $patient_bal = $api_response_data->data->patient_bal;
        $insurance_bal = $api_response_data->data->insurance_bal;
        $unit_details = $api_response_data->data->unit_details;
        $wallet = $api_response_data->data->wallet;
        $header = $api_response_data->data->header;
        $search_by = $api_response_data->data->search_by;
        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $practice_opt = $api_response_data->data->practiceopt;

        $pdf = new facility_summary_mypdf("L","mm","A4");
		$pdf->SetMargins(2,6);
		$pdf->SetTextColor(100,100,100);
		$pdf->SetDrawColor(217, 217, 217);
		$pdf->AddPage();
		$pdf->AddFont('Calibri','','calibri.php');
		$pdf->SetFont('Calibri','',7);
        self::BladeContent($facilities, $pdf, $charges, $adjustments, $patient, $insurance, $patient_bal, $insurance_bal, $unit_details, $wallet, $header, $practice_opt,$search_by);
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

	public function BladeContent($facilities, $pdf, $charges, $adjustments, $patient, $insurance, $patient_bal, $insurance_bal, $unit_details, $wallet, $header, $practice_opt,$search_by){
		$pdf->SetFillColor(255, 255, 255);
        $pdf->AddFont('Calibri','','calibrib.php');
        $pdf->SetFont('Calibri','',7.5);
        $c_width = 290/10;
        $c_height = 6;
        $total_adj = $patient_total = $insurance_total = $total = $count = $payments = $total_payments = $tot_avg = $cnts = 0;
        foreach($facilities as $key => $list){
        	$practice_timezone = Helpers::getPracticeTimeZone();
	        $exp = explode("to",$search_by->{'Transaction Date'});
	        $start_date = date("Y-m-d",strtotime(trim($exp[0])));
	        $end_date = date("Y-m-d",strtotime(trim($exp[1])));
	        $facility_name = $list->facility_name;
            $patient_balance = isset($patient_bal->$facility_name) ? $patient_bal->$facility_name : 0;
            $insurance_balance = isset($insurance_bal->$facility_name) ? $insurance_bal->$facility_name : 0;
            $charge = isset($charges->$facility_name) ? $charges->$facility_name : 0;
            $adjustment = isset($list->$adjustments) ? $list->$adjustments : 0;
            $payments = @$list->patient+@$list->insurance;
            $total_bal = @$list->patient_bal+@$list->insurance_bal;
	        if(!empty(Pos::select('id')->where('code',$list->code)->get()->toArray())){
	            $pos_id = Pos::select('id')->where('code',$list->code)->get()->toArray()[0]['id'];
	            $count = DB::select("select count(claim.facility_id) as cnt from (select facility_id, pos_id from claim_info_v1 where facility_id = ".$list->facility_id." and pos_id = ".$pos_id." and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date' and deleted_at is null group by patient_id) as claim");
	            if(!empty($count)){
	                $patient_cnt = ($count[0]->cnt!=0)?$count[0]->cnt:1;
	            }else{
	                $patient_cnt = 1;
	            }
	                $tot_avg_pmt = ((@$list->patient+@$list->insurance)!=0 && $patient_cnt != 0)?round((@$list->patient+@$list->insurance)/$patient_cnt):0.00;
	        }

	        $x_axis = $pdf->getx();
            $text = @$list->facility_name;
            $lengthToSplit = strlen($text);
            $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","L");

            $x_axis = $pdf->getx();
            $text = @$list->code.'-'.@$list->pos;
            $lengthToSplit = strlen($text);
            $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","L");

            $x_axis = $pdf->getx();
            $text = (@$list->unit_details!='')?$list->unit_details:0;
            $lengthToSplit = strlen($text);
            $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","L");

            $x_axis = $pdf->getx();
            $text = Helpers::priceFormat(isset($list->charges) ? @$list->charges : '0.00','',1);
            $lengthToSplit = strlen($text);
            $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");

            $x_axis = $pdf->getx();
            $text = Helpers::priceFormat(@$list->adjustments,'',1);
            $lengthToSplit = strlen($text);
            $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");

            $x_axis = $pdf->getx();
            $text = Helpers::priceFormat($payments,'',1);
            $lengthToSplit = strlen($text);
            $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");

            $x_axis = $pdf->getx();
            $text = Helpers::priceFormat($tot_avg_pmt,'',1);
            $lengthToSplit = strlen($text);
            $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");

            $x_axis = $pdf->getx();
            $text = Helpers::priceFormat(@$list->patient_bal,'',1);
            $lengthToSplit = strlen($text);
            $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");

            $x_axis = $pdf->getx();
            $text = Helpers::priceFormat(@$list->insurance_bal,'',1);
            $lengthToSplit = strlen($text);
            $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");

            $x_axis = $pdf->getx();
            $text = Helpers::priceFormat(@$total_bal,'',1);
            $lengthToSplit = strlen($text);
            $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");

            $pdf->Ln();

            $total_adj += $adjustment;
            $patient_total += $patient_balance;
            $insurance_total += $insurance_balance;
            $total += $total_bal;
            $tot_avg += $tot_avg_pmt;
        }
        $patient_payments = !empty($patient)?array_sum((array)$patient):0.00;
        $insurance_payments = !empty($insurance)?array_sum((array)$insurance):0.00;
        $pat_balance = !empty($patient_bal)?array_sum((array)$patient_bal):0.00;
        $ins_balance = !empty($insurance_bal)?array_sum((array)$insurance_bal):0.00;
        $total_balance = $pat_balance+$ins_balance;
        if(!empty($header)){
            $counts = DB::select("select count(claim.facility_id) as cnt from (select facility_id, pos_id from claim_info_v1 where facility_id in (".implode(',', $header).") and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date' and deleted_at is null group by patient_id) as claim");
        } else {
            $counts = DB::select("select count(claim.facility_id) as cnt from (select facility_id, pos_id from claim_info_v1 where DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date' and deleted_at is null group by patient_id) as claim");
        }
        if(!empty($counts)){
            $cnts = $counts[0]->cnt;
            $tot_avg = ($cnts != 0)?$tot_avg/$cnts:$tot_avg;
        }
        $x_axis = $pdf->getx();
        $text = 'Totals';
        $lengthToSplit = strlen($text);
        $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");

        $x_axis = $pdf->getx();
        $text = '';
        $lengthToSplit = strlen($text);
        $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");

        $x_axis = $pdf->getx();
        $text = @$unit_details;
        $lengthToSplit = strlen($text);
        $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","L");

        $x_axis = $pdf->getx();
        $text = Helpers::priceFormat(@$charges,'',1);
        $lengthToSplit = strlen($text);
        $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");

        $x_axis = $pdf->getx();
        $text = Helpers::priceFormat(@$adjustments,'',1);
        $lengthToSplit = strlen($text);
        $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");

        $x_axis = $pdf->getx();
        $text = Helpers::priceFormat(@$patient_payments+@$insurance_payments,'',1);
        $lengthToSplit = strlen($text);
        $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");

        $x_axis = $pdf->getx();
        $text = Helpers::priceFormat(@$tot_avg,'',1);
        $lengthToSplit = strlen($text);
        $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");

        $x_axis = $pdf->getx();
        $text = Helpers::priceFormat(@$pat_balance,'',1);
        $lengthToSplit = strlen($text);
        $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");

        $x_axis = $pdf->getx();
        $text = Helpers::priceFormat(@$ins_balance,'',1);
        $lengthToSplit = strlen($text);
        $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");

        $x_axis = $pdf->getx();
        $text = Helpers::priceFormat(@$total_balance,'',1);
        $lengthToSplit = strlen($text);
        $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","R");
        $pdf->Ln();
        $pdf->Ln();
        $x_axis = $pdf->getx();
        $text = 'Wallet Balance';
        $lengthToSplit = strlen($text);
        $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","L");

        $x_axis = $pdf->getx();
        $text = Helpers::priceFormat(@$wallet,'',1);
        $lengthToSplit = strlen($text);
        $pdf->SetTextColor(240, 125, 8);
        $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","L");
	}
}

class facility_summary_mypdf extends FPDF
{
    public function header(){

        $request = Request::All();
        $controller = New FacilitylistApiController;
        $api_response = $controller->getFilterResultSummaryApi('pdf');
        $api_response_data = $api_response->getData();
        $search_by = $api_response_data->data->search_by;
        $this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0, 135, 127);
        $this->AddFont('Calibri-Bold','','calibrib.php');
        $this->SetFont('Calibri-Bold','',10.5);
        $x_axis=$this->getx();
        $c_width = 290;
        $c_height = 0;
        $text = Practice::getPracticeName()." - Facility Summary";
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
        $this->Vcell(290,12,$x_axis,$text_imp,160,"","C");

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

        $header = ['Facility Name', 'POS', 'Units', 'Charges($)', 'Adj($)', 'Payments($)', 'Avg pmts/Pat($)', 'Pat Balance($)', 'Ins Balance($)', 'Total Balance($)'];

        foreach ($header as $key => $value) {
            $this->SetFont('Calibri-Bold','',8);
            $this->SetTextColor(100, 100, 100);
            $x_axis=$this->getx();// now get current pdf x axis value
            $c_height = 6;// cell height
            $c_width = (290/count($header));// cell width 
            $lengthToSplit = 16;

			if($value == 'Charges($)' || $value == 'Adj($)' || $value == 'Payments($)' || $value == 'Avg pmts/Pat($)' || $value == 'Pat Balance($)' || $value == 'Ins Balance($)' || $value == 'Total Balance($)') {
				$align = 'R';
			}else{
				$align = 'L';
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
