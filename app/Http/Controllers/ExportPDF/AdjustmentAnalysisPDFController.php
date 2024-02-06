<?php

namespace App\Http\Controllers\ExportPDF;

use DB;
use Carbon;
use Request;
use Auth;
use FPDF;
use session;
use App\Http\Controllers\Controller;
use App\Models\Practice as Practice;
use App\Http\Controllers\Reports\Api\ReportApiController as ReportApiController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\ReportExport as ReportExport;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;

$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);

class AdjustmentAnalysisPDFController extends Controller
{
    public function index(){
		  
		try {
	        $request = Request::All();
	        $controller = New ReportApiController;
	    	$api_response = $controller->getAdjustmentsearchApi('pdf');
	    	$api_response_data = $api_response->getData();
	        $adjustments = $api_response_data->data->adjustment;    
	        $tot_adjs = $api_response_data->data->tot_adjs; 
	        $instype = $api_response_data->data->instype;
	        $header = $api_response_data->data->header;

	        $text = $headers = [];
	        $i = 0;
	        if(!empty($header)) {
	            foreach ((array)$header as $key => $val) {
	                $text[] = $key."=".(is_array($val)? $val[0] : $val);                           
	                $i++; 
	            }
	        }
	        $headers = implode('&', $text);

	        $created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
	        $filename = 'Adjustment_Analysis_'.$created_date.'.pdf';
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
	        $report_export->parameter = @$headers;
	        $report_export->report_type = $request['export'];
	        $report_export->report_file_name = $filename;
	        $report_export->report_controller_name = 'CollectionController';
	        $report_export->report_controller_func = 'patientInsurancePaymentSearchexport';
	        $report_export->status = 'Inprocess';
	        $report_export->created_by = $user_id;
	        $report_export->save();
	        $report_export_id = $report_export->id;
	        $pdf = new adjustment_mypdf("L","mm","A4");
	        $pdf->SetMargins(2,6);
	        $pdf->SetTextColor(100,100,100);
	        $pdf->SetDrawColor(217, 217, 217);
	        $pdf->AddPage();
	        $pdf->AddFont('Calibri','','calibri.php');
	        $pdf->SetFont('Calibri','',7);
	        self::BladeContent($adjustments, $pdf, $instype, $header, $tot_adjs);
	        // $pdf->Output();

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

    public function BladeContent($adjustments, $pdf, $instype, $header, $tot_adjs){
    	$claim_number=0;
		if(!empty($adjustments)){
    		foreach ($adjustments as $adjustment) {
    			if($adjustment->claim_number!=$claim_number){ 
		    		$claim_number =$adjustment->claim_number;
	    			$patient_name = 	@$adjustment->title.' '.Helpers::getNameformat(@$adjustment->last_name,@$adjustment->first_name,@$adjustment->middle_name);

				    $pdf->SetFillColor(255, 255, 255);
	    			$pdf->AddFont('Calibri-Bold','','calibrib.php');
				    $pdf->SetFont('Calibri-Bold','',7.5);
					$x_axis=$pdf->getx();
					$c_width = 50;
					$c_height = 7;
					// $pdf->Vcell(0,30,$x_axis,"",20,"L","L");
					$pdf->Vcell($c_width,$c_height,$x_axis,"Claim No",20,"TL","L");

					$pdf->AddFont('Calibri','','calibrib.php');
				    $pdf->SetFont('Calibri','',7.5);
					$x_axis=$pdf->getx();
			    	$pdf->Vcell($c_width,$c_height,$x_axis,$adjustment->claim_number,20,"T","L");

			    	$x_axis=$pdf->getx();
			    	$pdf->AddFont('Calibri-Bold','','calibrib.php');
				    $pdf->SetFont('Calibri-Bold','',7.5);
			    	$pdf->Vcell($c_width,$c_height,$x_axis,'Patient Name',20,"T","L");

			    	$x_axis=$pdf->getx();
			    	$pdf->AddFont('Calibri','','calibrib.php');
				    $pdf->SetFont('Calibri','',7.5);
				    $text = ($adjustment->patient_name!='')? $adjustment->patient_name : '-Nil-';
			    	$pdf->Vcell($c_width,$c_height,$x_axis,@$text,20,"T","L");

			    	$x_axis=$pdf->getx();
			    	$pdf->AddFont('Calibri-Bold','','calibrib.php');
				    $pdf->SetFont('Calibri-Bold','',7.5);
			    	$pdf->Vcell($c_width,$c_height,$x_axis,'Acc No',20,"T","L");

			    	$x_axis=$pdf->getx();
			    	$pdf->AddFont('Calibri','','calibrib.php');
				    $pdf->SetFont('Calibri','',7.5);
				    $text = !empty($adjustment->account_no)? $adjustment->account_no : '-Nil-';
			    	$pdf->Vcell($c_width-10,$c_height,$x_axis,$text,20,"TR","L");
			    	$pdf->Ln();

			    	$pdf->AddFont('Calibri-Bold','','calibrib.php');
				    $pdf->SetFont('Calibri-Bold','',7.5);
					$x_axis=$pdf->getx();
					// $pdf->Vcell(0,30,$x_axis,"",20,"L","L");
					$pdf->Vcell($c_width,$c_height,$x_axis,"Responsibility",20,"L","L");

					$pdf->AddFont('Calibri','','calibrib.php');
				    $pdf->SetFont('Calibri','',7.5);
					$x_axis=$pdf->getx();
					if($adjustment->self_pay =='Yes'){
						$text = 'Patient';
					}else{
						$text = @$adjustment->self_pay;
					}
			    	$pdf->Vcell($c_width,$c_height,$x_axis,$text,20,"","L");

			    	$x_axis=$pdf->getx();
			    	$pdf->AddFont('Calibri-Bold','','calibrib.php');
				    $pdf->SetFont('Calibri-Bold','',7.5);
			    	$pdf->Vcell($c_width,$c_height,$x_axis,'Billing',20,"","L");

			    	$x_axis=$pdf->getx();
			    	$pdf->AddFont('Calibri','','calibrib.php');
				    $pdf->SetFont('Calibri','',7.5);
				    $text = !empty($adjustment->billing_short_name)? $adjustment->billing_short_name : '-Nil-';
			    	$pdf->Vcell($c_width,$c_height,$x_axis,$text,20,"","L");

			    	$x_axis=$pdf->getx();
			    	$pdf->AddFont('Calibri-Bold','','calibrib.php');
				    $pdf->SetFont('Calibri-Bold','',7.5);
			    	$pdf->Vcell($c_width,$c_height,$x_axis,'Rendering',20,"","L");

			    	$x_axis=$pdf->getx();
			    	$pdf->AddFont('Calibri','','calibrib.php');
				    $pdf->SetFont('Calibri','',7.5);
				    $text = !empty($adjustment->rendering_short_name)? $adjustment->rendering_short_name : '-Nil-';
			    	$pdf->Vcell($c_width-10,$c_height,$x_axis,$text,20,"R","L");
			    	$pdf->Ln();

			    	$pdf->AddFont('Calibri-Bold','','calibrib.php');
				    $pdf->SetFont('Calibri-Bold','',7.5);
					$x_axis=$pdf->getx();
					$pdf->Vcell($c_width,$c_height,$x_axis,"Facility",20,"L","L");

					$pdf->AddFont('Calibri','','calibrib.php');
				    $pdf->SetFont('Calibri','',7.5);
					$x_axis=$pdf->getx();
					$text = !empty($adjustment->facility_short_name)? str_limit($adjustment->facility_short_name) : '-Nil-';
			    	$pdf->Vcell($c_width,$c_height,$x_axis,$text,20,"","L");

			    	/*$pdf->AddFont('Calibri-Bold','','calibrib.php');
				    $pdf->SetFont('Calibri-Bold','',7.5);
					$x_axis=$pdf->getx();
					$pdf->Vcell($c_width,$c_height,$x_axis,"Tot Adj($)",20,"","L");

					$pdf->AddFont('Calibri','','calibrib.php');
				    $pdf->SetFont('Calibri','',7.5);
					$x_axis=$pdf->getx();
					$text = !empty($adjustment->tot_adj)? Helpers::priceFormat(array_sum(array_flatten(json_decode(json_encode($adjustment->tot_adj), true))),'',1) : '-Nil-';
			    	$pdf->Vcell($c_width+90,$c_height,$x_axis,$text,20,"R","L");*/
			    	$pdf->Ln();
			    	$c_height = 4;
			    	$c_width = 36;

			    	$x_axis=$pdf->getx();
			    	$pdf->SetFillColor(232, 232, 232);
					$pdf->SetFont('Calibri-Bold','',7.5);
			    	$pdf->Vcell($c_width,$c_height,($x_axis),"DOS",20,"","L",1);

			    	$x_axis=$pdf->getx();
			    	$pdf->Vcell($c_width,$c_height,($x_axis),"CPT",20,"","L",1);

			    	$x_axis=$pdf->getx();
			    	$pdf->Vcell($c_width,$c_height,($x_axis),"Payer",20,"","L",1);

			    	$x_axis=$pdf->getx();
			    	$pdf->Vcell($c_width,$c_height,($x_axis),"Adj date",20,"","L",1);

			    	$x_axis=$pdf->getx();
			    	$pdf->Vcell($c_width,$c_height,($x_axis),"Adj Reason",20,"","L",1);

			    	$x_axis=$pdf->getx();
			    	$pdf->Vcell($c_width,$c_height,($x_axis),"CPT Adj($)",20,"","L",1);

			    	$x_axis=$pdf->getx();
			    	$pdf->Vcell($c_width,$c_height,($x_axis),"Reference",20,"","L",1);

			    	$x_axis=$pdf->getx();
			    	$pdf->Vcell($c_width+2.1,$c_height,($x_axis),"User",20,"","L",1);
			    	$pdf->Ln();
			    }
		    		$c_height = 5;
		    		$pdf->SetFillColor(255, 255, 255);
					$pdf->SetFont('Calibri','',7.5);
		    		
		    		$x_axis=$pdf->getx();
					$text = !empty($adjustment->dos_from)? $adjustment->dos_from : '-Nil-';
			    	$pdf->Vcell($c_width,$c_height,($x_axis),$text,20,"L","L",1);

		    		$x_axis=$pdf->getx();
		    		$text = !empty($adjustment->cpt_code)? @$adjustment->cpt_code : '-Nil-';
			    	$pdf->Vcell($c_width,$c_height,($x_axis),$text,20,"","L",1);

			    	$x_axis=$pdf->getx();
		    		$text = $adjustment->insurance_name;
			    	$pdf->Vcell($c_width,$c_height,($x_axis),$text,40,"","L",1);

		    		$x_axis=$pdf->getx();
		    		$text = !empty($adjustment->adj_date)? @$adjustment->adj_date : '-Nil-';
			    	$pdf->Vcell($c_width,$c_height,($x_axis),@$text,20,"","L",1);

		    		$x_axis=$pdf->getx();
		    		$text = !empty($adjustment->adjustment_shortname)? @$adjustment->adjustment_shortname : '-Nil-';
			    	$pdf->Vcell($c_width,$c_height,($x_axis),@$text,20,"","L",1);

			    	$x_axis=$pdf->getx();
			    	$text = !empty($adjustment->adjustment_amt)? @$adjustment->adjustment_amt : '-Nil-';
			    	$pdf->Vcell($c_width,$c_height,($x_axis),@$text,20,"","L",1);

		    		$x_axis=$pdf->getx();
		    		$text = !empty($adjustment->reference)? @$adjustment->reference : '-Nil-';
			    	$pdf->Vcell($c_width,$c_height,($x_axis),$text,20,"","L",1);

		    		$x_axis=$pdf->getx();
		    		$text = !empty($adjustment->created_by)? Helpers::user_names($adjustment->created_by) : '-Nil-';
			    	$pdf->Vcell($c_width+2,$c_height,($x_axis),$text,20,"R","L",1);
		    		$pdf->Ln();
		    	
			    $x_axis=$pdf->getx();
				$pdf->SetFont('Calibri','',7.5);
		    	$pdf->Vcell(290,0,($x_axis),"",20,"T","",1);
		    	$pdf->Ln();

		    	/*$abb_user[] = Helpers::user_names(@$adjustment->created_by)." - ".Helpers::getUserFullName(@$adjustment->created_by);
		    	$abb_user = array_unique($abb_user);
				foreach (array_keys($abb_user, ' - ') as $key) {
		        	unset($abb_user[$key]);
		        	}*/

    		}
    	}
    	self::SummaryContent($adjustments, $pdf, $instype, $header, $tot_adjs);
    	/*$x_axis=$pdf->getx();
    	$pdf->Ln();
    	$abbreviation = ['abb_user' => $abb_user];
		$abb_controller = New AbbreviationController;
		$abb_controller->abbreviation($abbreviation,$pdf);*/
    }

    public function SummaryContent($adjustments, $pdf, $instype, $header, $tot_adjs){
        $ins_adj=0;$pat_adj=0;
        foreach ($adjustments as $adjustment) {
            if($adjustment->adjustment_type=='Patient'){
                $pat_adj += $adjustment->adjustment_amt;                
            }else{
                $ins_adj += $adjustment->adjustment_amt;                
            }
        }
    	$x_axis = $pdf->getx();
    	$pdf->SetTextColor(240, 125, 8);
    	$pdf->SetFont('Calibri-Bold','',8);
    	$pdf->Vcell(30,10,$x_axis,"Summary",20,"","L");
    	$pdf->Ln();

    	$pdf->MultiCell(80, 0, "",'T', "L");
    	$x_axis=$pdf->getx();
    	$pdf->SetTextColor(100,100,100);
    	$pdf->SetFont('Calibri-Bold','',7.5);
 		$pdf->Vcell(40,5,$x_axis,"Transaction Date",30,"L","L");
 		$x_axis=$pdf->getx();
 		$pdf->SetFont('Calibri','',7.5);
 		$pdf->Vcell(40,5,$x_axis,@$header->{'Transaction Date'}[0],30,"R","R");
 		$pdf->Ln();	

 		if($instype == "all"){
			$x_axis=$pdf->getx();
	    	$pdf->SetTextColor(100,100,100);
	    	$pdf->SetFont('Calibri-Bold','',7.5);
	 		$pdf->Vcell(40,5,$x_axis,"Total Insurance Adjustments",30,"L","L");
	    	$pdf->SetFont('Calibri','',7.5);
	 		$x_axis=$pdf->getx();
	 		$pdf->Vcell(40,5,$x_axis,"$ ".Helpers::priceFormat($ins_adj),30,"R","R");
	 		$pdf->Ln();	 	

	 		$x_axis=$pdf->getx();
	    	$pdf->SetTextColor(100,100,100);
	    	$pdf->SetFont('Calibri-Bold','',7.5);
	 		$pdf->Vcell(40,5,$x_axis,"Total Patient Adjustments",30,"L","L");
	 		$x_axis=$pdf->getx();
	    	$pdf->SetFont('Calibri','',7.5);
	 		$pdf->Vcell(40,5,$x_axis,"$ ".Helpers::priceFormat($pat_adj),30,"R","R");
	 		$pdf->Ln();	 			

	 		$x_axis=$pdf->getx();
	    	$pdf->SetTextColor(100,100,100);
	    	$pdf->SetFont('Calibri-Bold','',7.5);
	 		$pdf->Vcell(40,5,$x_axis,"Total Adjustments",30,"L","L");
	 		$x_axis=$pdf->getx();
	    	$pdf->SetFont('Calibri','',7.5);
	 		$pdf->Vcell(40,5,$x_axis,"$ ".Helpers::priceFormat($ins_adj+$pat_adj),30,"R","R");
	 		$pdf->Ln();	 	
 		}

 		if($instype == "insurance"){
 			$x_axis=$pdf->getx();
	    	$pdf->SetTextColor(100,100,100);
	    	$pdf->SetFont('Calibri-Bold','',7.5);
	 		$pdf->Vcell(40,5,$x_axis,"Total Insurance Adjustments",30,"L","L");
	 		$x_axis=$pdf->getx();
	    	$pdf->SetFont('Calibri','',7.5);
	 		$pdf->Vcell(40,5,$x_axis,"$ ".Helpers::priceFormat($ins_adj),30,"R","R");
	 		$pdf->Ln();
 		}

 		if($instype == "self"){
 			$x_axis=$pdf->getx();
	    	$pdf->SetTextColor(100,100,100);
	    	$pdf->SetFont('Calibri-Bold','',7.5);
	 		$pdf->Vcell(40,5,$x_axis,"Total Patient Adjustments",30,"L","L");
	 		$x_axis=$pdf->getx();
	    	$pdf->SetFont('Calibri','',7.5);
	 		$pdf->Vcell(40,5,$x_axis,"$ ".Helpers::priceFormat($pat_adj),30,"R","R");
	 		$pdf->Ln();
 		}
 		$x_axis=$pdf->getx();
		$pdf->SetFont('Calibri','',7.5);
	    $pdf->Vcell(80,0,($x_axis),"",20,"T","",1);
	}
}

class adjustment_mypdf extends FPDF
{
	public function header(){

		$request = Request::All();
        $controller = New ReportApiController;
    	$api_response = $controller->getAdjustmentsearchApi('pdf');
    	$api_response_data = $api_response->getData();
        $search_by = $api_response_data->data->header;

	    $this->SetFillColor(255, 255, 255);
		$this->SetTextColor(0, 135, 127);
	    $this->AddFont('Calibri-Bold','','calibrib.php');
	    $this->SetFont('Calibri-Bold','',10.5);
		$x_axis=$this->getx();
		$c_width = 295;
		$c_height = 0;
		$text = Practice::getPracticeName()." - Adjustment Analysis Detailed";
		$lengthToSplit = strlen($text);
		$this->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","C");
        $this->AddFont('Calibri','','calibri.php');
        $this->SetFont('Calibri','',9.5);
		$this->SetTextColor(100, 100, 100);
		$this->Ln();

		$i = 0;
		$text = [];
		foreach ($search_by as $key => $val) {
			$text[] = $key.":".@$val[0];                           
  		          $i++; 
		}
		$text_imp = implode(" | ", $text);
		$this->Vcell(295,12,$x_axis,"$text_imp",160,"","C");

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
	}

	public function footer()
	{
	    $this->AddFont('Calibri','','calibri.php');
	    $this->SetFont('Calibri','',8);
	    $this->SetY(290);
	    $x_axis=$this->getx();
	    $c_width = 100;
	    $c_height = 0;
	    $year = date('Y');
	    $this->SetTextColor(82,82,82);
	    //$this->SetFont('Times','',7);
	    $text =  "Copyright ". chr(169) ." ".$year." Medcubics. All Rights Reserved.";
	    $lengthToSplit = strlen($text);
	    $this->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,'', "L");
	    $c_width = 205;
	    $text =  "Page No :".$this->PageNo();
	    $this->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,'', "R");
	}

	public function vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,$border="",$align="L",$fill=0)
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
			$this->Cell($c_width,$c_height,'',$border,0,$align,$fill);
		}
		else{
		    $this->SetX($x_axis);
		    $this->Cell($c_width,$c_height,$text,$border,0,$align,$fill);
		}
	}
}
