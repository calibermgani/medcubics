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
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;

$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);

class AgingSummaryPDFController extends Controller
{
    public function index()
    {
    	set_time_limit(300);
    	try {
	    	$created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
			$filename = 'Aging_Summary_'.$created_date.'.pdf';
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
			$report_export->report_controller_func = 'getAgingReportSearchApi';
			$report_export->status = 'Inprocess';
			$report_export->created_by = $user_id;
			$report_export->save();
			$report_export_id = $report_export->id;

	        $controller = New ReportApiController;
	        $api_response = $controller->getAgingReportSearchApi('pdf');
	        $api_response_data = $api_response->getData();
	        $aging_report_list = $api_response_data->data->aging_report_list;
	        $header = $api_response_data->data->header;

	        // Parameters for filter
	        $search_by = (array)$api_response_data->data->header;
	        $text = $headers = [];
	        $i = 0;
	        if(!empty($search_by)) {
	            foreach ((array)$search_by as $key => $val) {
	                $text[] = $key."=".(is_array($val)? $val[0] : $val);                           
	                $i++; 
	            }
	        }    
	        $headers = implode('&', $text);
	        
	        $pdf = new aging_summary_mypdf("L","mm","A4");
			$pdf->SetMargins(2,6);
			$pdf->SetTextColor(100,100,100);
			$pdf->SetDrawColor(217, 217, 217);
			$pdf->AddPage();
			$pdf->AddFont('Calibri','','calibri.php');
			$pdf->SetFont('Calibri','',7);
	        self::BladeContent($aging_report_list, $pdf, $header);
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
	    	\Log::info("Error Occured While export Aging Summary report. Message:".$e->getMessage() );
	    }
		exit();
	}

	public function BladeContent($aging_report_list, $pdf, $header){
		$count_r = 1;
		foreach($header as $header_name => $header_val){
			$pdf->SetFont('Calibri-Bold','',8);
			$pdf->SetTextColor(100, 100, 100);
			$x_axis=$pdf->getx();// now get current pdf x axis value
			$c_height = 6;// cell height
			foreach (array_keys($header, '') as $key) {
				unset($header[$key]);
			}
			$c_width = (293/count($header));// cell width 
			$lengthToSplit = strlen($header_val);
			$align = 'L';
			if ($header_val == "0-30" || $header_val == "31-60" || $header_val == "61-90" || $header_val == "91-120" || $header_val == "121-150" || $header_val == ">150") {
				$pdf->SetFont('Times','B',7);
			}
			if($count_r ==1){
				if ($header_val == "AR Days") {
					$align = "C";
				}
				$pdf->vcell($c_width,$c_height,$x_axis,$header_val,$lengthToSplit,"B",@$align);
			}	
			elseif($count_r % 2 == 0){
				$align = "C";
				$pdf->vcell($c_width,$c_height,$x_axis,$header_val,$lengthToSplit,"B",@$align);
			}
			$count_r++;
		}
		$pdf->Ln();
		if(isset($aging_report_list) && !empty($aging_report_list)){
			$i = 0;
			foreach($aging_report_list->name as $key => $name){
				$i = ++$i;
				$pdf->SetFont('Calibri','',8);
				$pdf->SetTextColor(100, 100, 100);
				$x_axis = $pdf->getx();// now get current pdf x axis value
				$c_height = 6;// cell height
				$c_width = (293/(count($aging_report_list->name)+1));
				if ($i == 1) {
					$c_width = $c_width*2;
				}else{
					$c_width = $c_width;
				}
				if(@$name=='Claims'){
					$align = "L";
					$pdf->vcell($c_width,$c_height,$x_axis,@$name,$lengthToSplit,"",@$align);
				}
				elseif ($key > 0) {
					$align = "R";
					$pdf->vcell($c_width,$c_height,$x_axis,@$name."($)",$lengthToSplit,"",@$align);	
				}
				else{
					$align = "R";
					$pdf->vcell($c_width,$c_height,$x_axis,@$name,$lengthToSplit,"",@$align);
				}
			}	
			$pdf->Ln();
		}
		if(isset($aging_report_list->patient)) {
			$i = 0;
			foreach($aging_report_list->patient as $key => $val){
				$i = ++$i;
				$pdf->SetFont('Calibri','',8);
				$pdf->SetTextColor(100, 100, 100);
				$x_axis = $pdf->getx();// now get current pdf x axis value
				$c_height = 6;// cell height
				$c_width = (293/(count($aging_report_list->patient)+1));

				if ($i == 1) {
					$c_width = $c_width*2;
				}else{
					$c_width = $c_width;
				}
				if(@$key==0){
					$align = "L";
					$pdf->vcell($c_width,$c_height,$x_axis,@$val,$lengthToSplit,"",@$align);					
				}
				elseif(@$key%2==0){
					if ($val < 0) {
						$pdf->SetTextColor(255, 0, 0);
					}
					$align = "R";
					$pdf->vcell($c_width,$c_height,$x_axis,Helpers::priceFormat(@$val,'',1),$lengthToSplit,"",@$align);	
					$pdf->SetTextColor(100,100,100);
				}
				else{
					$align = "L";
					$pdf->vcell($c_width,$c_height,$x_axis,@$val,$lengthToSplit,"",@$align);	
				}
			}
			$pdf->Ln();
		}
		$insurance_provider = array_except((array)$aging_report_list,['name','patient','total','total_percentage']);
		if(isset($insurance_provider) && !empty($insurance_provider)){
				$i = 0;
			foreach($insurance_provider as $list){
				foreach($list as $key => $val){
					$i = ++$i;
					$pdf->SetFont('Calibri','',8);
					$pdf->SetTextColor(100, 100, 100);
					$x_axis = $pdf->getx();// now get current pdf x axis value
					$c_height = 6;// cell height
					$c_width = (293/(count($list)+1));

					if ($i == 1) {
						$c_width = $c_width*2;
					}else{
						$c_width = $c_width;
					}
					if(@$key==0){
						$align = "L";
						$lengthToSplit = 25;
						$pdf->vcell($c_width,$c_height,$x_axis,@$val,$lengthToSplit,"",@$align);
					}
					elseif(@$key%2==0){
						if ($val < 0) {
							$pdf->SetTextColor(255, 0, 0);
						}
						$align = "R";
						$pdf->vcell($c_width,$c_height,$x_axis,Helpers::priceFormat(@$val,'',1),$lengthToSplit,"",@$align);	
						$pdf->SetTextColor(100,100,100);
					}
					else{
						$align = "L";
						$pdf->vcell($c_width,$c_height,$x_axis,@$val,$lengthToSplit,"",@$align);	
					}
				}
			$pdf->Ln();
			$i = 0;
			}
		}
		$i = 0;
		foreach($aging_report_list->total as $key => $val){
			$i = ++$i;
			$pdf->SetFont('Calibri','',8);
			$pdf->SetTextColor(100, 100, 100);
			$x_axis = $pdf->getx();// now get current pdf x axis value
			$c_height = 6;// cell height
			$c_width = (293/(count($aging_report_list->total)+1));

			if ($i == 1) {
				$c_width = $c_width*2;
			}else{
				$c_width = $c_width;
			}
			if(@$key==0){
				$align = "L";
				$pdf->vcell($c_width,$c_height,$x_axis,@$val,$lengthToSplit,"",@$align);
			}
			elseif(@$key%2==0){
				if ($val < 0) {
					$pdf->SetTextColor(255, 0, 0);
				}
				$align = "R";
				$pdf->vcell($c_width,$c_height,$x_axis,Helpers::priceFormat(@$val,'',1),$lengthToSplit,"",@$align);	
				$pdf->SetTextColor(100,100,100);
			}
			else{
				$align = "L";
				$pdf->vcell($c_width,$c_height,$x_axis,@$val,$lengthToSplit,"",@$align);	
			}
		}
		$pdf->Ln();
		$i = 0;
		foreach($aging_report_list->total_percentage as $key => $val){
			$i = ++$i;
			$pdf->SetFont('Calibri','',8);
			$pdf->SetTextColor(100, 100, 100);
			$x_axis = $pdf->getx();// now get current pdf x axis value
			$c_height = 6;// cell height
			$c_width = (293/(count($aging_report_list->total_percentage)+1));

			if ($i == 1) {
				$c_width = $c_width*2;
			}else{
				$c_width = $c_width;
			}
			if(@$key==0){
				$align = "L";
				$pdf->vcell($c_width,$c_height,$x_axis,@$val,$lengthToSplit,"",@$align);
			}
			elseif(@$key%2==0){
				if ($val < 0) {
					$pdf->SetTextColor(255, 0, 0);
				}
				$align = "R";
				$pdf->vcell($c_width,$c_height,$x_axis,@$val,$lengthToSplit,"",@$align);
				$pdf->SetTextColor(100,100,100);	
			}
			else{
				$align = "L";
				$pdf->vcell($c_width,$c_height,$x_axis,@$val,$lengthToSplit,"",@$align);	
			}
		}
	}
}

class aging_summary_mypdf extends FPDF
{
	public function header(){

		$request = Request::All();
    	// dd($request);
        $controller = New ReportApiController;
        $api_response = $controller->getAgingReportSearchApi('pdf');
        $api_response_data = $api_response->getData();
        $search_by = $api_response_data->data->headers;

		$this->SetFillColor(255, 255, 255);
		$this->SetTextColor(0, 135, 127);
	    $this->AddFont('Calibri-Bold','','calibrib.php');
	    $this->SetFont('Calibri-Bold','',10.5);
		$x_axis=$this->getx();
		$c_width = 295;
		$c_height = 0;
		$text = Practice::getPracticeName()." - Aging Summary";
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

		// $this->Ln();

		// $header = ['Appt Date', 'Appt Time', 'Appt Status', 'Acc No', 'Patient Name', 'Rendering Provider', 'Facility', 'Reason for Visit', 'Responsibility', 'Eligibility','Co-Pay Amt($)', 'Mode of Pmt', 'Paid Date', 'Future Appt', 'Pat Bal($)', 'User', 'Created Date'];

		// foreach ($header as $key => $value) {
		// 	$this->SetFont('Calibri-Bold','',8);
		// 	$this->SetTextColor(100, 100, 100);
		// 	$x_axis=$this->getx();// now get current pdf x axis value
		// 	$c_height = 6;// cell height
		// 	$c_width = (295/count($header));// cell width 
		// 	$lengthToSplit = strlen($value);
		// 	$align = 'L';
			
		// 	$this->vcell($c_width,$c_height,$x_axis,$value,$lengthToSplit,"B",@$align);// pass all values inside the cell 
		// }

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