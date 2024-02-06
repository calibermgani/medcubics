<?php
namespace App\Http\Controllers\ExportPDF;

use DB;
use Carbon;
use Request;
use Auth;
use Session;
use FPDF;
use App\Models\Icd as Icd;
use App\Models\Medcubics\Cpt as Cpt;
use App\Models\Practice as Practice;
use App\Http\Controllers\Controller;
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
use App\Models\ReportExport as ReportExport;
use App\Http\Controllers\Reports\CollectionController as CollectionController;
use App\Http\Controllers\Reports\Api\ReportApiController as ReportApiController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;

$path = public_path() . '/fpdf/font/';
define('FPDF_FONTPATH',$path);

class PaymentAnalysisDetailedPDFController extends Controller
{
	public $count_header='';

	public function index(){
		set_time_limit(300);
        $request = Request::All();
        $controller = New ReportApiController;
        $api_response = $controller->getPaymentSearchApi('pdf');
        $api_response_data = $api_response->getData();
        $header = $api_response_data->data->header;
        $dataArr = $api_response_data->data->dataArr;
        // self::$headers_ = $header;
        $column = $api_response_data->data->column;
        // self::$column_ = $column;

        $h_payer = $header->Payer;
        $payments = $api_response_data->data->payments;
        $p_payments_count1 = count((array)$payments)+10;
        $p_payments_count2 = count((array)$payments)+14;
        $p_payments_count_var = "C".$p_payments_count1.":"."C".$p_payments_count2;
        $i_payments_count1 = count((array)$payments)+10;
        $i_payments_count2 = count((array)$payments)+19;
        $i_payments_count_var = "C".$i_payments_count1.":"."C".$i_payments_count2;
        $totalRec = 0;
        $paymentCptRowCount = $totalRec;
        $paymentCptRowCount1 = $paymentCptRowCount+10;
        $paymentCptRowCount2 = $paymentCptRowCount+18;
        $paymentCptRowCountVar = "C".$paymentCptRowCount1.":"."C".$paymentCptRowCount2;
        $page = $api_response_data->data->page;
        $dataArr = $api_response_data->data->dataArr;
        $date = Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $patient_wallet_balance = $api_response_data->data->patient_wallet_balance;

		$text = [];
		foreach ((array)$header as $key => $val) {
			$text[] = $key.":".@$val;                           
		}
		$headers = implode(" | ", $text);

		$created_date = Helpers::timezone(date("m/d/y H:i:s"), 'm-d-y-H-i-s');
		$filename = 'Downloads/Payment_Analysis_detailed'.$created_date.'.pdf';
		$url = Request::url();
		$user_id = Auth()->user()->id;
		$report_exoprt = new ReportExport;
		$practice_id = Session::get('practice_dbid');
		$report_exoprt->practice_id = $practice_id;
		$report_exoprt->report_name = $request['report_name'];
		$report_exoprt->report_url = $url;
		$report_exoprt->parameter = $headers;
		$report_exoprt->report_type = $request['export'];
		$report_exoprt->report_file_name = 'Downloads/Payment_Analysis_detailed'.$created_date.'.pdf';
		$report_exoprt->report_controller_name = 'ReportApiController';
		$report_exoprt->report_controller_func = 'getPaymentSearchApi';
		$report_exoprt->status = 'Inprocess';
		$report_exoprt->created_by = $user_id;
		$report_exoprt->save();
		$report_exoprt_id = $report_exoprt->id;

        $pdf = new Payment_Analysis_detailed("L","mm",array(480,100));
		$pdf->SetMargins(2,6);
		$pdf->SetTextColor(100,100,100);
		$pdf->SetDrawColor(217, 217, 217);
		$pdf->AddPage();
		$pdf->AddFont('Calibri','','calibri.php');
		$pdf->SetFont('Calibri','',7);
		$grand_total = '';
		
        self::BladeContent($payments, $pdf, $header, $column, $dataArr);
        $pdf->Output($filename,'F');

        $report_exoprt->update(['status'=>'Pending']);
        $pdf->Output('D','Patient_Insurance_Payment_'.$created_date.'.pdf');
        // $pdf->Output();
		exit();
	}

    public function BladeContent($payments, $pdf, $header, $column, $dataArr){
    	$total_ins = 0;
        $total_pat = 0;
        $claim_paid = [];
        if(!empty($payments)){
        	foreach($payments as $key => $payments_list){
        		$claim = @$payments_list->claim;
                $patient = @$payments_list->claim_patient_det;
                $check_details = @$payments_list->pmt_info->check_details;
                $eft_details = @$payments_list->pmt_info->eft_details;
                $creditCardDetails = @$payments_list->pmt_info->credit_card_details;
                if ($header->Payer == "Insurance Only") {
                    $payment_info = @$payments_list->pmt_info;
                } elseif ($header->Payer == "Patient Payments") {
                    $patient = @$payments_list->patient;
                    $check_details = @$payments_list->check_details;
                    $eft_details = @$payments_list->eft_details;
                    $creditCardDetails = @$payments_list->credit_card_details;
                    $payment_info = $payments_list;
                } else {
                    $payment_info = $payments_list->pmt_info;
                }
                $set_title = (@$patient->title) ? @$patient->title . ". " : '';
                $patient_name = $set_title . "" . Helpers::getNameformat(@$patient->last_name, @$patient->first_name, @$patient->middle_name);
                if ($header->Payer == "Insurance Only") {
                    $claim = @$payments_list;
                    $patient = @$payments_list;
                    $check_details = @$payments_list;
                    $eft_details = @$payments_list;
                    $creditCardDetails = @$payments_list;
                    $payment_info = $payments_list;
                    $set_title = (@$patient->title) ? @$patient->title . ". " : '';
                    $patient_name = $set_title . "" . Helpers::getNameformat(@$patient->last_name, @$patient->first_name, @$patient->middle_name);
                }

                logger('claim no - '.@$claim->claim_number.' ## created at Log - '.@$payments_list->created_at);

				$pdf->AddFont('Calibri','','calibri.php');
			    $pdf->SetFont('Calibri','',7);
				$pdf->SetTextColor(100, 100, 100);
				$c_width=(480/$pdf->count_header);
				$c_height=5;// cell height

				$x_axis=$pdf->getx();// now get current pdf x axis value
				$text = (!empty($payments_list->created_at))? Helpers::timezone(@$payments_list->created_at, 'm/d/y'): '-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				if(@$patient->account_no != ''){
					$x_axis = $pdf->getx();// now get current pdf x axis value
					$text = (!empty($patient->account_no))? @$patient->account_no: '-Nil-';
					$lengthToSplit = strlen($text);
					if ($pdf->count_header > 10) {
						$c_width = $c_width-8;
					}
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
				}else{
					$x_axis = $pdf->getx();// now get current pdf x axis value
					$text = '-Nil-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
				}

				if($patient_name != ''){
					$x_axis = $pdf->getx();// now get current pdf x axis value
					$text = @$patient_name;
					$lengthToSplit = strlen($text);
					if ($pdf->count_header > 10) {
						$c_width = $c_width + 16;
					}
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
					$c_width=(480/$pdf->count_header);
				}else{
					$x_axis = $pdf->getx();// now get current pdf x axis value
					$text = '-Nil-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
				}

				if($header->Payer=="Insurance Only" || $header->Payer=="Patient Payments – Detailed Transaction"){

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = (!empty($claim->date_of_service))? date('m/d/Y',strtotime(@$claim->date_of_service)):'-Nil-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = (!empty($claim->claim_number))? @$claim->claim_number:'-Nil-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					if($header->Payer=="Insurance Only"){

						$x_axis=$pdf->getx();// now get current pdf x axis value
						$text = (!empty($payments_list->billing_short_name))? @$payments_list->billing_short_name:'-Nil-';
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width-10,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

						$x_axis=$pdf->getx();// now get current pdf x axis value
						$text = (!empty($payments_list->rendering_short_name))? @$payments_list->rendering_short_name:'-Nil-';
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width-5,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

						$x_axis=$pdf->getx();// now get current pdf x axis value
						$text = (!empty($payments_list->facility_short_name))? @$payments_list->facility_short_name:'-Nil-';
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width-10,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
					}

					else{

						$x_axis=$pdf->getx();// now get current pdf x axis value
						$text = (!empty($payments_list->claim->billing_provider->short_name))? @$payments_list->claim->billing_provider->short_name:'-Nil-';
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width-10,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

						$x_axis=$pdf->getx();// now get current pdf x axis value
						$text = (!empty($payments_list->claim->rendering_provider->short_name))? @$payments_list->claim->rendering_provider->short_name:'-Nil-';
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width-5,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

						$x_axis=$pdf->getx();// now get current pdf x axis value
						$text = (!empty($payments_list->claim->facility_detail->short_name))? @$payments_list->claim->facility_detail->short_name:'-Nil-';
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");	
					}
				}

				if(@$column->ins_pat =='1' || @$column->insurance =='1'){
					$x_axis=$pdf->getx();// now get current pdf x axis value
					if ($payments_list->payer_insurance_id==0) {
						$text = "Self";
					}else{
						$text = (!empty($payments_list->payer_insurance_id))? Helpers::getInsuranceNameWithType($payments_list->payer_insurance_id) : '-Nil-';
					}
					$lengthToSplit = strlen($text['insurance']);
					$pdf->Vcell($c_width-10,$c_height,$x_axis,@$text['insurance'],$lengthToSplit,'', "L");
					
					 $lengthToSplit = strlen($text['insuranceType']);
					$pdf->Vcell($c_width-10,$c_height,$x_axis,@$text['insuranceType'],$lengthToSplit,'', "L"); 
				}

				if($header->Payer=="Insurance Only"){
					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = (!empty($payments_list->posting_date))? Helpers::dateFormat($payments_list->posting_date):'-Nil-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
				}

				if(@$column->payment_type =='1'){
					$x_axis=$pdf->getx();// now get current pdf x axis value
					$text = isset($payment_info->pmt_mode)?@$payment_info->pmt_mode:'-Nil-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
				}
				if(@$payment_info->pmt_mode =='Check'){

					if(!empty(@$check_details->check_no) || @$check_details->check_no==0){
						$x_axis=$pdf->getx();// now get current pdf x axis value
						$text = (!empty($check_details->check_no))? @$check_details->check_no:'-Nil-';
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width+5,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
					}
				}
				elseif(@$payment_info->pmt_mode =='EFT'){

					if(!empty(@$eft_details->eft_no) || @$eft_details->eft_no==0){
						$x_axis = $pdf->getx();// now get current pdf x axis value
						$text = (!empty($eft_details->eft_no))? @$eft_details->eft_no:'-Nil-';
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width+5,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
					}
				}
				elseif(@$payment_info->pmt_mode =='Money Order'){

					if(!empty(@$check_details->check_no) || @$check_details->check_no==0){
						$exp = explode("MO-", @$check_details->check_no);
						$x_axis = $pdf->getx();// now get current pdf x axis value
						$text = (!empty($exp[1]))? $exp[1]:'-Nil-';
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width+5,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
					}
				}
				elseif(@$payment_info->pmt_mode =='Credit'){
					$x_axis = $pdf->getx();// now get current pdf x axis value
					$text = (!empty($creditCardDetails->card_last_4))? @$creditCardDetails->card_last_4 : '-Nil-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width+5,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
				}
				elseif(@$payment_info->pmt_mode =='Cash'){
					$x_axis = $pdf->getx();// now get current pdf x axis value
					$text = (!empty($creditCardDetails->card_last_4))? @$creditCardDetails->card_last_4:'-Nil-' ;
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width+5,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
				}
				else{
					$x_axis = $pdf->getx();// now get current pdf x axis value
					$text = "-Nil-";
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width+5,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
				}

				if(@$payment_info->pmt_mode =='Check'){
					$x_axis = $pdf->getx();// now get current pdf x axis value
					$text = (!empty($check_details->check_date)  && $check_details->check_date !== '0000-00-00')? Helpers::dateFormat($check_details->check_date):'-Nil-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width+10,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
					
				}
				elseif(@$payment_info->pmt_mode =='EFT'){
					if(!empty(@$eft_details->eft_date)){
						$x_axis = $pdf->getx();// now get current pdf x axis value
						$text = (!empty($eft_details->eft_date))? Helpers::dateFormat($eft_details->eft_date):'-Nil-';
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width+10,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
					}
				}
				elseif(@$payment_info->pmt_mode =='Money Order'){
					if(!empty(@$check_details->check_date) && $check_details->check_date !== '0000-00-00'){
						$x_axis = $pdf->getx();// now get current pdf x axis value
						$text = (!empty($check_details->check_date))? Helpers::dateFormat($check_details->check_date):'-Nil-';
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width+10,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
					}else{
						$x_axis = $pdf->getx();// now get current pdf x axis value
						$text = '-Nil-';
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width+10,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
					}
				}
				elseif(@$payment_info->pmt_mode =='Credit'){
					$x_axis = $pdf->getx();// now get current pdf x axis value
					$text = (!empty($creditCardDetails->created_at))? Helpers::dateFormat($creditCardDetails->created_at):'-Nil-' ;
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width+10,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
				}
				elseif(@$payment_info->pmt_mode =='Cash'){
					$x_axis = $pdf->getx();// now get current pdf x axis value
					$text = "-Nil-";
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width+10,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
				}
				else{
					$x_axis = $pdf->getx();// now get current pdf x axis value
					$text = "-Nil-";
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width+10,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
				}

				if($header->Payer=="Insurance Only"){
					$x_axis = $pdf->getx();// now get current pdf x axis value
					$text = (isset($claim->total_charge))?@$claim->total_charge:'-Nil-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
				}

				if(@$column->ins_pat =='1' || @$column->insurance =='1'){
					$x_axis = $pdf->getx();// now get current pdf x axis value
					$text = (!empty($payments_list->total_allowed))? @$payments_list->total_allowed:'-Nil-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width+5,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$x_axis = $pdf->getx();// now get current pdf x axis value
					$text = (!empty($payments_list->total_writeoff))? @$payments_list->total_writeoff:'-Nil-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$x_axis = $pdf->getx();// now get current pdf x axis value
					$text = (!empty($payments_list->total_deduction))? @$payments_list->total_deduction:'-Nil-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$x_axis = $pdf->getx();// now get current pdf x axis value
					$text = (!empty($payments_list->total_copay))? @$payments_list->total_copay:'-Nil-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$x_axis = $pdf->getx();// now get current pdf x axis value
					$text = (!empty($payments_list->total_coins))? @$payments_list->total_coins:'-Nil-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

					$x_axis = $pdf->getx();// now get current pdf x axis value
					$text = (!empty($payments_list->total_withheld))? @$payments_list->total_withheld:'-Nil-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
				}

				if($header->Payer=="Insurance Only"){
					$x_axis = $pdf->getx();// now get current pdf x axis value
					$text = (!empty($payments_list->total_paid))? Helpers::priceFormat(@$payments_list->total_paid,'',1):'-Nil-';
					$lengthToSplit = strlen($text);
					$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
				}
				elseif($header->Payer=="Patient Payments"){
					if($payment_info->pmt_type =='Credit Balance'){
						$x_axis = $pdf->getx();// now get current pdf x axis value
						$text = (!empty($payments_list->pmt_amt))? Helpers::priceFormat(@$payments_list->pmt_amt,'',1):'-Nil-';
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
					}
					else{
						$x_axis = $pdf->getx();// now get current pdf x axis value
						$text = (!empty($payments_list->pmt_amt))? Helpers::priceFormat(@$payments_list->pmt_amt,'',1):'-Nil-';
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
					}
				}
				else{
					if($payments_list->pmt_type =='Credit Balance' && $payments_list->source_id==0){
						$x_axis = $pdf->getx();// now get current pdf x axis value
						$text = isset($payments_list->total_paid)? Helpers::priceFormat(@$payments_list->total_paid*(-1),'',1):'-Nil-';
						$lengthToSplit = strlen($text);
						$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
					}
					else{
						if($payments_list->used!=''){
							$x_axis = $pdf->getx();// now get current pdf x axis value
							$text = (!empty($payments_list->used))? Helpers::priceFormat(@$payments_list->used,'',1):'-Nil-';
							$lengthToSplit = strlen($text);
							$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
						}
						else{
							$x_axis = $pdf->getx();// now get current pdf x axis value
							$text = isset($payments_list->total_paid)? Helpers::priceFormat(@$payments_list->total_paid,'',1):'-Nil-';
							$lengthToSplit = strlen($text);
							$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");
						}
					}
				}

				$x_axis = $pdf->getx();// now get current pdf x axis value
				$text = (isset($payment_info->reference) && !empty($payment_info->reference))? @$payment_info->reference:'-Nil-';
				if ($pdf->count_header > 10) {
					$lengthToSplit = 10;
				}else{
					$lengthToSplit = strlen($text);
				}
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$x_axis = $pdf->getx();// now get current pdf x axis value
				$text = (!empty($payments_list->created_by))? Helpers::shortname($payments_list->created_by):'-Nil-';
				$lengthToSplit = strlen($text);
				$pdf->Vcell($c_width,$c_height,$x_axis,@$text,$lengthToSplit,'', "L");

				$trans_cpt_details = [];

				$adj = @$payments_list->total_adjusted + @$payments_list->total_withheld;
                $ins_over_pay = @$claim->insurance_paid - @$claim->total_allowed;
                $trans_amt = @$trans_cpt_details->co_pay + @$trans_cpt_details->co_ins + @$trans_cpt_details->deductable;
                // $total = $total + @$payments_list->pmt_amt;

                if ($payments_list->pmt_method == 'Insurance'){
                	$total_ins = $total_ins + @$payments_list->pmt_amt;
                }
                else{
                	$total_pat = $total_pat + @$payments_list->pmt_amt;
                }
				$pdf->Ln();

				$abb_user[] = Helpers::user_names(@$payments_list->created_by)." - ".Helpers::getUserFullName(@$payments_list->created_by);
		    	$abb_user = array_unique($abb_user);
				foreach (array_keys($abb_user, ' - ') as $key) {
		        	unset($abb_user[$key]);
		    	}

		    	$abb_rendering[] = @$payments_list->claim->rendering_provider->short_name." - ".@$payments_list->claim->rendering_provider->provider_name;
		    	$abb_rendering = array_unique($abb_rendering);
				foreach (array_keys($abb_rendering, ' - ') as $key) {
		        	unset($abb_rendering[$key]);
		    	}

		    	$abb_billing[] = @$payments_list->claim->billing_provider->short_name." - ".@$payments_list->claim->billing_provider->provider_name;
		    	$abb_billing = array_unique($abb_billing);
				foreach (array_keys($abb_billing, ' - ') as $key) {
		        	unset($abb_billing[$key]);
		    	}

		    	$abb_facility[] = @$payments_list->claim->facility_detail->short_name." - ".@$payments_list->claim->facility_detail->facility_name;
		    	$abb_facility = array_unique($abb_facility);
				foreach (array_keys($abb_facility, ' - ') as $key) {
		        	unset($abb_facility[$key]);
		    	}
        	}
        	if ($header->Payer!="Patient Payments – Detailed Transaction") {
        		self::SummaryContent($pdf,$dataArr,$header);
        	}
        	$pdf->Ln();
        	/*$abbreviation = ['abb_user' => $abb_user, 'abb_rendering' => $abb_rendering, 'abb_billing' => $abb_billing, 'abb_facility' => $abb_facility];
			$abb_controller = New AbbreviationController;
			$abb_controller->abbreviation($abbreviation,$pdf);*/
        }
    }
	
	public function SummaryContent($pdf,$dataArr,$header){
		
		$x_axis = $pdf->getx();
    	$pdf->SetTextColor(240, 125, 8);
    	$pdf->SetFont('Calibri-Bold','',8);
    	$pdf->Vcell(30,10,$x_axis,"Summary",20,"","L");
    	$pdf->Ln();
    	$c_height = 6;
    	$c_width = 40;
    	$lengthToSplit = 30;
    	if ($header->Payer=="Patient Payments") {
	 		$x_axis=$pdf->getx();
	    	$pdf->SetTextColor(100,100,100);
	    	$pdf->SetFont('Calibri-Bold','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,"Total Patient Payments",$lengthToSplit,"TL","L");
	 		$x_axis=$pdf->getx();
	    	$pdf->SetFont('Calibri','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,Helpers::priceFormat(@$dataArr->patPmt,'',1),$lengthToSplit,"TR","R");
	 		$pdf->Ln();
	 		$x_axis=$pdf->getx();
	    	$pdf->SetTextColor(100,100,100);
	    	$pdf->SetFont('Calibri-Bold','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,"Check Payments",$lengthToSplit,"L","L");
	 		$x_axis=$pdf->getx();
	    	$pdf->SetFont('Calibri','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,Helpers::priceFormat(@$dataArr->check,'',1),$lengthToSplit,"R","R");
	 		$pdf->Ln();

 		
 			$x_axis=$pdf->getx();
	    	$pdf->SetTextColor(100,100,100);
	    	$pdf->SetFont('Calibri-Bold','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,"Cash Payments",$lengthToSplit,"L","L");
	 		$x_axis=$pdf->getx();
	    	$pdf->SetFont('Calibri','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,Helpers::priceFormat(@$dataArr->cash,'',1),$lengthToSplit,"R","R");
	 		$pdf->Ln();

	 		$x_axis=$pdf->getx();
	    	$pdf->SetTextColor(100,100,100);
	    	$pdf->SetFont('Calibri-Bold','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,"MO Payments",$lengthToSplit,"L","L");
	 		$x_axis=$pdf->getx();
	    	$pdf->SetFont('Calibri','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,Helpers::priceFormat(@$dataArr->mo,'',1),$lengthToSplit,"R","R");
	 		$pdf->Ln();

	 	}
 		if ($header->Payer=="Insurance Only") {
	 		$x_axis=$pdf->getx();
	    	$pdf->SetTextColor(100,100,100);
	    	$pdf->SetFont('Calibri-Bold','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,"Total Insurance Payments",$lengthToSplit,"TL","L");
	 		$x_axis=$pdf->getx();
	    	$pdf->SetFont('Calibri','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,Helpers::priceFormat(@$dataArr->insPmt,'',1),$lengthToSplit,"TR","R");
	 		$pdf->Ln();
	 		$x_axis=$pdf->getx();
	    	$pdf->SetTextColor(100,100,100);
	    	$pdf->SetFont('Calibri-Bold','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,"Write-Off",$lengthToSplit,"L","L");
	 		$x_axis=$pdf->getx();
	    	$pdf->SetFont('Calibri','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,Helpers::priceFormat(@$dataArr->wrtOff,'',1),$lengthToSplit,"R","R");
	 		$pdf->Ln();

 		
 			$x_axis=$pdf->getx();
	    	$pdf->SetTextColor(100,100,100);
	    	$pdf->SetFont('Calibri-Bold','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,"Other Adjustments",$lengthToSplit,"L","L");
	 		$x_axis=$pdf->getx();
	    	$pdf->SetFont('Calibri','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,Helpers::priceFormat(@$dataArr->other,'',1),$lengthToSplit,"R","R");
	 		$pdf->Ln();

	 		$x_axis=$pdf->getx();
	    	$pdf->SetTextColor(100,100,100);
	    	$pdf->SetFont('Calibri-Bold','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,"Deductible",$lengthToSplit,"L","L");
	 		$x_axis=$pdf->getx();
	    	$pdf->SetFont('Calibri','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,Helpers::priceFormat(@$dataArr->deduction,'',1),$lengthToSplit,"R","R");
	 		$pdf->Ln();

	 		$x_axis=$pdf->getx();
	    	$pdf->SetTextColor(100,100,100);
	    	$pdf->SetFont('Calibri-Bold','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,"Co-Pay",$lengthToSplit,"L","L");
	 		$x_axis=$pdf->getx();
	    	$pdf->SetFont('Calibri','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,Helpers::priceFormat(@$dataArr->copay,'',1),$lengthToSplit,"R","R");
	 		$pdf->Ln();

	 		$x_axis=$pdf->getx();
	    	$pdf->SetTextColor(100,100,100);
	    	$pdf->SetFont('Calibri-Bold','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,"Co-Insurance",$lengthToSplit,"L","L");
	 		$x_axis=$pdf->getx();
	    	$pdf->SetFont('Calibri','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,Helpers::priceFormat(@$dataArr->coins,'',1),$lengthToSplit,"R","R");
	 		$pdf->Ln();

	 		$x_axis=$pdf->getx();
	    	$pdf->SetTextColor(100,100,100);
	    	$pdf->SetFont('Calibri-Bold','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,"EFT Payments",$lengthToSplit,"L","L");
	 		$x_axis=$pdf->getx();
	    	$pdf->SetFont('Calibri','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,Helpers::priceFormat(@$dataArr->eft,'',1),$lengthToSplit,"R","R");
	 		$pdf->Ln();

	 		$x_axis=$pdf->getx();
	    	$pdf->SetTextColor(100,100,100);
	    	$pdf->SetFont('Calibri-Bold','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,"Check Payments",$lengthToSplit,"L","L");
	 		$x_axis=$pdf->getx();
	    	$pdf->SetFont('Calibri','',7.5);
	 		$pdf->Vcell($c_width,$c_height,$x_axis,Helpers::priceFormat(@$dataArr->check,'',1),$lengthToSplit,"R","R");
	 		$pdf->Ln();
 		}

 		$x_axis=$pdf->getx();
    	$pdf->SetTextColor(100,100,100);
    	$pdf->SetFont('Calibri-Bold','',7.5);
 		$pdf->Vcell($c_width,$c_height,$x_axis,"CC Payments",$lengthToSplit,"BL","L");
 		$x_axis=$pdf->getx();
    	$pdf->SetFont('Calibri','',7.5);
 		$pdf->Vcell($c_width,$c_height,$x_axis,Helpers::priceFormat(@$dataArr->cc,'',1),$lengthToSplit,"BR","R");
 		$pdf->Ln();

	}
}


class Payment_Analysis_detailed extends FPDF
{
	public function header(){

		$request = Request::All();
   		$controller = New ReportApiController;
        $api_response = $controller->getPaymentSearchApi('pdf');
        $api_response_data = $api_response->getData();
        $header = $api_response_data->data->header;
        $column = $api_response_data->data->column;

		$this->SetFillColor(255, 255, 255);
		$this->SetTextColor(0, 135, 127);
	    $this->AddFont('Calibri-Bold','','calibrib.php');
	    $this->SetFont('Calibri-Bold','',10.5);
		$x_axis=$this->getx();
		$c_width = 480;
		$c_height = 0;
		$text = Practice::getPracticeName()." - Payment Analysis Detailed Report";
		$lengthToSplit = strlen($text);
		$this->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,"","C");

        $this->AddFont('Calibri','','calibri.php');
        $this->SetFont('Calibri','',9.5);
		$this->SetTextColor(100, 100, 100);
		$this->Ln();
		// $header = $this->headers_;
		// $column = $this->column_;
		$i = 0;
		$text = [];
		foreach ((array)$header as $key => $val) {
			if ($val == 'Patient Payments – Detailed Transaction') {
				$val = 'Patient Payments Detailed Transaction';
			}
			$text[] = $key.":".@$val;                           
            $i++; 
		}
		$text_imp = implode(" | ", $text);
		$this->Vcell(480,12,$x_axis,$text_imp,160,"","C");

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
		$this->Vcell(190,10,$x_axis,$text,$lengthToSplit,"","");

		$x_axis=$this->getx();
		$text = "Created Date : ";
		$lengthToSplit = strlen($text);
        $this->SetFont('Calibri-Bold','',8.5);
		$this->SetTextColor(100, 100, 100);
		$this->Vcell(190,10,$x_axis,$text,$lengthToSplit,"","R");

		$x_axis=$this->getx();
		$text = Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y');
		$lengthToSplit = strlen($text);
        $this->SetFont('Times','B',7.5);
		$this->SetTextColor(0, 135, 127);
		$this->Vcell(10,10,$x_axis,$text,$lengthToSplit,"","L");

		$this->Ln();

		// $this->headers($header, $column);

		$header = ['Transaction Date', 'Acc No', 'Patient Name',($header->Payer=="Insurance Only" || $header->Payer=="Patient Payments – Detailed Transaction")? 'DOS' : NULL , ($header->Payer=="Insurance Only" || $header->Payer=="Patient Payments – Detailed Transaction")? 'Claim No' : NULL, ($header->Payer=="Insurance Only" || $header->Payer=="Patient Payments – Detailed Transaction")? 'Billing' : NULL, ($header->Payer=="Insurance Only" || $header->Payer=="Patient Payments – Detailed Transaction")? 'Rendering' : NULL, ($header->Payer=="Insurance Only" || $header->Payer=="Patient Payments – Detailed Transaction")? 'Facility' : NULL, (@$column->ins_pat =='1' || @$column->insurance =='1')? 'Payer' : NULL, (@$column->ins_pat =='1' || @$column->insurance =='1')? 'Insurance Type' : NULL, ($header->Payer=="Insurance Only")? "Payment Date" : NULL, (@$column->payment_type =='1') ? "Payment Type" : NULL, 'Check/EFT/CC'.(($header->Payer!="Insurance Only")? "/MO" : NULL).' No', 'Check/EFT/CC'.(($header->Payer!="Insurance Only")? "/MO" : NULL).' Date', ($header->Payer=="Insurance Only")? "Billed" : NULL, (@$column->ins_pat =='1' || @$column->insurance =='1')? 'Allowed' :NULL,(@$column->ins_pat =='1' || @$column->insurance =='1')? 'W/O' :NULL,(@$column->ins_pat =='1' || @$column->insurance =='1')? 'Ded' :NULL,(@$column->ins_pat =='1' || @$column->insurance =='1')? 'Co-Pay' :NULL,(@$column->ins_pat =='1' || @$column->insurance =='1')? 'Co-Ins' :NULL, (@$column->ins_pat =='1' || @$column->insurance =='1')? 'Other Adjustment' :NULL, ($header->Payer!="Patient Payments – Detailed Transaction")? 'Paid($)' : 'Applied($)', 'Reference', 'User'];
		foreach ($header as $key => $value) {
			if ($value == NULL) {
				unset($header[$key]);
			}
		}

		$this->count_header = count($header);

		foreach ($header as $key => $value) {
			$this->SetFont('Calibri-Bold','',8);
			$this->SetTextColor(100, 100, 100);
			$x_axis=$this->getx();// now get current pdf x axis value
			$c_height = 6;// cell height
			$c_width = (480/count($header));// cell width 
			$lengthToSplit = strlen($value);
			if ($this->count_header > 10) {
				if ($value == "Check/EFT/CC/MO No" || $value == "Check/EFT/CC/MO Date") {
					$lengthToSplit = 10;
				}
				else{
					$lengthToSplit = strlen($value);
				}
				if ($value == "Patient Name") {
					$c_width = $c_width;					
				}
				if ($value == "Acc No"){
					$c_width = $c_width;
				}
				if ($value == "Payer" || $value == "Facility" || $value == "Billing"){
					$c_width = $c_width-10;
				}
				if ($value == "Rendering"){
					$c_width = $c_width-5;
				}
				if ($value == "Allowed" || $value == "Check/EFT/CC No" || $value == "Check/EFT/CC Date"){
					$c_width = $c_width+5;
				}
				if ($value == "Check/EFT/CC Date") {
					$c_width = $c_width+5;
				}
			}

			
			$this->vcell($c_width,$c_height,$x_axis,$value,$lengthToSplit,"B",@$align);// pass all values inside the cell 
		}

		$this->Ln();
	}

	public function headers($header, $column){
		$this->SetFont('Calibri-Bold','',8);
		$this->SetTextColor(100, 100, 100);

		// $this->SetY(35);
	 //    $this->SetX(2);
	 //    $this->MultiCell(290,0,"" ,'B', "L");
	 //    $this->SetY(32);
	 //    $this->SetX(3);
	 //    $this->MultiCell(0, 0,"Transaction Date" ,'', "L");
// dd($header);
	    $c_height = 6;
		$c_width = (480/count($header));
		$lengthToSplit = strlen($value);

		$x_axis=$this->getx();
		$this->vcell($c_width,$c_height,$x_axis,"Transaction Date",$lengthToSplit,"B",@$align); 
	}


	public function footer()
	{
	    $this->AddFont('Calibri','','calibri.php');
	    $this->SetFont('Calibri','',9);
	    $this->SetY(200);
	    $x_axis=$this->getx();
	    $c_width = 480;
	    $c_height = 0;
	    $year = date('Y');
	    $this->SetTextColor(82,82,82);
	    //$this->SetFont('Times','',7);
	    $text =  "Copyright ". chr(169) ." ".$year." Medcubics. All Rights Reserved.";
	    $lengthToSplit = strlen($text);
	    $this->Vcell($c_width,$c_height,$x_axis,$text,$lengthToSplit,'', "L");
	    $c_width = 480;
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

