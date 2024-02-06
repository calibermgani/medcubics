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
use App\Models\Provider as Provider;
use App\Models\Practice as Practice;
use App\Http\Controllers\Controller;
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
use App\Models\ReportExport as ReportExport;
use App\Models\ReportExportTask as ReportExportTask;
use App\Http\Controllers\Reports\Api\ReportApiController as ReportApiController;
use App\Http\Controllers\ExportPDF\AbbreviationController as AbbreviationController;
use App\Http\Controllers\Reports\Financials\Api\FinancialApiController as FinancialApiController;
use App\Http\Controllers\Reports\ReportController as ReportController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\ProcedureCategory as ProcedureCategory;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;

$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);

class ChargeCategoryPDFController extends Controller
{
    public function index()
    {
    	set_time_limit(300);
    	try {
	    	$created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
			$filename = 'Charge_Category_Report_'.$created_date.'.pdf';
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
			$report_export->report_controller_name = 'FinancialApiController';
			$report_export->report_controller_func = 'getchargecategoryresultApiSP';
			$report_export->status = 'Inprocess';
			$report_export->created_by = $user_id;
			$report_export->save();
			$report_export_id = $report_export->id;

	        $controller = New FinancialApiController;
	        $api_response = $controller->getchargecategoryresultApiSP('pdf');
	        $api_response_data = $api_response->getData();
	        $charges_list = $api_response_data->data->export_array;
	        $total_arr = $api_response_data->data->total_arr;

	        // Parameters for filter
	        $search_by = (array)$api_response_data->data->search_by;
	        $text = $headers = [];
	        $i = 0;
	        if(!empty($search_by)) {
	            foreach ((array)$search_by as $key => $val) {
	            	if(is_array($val)) {
	            		$val = array_flatten($val);
	            		$text[] = $key."=".$val[0];
	            	} else {
	            		$text[] = $key."=".$val;
	            	}
	                
	                $i++; 
	            }
	        }    
	        $headers = implode('&', $text);

	        $pdf = new charge_category_mypdf("L","mm","A4");
			$pdf->SetMargins(2,6);
			$pdf->SetTextColor(100,100,100);
			$pdf->SetDrawColor(217, 217, 217);
			$pdf->AddPage();
			$pdf->AddFont('Calibri','','calibri.php');
			$pdf->SetFont('Calibri','',7);
			$grand_total = '';
	        self::BladeContent($charges_list, $total_arr,$pdf);
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
	        // $pdf->Output('D','Charge_Category_Report'.$created_date.'.pdf');
	        // $pdf->Output();
	    } catch(Exception $e) {
	    	\Log::info("Error Occured While export charge category report. Message:".$e->getMessage() );
	    }
		exit();
	}

	public function BladeContent($charges_list, $total_arr,$pdf){
		$temp = 0;
        $inc = 0;
        $total_arr = json_decode(json_encode($total_arr), true);
		foreach ($charges_list as $result) {
			$inc++;                        
	        $provider_id = $result->provider_id;
	        $provider_name = 'Rendering Provider - '.Provider::getProviderFullName(@$provider_id);
	        $pdf->AddFont('Calibri','','calibri.php');
			$pdf->SetFont('Calibri','',7);
			$pdf->SetTextColor(100, 100, 100);
	        if ($temp != $provider_id) {
				$c_width=(290);
				$c_height=5;// cell height

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$provider_name;
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
				$pdf->Ln();
	        }
	        $c_width=(290/8);
			$c_height=5;// cell height

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = @$result->procedure_category;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width+15,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = @$result->cpt_code;
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($result->description)? $result->description : '-Nil-' ;
			$lengthToSplit = "59";
			$pdf->Vcell($c_width+35,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($result->provider_short_name)? $result->provider_short_name : '-Nil-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width+15,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = !empty($result->units)? $result->units : '-Nil-';
			$lengthToSplit = strlen($text);
			$pdf->Vcell($c_width-30,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$result->charge,'',1);
			$lengthToSplit = strlen($text);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			} else {
				$pdf->SetTextColor(100, 100, 100);	
			}
			$pdf->Vcell($c_width+5,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

			/*$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$result->payment,'',1);
			$lengthToSplit = strlen($text);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			} else {
				$pdf->SetTextColor(100, 100, 100);	
			}
			$pdf->Vcell($c_width-5,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");*/

			$x_axis=$pdf->getx();// now get current pdf x axis value
			$text = Helpers::priceFormat(@$result->work_rvu,'',1);
			$lengthToSplit = strlen($text);
			if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			} else {
				$pdf->SetTextColor(100, 100, 100);	
			}
			$pdf->Vcell($c_width-5,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

			$pdf->Ln();

			if ($inc == @$total_arr[$provider_id]['last_rec'] + 1) {
				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = "Totals";
				$lengthToSplit = strlen($text);
				$pdf->SetFont('Calibri-Bold','',7);
				$pdf->Vcell($c_width+30,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = "";
				$lengthToSplit = strlen($text);
				$pdf->SetFont('Calibri','',7);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = "";
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width+35,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = "";
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = @$total_arr[$provider_id]['units'];
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width-40,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat(@$total_arr[$provider_id]['charge'],'',1);
				$lengthToSplit = strlen($text);
				if ($text < 0) {
					$pdf->SetTextColor(255, 0, 0);
				} else {
					$pdf->SetTextColor(100, 100, 100);	
				}
				$pdf->Vcell($c_width+15,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");

				/*$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat(@$total_arr[$provider_id]['payment'],'',1);
				$lengthToSplit = strlen($text);
				if ($text < 0) {
					$pdf->SetTextColor(255, 0, 0);
				} else {
					$pdf->SetTextColor(100, 100, 100);	
				}
				$pdf->Vcell($c_width-5,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");*/

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = Helpers::priceFormat(@$total_arr[$provider_id]['work_rvu'],'',1);
				$lengthToSplit = strlen($text);
				if ($text < 0) {
					$pdf->SetTextColor(255, 0, 0);
				} else {
					$pdf->SetTextColor(100, 100, 100);	
				}
				$pdf->Vcell($c_width-5,$c_height,$x_axis,@$text,$lengthToSplit,'', "R");
				$pdf->Ln();
			}
			$temp = $provider_id;

			/*$abb_rendering[] = @$result->provider_short_name." - ".@$result->provider_name;
	    	$abb_rendering = array_unique($abb_rendering);
			foreach (array_keys($abb_rendering, ' - ') as $key) {
	        	unset($abb_rendering[$key]);
	    	}*/
		}
		$pdf->Ln();
		/*$abbreviation = ['abb_rendering' => $abb_rendering];
		$abb_controller = New AbbreviationController;
		$abb_controller->abbreviation($abbreviation,$pdf);*/
	}
}

class charge_category_mypdf extends FPDF
{
    public function header(){
        $request = Request::All();
        $search_by = array();
        $start_date = $end_date = $dos_start_date =  $dos_end_date = $cpt_category = $cpt_code_id = $cpt_custom_type_from = $cpt_custom_type_to = $rendering_provider_id =  '';
        $charges_lists = $charges_list = $total_arr = [];
        
        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='transaction_date')) {
	        if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
	            $date = explode('-',$request['select_transaction_date']);            
	            $search_by['Transaction Date'][]= date("m/d/y",strtotime($date[0])).' to '.date("m/d/y",strtotime($date[1]));
	        }
	    }

        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='DOS')) {
	        if(isset($request['select_date_of_service']) && !empty($request['select_date_of_service'])){
	            $date = explode('-',$request['select_date_of_service']);
	            $dos_start_date = date("Y-m-d", strtotime($date[0]));
	            if($dos_start_date == '1970-01-01'){
	                $dos_start_date = '0000-00-00';
	            }
	            $dos_end_date = date("Y-m-d", strtotime($date[1]));
	            $search_by['DOS'][]= date("m/d/y",strtotime($dos_start_date)).' to '.date("m/d/y",strtotime($dos_end_date));
	        }
	    }

        if(!empty($request['cpt_category'])){
            $cpt_category = $request['cpt_category'];
            if($cpt_category != '0'){
                 $category_name = ProcedureCategory::where('id',$cpt_category)->where('status','Active')->pluck('procedure_category')->first();
            } else{
                $category_name = 'All';
            }
            $search_by['CPT/HCPCS Category'][] = $category_name;
        }

        if(!empty($request['cpt_type']) && $request['cpt_type'] == 'custom_type'){
            $search_by['CPT Type'][] = 'Custom Range';
            if(!empty($request['custom_type_from']) && !empty($request['custom_type_to'])){
                $cpt_custom_type_from = $request['custom_type_from'];
                $cpt_custom_type_to = $request['custom_type_to'];
            }
        }

        if(!empty($request['cpt_type']) && $request['cpt_type'] == 'cpt_code'){
            $search_by['CPT Type'][] = 'CPT Code';
            if(!empty($request['cpt_code_id'])){
                $cpt_code_id = $request['cpt_code_id'];
                $search_by['CPT Code'][] = $request['cpt_code_id'];
            }
        }

        if(!empty($request['rendering_provider_id'])){
            $rendering_provider_id = $request['rendering_provider_id'];
            foreach ((array)$rendering_provider_id as $id) {
                $value_name[] = Provider::getProviderFullName($id);
            }
            $search_render = array_unique($value_name);
            $search_by['Rendering Provider'][] = $search_render;
        }

		$this->SetFillColor(255, 255, 255);
		$this->SetTextColor(0, 135, 127);
	    $this->AddFont('Calibri-Bold','','calibrib.php');
	    $this->SetFont('Calibri-Bold','',10.5);
		$x_axis=$this->getx();
		$c_width = 295;
		$c_height = 0;
		$text = Practice::getPracticeName()." - Charge Category Report";
		$lengthToSplit = strlen($text);
		$this->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","C");

        $this->AddFont('Calibri','','calibri.php');
        $this->SetFont('Calibri','',9.5);
		$this->SetTextColor(100, 100, 100);
		$this->Ln();

		$i = 0;
		$text = [];
		foreach ((array)$search_by as $key => $val) {
			if(is_array($val)) 	
            	$val = array_flatten($val);
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

		$header = ['CPT/HCPCS Category', 'CPT/HCPCS', 'Description', 'Rendering Provider', 'Units', 'Charge Amt($)',  'Work RVU($)'];

		foreach ($header as $key => $value) {
			$this->SetFont('Calibri-Bold','',8);
			$this->SetTextColor(100, 100, 100);
			$x_axis=$this->getx();// now get current pdf x axis value
			$c_height = 6;// cell height
			$c_width = (290/count($header));// cell width 
			$lengthToSplit = strlen($value);
			if ($value == "Charge Amt($)" || $value == "Work RVU($)") {
				$align = "R";
				$c_width = $c_width-5;
			}
			if ($value == "Units") {
				$align = "L";
				$c_width = $c_width-30;
			}
			if ($value == "Description") {
				$align = "L";
				$c_width = $c_width+20;
			}
			if ($value == "CPT/HCPCS Category") {
				$align = "L";
				$c_width = $c_width+15;
			}
			if ($value == "Rendering Provider") {
				$align = "L";
				$c_width = $c_width+5;
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