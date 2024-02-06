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
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;

$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);

class ICDWorksheetPDFController extends Controller
{
    public function index()
    {
        try{
    	set_time_limit(300);
        $created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
        $filename = 'ICD_Worksheet_'.$created_date.'.pdf';
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
        $report_export->report_file_name = 'Downloads/ICD_Worksheet_'.$created_date.'.pdf';
        $report_export->report_controller_name = 'ReportApiController';
        $report_export->report_controller_func = 'getPatientIcdWorksheetFilterApiSP';
        $report_export->status = 'Inprocess';
        $report_export->created_by = $user_id;
        $report_export->save();
        $report_export_id = $report_export->id; 

        $controller = New ReportApiController;
        $api_response = $controller->getPatientIcdWorksheetFilterApi('pdf');
        $api_response_data = $api_response->getData();

        $icd_result = $api_response_data->data->payment_details;
        $search_by = $api_response_data->data->search_by;
        $pdf = new ICD_worksheet_mypdf("L","mm","A4");
		$pdf->SetMargins(2,6);
		$pdf->SetTextColor(100,100,100);
		$pdf->SetDrawColor(217, 217, 217);
		$pdf->AddPage();
		$pdf->AddFont('Calibri','','calibri.php');
		$pdf->SetFont('Calibri','',7);
        self::BladeContent($icd_result, $pdf);
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

	public function BladeContent($icd_result, $pdf){
		$pdf->SetFillColor(255, 255, 255);
        $pdf->AddFont('Calibri','','calibrib.php');
        $pdf->SetFont('Calibri','',7.5);
        $c_width = 295/6;
        $c_height = 6;
        

        if(isset($icd_result) && $icd_result !=''){
            foreach($icd_result as $list){
                $icd_values = Icd::getIcdValues(@$list->patient_icd);
                $name = Helpers::getNameformat(@$list->last_name,@$list->first_name,@$list->middle_name);

                $x_axis = $pdf->getx();
                $text = !empty($list->account_no)? @$list->account_no : '-Nil-';
                $lengthToSplit = strlen($text);
                $pdf->Vcell($c_width-1,$c_height,$x_axis,$text,$lengthToSplit,"","L");

                $x_axis = $pdf->getx();
                $text = !empty($name)? @$name : '-Nil-';
                $lengthToSplit = strlen($text);
                $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","L");

                $x_axis = $pdf->getx();
                $text = !empty($list->dob)? Helpers::dateFormat(@$list->dob,'dob') : '-Nil-';
                $lengthToSplit = strlen($text);
                $pdf->Vcell($c_width-2,$c_height,$x_axis,$text,$lengthToSplit,"","L");

                $x_axis = $pdf->getx();
                if(@$list->ssn != ''){
                    $text = @$list->ssn;
                }else{
                    $text = '-Nil-';
                }
                $lengthToSplit = strlen($text);
                $pdf->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","L");

                $x_axis = $pdf->getx();
                if(isset($list->patient_insurance[0])){
                    $text = (!empty($list->patient_insurance[0]->insurance_details->short_name))?$list->patient_insurance[0]->insurance_details->short_name:'-Nil-';
                }else{
                    $text = 'Self';
                }
                $lengthToSplit = strlen($text);
                $pdf->Vcell($c_width-21,$c_height,$x_axis,$text,$lengthToSplit,"","L");

                $x_axis = $pdf->getx();
                $text = !empty($icd_values)? implode(', ', @$icd_values) : '-Nil-';
                $lengthToSplit = 50;
                $pdf->Vcell($c_width+20,$c_height,$x_axis,$text,$lengthToSplit,"","L");
                $pdf->Ln();
            }
        }
	}
}

class ICD_worksheet_mypdf extends FPDF
{
    public function header(){

        $request = Request::All();
        $controller = New ReportApiController;
        $api_response = $controller->getPatientIcdWorksheetFilterApiSP('pdf');
        $api_response_data = $api_response->getData();
        $search_by = $api_response_data->data->search_by;
        $this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0, 135, 127);
        $this->AddFont('Calibri-Bold','','calibrib.php');
        $this->SetFont('Calibri-Bold','',10.5);
        $x_axis=$this->getx();
        $c_width = 295;
        $c_height = 0;
        $text = Practice::getPracticeName()." - ICD Worksheet";
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

        $header = ['Acc No', 'Patient Name', 'DOB', 'SSN', 'Payer', 'ICD10'];

        foreach ($header as $key => $value) {
            $this->SetFont('Calibri-Bold','',8);
            $this->SetTextColor(100, 100, 100);
            $x_axis=$this->getx();// now get current pdf x axis value
            $c_height = 6;// cell height
            $c_width = (290/count($header));// cell width 
            $lengthToSplit = strlen($value);

            if ($value == "Payer") {
                $c_width = $c_width - 20;
            }
            if ($value == "ICD10") {
                $this->SetFont('Times','B',7);
                $c_width = $c_width + 20;
            }
            // else{
            //     $c_width = (295/count($header));
            // }
            
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
