<?php

namespace App\Http\Controllers\ExportPDF;

use DB;
use Carbon;
use Request;
use Auth;
use Session;
use FPDF;
use App\Models\Provider as Provider;
use App\Models\Medcubics\Cpt as Cpt;
use App\Http\Controllers\Controller;
use App\Models\Holdoption as Holdoption;
use App\Models\Practice as Practice;
use App\Models\Facility as Facility;
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
use App\Models\ReportExport as ReportExport;
use App\Http\Controllers\Reports\Api\ReportApiController as ReportApiController;
use App\Http\Controllers\ExportPDF\AbbreviationController as AbbreviationController;
use App\Http\Helpers\Helpers as Helpers;
$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);

class ChargeAnalysisPDFController extends Controller
{
    public function index(){
    	set_time_limit(300);
    	try {
	        $request = Request::All();
	    	$controller = New ReportApiController;
	    	$api_response = $controller->getChargesearchApiSP('pdf');
	    	$api_response_data = $api_response->getData();
	        $claims = $api_response_data->data->claims;
	        $include_cpt_option = $api_response_data->data->include_cpt_option;
	        $tot_summary = $api_response_data->data->tot_summary;
	        $header = (array)$api_response_data->data->header;
			
	        // Parameters for filter
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
			$filename = 'Charge_Analysis_Detailed_'.$created_date.'.pdf';
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
			//$report_export->report_file_name = 'Downloads/Charge_Analysis_Detailed_'.$created_date.'.pdf';
			$report_export->report_file_name = $filename;
			$report_export->report_type = $request['export'];
			$report_export->parameter = $headers;
			$report_export->report_controller_name = 'ReportController';
			$report_export->report_controller_func = 'chargesearchexport';
			$report_export->status = 'Inprocess';
			$report_export->created_by = $user_id;
			$report_export->save();
			$report_export_id = $report_export->id;

	        $pdf = new charge_analysis_mypdf("P","mm","A4");
			$pdf->SetMargins(4,6);
			$pdf->AddPage();
			$pdf->SetTextColor(100,100,100);
			$pdf->SetDrawColor(217, 217, 217);
			$pdf->SetFont('Times','',9);
			self::BladeContent($claims,$pdf, $tot_summary, $include_cpt_option);//write body content
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
	    	\Log::info("Error Occured While export charge Analysis report. Message:".$e->getMessage() );
	    }		
		exit();	
    }

    public function BladeContent($claims,$pdf,$tot_summary, $include_cpt_option)
    {
		
    	$count = 0;  $total_amt_bal = 0; $count_cpt =0; $claim_billed_total = 0; $claim_paid_total = 0; 
        $claim_bal_total = $total_claim = $total_cpt =  0; $claim_units_total = 0;  $claim_cpt_total = 0;
		$abb_facility = $abb_insurance = $abb_user = $abb_rendering = $abb_billing = $abb_pos = [];

		foreach ($claims as $claims_list) 
		{
			$set_title = (@$claims_list->title)? @$claims_list->title.". ":'';
    		$patient_name = $set_title.$claims_list->last_name .', '. $claims_list->first_name .' '. $claims_list->middle_name;

	    	$pdf->AddFont('Calibri-Bold','','calibrib.php');
		    $pdf->SetFont('Calibri-Bold','',7.5);
		    $pdf->SetFillColor(255, 255, 255);
			$x_axis=$pdf->getx();
			if(@$claims_list->claim_reference !=''){
	    		$pdf->Vcell(0,35,$x_axis,"",20,"L","L");
	    	}else{
	    		$pdf->Vcell(0,30,$x_axis,"",20,"L","L");
	    	}
	    	$pdf->Vcell(30,10,$x_axis,"Claim No",20,"TL","L");

	    	$pdf->AddFont('Calibri','','calibrib.php');
		    $pdf->SetFont('Calibri','',7.5);
			$x_axis=$pdf->getx();
			$text = !empty($claims_list->claim_number)? $claims_list->claim_number : '-Nil-';
	    	$pdf->Vcell(30,10,$x_axis,$text,20,"T","L");

		    $pdf->SetFont('Calibri-Bold','',7.5);
			$x_axis=$pdf->getx();
	    	$pdf->Vcell(30,10,$x_axis,"Acc No",20,"T","L");

		    $pdf->SetFont('Calibri','',7.5);
			$x_axis=$pdf->getx();
			$text = !empty($claims_list->account_no)? $claims_list->account_no : '-Nil-';
	    	$pdf->Vcell(30,10,$x_axis,$text,20,"T","L");

		    $pdf->SetFont('Calibri-Bold','',7.5);
			$x_axis=$pdf->getx();
	    	$pdf->Vcell(40,10,$x_axis,"Patient Name",20,"T","L");

		    $pdf->SetFont('Calibri','',7.5);
			$x_axis=$pdf->getx();
			if(@$claims_list->claim_reference !=''){
	    		$pdf->Vcell(0,35,150,"",20,"R","L");
			}else{
				$pdf->Vcell(0,30,150,"",20,"R","L");
			}
			$text = !empty($patient_name)? $patient_name : '-Nil-';
	    	$pdf->Vcell(42,10,$x_axis,$text,20,"TR","L");
	    	$pdf->Ln();

	    	$pdf->SetFont('Calibri-Bold','',7.5);
			$x_axis=$pdf->getx();
	    	$pdf->Vcell(30,0,$x_axis,"Billing",20,"","L");

		    $pdf->SetFont('Calibri','',7.5);
			$x_axis=$pdf->getx();
			$text = !empty($claims_list->billProvider_short_name)? $claims_list->billProvider_short_name : '-Nil-';
	    	$pdf->Vcell(30,0,$x_axis,@$text,20,"","");

		    $pdf->SetFont('Calibri-Bold','',7.5);
			$x_axis=$pdf->getx();
	    	$pdf->Vcell(30,0,$x_axis,"Rendering",20,"","L");

		    $pdf->SetFont('Calibri','',7.5);
			$x_axis=$pdf->getx();
			$text = !empty($claims_list->rendProvider_short_name)? $claims_list->rendProvider_short_name : '-Nil-';
	    	$pdf->Vcell(30,0,$x_axis,@$text,20,"","L");

		    $pdf->SetFont('Calibri-Bold','',7.5);
			$x_axis=$pdf->getx();
	    	$pdf->Vcell(40,0,$x_axis,"Facility",20,"","L");

		    $pdf->SetFont('Calibri','',7.5);
			$x_axis=$pdf->getx();
			$text = !empty($claims_list->facility_short_name)? $claims_list->facility_short_name : '-Nil-';
	    	$pdf->Vcell(45,0,$x_axis,@$text,20,"","L");
	    	$pdf->Ln();

	    	$pdf->SetFont('Calibri-Bold','',7.5);
			$x_axis=$pdf->getx();
	    	$pdf->Vcell(30,10,$x_axis,"Responsibility",20,"L","L");

		    $pdf->SetFont('Calibri','',7.5);
			$x_axis=$pdf->getx();
			if($claims_list->self_pay=="Yes"){
	    		$pdf->Vcell(30,10,$x_axis,"Self",20,"","L");
			}else{
				$text = !empty($claims_list->insurance_short_name)? $claims_list->insurance_short_name : '-Nil-';
				$pdf->Vcell(30,10,$x_axis,@$text,20,"","L");
			}

		    $pdf->SetFont('Calibri-Bold','',7.5);
			$x_axis=$pdf->getx();
	    	$pdf->Vcell(30,10,$x_axis,"User",20,"","L");

		    $pdf->SetFont('Calibri','',7.5);
			$x_axis=$pdf->getx();
			if($claims_list->created_by != 0 && Helpers::user_names(@$claims_list->created_by) ){
	    		$pdf->Vcell(30,10,$x_axis,Helpers::user_names(@$claims_list->created_by),30,"","L");
			}else{
				$pdf->Vcell(30,10,$x_axis,"-Nil-",20,"","L");
			}

		    $pdf->SetFont('Calibri-Bold','',7.5);
			$x_axis=$pdf->getx();
	    	$pdf->Vcell(40,10,$x_axis,"Entry Date",20,"","L");

		    $pdf->SetFont('Calibri','',7.5);
			$x_axis=$pdf->getx();
			if(@$claims_list->entry_date != "0000-00-00" && $claims_list->entry_date != "1970-01-01" && !empty($claims_list->entry_date)){
	    		$pdf->Vcell(45,10,$x_axis,@$claims_list->entry_date,20,"","L");
			}else{
				$pdf->Vcell(45,10,$x_axis,'-Nil-',20,"","L");
			}
	    	$pdf->Ln();

	    	$pdf->SetFont('Calibri-Bold','',7.5);
			$x_axis=$pdf->getx();
	    	$pdf->Vcell(30,0,$x_axis,"POS",20,"","L");

		    $pdf->SetFont('Calibri','',7.5);
			$x_axis=$pdf->getx();
			$text = !empty($claims_list->code)? $claims_list->code : '-Nil-';
	    	$pdf->Vcell(30,0,$x_axis,@$text,20,"","L");

		    $pdf->SetFont('Calibri-Bold','',7.5);
			$x_axis=$pdf->getx();
	    	$pdf->Vcell(30,0,$x_axis,"Status",20,"","L");

		    $pdf->SetFont('Calibri','',7.5);
			$x_axis=$pdf->getx();
			$text = !empty($claims_list->status)? $claims_list->status : '-Nil-';
	    	$pdf->Vcell(30,0,$x_axis,@$text,20,"","L");

	    	$pdf->SetFont('Calibri-Bold','',7.5);
			$x_axis=$pdf->getx();
	    	$pdf->Vcell(40,0,$x_axis,"Sub Status",20,"","L");

		    $pdf->SetFont('Calibri','',7.5);
			$x_axis=$pdf->getx();
			if(isset($claims_list->sub_status_desc) && $claims_list->sub_status_desc !== null){
				$text = $claims_list->sub_status_desc;
			}else{
				$text = '-Nil-';
			}
	    	$pdf->Vcell(46,0,$x_axis,$text,30,"","L");
	    	$pdf->Ln();

		    $pdf->SetFont('Calibri-Bold','',7.5);
			$x_axis=$pdf->getx();
	    	$pdf->Vcell(30,10,$x_axis,"Insurance Type",20,"","L");

		    $pdf->SetFont('Calibri','',7.5);
			$x_axis=$pdf->getx();
			$text = !empty($claims_list->type_name)? $claims_list->type_name :'-Nil-';
	    	$pdf->Vcell(30,10,$x_axis,@$text,30,"","L");
	    	// $pdf->Ln();
                
                $pdf->SetFont('Calibri-Bold','',7.5);
			$x_axis=$pdf->getx();
	    	$pdf->Vcell(30,10,$x_axis,"Policy ID",20,"","L");

		    $pdf->SetFont('Calibri','',7.5);
			$x_axis=$pdf->getx();
			$text = !empty($claims_list->policy_id)? $claims_list->policy_id :'-Nil-';
	    	$pdf->Vcell(30,10,$x_axis,@$text,20,"","L");

	    	if(@$claims_list->claim_reference !='' && isset($claims_list->option_reason) && $claims_list->option_reason != ''){
	    		$pdf->SetFont('Calibri-Bold','',7.5);
				$x_axis=$pdf->getx();
		    	$pdf->Vcell(30,10,$x_axis,"Reference",20,"","L");	
		    	$pdf->SetFont('Calibri','',7.5);
				$x_axis=$pdf->getx();
				$text = !empty($claims_list->claim_reference)? $claims_list->claim_reference : '-Nil-';
				$pdf->Vcell(20,10,$x_axis,@$text,20,"","L");

				$pdf->SetFont('Calibri-Bold','',7.5);
				$x_axis=$pdf->getx();
		    	$pdf->Vcell(20,10,$x_axis,"Hold Reason",15,"","L");	
		    	$pdf->SetFont('Calibri','',7.5);
				$x_axis=$pdf->getx();
				$text = !empty($claims_list->option_reason)? $claims_list->option_reason : '-Nil-';
				$pdf->Vcell(15,10,$x_axis,@$text,15,"","L");	
				$pdf->Ln();

				$x_axis=$pdf->getx();
	    		$pdf->MultiCell(205, 0, "",'', "L");
	    	}
	    	elseif (isset($claims_list->claim_reference) && $claims_list->claim_reference != '') {
	    		$pdf->SetFont('Calibri-Bold','',7.5);
				$x_axis=$pdf->getx();
		    	$pdf->Vcell(30,10,$x_axis,"Reference",20,"","L");	
		    	$pdf->SetFont('Calibri','',7.5);
				$x_axis=$pdf->getx();
				$text = !empty($claims_list->claim_reference)? $claims_list->claim_reference : '-Nil-';
				$pdf->Vcell(20,10,$x_axis,@$text,20,"","L");
				$pdf->Ln();

				$x_axis=$pdf->getx();
	    		$pdf->MultiCell(205, 0, "",'', "L");
	    	}
	    	elseif (isset($claims_list->option_reason) && $claims_list->option_reason != '') {
	    		$pdf->SetFont('Calibri-Bold','',7.5);
				$x_axis=$pdf->getx();
		    	$pdf->Vcell(20,10,$x_axis,"Hold Reason",20,"","L");	
		    	$pdf->SetFont('Calibri','',7.5);
				$x_axis=$pdf->getx();
				$text = !empty($claims_list->option_reason)? $claims_list->option_reason : '-Nil-';
				$pdf->Vcell(20,10,$x_axis,@$text,100,"","L");	
				$pdf->Ln();

				$x_axis=$pdf->getx();
	    		$pdf->MultiCell(205, 0, "",'', "L");
	    	}
	    	else{
	    		$x_axis=$pdf->getx();
	    		$pdf->MultiCell(205, 4, "",'', "L");
	    	}
	   //  	if(isset($claims_list->option)){
	   //  		$pdf->SetFont('Calibri-Bold','',7.5);
				// $x_axis=$pdf->getx();
		  //   	$pdf->Vcell(30,10,$x_axis,"Hold Reason",20,"","L");	
		  //   	$pdf->SetFont('Calibri','',7.5);
				// $x_axis=$pdf->getx();
				// $pdf->Vcell(30,10,$x_axis,@$claims_list->option,20,"","L");	
				// $pdf->Ln();
				// $x_axis=$pdf->getx();
	   //  		$pdf->MultiCell(205, 0, "",'', "L");
	   //  	}

			$x_axis=$pdf->getx();
	    	$pdf->SetFillColor(232, 232, 232);
			$pdf->SetFont('Calibri-Bold','',7.5);
	    	$pdf->Vcell(30,4,($x_axis+1),"DOS",20,"","L",1);
	    	$x_axis=$pdf->getx();
	    	$pdf->Vcell(20,4,($x_axis-1),"CPT",20,"","L",1);
	    	if (!empty($include_cpt_option)) {
		    	$include_cpt_array = is_string($include_cpt_option) ? explode(',', $include_cpt_option) : $include_cpt_option;
	    	}
	    	$include_cpt_array = (isset($include_cpt_array))? $include_cpt_array : [];
	    	$include_cpt_count = (isset($include_cpt_array))? count($include_cpt_array) : "";

    		if ($include_cpt_count == 1) {
	    		$c_width = 50;
    		}elseif ($include_cpt_count == 2) {
    			$c_width = 38;
    		}elseif ($include_cpt_count == 3) {
    			$c_width = 30;
    		}else{
    			$c_width = 50;
    		}

	    	if (in_array('include_cpt_description',$include_cpt_array)) {
	    		$x_axis=$pdf->getx();
	    		$pdf->Vcell($c_width,4,$x_axis,"CPT Description",20,"","L",1);
	    	}
	    	if (in_array('include_modifiers',$include_cpt_array)) {
	    		$x_axis=$pdf->getx();
	    		$pdf->Vcell($c_width,4,$x_axis,"Modifiers",20,"","L",1);
	    	}
	    	if (in_array('include_icd',$include_cpt_array)) {
	    		$x_axis=$pdf->getx();
	    		$pdf->Vcell($c_width,4,$x_axis,"ICD-10",20,"","L",1);
	    	}	
	    	$x_axis=$pdf->getx();
	    	$total_x_axis = $x_axis;
	    	$pdf->Vcell($c_width,4,$x_axis,"Units",20,"","L",1);
	    	$x_axis=$pdf->getx();
	    	/*$units_x_axis = $x_axis;
	    	$pdf->Vcell($c_width,4,$x_axis,"Charges($)",20,"","R",1);
	    	$x_axis=$pdf->getx();
	    	$pdf->Vcell($c_width,4,$x_axis,"Paid($)",20,"","R",1);
	    	$x_axis=$pdf->getx();
*/

			$units_x_axis = $x_axis;
	    	$pdf->Vcell($c_width,4,$x_axis,"Charges($)",20,"","R",1);
	    	$x_axis=$pdf->getx();
	    	$pdf->Vcell($c_width,4,$x_axis,"Paid($)",20,"","R",1);
	    	$x_axis=$pdf->getx();

			
	    	/* $pdf->Vcell($c_width,4,$x_axis,"Total Bal($)",20,"","R",1); */
	    	$pdf->Ln();

	    	$dos = $cpt = $cpt_description = $modifier1 = $modifier2 = $modifier3 = $modifier4 = $icd_10 =  $charges = $paid = $total_bal = $claim_cpt = '';
	    		// $claim_units_total = $units = 0;
            if(isset($claims_list->claim_dos_list) && $claims_list->claim_dos_list != '') {
                $claim_line_item = explode("^^", $claims_list->claim_dos_list);

                foreach($claim_line_item as $claim_line_item_val){
                    if($claim_line_item_val != ''){
                        $line_item_list = explode("$$", $claim_line_item_val);
						$claim_cpt = $line_item_list[0];
                        if(($line_item_list[0]) != ''){
                            $dos       = @$line_item_list[1];
                            $cpt       = @$line_item_list[2];
                            $cpt_description = @$line_item_list[3];
                            $modifier1 = @$line_item_list[4];
                            $modifier2 = @$line_item_list[5];
                            $modifier3 = @$line_item_list[6];
                            $modifier4 = @$line_item_list[7];
                            $icd_10    = @$line_item_list[8];
                            $units     = @$line_item_list[9];
                            $charges   = @$line_item_list[10];
                            $paid      = @$line_item_list[11];
							
                            //$total_bal = @$line_item_list[12];                                                
                        }
                    }
					
			    	$x_axis=$pdf->getx();
					$pdf->SetFont('Calibri','',7.5);
					$text = !empty($dos)? $dos : '-Nil-';
			    	$pdf->Vcell(30,5,($x_axis),$text,20,"L","L",0);
			    	$x_axis=$pdf->getx();
			    	$text = !empty($cpt)? $cpt : '-Nil-';
			    	$pdf->Vcell(20,5,($x_axis),$text,20,"","L",0);
			    	if (in_array('include_cpt_description',$include_cpt_array)) {
			    		$x_axis=$pdf->getx();
			    		$text = !empty($cpt_description)? $cpt_description : '-Nil-';
			    		$pdf->Vcell($c_width,4,$x_axis,$text,18,"","L",0);
			    	}
			    	if (in_array('include_modifiers',$include_cpt_array)) {
			    		$modifier_arr = array();
                            if ($modifier1 != '')
                                array_push($modifier_arr, $modifier1);
                            if ($modifier2 != '')
                                array_push($modifier_arr, $modifier2);
                            if ($modifier3 != '')
                                array_push($modifier_arr, $modifier3);
                            if ($modifier4 != '')
                                array_push($modifier_arr, $modifier4);
                            if (count($modifier_arr) > 0) {
                                $modifier_val = implode($modifier_arr, ',');
                            } else {
                                $modifier_val = '-Nil-';
                            }
			    		$x_axis=$pdf->getx();
			    		$text = !empty($modifier_val)? $modifier_val : '-Nil-';
			    		$pdf->Vcell($c_width,4,$x_axis,@$text,20,"","L",0);
			    	}
			    	$exp = explode(',', $icd_10);
			    	if (in_array('include_icd',$include_cpt_array)) {
			    		for($i=0; $i<12;$i++){
			    			$x_axis=$pdf->getx();
			    			$exp_icd[] = @$exp[$i];
			    		}
			    		foreach (array_keys($exp_icd, null) as $key) {
			    			unset($exp_icd[$key]);
			    		}
		    			$imp_icd = implode(' ',@$exp_icd);
		    			$text = !empty($imp_icd)? $imp_icd : '-Nil-';
		    			$pdf->Vcell($c_width,4,$x_axis,@$text,15,"","L",0);
		    			$exp_icd = [];
			    	}

			    	$x_axis=$pdf->getx();
			    	$pdf->Vcell($c_width,5,$x_axis,$units,20,"","L",0);
			    	$x_axis=$pdf->getx();
			    	$text = !empty($charges)? $charges : '-Nil-';
			    	$pdf->Vcell($c_width,5,$x_axis,Helpers::priceFormat(@$text,'',1) ,20,"","R",0);
			    	$x_axis=$pdf->getx();
			    	$text = !empty($paid)? $paid :'-Nil-';					
			    	$pdf->Vcell($c_width+1,5,$x_axis,Helpers::priceFormat(@$text,'',1),20,"","R",0);

			    	$x_axis=$pdf->getx();
			    	$text = !empty($total_bal)? $total_bal : '0.00';
			    	//$pdf->Vcell($c_width+1,5,$x_axis,Helpers::priceFormat(@$text,'',1),20,"R","R",0);
			    	$pdf->Ln();

			    	$claim_billed_total += (is_numeric($charges) && !empty($charges)) ? $charges : 0;
		            $claim_paid_total += (is_numeric($paid) && !empty($paid)) ? $paid : 0;
		            $claim_bal_total += (is_numeric($total_bal) && !empty($total_bal)) ? $total_bal : 0;
		            //\Log::info("Tot::".$claim_units_total." ###".$units."##");
		            $claim_units_total += (is_numeric($units) && !empty($units)) ? $units : 0; 
		            //\Log::info("TOT::".$claim_cpt_total."##".count((array)$claim_cpt));
		            $claim_cpt_total += count((array)$claim_cpt); 

		        }
	    	}
	    	$pdf->SetFont('Times','B',7);
	    	$x_axis=$pdf->getx();
	    	$pdf->Vcell(0,5,$x_axis,"",20,"L","R",0);
	    
	    	$x_axis=$total_x_axis;
	    	$pdf->Vcell($c_width,5,$x_axis,"Total",20,"","L",0);
	    	$x_axis=$units_x_axis;
	    	if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			else{
				$pdf->SetTextColor(100, 100, 100);	
			}
	    	$text = !empty($claim_billed_total)? Helpers::priceFormat(@$claim_billed_total,'',1):'-Nil-';
	    	$pdf->Vcell($c_width,5,$x_axis,$text,20,"","R",0);
	    	$claim_billed_total = 0;
	    	$x_axis=$pdf->getx();
	    	$text = !empty($claim_paid_total)? Helpers::priceFormat(@$claim_paid_total,'',1):'0.00';
	    	if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			else{
				$pdf->SetTextColor(100, 100, 100);	
			}
	    	$pdf->Vcell($c_width+1,5,$x_axis,$text,20,"","R",0);
	    	$claim_paid_total = 0;
	    	$x_axis=$pdf->getx();
	    	$text = !empty($claim_bal_total)? Helpers::priceFormat(@$claim_bal_total,'',1):'0.00';
	    	if ($text < 0) {
				$pdf->SetTextColor(255, 0, 0);
			}
			else{
				$pdf->SetTextColor(100, 100, 100);	
			}
	    	//$pdf->Vcell($c_width+1,5,$x_axis,$text,20,"R","R",0);
	    	$pdf->SetTextColor(100, 100, 100);
	    	$claim_bal_total = 0;
	    	$pdf->Ln();
		    $x_axis=$pdf->getx();
	    	$pdf->Vcell(202,0,$x_axis,"",20,"B","L",0);
	    	$pdf->Ln();
    		$count++;

    		$abb_billing[] = @$claims_list->billProvider_short_name." - ".@$claims_list->billProvider_name;
    		$abb_billing = array_unique($abb_billing);
    		foreach (array_keys($abb_billing, ' - ') as $key) {
            	unset($abb_billing[$key]);
        	}

        	$abb_rendering[] = @$claims_list->rendProvider_short_name." - ".@$claims_list->rendProvider_name;
        	$abb_rendering = array_unique($abb_rendering);
    		foreach (array_keys($abb_rendering, ' - ') as $key) {
            	unset($abb_rendering[$key]);
        	}
			
        	$abb_facility[] = @$claims_list->facility_short_name." - ".@$claims_list->facility_name;
        	$abb_facility = array_unique($abb_facility);
    		foreach (array_keys($abb_facility, ' - ') as $key) {
            	unset($abb_facility[$key]);
        	}

        	$abb_insurance[] = @$claims_list->insurance_short_name." - ".@$claims_list->insurance_name;
        	$abb_insurance = array_unique($abb_insurance);
    		foreach (array_keys($abb_insurance, ' - ') as $key) {
            	unset($abb_insurance[$key]);
        	}

        	$abb_user[] = @$user_names[@$claims_list->created_by]." - ".@$claims_list->user->name;
        	$abb_user = array_unique($abb_user);
    		foreach (array_keys($abb_user, ' - ') as $key) {
            	unset($abb_user[$key]);
        	}

        	$abb_pos[] = @$claims_list->code." - ".@$claims_list->pos;
        	$abb_pos = array_unique($abb_pos);
    		foreach (array_keys($abb_pos, ' - ') as $key) {
            	unset($abb_pos[$key]);
        	}
		}

		//Suammry Content Write
		self::SummaryContent($tot_summary, $pdf, $claim_units_total, $claim_cpt_total);

		//Abbreviation Content
		$abbreviation = ['abb_facility' => $abb_facility, 'abb_rendering' => $abb_rendering, 'abb_billing' => $abb_billing, 'abb_insurance' => $abb_insurance, 'abb_user' => $abb_user, 'abb_pos' => $abb_pos];
		$abb_controller = New AbbreviationController;
		$orie = 'portrait';
		$abb_controller->abbreviation($abbreviation,$pdf,$orie);
    }

    public function SummaryContent($tot_summary,$pdf, $claim_units_total, $claim_cpt_total)
    {
    	$x_axis = $pdf->getx();
    	$pdf->SetTextColor(240, 125, 8);
    	$pdf->SetFont('Calibri-Bold','',8);
    	$pdf->Vcell(30,10,$x_axis,"Summary",20,"","L");
    	$pdf->Ln();
    	$x_axis=$pdf->getx();
    	$pdf->Vcell(0,30,$x_axis,"",20,"L","L");
    	$pdf->SetTextColor(0, 135, 127);
    	$pdf->SetFont('Calibri-Bold','',8);
 		$pdf->Vcell(50,10,$x_axis,"Counts",20,"T","R");
 		$x_axis=$pdf->getx();
 		$pdf->Vcell(40,30,$x_axis,"",20,"R","L");
 		$pdf->Vcell(40,10,$x_axis,"Values($)",20,"T","R");
 		$pdf->Ln();

 		$x_axis=$pdf->getx();
    	$pdf->SetTextColor(100,100,100);
    	$pdf->SetFont('Calibri-Bold','',7.5);
 		$pdf->Vcell(40,5,$x_axis,"Total Patients",20,"","L");
 		$x_axis=$pdf->getx();
    	$pdf->SetFont('Calibri','',7.5);
 		$pdf->Vcell(10,5,$x_axis,$tot_summary->total_patient,20,"","L");
 		$x_axis=$pdf->getx();
 		$pdf->Vcell(40,5,$x_axis,Helpers::priceFormat($tot_summary->total_charge,'',1),20,"","R");
 		$pdf->Ln();

 		$x_axis=$pdf->getx();
    	$pdf->SetTextColor(100,100,100);
    	$pdf->SetFont('Calibri-Bold','',7.5);
 		$pdf->Vcell(40,5,$x_axis,"Total CPT",20,"","L");
 		$x_axis=$pdf->getx();
    	$pdf->SetFont('Calibri','',7.5);
 		$pdf->Vcell(10,5,$x_axis,$claim_cpt_total,20,"","L");
 		$x_axis=$pdf->getx();
 		$pdf->Vcell(40,5,$x_axis,Helpers::priceFormat($tot_summary->total_charge,'',1),20,"","R");
 		$pdf->Ln();

 		$x_axis=$pdf->getx();
    	$pdf->SetTextColor(100,100,100);
    	$pdf->SetFont('Calibri-Bold','',7.5);
 		$pdf->Vcell(40,5,$x_axis,"Total Units",20,"","L");
 		$x_axis=$pdf->getx();
    	$pdf->SetFont('Calibri','',7.5);
 		$pdf->Vcell(10,5,$x_axis,@$claim_units_total,20,"","L");
 		$x_axis=$pdf->getx();
 		$pdf->Vcell(40,5,$x_axis,Helpers::priceFormat($tot_summary->total_charge,'',1),20,"","R");
 		$pdf->Ln();

 		$x_axis=$pdf->getx();
    	$pdf->SetTextColor(100,100,100);
    	$pdf->SetFont('Calibri-Bold','',7.5);
 		$pdf->Vcell(40,5,$x_axis,"Total Charges",20,"","L");
 		$x_axis=$pdf->getx();
    	$pdf->SetFont('Calibri','',7.5);
 		$pdf->Vcell(10,5,$x_axis,$tot_summary->total_claim,20,"","L");
 		$x_axis=$pdf->getx();
 		$pdf->Vcell(40,5,$x_axis,Helpers::priceFormat($tot_summary->total_charge,'',1),20,"","R");
 		$pdf->Ln();
 		$x_axis=$pdf->getx();
 		$pdf->Vcell(90,0,$x_axis,"",20,"B","L");
 		$pdf->Ln();
    }
}

    class charge_analysis_mypdf extends FPDF {
        public function header(){
            $request = Request::all();
            
            if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
                $exp = explode("-",$request['select_transaction_date']);
                $start_date = str_replace('"', '', $exp[0]);
                $end_date = str_replace('"', '', $exp[1]);
                $search_by["Transaction Date"][] = date("m/d/y",strtotime($start_date)) . "  To " . date("m/d/y",strtotime($end_date));                
            }
            
            if(isset($request['select_date_of_service']) && !empty($request['select_date_of_service'])){
                $exp = explode("-",$request['select_date_of_service']);
                $start_date = str_replace('"', '', $exp[0]);
                $end_date = str_replace('"', '', $exp[1]);
                $search_by["Date Of Service"][] = date("m/d/Y",strtotime($start_date)) . "  To " . date("m/d/Y",strtotime($end_date));
            }
            if(isset($request['status_option']) && !empty($request['status_option'])) {
                $statusArr =  explode(',', $request["status_option"]);
                $search_by["Status"][] = is_array($statusArr) ? implode(",", $statusArr) : $statusArr;
                if (in_array("Hold", $statusArr)) {
                    $search_by["Hold Reason"][] = 'All';
                    if(isset($request['hold_reason']) && !empty($request['hold_reason'])) { 
                        $holdReasonArr = Holdoption::whereIn('id',explode(',',$request['hold_reason']))->pluck('option')->toArray();
                        $search_by["Hold Reason"][] = is_array($holdReasonArr) ? implode(",", $holdReasonArr) : $holdReasonArr; 
                    }
                }
            }
            if (isset($request["billing_provider_id"]) && !empty($request['billing_provider_id'])) {
                $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name");
                $provider= $provider->whereIn('id', explode(',', $request["billing_provider_id"]))->get()->toArray();
                $search_by["Billing Provider"][] =  @array_flatten($provider)[0];
            }
            if (isset($request["rendering_provider_id"]) && !empty($request['rendering_provider_id'])) {
                $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name")->whereIn('id', explode(',', $request['rendering_provider_id']))->get()->toArray();
                $search_by["Rendering Provider"][] =  @array_flatten($provider)[0];
            }
            if (isset($request['facility_id']) && !empty($request['facility_id'])) {
                $facility = Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ', ') as facility_name")->whereIn('id', explode(',', $request["facility_id"]))->get()->toArray();
                $search_by["Facility Name"][] =  @array_flatten($facility)[0];
            }
            if(isset($request['insurance_charge'])){
                if ($request['insurance_charge'] == 'self') {
                    $search_by["Payer"][] ="Self Pay";
                }
                if ($request['insurance_charge'] == 'insurance') {
                    if((isset($request['insurance_id']) && is_array($request['insurance_id']) && array_sum($request['insurance_id'])!=0)) {
                        if(is_array($request['insurance_id']) && array_sum($request['insurance_id'])!=0 || isset($request['export']) || is_string($request["insurance_id"]))   {
                            $insurance_id = $request["insurance_id"];
                            $insurance_name = Insurance::selectRaw("GROUP_CONCAT(insurance_name SEPARATOR ', ') as insurance_name")->whereIn('id', $insurance_id)->get()->toArray();
                            $hide_col["insurance"] = 1;
                            $search_by["Insurance"][] = @array_flatten($insurance_name)[0];                            
                        }                        
                    }else{
                        $search_by["Insurance"][] = 'All';
                    }
                    $search_by["Payer"][] ="Insurance Only";
                }
                if ($request['insurance_charge'] == 'all') {
                    $search_by["Payer"][] ="All";
                }
            }
            if(isset($request['reference']))
                if ($request['reference'] != '') {
                    $search_by["Reference"][] =$request['reference'];
                }
            if(isset($request["created_by"]) && $request["created_by"] !='') {
                if($request["created_by"] != "") {
                    $user = (isset($request['export']) || is_string($request['created_by'])) ? explode(',',$request['created_by']):$request['created_by'];
                    $short_name = DB::connection('responsive')->table('users')
                            ->whereIn('id',$user)
                            ->pluck('short_name')->all();
                    $search_by["User"][] = (in_array("0", $user))? ("All".(isset($user[1])? "," :"" ).implode(',',$short_name)) : implode(',',$short_name);
                }
            }
		
	    $this->SetFillColor(255, 255, 255);
            $this->SetTextColor(0, 135, 127);
	    $this->AddFont('Calibri-Bold','','calibrib.php');
	    $this->SetFont('Calibri-Bold','',10);
		$x_axis=$this->getx();
		$c_width = 210;
		$c_height = 0;
		$text = Practice::getPracticeName()." - Charge Analysis - Detailed";
		$lengthToSplit = strlen($text);
		$this->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","C");

		$i = 0;
		$text = [];
		foreach ((array)$search_by as $key => $val) {
			$text[] = $key.":".@$val[0];                           
            $i++; 
		}
		$text_imp = implode(" | ", $text);
		// $text_imp = "Transaction Date : 10/02/19 To 10/04/19 | Payer : All";
		$lengthToSplit = 140;
        $this->AddFont('Calibri','','calibri.php');
        $this->SetFont('Calibri','',9);
		$this->SetTextColor(100, 100, 100);
		$this->Ln();
		$this->Vcell(210,12,$x_axis,$text_imp,$lengthToSplit,"","C");

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
		$this->Vcell(150,10,$x_axis,$text,$lengthToSplit,"","R");

		$x_axis=$this->getx();
		$text = Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y');
		$lengthToSplit = strlen($text);
        $this->SetFont('Times','B',7.5);
		$this->SetTextColor(0, 135, 127);
		$this->Vcell(10,10,$x_axis,$text,$lengthToSplit,"","L");

		$this->Ln();
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