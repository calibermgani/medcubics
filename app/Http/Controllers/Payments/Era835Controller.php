<?php

namespace App\Http\Controllers\Payments;

use Auth;
use App;
use DB;
use View;
use Input;
use Config;
use Session;
use Exception;
use Request;
use Response;
use Redirect;
use Validator;
use App\Models\Eras;
use App\Models\Insurance;
use App\Models\Code;
use App\Http\Helpers\Helpers;
use App\Models\Medcubics\ClearingHouse as ClearingHouse;
use PDF;
use SSH;
use App\Traits\ClaimUtil;
use App\Models\Payments\ClaimInfoV1;
use App\Http\Controllers\Payments\Api\PaymentV1ApiController;
use App\Models\Payments\ClaimCPTInfoV1;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Models\Patients\Patient as Patient;
use App\Models\Payments\PMTInfoV1;
use App\Models\Payments\PMTClaimFINV1;
use App\Models\AdjustmentReason;
use App\Http\Controllers\RuleEngine\RuleEngineContoller;
use App\Models\CodesRuleEngine as CodesRuleEngine;
use Log;

class Era835Controller extends Api\PaymentApiController {

    use ClaimUtil;

    public function __construct() {
        View::share('heading', 'Payments');
        View::share('selected_tab', 'payments');
        View::share('heading_icon', 'fa-money');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {

    }

    public function pdf_generation($id = '', $cheque = '',$type = '') {
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        $erasInfo = Eras::where('id', $id)->get()->first();
        $filename = $erasInfo->pdf_name;
		$eraFileName = explode('.',$filename);
		$orgERAFolderName = $eraFileName[0];
		$orgERAFileName = str_replace('STATUS','835',$eraFileName[0]).'.835';
        $path_medcubic = public_path() . '/';
        $local_path = $path_medcubic . 'media/era_files/' . Session::get('practice_dbid') . '/'.$orgERAFolderName.'/';
        /**
         * Declaration part array's and variables
         */
		$checkNo = -1;
        $glossary = $basic_info = $insert_data = [];
		$fileReceivedDate = $erasInfo->receive_date;
        foreach (glob($local_path . $orgERAFileName) as $list) {

            /**
             * Getting file content using file function
             * Convert the file content into array using (~)
             */
            $file_content = file($list);
            $file_full_content = explode('~', $file_content[0]);



            /**
             * Using file content to find separator
             */
            $symb_check = implode('', $file_full_content);
            $first_segment = $file_full_content[0];
            if (count(explode('|', $symb_check)) > 5) {
                $separate = "|";
            } elseif (count(explode('*', $symb_check)) > 1) {
                $separate = "*";
            }
            $spl_symb = explode($separate, $first_segment);
            $spl_separate = $spl_symb[16];

            /**
             * Separating the segment and getting data in the segment
             */
			
			foreach ($file_full_content as $key => $segment) {
				if (substr($segment, 0, 4) == 'TRN' . $separate) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[2])){
						// Remove remove special characters from cheque no in era
						// Revision 1 : MR-2799 : 6 Sep 2019 : Selva
						
						// Added replace option with special characters
						// Revision 2 : MR-2916 : 20 Sep 2019 : Selva 
						
						$temp[2] = preg_replace("/[^a-zA-Z0-9]/", "", $temp[2]);
                       $checkNoArr[] = $temp[2];
					}
                }	
			}
			$checkKey = array_search($cheque, $checkNoArr);
			$checkCount = 0;
            foreach ($file_full_content as $key => $segment) {
				
				
                if (substr($segment, 0, 3) == 'ST' . $separate) {
                    $basic_count = 0;
                    $claim_count = 0;
                    $claim_cpt_count = 0;
					$checkNo++;
                }

                if (substr($segment, 0, 6) == 'N1' . $separate . 'PR' . $separate) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[2]))
                        $basic_info[$checkNo]['payer']['insurance_company'] = $temp[2];
                    $basic_count ++;
                }

                if (substr($segment, 0, 3) == 'N3' . $separate && $basic_count == 1) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[1]))
                        $basic_info[$checkNo]['payer']['insurance_address_info1'] = $temp[1];
                    if (!empty($temp[2])) {
                        $basic_info[$checkNo]['payer']['insurance_address_info2'] = $temp[2];
                    }
                }

                if (substr($segment, 0, 3) == 'N4' . $separate && $basic_count == 1) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[1]))
                        $basic_info[$checkNo]['payer']['insurance_city'] = $temp[1];
                    if (!empty($temp[2]))
                        $basic_info[$checkNo]['payer']['insurance_state'] = $temp[2];
                    if (!empty($temp[3]))
                        $basic_info[$checkNo]['payer']['insurance_zipcode'] = $temp[3];
                }

                if (substr($segment, 0, 6) == 'N1' . $separate . 'PE' . $separate) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[2]))
                        $basic_info[$checkNo]['payee']['practice_company'] = $temp[2];
                    if (!empty($temp[4]))
                        $basic_info[$checkNo]['payee']['payee_npi_id'] = $temp[4];
                    $basic_count ++;
                }

                if (substr($segment, 0, 3) == 'N3' . $separate && $basic_count == 2) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[1]))
                        $basic_info[$checkNo]['payee']['practice_address_info1'] = $temp[1];
                    if (!empty($temp[2])) {
                        $basic_info[$checkNo]['payee']['practice_address_info2'] = $temp[2];
                    }
                }

                if (substr($segment, 0, 3) == 'N4' . $separate && $basic_count == 2) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[1]))
                        $basic_info[$checkNo]['payee']['practice_city'] = $temp[1];
                    if (!empty($temp[2]))
                        $basic_info[$checkNo]['payee']['practice_state'] = $temp[2];
                    if (!empty($temp[3]))
                        $basic_info[$checkNo]['payee']['practice_zipcode'] = $temp[3];
                }

                if (substr($segment, 0, 4) == 'TRN' . $separate) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[2])){
						// Remove remove special characters from cheque no in era
						// Revision 1 : MR-2799 : 6 Sep 2019 : Selva
						$temp[2] = preg_replace("/[^a-zA-Z0-9]/", "", $temp[2]);
                        $basic_info[$checkNo]['check_details']['check_no'] = $temp[2];
					}
                }

                if (substr($segment, 0, 4) == 'BPR' . $separate) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[16]))
                        $basic_info[$checkNo]['check_details']['check_date'] = date('Y-m-d', strtotime($temp[16]));
                    if (!empty($temp[2]))
                        $basic_info[$checkNo]['check_details']['check_paid_amount'] = $temp[2];
                }

                if (substr($segment, 0, 4) == 'CLP' . $separate) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[3]))
                        $basic_info[$checkNo]['check_details']['check_amount'] = $temp[3];
                }

                if (substr($segment, 0, 4) == 'CLP' . $separate) {
                    $claim_count++;
					$claim_cpt_count = 0;
                    $temp = explode($separate, $segment);
                    if (!empty($temp[1]))
                        $basic_info[$checkNo]['claim'][$claim_count]['claim_id'] = $temp[1];
                    if (!empty($temp[2])){
						$processData = $this->processData($temp[2]);
                        $basic_info[$checkNo]['claim'][$claim_count]['claim_insurance_type'] = $processData['msg'];
                        $basic_info[$checkNo]['claim'][$claim_count]['claim_insurance_type_withMsg'] = $processData['type'];
					}
                    if (!empty($temp[3]))
                        $basic_info[$checkNo]['claim'][$claim_count]['charge_amount'] = abs($temp[3]);
                    if (!empty($temp[4]))
                        $basic_info[$checkNo]['claim'][$claim_count]['charge_paid_amount'] = $temp[4];
                    if (!empty($temp[5]))
                        $basic_info[$checkNo]['claim'][$claim_count]['charge_coins_amount'] = $temp[5];
                    if (!empty($temp[7]))
                        $basic_info[$checkNo]['claim'][$claim_count]['patient_icn'] = $temp[7];
                }

                if (substr($segment, 0, 4) == 'CAS' . $separate) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[1]))
                        $basic_info[$checkNo]['claim'][$claim_count]['claims_adj'] = $temp[1];
                }

                if (substr($segment, 0, 7) == 'NM1' . $separate . 'QC' . $separate) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[3]))
                        $basic_info[$checkNo]['claim'][$claim_count]['patient_lastname'] = $temp[3];
                    if (!empty($temp[4]))
                        $basic_info[$checkNo]['claim'][$claim_count]['patient_firstname'] = $temp[4];
                    if (!empty($temp[5]))
                        $basic_info[$checkNo]['claim'][$claim_count]['patient_Suffix'] = $temp[5];
                    if (!empty($temp[9]))
                        $basic_info[$checkNo]['claim'][$claim_count]['patient_hic'] = $temp[9];
                }
				
				
				if (substr($segment, 0, 7) == 'NM1' . $separate . '74' . $separate) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[9]))
                        $basic_info[$checkNo]['claim'][$claim_count]['patient_hic'] = $temp[9];
                }
				
				if (substr($segment, 0, 7) == 'NM1' . $separate . 'IL' . $separate) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[9]))
                        $basic_info[$checkNo]['claim'][$claim_count]['patient_hic'] = $temp[9];
                }

                if (substr($segment, 0, 4) == 'MOA' . $separate) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[3]))
                        $basic_info[$checkNo]['claim'][$claim_count]['patient_moa'] = $temp[3];
                }

                if (substr($segment, 0, 4) == 'DTM' . $separate) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[2]) && $temp[1] == 232)
                        $basic_info[$checkNo]['claim'][$claim_count]['start_date'] = $temp[2];
                    if (!empty($temp[2]) && $temp[1] == 233)
                        $basic_info[$checkNo]['claim'][$claim_count]['end_date'] = $temp[2];
                    if (!empty($temp[2]) && $temp[1] == 050)
                        $basic_info[$checkNo]['claim'][$claim_count]['patient_statement_date'] = date('d/m/Y', strtotime($temp[2]));
                }

                if (substr($segment, 0, 4) == 'SVC' . $separate) {
                    $claim_cpt_count ++;
                    $temp = explode($separate, $segment);
                    $temp_proc = explode($spl_separate, $temp[1]);
                    if (!empty($temp_proc[1]))
                        $basic_info[$checkNo]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['proc'] = $temp_proc[1];
                    if (!empty($temp[2]))
                        $basic_info[$checkNo]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['billed_amount'] = $temp[2];
                    if (!empty($temp[3]))
                        $basic_info[$checkNo]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['insurance_paid_amount'] = $temp[3];
                    if (isset($temp[5]) && !empty($temp[5]))
                        $basic_info[$checkNo]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['units'] = $temp[5];
					else
                        $basic_info[$checkNo]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['units'] = 1;
					
					if(isset($basic_info[$checkNo]['claim'][$claim_count]['start_date']) && !empty($basic_info[$checkNo]['claim'][$claim_count]['start_date']) && $claim_cpt_count == 1)
						$basic_info[$checkNo]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['service_date'] = $basic_info[$checkNo]['claim'][$claim_count]['start_date'];
                }
                if (substr($segment, 0, 4) == 'DTM' . $separate && $claim_cpt_count != 0) { 
                    $temp = explode($separate, $segment);
                    if (!empty($temp[2]) && $temp[1] == 472)
                        $basic_info[$checkNo]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['service_date'] = $temp[2]; 
                    if (!empty($temp[2]) && $temp[1] == 150)
                        $basic_info[$checkNo]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['service_date'] = $basic_info[$checkNo]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['start_date'] = $temp[2];
                    if (!empty($temp[2]) && $temp[1] == 151)
                        $basic_info[$checkNo]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['end_date'] = $temp[2];
					
                }
				if (substr($segment, 0, 4) == 'CAS' . $separate && $claim_cpt_count != 0) {
					$temp = explode($separate, $segment);
					//$temp = array_filter($temp, function($value) { return $value !== ''; });
					$adjCount = 0;
					foreach($temp as $key => $tempData){
						if($tempData != 'CAS' && $tempData != 'CO' && $tempData != 'OA' && $tempData != 'PI' && $tempData != 'PR' ){
							if($adjCount == 0){
								$prevAdjCode = $temp[1].$tempData;
								$glossary[] = $tempData;
								$exemptArr = ['PR1', 'PR2','PR3', 'CO1','CO2','CO3', 'OA1','OA2','OA3','PI1','PI2','PI3'];
								if(!in_array($temp[1].$tempData, $exemptArr)){
									$this->checkAdjReason($temp[1],$tempData);
									 $basic_info[$checkNo]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['adj_reason'][] = $temp[1].$tempData;	
								}
								$adjCount ++;	
							}elseif($adjCount == 2){
								$adjCount = 0;
							}elseif($adjCount == 1){
								$adjCount ++; 
								if($prevAdjCode == 'PR1' || $prevAdjCode == 'CO1' || $prevAdjCode == 'OA1' || $prevAdjCode == 'PI1'){
									$basic_info[$checkNo]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['deductible'] = $tempData;
									$prevAdjCode = '';
								}elseif($prevAdjCode == 'PR2' || $prevAdjCode == 'CO2' || $prevAdjCode == 'OA2' || $prevAdjCode == 'PI2'){
									$basic_info[$checkNo]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['coinsurance'] = $tempData;
									$prevAdjCode = '';
								}elseif($prevAdjCode == 'PR3' || $prevAdjCode == 'CO3' || $prevAdjCode == 'OA3' || $prevAdjCode == 'PI3'){
									$basic_info[$checkNo]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['co_payment'] = $tempData;
									$prevAdjCode = '';
								}elseif(!empty($tempData)){
									$basic_info[$checkNo]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['adj_reason_val'][] = $tempData;	
								}
							}
						}
						
					}
				}
				
				if (substr($segment, 0, 6) == 'LQ' . $separate."HE". $separate && $claim_cpt_count != 0) {
					$temp = explode($separate, $segment);
					$glossary[] = $temp[2];
					$basic_info[$checkNo]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['adj_reason'][] = $temp[2];
				
				}
				
				if (substr($segment, 0, 4) == 'MOA' . $separate) {
					$tempRemarkArr = ['3','4','5','6','7'];
					$temp = explode($separate, $segment);
					foreach($temp as $key => $list){
						if(in_array($key,$tempRemarkArr))
							$glossary[] = $list;
					}
					
				}
				
				if (substr($segment, 0, 4) == 'MIA' . $separate) {
					$tempRemarkArr = ['5','20','21','22','23'];
					$temp = explode($separate, $segment);
					foreach($temp as $key => $list){
						if(in_array($key,$tempRemarkArr))
							$glossary[] = $list;
					}
					
				}
				
				

                if (substr($segment, 0, 7) == 'REF' . $separate . 'LU' . $separate && $claim_cpt_count != 0) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[2]))
                        $basic_info[$checkNo]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['pos'] = $temp[2];
                }

                if (substr($segment, 0, 7) == 'AMT' . $separate . 'B6' . $separate && $claim_cpt_count != 0) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[2]))
                        $basic_info[$checkNo]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['allowed'] = $temp[2];
                }
				
				if (substr($segment, 0, 4) == 'PLB' . $separate && $claim_cpt_count != 0) {
                    $temp = explode($separate, $segment);
                    
					$plbCount = 0;
					foreach($temp as $key => $plbList){
						//Added new segment codes for provider level adjustment in era pdf generation issues fixed
						// Revision 1 : Ref :  MR-2759 : 28 Aug 2019 : Selva
						if(substr($plbList, 0, 3) == 'WO:' || substr($plbList, 0, 3) == 'FB:' || substr($plbList, 0, 3) == 'C5:' || substr($plbList, 0, 3) == 'CS:' || substr($plbList, 0, 3) == '72:' || substr($plbList, 0, 3) == 'B2:'){
							$woData = explode(':',$plbList);
							$basic_info[$checkNo]['plb'][$plbCount]['reason'] = @$woData[0];
							$basic_info[$checkNo]['plb'][$plbCount]['desc'] = @$woData[1];
							$basic_info[$checkNo]['plb'][$plbCount]['amt']= @$temp[$key+1];
							$plbCount++;
						}
					}
                }
            }
        }
		
		$basic_info = $basic_info[$checkKey];
		$fileReceivedDate = date("m/d/Y",strtotime($fileReceivedDate));
        $glossary_details = array();
        foreach ($glossary as $code_list) {
            $codes_details = Code::where('transactioncode_id', $code_list)->value('description');
            $glossary_details[$code_list] = $codes_details;
        }
        $filename_only = explode('.', $filename);
		$html = view('payments/payments/era_pdf_generation',compact('basic_info','glossary_details','cheque','filename','fileReceivedDate'));
		if($type == 'show')
			return PDF::loadHTML($html, 'A4', '')->download($filename_only[0] . ".pdf");
		else{
			return PDF::loadHTML($html, 'A4', '')->download($filename_only[0] . ".pdf");
		}
		
        PDF::loadHTML(view('payments/payments/era_pdf_generation', compact('basic_info', 'glossary_details', 'cheque')))->filename($filename_only[0] . ".pdf")->download();
    }
	
	public function processData($code){
		switch($code){
			case 1:
				$data['msg'] = 'Processed as Primary';
				$data['type'] = 'Primary';
				return $data;
				break;
			case 2:
				$data['msg'] = 'Processed as Secondary';
				$data['type'] = 'Secondary';
				return $data;
				break;
			case 3:
				$data['msg'] = 'Processed as Tertiary';
				$data['type'] = 'Tertiary';
				return $data;
				break;
			case 4:
				$data['msg'] = 'Denied';
				$data['type'] = 'Denied';
				return $data;
				break;
			case 19:
				$data['msg'] = 'Processed as Primary, Forwarded to Additional Payer(s)';
				$data['type'] = 'Primary';
				return $data;
				break;
			case 20:
				$data['msg'] = 'Processed as Secondary, Forwarded to Additional Payer(s)';
				$data['type'] = 'Secondary';
				return $data;
				break;
			case 21:
				$data['msg'] = 'Processed as Tertiary, Forwarded to Additional Payer(s)';
				$data['type'] = 'Tertiary';
				return $data;
				break;
			case 22:
				$data['msg'] = 'Reversal of Previous Payment';
				$data['type'] = 'Reversal';
				return $data;
				break;
			case 23:
				$data['msg'] = 'Not Our Claim, Forwarded to Additional Payer';
				$data['type'] = 'Not Our Claim, Forwarded to Additional Payer';
				return $data;
				break;
			case 25:
				$data['msg'] = 'Predetermination Pricing Only - No Payment';
				$data['type'] = 'Predetermination Pricing Only - No Payment';
				return $data;
				break;
			
		}
	}
	
	
	public function autoPostData(){
		$insert_data = $basic_info = $postedClaim = $unpostedClaim = array();
		$request = Request::all();
		foreach($request['id'] as $id){
			$erasInfo = Eras::where('id', $id)->get()->first();
			$filename = $erasInfo->pdf_name;
			$fileCheckNo = $erasInfo->check_no;
			//\Log::info('Check No before preg DB -> '.$fileCheckNo);
			$fileCheckNo = preg_replace("/[^a-zA-Z0-9]/", "", $fileCheckNo);
			//\Log::info('Check No after preg DB -> '.$fileCheckNo);
			$eraFileName = explode('.',$filename);
			$orgERAFolderName = $eraFileName[0];
			$orgERAFileName = str_replace('STATUS','835',$eraFileName[0]).'.835';
			$path_medcubic = public_path() . '/';
			$local_path = $path_medcubic . 'media/era_files/' . Session::get('practice_dbid') . '/'.$orgERAFolderName.'/';
			$glossary = $basic_info = $insert_data = [];
			$fileReceivedDate = $erasInfo->receive_date;
			foreach (glob($local_path . $orgERAFileName) as $list) {

				$file_content = file($list);
				$file_full_content = explode('~', $file_content[0]);

				$symb_check = implode('', $file_full_content);
				$first_segment = $file_full_content[0];
				if (count(explode('|', $symb_check)) > 5) {
					$separate = "|";
				} elseif (count(explode('*', $symb_check)) > 1) {
					$separate = "*";
				}
				$spl_symb = explode($separate, $first_segment);
				$spl_separate = $spl_symb[16];
				$basic_info['payment_method'] = $basic_info['type'] = 'Insurance';
				$basic_info['card_type'] = $basic_info['next_insurance_id'] = $basic_info['insurance_cat'] = $basic_info['eob_id'] = $basic_info['checkexist'] = $basic_info['reference'] =  $basic_info['next'] = $basic_info['resubmit'] = $basic_info['adjustment_reason'] = $basic_info['content'] = $basic_info['payment_hold_reason'] = $basic_info['status'] = $basic_info['changed_insurance_id'] = $basic_info['change_insurance_category'] = '';
				$basic_info['patient_paid'] = '0.00';
				$basic_info['deposite_date'] = date('m/d/Y');
				$basic_info['payment_mode'] = 'EFT';
				$basic_info['payment_type'] = 'Payment';
				
				foreach ($file_full_content as $key => $segment) {
					if (substr($segment, 0, 3) == 'ST' . $separate) {
						$CLPCount = -1;
						$claim_cpt_count = 0;
					}
					
					if (substr($segment, 0, 6) == 'N1' . $separate . 'PR' . $separate) {
						$temp = explode($separate, $segment);
						if (!empty($temp[2]))
							$basic_info['insurance_company'] = $temp[2];
					}
					
					/* Getting check no */
					if (substr($segment, 0, 4) == 'BPR' . $separate) {
						$temp = explode($separate, $segment);
						if (!empty($temp[16]))
							$basic_info['check_date'] = date('Y-m-d', strtotime($temp[16]));
						if (!empty($temp[2]))
							$basic_info['check_amount'] = $basic_info['payment_amt'] = $basic_info['check_paid_amount'] = $temp[2];
						else
							$basic_info['check_amount'] = $basic_info['payment_amt'] = $basic_info['check_paid_amount'] = 0;
						if((!empty($temp[4])) && $temp[4] != 'CHK')
							$basic_info['payment_mode'] = 'EFT';
						else
							$basic_info['payment_mode'] = 'Check';
							
					}
					
					/* Getting check no */
					if (substr($segment, 0, 4) == 'TRN' . $separate) {
						$temp = explode($separate, $segment);
						/* \Log::info('TRN Segment');
						\Log::info($temp); */
						if (!empty($temp[2])){
							// Remove remove special characters from cheque no in era
							// Revision 1 : MR-2799 : 6 Sep 2019 : Selva
							$temp[2] = preg_replace("/[^a-zA-Z0-9]/", "", $temp[2]);
							/* \Log::info('Check No -> '.$temp[2]); */
							$basic_info['check_no'] = $temp[2];
						}
						
						if($basic_info['payment_mode'] == 'Check'){
							$checkNo = $basic_info['check_no'];
							$checkDetails = PMTInfoV1::with('checkDetails')
										->whereHas('checkDetails', function ($q) use ($checkNo) {
											$q->where('check_no', '=', $checkNo);
										})
										->where('pmt_method', '=', 'Insurance')
										->where('pmt_mode', '=', 'Check')
										->whereNull('void_check');
							$checkInfo = $checkDetails->get()->first();
							$checkCount = $checkDetails->count();
							if($checkCount > 0)
								$basic_info['payment_detail_id'] =  Helpers::getEncodeAndDecodeOfId($checkInfo->id,'encode');
							else
								$basic_info['payment_detail_id'] = '';
						}elseif($basic_info['payment_mode'] == 'EFT'){
							$checkNo = $basic_info['check_no'];
							$eftDetails = PMTInfoV1::with('eftDetails')
											->whereHas('eftDetails', function ($q) use ($checkNo) {
												$q->where('eft_no', '=', $checkNo);
											})->where('pmt_method', '=', 'Insurance')
											->where('pmt_mode', '=', 'EFT')
											->whereNull('void_check');
							$eftInfo = $eftDetails->get()->first();
							$eftCount = $eftDetails->count();
							if($eftCount > 0)
								$basic_info['payment_detail_id'] =  Helpers::getEncodeAndDecodeOfId($eftInfo->id,'encode');
							else
								$basic_info['payment_detail_id'] = '';
						}
					}
					
					// Getting payer insurance id
					if((substr($segment, 0, 7) == 'REF' . $separate . '2U' . $separate)){
						$temp = explode($separate, $segment);
						if(isset($temp[2])){
							$paymentInsurance = $this->findPaymentInsurance($temp[2]);
							if(!empty($paymentInsurance))
								$basic_info['insurance_id'] = $paymentInsurance;
						}
					}
					
					if (substr($segment, 0, 4) == 'CLP' . $separate) {
						$temp = explode($separate, $segment);
						/* if (!empty($temp[3]))
							$basic_info['check_amount'] = $temp[3]; */
						
						
						if($basic_info['payment_mode'] == 'Check'){
							$checkNo = $basic_info['check_no'];
							$checkDetails = PMTInfoV1::with('checkDetails')
										->whereHas('checkDetails', function ($q) use ($checkNo) {
											$q->where('check_no', '=', $checkNo);
										})
										->where('pmt_method', '=', 'Insurance')
										->where('pmt_mode', '=', 'Check')
										->whereNull('void_check');
							$checkInfo = $checkDetails->get()->first();
							$checkCount = $checkDetails->count();
							if($checkCount > 0)
								$basic_info['payment_detail_id'] =  Helpers::getEncodeAndDecodeOfId($checkInfo->id,'encode');
							else
								$basic_info['payment_detail_id'] = '';
						}elseif($basic_info['payment_mode'] == 'EFT'){
							$checkNo = $basic_info['check_no'];
							$eftDetails = PMTInfoV1::with('eftDetails')
											->whereHas('eftDetails', function ($q) use ($checkNo) {
												$q->where('eft_no', '=', $checkNo);
											})->where('pmt_method', '=', 'Insurance')
											->where('pmt_mode', '=', 'EFT')
											->whereNull('void_check');
							$eftInfo = $eftDetails->get()->first();
							$eftCount = $eftDetails->count();
							if($eftCount > 0)
								$basic_info['payment_detail_id'] =  Helpers::getEncodeAndDecodeOfId($eftInfo->id,'encode');
							else
								$basic_info['payment_detail_id'] = '';
						}
					}
					
					
					if (substr($segment, 0, 4) == 'CLP' . $separate) {
						$temp = explode($separate, $segment);
						$CLPCount ++;
						$tempClaimStatus = '';
						$claim_cpt_count = -1;
						if (!empty($temp[1])){
							$basic_info['claim_number'] = Helpers::getEncodeAndDecodeOfId($temp[1], 'encode');
							$claimDetails = ClaimInfoV1::where('claim_number',$temp[1])
											->get()
											->first();
							if(isset($claimDetails->patient_id)){
								$patientInfo = Patient::where('id',$claimDetails->patient_id)->get()->first();
								$insert_data[$basic_info['check_no']][$CLPCount]['patient_name'] = @$patientInfo->last_name.", ".@$patientInfo->first_name;
								$insert_data[$basic_info['check_no']][$CLPCount]['patient_acct'] = @$patientInfo->account_no;
								$insert_data[$basic_info['check_no']][$CLPCount]['claim_current_insurance_id'] = @$claimDetails->insurance_id;
								$insert_data[$basic_info['check_no']][$CLPCount]['resp'] = (isset($claimDetails->insurance_details->short_name)) ? $claimDetails->insurance_details->short_name : "Patient" ;
							}else{
								$insert_data[$basic_info['check_no']][$CLPCount]['claim_current_insurance_id'] = $insert_data[$basic_info['check_no']][$CLPCount]['patient_acct'] = $insert_data[$basic_info['check_no']][$CLPCount]['resp'] = '-';
							}
							$insert_data[$basic_info['check_no']][$CLPCount]['claim_id'] = Helpers::getEncodeAndDecodeOfId(@$claimDetails->id, 'encode');
							$insert_data[$basic_info['check_no']][$CLPCount]['patient_id'] = Helpers::getEncodeAndDecodeOfId(@$claimDetails->patient_id, 'encode');
							$insert_data[$basic_info['check_no']][$CLPCount] = array_merge($basic_info,$insert_data[$basic_info['check_no']][$CLPCount]);
						}
						if (!empty($temp[2])){
							$processData = $this->processData($temp[2]);
							$insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] = $processData['type'];
							if(isset($basic_info['insurance_id']) && !empty($basic_info['insurance_id']))
								$insert_data[$basic_info['check_no']][$CLPCount]['change_insurance_category'] = ($processData['type'] == 'Primary' || $processData['type'] == 'Secondary' || $processData['type'] == 'Tertiary') ? $processData['type'] : '';
								
							if($insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == "Denied")
								$insert_data[$basic_info['check_no']][$CLPCount]['status'] = 'Denied';
						}
						if (!empty($temp[3]))
							$insert_data[$basic_info['check_no']][$CLPCount]['charge_amount'] = abs($temp[3]);
						if (!empty($temp[4]))
							$insert_data[$basic_info['check_no']][$CLPCount]['claim_paid_amt'] = $insert_data[$basic_info['check_no']][$CLPCount]['charge_paid_amount'] = $temp[4];
						else
							$insert_data[$basic_info['check_no']][$CLPCount]['claim_paid_amt'] = $insert_data[$basic_info['check_no']][$CLPCount]['charge_paid_amount'] = 0;
						
						if($insert_data[$basic_info['check_no']][$CLPCount]['claim_paid_amt'] == 0)
							$insert_data[$basic_info['check_no']][$CLPCount]['status'] = 'Denied';
						
						if (!empty($temp[7]))
							$insert_data[$basic_info['check_no']][$CLPCount]['patient_icn'] = $temp[7];
					}
					if (substr($segment, 0, 7) == 'NM1' . $separate . 'QC' . $separate) {
						$temp = explode($separate, $segment); 
						
						if (!empty($temp[3]))
							$insert_data[$basic_info['check_no']][$CLPCount]['patient_lastname'] = $temp[3];
						if (!empty($temp[4]))
							$insert_data[$basic_info['check_no']][$CLPCount]['patient_firstname'] = $temp[4];
						if(!isset($insert_data[$basic_info['check_no']][$CLPCount]['patient_name']))
							$insert_data[$basic_info['check_no']][$CLPCount]['patient_name'] = $insert_data[$basic_info['check_no']][$CLPCount]['patient_lastname'].", ".$insert_data[$basic_info['check_no']][$CLPCount]['patient_firstname'];
						
						
						if(isset($temp[8]) && ($temp[8] == 'MI' || $temp[8] == 'MR' || $temp[8] == 'HN') && isset($temp[9]) && !empty($temp[9])){
							if(isset($basic_info['insurance_id']) && !empty($basic_info['insurance_id']))
								$insert_data[$basic_info['check_no']][$CLPCount]['insurance_id'] = $basic_info['insurance_id'];
							else
								$insert_data[$basic_info['check_no']][$CLPCount]['insurance_id'] = $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_id'] = $this->findInsuranceID($temp[9]);
							
							$insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_id'] = $this->findInsuranceID($temp[9]);
						}
								
						if(isset($temp[9]))
							$insert_data[$basic_info['check_no']][$CLPCount]['pat_policyId'] = $temp[9];
					}
					
					if ((substr($segment, 0, 7) == 'NM1' . $separate . '74' . $separate) && (empty(@$insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_id']))) {
						$temp = explode($separate, $segment); 
						if(isset($temp[8]) && $temp[8] == 'C' && isset($temp[9]) && !empty($temp[9])){
							if(isset($basic_info['insurance_id']) && !empty($basic_info['insurance_id']))
								$insert_data[$basic_info['check_no']][$CLPCount]['insurance_id'] = $basic_info['insurance_id'];
							else
								$insert_data[$basic_info['check_no']][$CLPCount]['insurance_id'] = $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_id'] = $this->findInsuranceID($temp[9]);
							
							$insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_id'] = $this->findInsuranceID($temp[9]);	
						}
						if(isset($temp[9]))
							$insert_data[$basic_info['check_no']][$CLPCount]['pat_policyId'] = $temp[9];
					}
					
					if ((substr($segment, 0, 7) == 'NM1' . $separate . 'IL' . $separate) && (empty(@$insert_data[$CLPCount]['claim_insurance_id']))) {
						$temp = explode($separate, $segment); 
						if(isset($temp[8]) && ($temp[8] == 'MI' || $temp[8] == 'MR' || $temp[8] == 'HN') && isset($temp[9]) && !empty($temp[9])){
							if(isset($basic_info['insurance_id']) && !empty($basic_info['insurance_id']))
								$insert_data[$basic_info['check_no']][$CLPCount]['insurance_id'] = $basic_info['insurance_id'];
							else
								$insert_data[$basic_info['check_no']][$CLPCount]['insurance_id'] = $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_id'] = $this->findInsuranceID($temp[9]);
							
							$insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_id'] = $this->findInsuranceID($temp[9]);	
						}
						if(isset($temp[9]))
							$insert_data[$basic_info['check_no']][$CLPCount]['pat_policyId'] = $temp[9];
					}
					
					

					if (substr($segment, 0, 4) == 'MOA' . $separate) {
						$temp = explode($separate, $segment);
						if (!empty($temp[3]))
							$insert_data[$basic_info['check_no']][$CLPCount]['patient_moa'] = $temp[3];
					}

					if (substr($segment, 0, 4) == 'DTM' . $separate) {
						$temp = explode($separate, $segment);
						if (!empty($temp[2]) && $temp[1] == 232)
							$insert_data[$basic_info['check_no']][$CLPCount]['start_date'] = $temp[2];
						if (!empty($temp[2]) && $temp[1] == 233)
							$insert_data[$basic_info['check_no']][$CLPCount]['end_date'] = $temp[2];
						if (!empty($temp[2]) && $temp[1] == 050)
							$insert_data[$basic_info['check_no']][$CLPCount]['patient_statement_date'] = date('d/m/Y', strtotime($temp[2]));
					}

					if (substr($segment, 0, 4) == 'SVC' . $separate) {
						$claim_cpt_count ++;
						$remarks_codes = '';
						/* Global Declaration Cpt Variable  */
						$insert_data[$basic_info['check_no']][$CLPCount]['adjustment'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['remarkcode'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['adj_reson_amount'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['ids'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['id'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['paid_amt'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['deductable'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['cpt_billed_amt'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['cpt_allowed_amt'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['co_pay'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['co_ins'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['with_held'][$claim_cpt_count] = 0;
						
						$insert_data[$basic_info['check_no']][$CLPCount]['adj_reason'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['adj_code_desc'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['adj_reson_amount'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['active_lineitem'][$claim_cpt_count] = array();
						
						$temp = explode($separate, $segment);
						$temp_proc = explode($spl_separate, $temp[1]);
						
						if (!empty($temp_proc[1])){
							$insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count] = $temp_proc[1];
							$insert_data[$basic_info['check_no']][$CLPCount]['active_lineitem'][$claim_cpt_count] = 1;
							
						}
						if (!empty($temp[2]))
							$insert_data[$basic_info['check_no']][$CLPCount]['cpt_billed_amt'][$claim_cpt_count] = $temp[2];
						if (!empty($temp[3]))
							$insert_data[$basic_info['check_no']][$CLPCount]['paid_amt'][$claim_cpt_count] = $temp[3];
						
						/* Claim date Only received in era means it will take claim date other wise receive DTM 472 date will take  */
						if (isset($insert_data[$basic_info['check_no']][$CLPCount]['start_date']) && !empty($insert_data[$basic_info['check_no']][$CLPCount]['start_date'])){
						   $insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['start_date'];
						   if($claim_cpt_count >= 0 && isset($insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count]) && !empty($insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count]) && isset($insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count]) && !empty($insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count])){
								$claimCptDetails = 	ClaimCPTInfoV1::where('claim_id',Helpers::getEncodeAndDecodeOfId($insert_data[$basic_info['check_no']][$CLPCount]['claim_id'],'decode'))
														->where('cpt_code',$insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count])
														->where('dos_from',date('Y-m-d',strtotime($insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count])))
														->get()
														->first();
								if(isset($claimCptDetails->id))
									$insert_data[$basic_info['check_no']][$CLPCount]['id'][$claim_cpt_count] = $claimCptDetails->id; 
								$insert_data[$basic_info['check_no']][$CLPCount]['ids'][$claim_cpt_count] = Helpers::getEncodeAndDecodeOfId(@$claimCptDetails->id,'encode'); 
								
							}
						}
					}

					if (substr($segment, 0, 4) == 'DTM' . $separate && $claim_cpt_count != -1) {
						$temp = explode($separate, $segment);
						if (!empty($temp[2]) && $temp[1] == 472)
						   $insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count] = $temp[2];
						if (!empty($temp[2]) && $temp[1] == 150)
						   $insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count] = $temp[2];
					   
						if($claim_cpt_count >= 0 && isset($insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count]) && !empty($insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count]) && isset($insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count]) && !empty($insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count])){
							$claimCptDetails = 	ClaimCPTInfoV1::where('claim_id',Helpers::getEncodeAndDecodeOfId($insert_data[$basic_info['check_no']][$CLPCount]['claim_id'],'decode'))
													->where('cpt_code',$insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count])
													->where('dos_from',date('Y-m-d',strtotime($insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count])))
													->get()
													->first();
							if(isset($claimCptDetails->id))
								$insert_data[$basic_info['check_no']][$CLPCount]['id'][$claim_cpt_count] = $claimCptDetails->id; 
							$insert_data[$basic_info['check_no']][$CLPCount]['ids'][$claim_cpt_count] = Helpers::getEncodeAndDecodeOfId(@$claimCptDetails->id,'encode'); 
						
						}
					}

					if (substr($segment, 0, 4) == 'CAS' . $separate && $claim_cpt_count != -1) {
						$temp = explode($separate, $segment);
						//$temp = array_filter($temp, function($value) { return $value !== ''; });
						$adjCount = 0;
						foreach($temp as $tempData){ 
							$adjTempArr = ['CO45'];
							if($tempData != 'CAS' && $tempData != 'CO' && $tempData != 'OA' && $tempData != 'PI' && $tempData != 'PR' ){
								if($adjCount == 0){
									$prevAdjCode = $temp[1].$tempData;
									$exemptArr = ['PR1', 'PR2','PR3', 'CO1','CO2','CO3', 'OA1','OA2','OA3','PI1','PI2','PI3','CO45'];
									if(!in_array($prevAdjCode, $exemptArr)){
										$insert_data[$basic_info['check_no']][$CLPCount]['adj_code_desc'][$claim_cpt_count][] = $prevAdjCode;
										$codeStatus = $this->getCodeStatus($prevAdjCode);
										$adjArr = ['Adjustment'];
										if($insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Primary' || $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Primary Fwd' || $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Denied'){
											if(in_array($codeStatus, $adjArr)){
												$insert_data[$basic_info['check_no']][$CLPCount]['adj_reason'][$claim_cpt_count][] = $this->checkAdjReason($temp[1],$tempData);
											}else{
												/* $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] = 'Denied';
												$tempClaimStatus = 'Denied';
												$insert_data[$basic_info['check_no']][$CLPCount]['status'] = 'Denied'; */
												$remarks_codes .= $temp[1].$tempData.',';
											}
											if($tempData !='253'){
												if($insert_data[$basic_info['check_no']][$CLPCount]['claim_paid_amt'] == 0)
													$remarks_codes .= $temp[1].$tempData.',';
											}
										}else{
											if($tempData !='253'){
												if($insert_data[$basic_info['check_no']][$CLPCount]['claim_paid_amt'] == 0)
													$remarks_codes .= $temp[1].$tempData.',';
											}
											$insert_data[$basic_info['check_no']][$CLPCount]['adj_reason'][$claim_cpt_count][] = '';
										}
									}
									$adjCount ++;
								}elseif($adjCount == 2){
									$adjCount = 0;
								}elseif($adjCount == 1){
									$adjCount ++; 
									if($prevAdjCode == 'PR1' || $prevAdjCode == 'CO1' || $prevAdjCode == 'OA1' || $prevAdjCode == 'PI1'){
										$insert_data[$basic_info['check_no']][$CLPCount]['deductable'][$claim_cpt_count] = $tempData;
										$prevAdjCode = '';
										if($insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] != "Denied")
											$insert_data[$basic_info['check_no']][$CLPCount]['status'] = '';
									}elseif($prevAdjCode == 'PR2' || $prevAdjCode == 'CO2' || $prevAdjCode == 'OA2' || $prevAdjCode == 'PI2'){
										$insert_data[$basic_info['check_no']][$CLPCount]['co_ins'][$claim_cpt_count] = $tempData;
										$prevAdjCode = '';
									}elseif($prevAdjCode == 'PR3' || $prevAdjCode == 'CO3' || $prevAdjCode == 'OA3' || $prevAdjCode == 'PI3'){
										$insert_data[$basic_info['check_no']][$CLPCount]['co_pay'][$claim_cpt_count] = $tempData;
										$prevAdjCode = '';
										if($insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] != "Denied")
											$insert_data[$basic_info['check_no']][$CLPCount]['status'] = '';
									}elseif(in_array($prevAdjCode, $adjTempArr) && ($insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Primary' || $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Primary Fwd')){
										$insert_data[$basic_info['check_no']][$CLPCount]['adjustment'][$claim_cpt_count] = $tempData;
										$prevAdjCode = '';
									}elseif(!empty($tempData)){
										if($insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Primary' || $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Primary Fwd'){
											$codeStatus = $this->getCodeStatus($prevAdjCode);
											$adjArr = ['Adjustment'];
											if(in_array($codeStatus, $adjArr)){
												$insert_data[$basic_info['check_no']][$CLPCount]['adj_reson_amount'][$claim_cpt_count][] = $tempData;
											}else{
												$insert_data[$basic_info['check_no']][$CLPCount]['adj_reson_amount'][$claim_cpt_count][] = 0;
											}
										}else{
											$insert_data[$basic_info['check_no']][$CLPCount]['adj_reson_amount'][$claim_cpt_count][] = 0;
										}
									}
								}
							}
						}
						// Adding remark codes in payment transaction 
						$insert_data[$basic_info['check_no']][$CLPCount]['remarkcode'][$claim_cpt_count] = $remarks_codes;
					} 
					
					if (substr($segment, 0, 6) == 'LQ' . $separate."HE". $separate && $claim_cpt_count != -1) {
						$temp = explode($separate, $segment);
						if(isset($insert_data[$basic_info['check_no']][$CLPCount]['remarkcode'][$claim_cpt_count])){
							$tempRemarkCode = '';
							$tempRemarkCode .= $remarks_codes;
							$remarkCode = (isset($temp[2])) ? $temp[2] : '';
							// Check and Add the remark code in codes table
							// Revision 1 : MR-2774 : 12 Sep 2019 : Selva
							$this->checkRemarkCode($remarkCode);
							if(isset($insert_data[$basic_info['check_no']][$CLPCount]['remarkcode'][$claim_cpt_count]))
								$tempRemarkCode =  $insert_data[$basic_info['check_no']][$CLPCount]['remarkcode'][$claim_cpt_count].$remarkCode.",";
							else
								$tempRemarkCode =  $tempRemarkCode.$remarkCode.",";
							
							$insert_data[$basic_info['check_no']][$CLPCount]['remarkcode'][$claim_cpt_count] = $tempRemarkCode; 
						}
					}
					if (substr($segment, 0, 7) == 'AMT' . $separate . 'B6' . $separate && $claim_cpt_count != -1) {
						$temp = explode($separate, $segment);
						if (!empty($temp[2]))
							$insert_data[$basic_info['check_no']][$CLPCount]['cpt_allowed_amt'][$claim_cpt_count] = $temp[2];
							if($insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Primary' || $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Primary Fwd')
								$insert_data[$basic_info['check_no']][$CLPCount]['with_held'][$claim_cpt_count] = array_sum($insert_data[$basic_info['check_no']][$CLPCount]['adj_reson_amount'][$claim_cpt_count]);  
							else
								$insert_data[$basic_info['check_no']][$CLPCount]['with_held'][$claim_cpt_count] = 0;
					}
					
				}
			}
			
			
			/* foreach($insert_data[$fileCheckNo] as $data){
				$claimsId = Helpers::getEncodeAndDecodeOfId($data['claim_id'],'decode');
				$claimCptInfo = ClaimCPTInfoV1::where('claim_id',$claimsId);
				$claimCptCount = $claimCptInfo->count();
				$claimCptDetails = $claimCptInfo->get()->toArray();
				
				$count = count($data['ids']);
				foreach($claimCptDetails as $list){
					if($claimCptCount != count($data['ids'])){
						$claimsCPTID = Helpers::getEncodeAndDecodeOfId($list['id'],'encode');
						if(!in_array($claimsCPTID,$data['ids'])){
							$count++;
							array_push($data['with_held'],0);
							array_push($data['co_ins'],0);
							array_push($data['co_pay'],0);
							array_push($data['cpt_allowed_amt'],0);
							array_push($data['cpt_billed_amt'],$list['charge']);
							array_push($data['deductable'],0);
							array_push($data['paid_amt'],0);
							array_push($data['cpt'],$list['cpt_code']);
							array_push($data['ids'],Helpers::getEncodeAndDecodeOfId($list['id'],'encode'));
							array_push($data['remarkcode'],'');
							array_push($data['adjustment'],0);
							array_push($data['active_lineitem'],1);
							$data['adj_reason'][$count][] = '';
							$data['adj_reson_amount'][$count][] = '';
							array_push($data['dos_from'],date('m/d/y',strtotime($list['dos_from'])));
						}
					}
				}
				
				$claimsBalanceInfo = PMTClaimFINV1::where('claim_id',$claimsId)->get()->first();
				if(isset($claimsBalanceInfo)){
					if($claimsBalanceInfo->insurance_due < 0)
						$insuranceDue = 0;
					else
						$insuranceDue = $claimsBalanceInfo->insurance_due;
					$claimsBalance = $claimsBalanceInfo->patient_due + $insuranceDue;
				}else{
					$claimsBalance = array_sum($data['cpt_billed_amt']);
				}
				$paidAmt = array_sum($data['paid_amt']);
				$adjAmt = 0;
				foreach($data['adj_reson_amount'] as $adjsumList){
					$adjAmt = $adjAmt + array_sum($adjsumList);
				}
				$witheldAmt = array_sum($data['with_held']);
				$totalAmt = $paidAmt + $adjAmt + $witheldAmt;
				if($totalAmt == $claimsBalance){
					$data['next_responsibility'] = $data['claim_insurance_type'].'-'.$data['claim_current_insurance_id'];
				}
			}
			die; */
			/* \Log::info('Insert data Array');
			\Log::info($insert_data);
			\Log::info('Check No'.$fileCheckNo); */
			DB::beginTransaction();
            try {
				$counts= 0;
				foreach($insert_data[$fileCheckNo] as $data){
					if(isset($data['ids'])){
						$claimsId = Helpers::getEncodeAndDecodeOfId($data['claim_id'],'decode');
						$claimCptInfo = ClaimCPTInfoV1::where('claim_id',$claimsId);
						$claimCptCount = $claimCptInfo->count();
						$claimCptDetails = $claimCptInfo->get()->toArray();
						
						$count = count($data['ids']) - 1;
						foreach($claimCptDetails as $list){
							if($claimCptCount != count($data['ids'])){
								$claimsCPTID = Helpers::getEncodeAndDecodeOfId($list['id'],'encode');
								if(!in_array($claimsCPTID,$data['ids'])){
									$count++;
									array_push($data['with_held'],0);
									array_push($data['co_ins'],0);
									array_push($data['co_pay'],0);
									array_push($data['cpt_allowed_amt'],0);
									array_push($data['cpt_billed_amt'],$list['charge']);
									array_push($data['deductable'],0);
									array_push($data['paid_amt'],0);
									array_push($data['cpt'],$list['cpt_code']);
									array_push($data['ids'],Helpers::getEncodeAndDecodeOfId($list['id'],'encode'));
									array_push($data['remarkcode'],'');
									array_push($data['adjustment'],0);
									array_push($data['active_lineitem'],1);
									$data['adj_reason'][$count][] = '';
									$data['adj_reson_amount'][$count][] = '';
									array_push($data['dos_from'],date('m/d/y',strtotime($list['dos_from'])));
								}
							}
						}
						
						$claimsBalanceInfo = PMTClaimFINV1::where('claim_id',$claimsId)->get()->first();
						if(isset($claimsBalanceInfo)){
							if($claimsBalanceInfo->insurance_due < 0)
								$insuranceDue = 0;
							else
								$insuranceDue = $claimsBalanceInfo->insurance_due;
							$claimsBalance = $claimsBalanceInfo->patient_due + $insuranceDue;
						}else{
							$claimsBalance = array_sum($data['cpt_allowed_amt']);
						}
						$paidAmt = array_sum($data['paid_amt']);
						$adjAmt = 0;
						foreach($data['adj_reson_amount'] as $adjsumList){
							$adjAmt = $adjAmt + array_sum($adjsumList);
						}
						$witheldAmt = array_sum($data['with_held']);
						$totalAmt = $paidAmt + $adjAmt + $witheldAmt;
						if($totalAmt == $claimsBalance){
							// added conditioned for insurance id not equal to zero
							if($data['claim_current_insurance_id'] != 0)
								$data['next_responsibility'] = $data['claim_insurance_type'].'-'.$data['claim_current_insurance_id'];
						}
						$counts++;
						$checkNo = $data['check_no'];
						if($data['payment_mode'] == 'Check'){
							$checkDetails = PMTInfoV1::with('checkDetails')
										->whereHas('checkDetails', function ($q) use ($checkNo) {
											$q->where('check_no', '=', $checkNo);
										})
										->where('pmt_method', '=', 'Insurance')
										->where('pmt_mode', '=', 'Check')
										->whereNull('void_check');
							$checkInfo = $checkDetails->get()->first();
							$checkCount = $checkDetails->count();
							if($checkCount > 0)
								$data['payment_detail_id'] =  Helpers::getEncodeAndDecodeOfId($checkInfo->id,'encode');
							else
								$data['payment_detail_id'] = '';
						}elseif($data['payment_mode'] == 'EFT'){
							$eftDetails = PMTInfoV1::with('eftDetails')
											->whereHas('eftDetails', function ($q) use ($checkNo) {
												$q->where('eft_no', '=', $checkNo);
											})->where('pmt_method', '=', 'Insurance')
											->where('pmt_mode', '=', 'EFT')
											->whereNull('void_check');
							$eftInfo = $eftDetails->get()->first();
							$eftCount = $eftDetails->count();
							if($eftCount > 0)
								$data['payment_detail_id'] =  Helpers::getEncodeAndDecodeOfId($eftInfo->id,'encode');
							else
								$data['payment_detail_id'] = '';
						}
						
						$ruleEngineContoller = new RuleEngineContoller();
						$dataArr = $ruleEngineContoller->getRuleEngine($data['adj_code_desc']);
						

						 if(isset($dataArr)){
							$tempRuleArr['ruleEngineClaimStatus'] = $dataArr['claim_status'];
							$tempRuleArr['ruleEngineNextResp'] = $dataArr['next_resp'];
							//dd($dataArr['reason_type']);
							ClaimInfoV1::where('id',$claimsId)->update(array('reason_type'=>$dataArr['reason_type']));
							
							if(empty($data['next_responsibility'])){
								if($dataArr['next_resp'] == 'Next'){
									$data['next_responsibility'] = '';
								}elseif($dataArr['next_resp'] == 'Same'){
									$claimInfodetails = ClaimInfoV1::where('id',$claimsId)->get()->first();
									if(isset($claimInfodetails->insurance_category) && !empty($claimInfodetails->insurance_category) && isset($claimInfodetails->insurance_id) && !empty($claimInfodetails->insurance_id))
										$data['next_responsibility'] = $claimInfodetails->insurance_category."-".$claimInfodetails->insurance_id;
									else
										$data['next_responsibility'] = 'patient';
								}elseif($dataArr['next_resp'] == 'Patient'){
									$data['next_responsibility'] = 'patient';
								}
							}
							
							if(isset($data['status']) && empty($data['status'])){
								if($dataArr['claim_status'] == 'Denied')
									$data['status'] = 'Denied';
								elseif($dataArr['claim_status'] == 'Patient')
									$data['status'] = 'Patient';
							}
							
						} 
						
					
						$timeStamp = microtime();
						$errorMsg = $this->autoPostValidation($data,$timeStamp);	
						$claimNumber = Helpers::getEncodeAndDecodeOfId($data['claim_number'],'decode');
						
						$postedClaim[$fileCheckNo]['insurance_company'] = $data['insurance_company'];
						$postedClaim[$fileCheckNo]['check_no'] = $data['check_no'];
						$postedClaim[$fileCheckNo]['check_date'] = $data['check_date'];
						$postedClaim[$fileCheckNo]['check_amount'] = $data['check_amount'];
						if(count($errorMsg) == 0){
							
							$PaymentProcess = new PaymentV1ApiController();
							$PaymentProcess->insurancePaymentProcessHandler($data);
							$postedClaim[$fileCheckNo]['posted'][$timeStamp]['patient_name'] = (isset($data['patient_name']) ? $data['patient_name'] : "-" );
							$postedClaim[$fileCheckNo]['posted'][$timeStamp]['patient_acct'] = (isset($data['patient_acct']) ? $data['patient_acct'] : "-" );
							$postedClaim[$fileCheckNo]['posted'][$timeStamp]['dos_from'] = @$data['dos_from'];
							$postedClaim[$fileCheckNo]['posted'][$timeStamp]['cpt_billed_amt'] = @$data['cpt_billed_amt'];
							$postedClaim[$fileCheckNo]['posted'][$timeStamp]['cpt_allowed_amt'] = @$data['cpt_allowed_amt'];
							$postedClaim[$fileCheckNo]['posted'][$timeStamp]['co_ins'] = @$data['co_ins'];
							$postedClaim[$fileCheckNo]['posted'][$timeStamp]['co_pay'] = @$data['co_pay'];
							$postedClaim[$fileCheckNo]['posted'][$timeStamp]['deductable'] = @$data['deductable'];
							$postedClaim[$fileCheckNo]['posted'][$timeStamp]['adj_reson_amount'] = @$data['adj_reson_amount'];
							$postedClaim[$fileCheckNo]['posted'][$timeStamp]['pat_policyId'] = @$data['pat_policyId'];
							$postedClaim[$fileCheckNo]['posted'][$timeStamp]['resp'] = @$data['resp'];
							$postedClaim[$fileCheckNo]['posted'][$timeStamp]['paid_amt'] = @$data['paid_amt'];
							$postedClaim[$fileCheckNo]['posted'][$timeStamp]['cpt'] = @$data['cpt'];
							$postedClaim[$fileCheckNo]['posted'][$timeStamp]['claim_id'] = @$data['claim_id'];
							$postedClaim[$fileCheckNo]['posted'][$timeStamp]['patient_id'] = @$data['patient_id'];
							$postedClaim[$fileCheckNo]['posted'][$timeStamp]['status'] = "Posted";
							$postedClaim[$fileCheckNo]['posted'][$timeStamp]['claim_no'] = $claimNumber;
							$claimPostedStatus[$data['check_no']][$claimNumber] = "Yes";
						}else{  
							$eras_details = Eras::where('check_no',$data['check_no'])->get()->first();
							$claims = json_decode($eras_details->claim_nos,true);
							if(isset($claims[$claimNumber]) && $claims[$claimNumber] == 'Yes'){
								$postedClaim[$fileCheckNo]['posted'][$timeStamp]['error'][] = $errorMsg[$timeStamp];	
								$postedClaim[$fileCheckNo]['posted'][$timeStamp]['patient_name'] = (isset($data['patient_name']) ? $data['patient_name'] : "-" );
								$postedClaim[$fileCheckNo]['posted'][$timeStamp]['patient_acct'] = (isset($data['patient_acct']) ? $data['patient_acct'] : "-" );
								$postedClaim[$fileCheckNo]['posted'][$timeStamp]['dos_from'] = @$data['dos_from'];
								$postedClaim[$fileCheckNo]['posted'][$timeStamp]['cpt_billed_amt'] = @$data['cpt_billed_amt'];
								$postedClaim[$fileCheckNo]['posted'][$timeStamp]['cpt_allowed_amt'] = @$data['cpt_allowed_amt'];
								$postedClaim[$fileCheckNo]['posted'][$timeStamp]['co_ins'] = @$data['co_ins'];
								$postedClaim[$fileCheckNo]['posted'][$timeStamp]['co_pay'] = @$data['co_pay'];
								$postedClaim[$fileCheckNo]['posted'][$timeStamp]['deductable'] = @$data['deductable'];
								$postedClaim[$fileCheckNo]['posted'][$timeStamp]['adj_reson_amount'] = @$data['adj_reson_amount'];
								$postedClaim[$fileCheckNo]['posted'][$timeStamp]['pat_policyId'] = @$data['pat_policyId'];
								$postedClaim[$fileCheckNo]['posted'][$timeStamp]['resp'] = @$data['resp'];
								$postedClaim[$fileCheckNo]['posted'][$timeStamp]['paid_amt'] = @$data['paid_amt'];
								$postedClaim[$fileCheckNo]['posted'][$timeStamp]['cpt'] = @$data['cpt'];
								$postedClaim[$fileCheckNo]['posted'][$timeStamp]['claim_id'] = @$data['claim_id'];
								$postedClaim[$fileCheckNo]['posted'][$timeStamp]['patient_id'] = @$data['patient_id'];
								$claimPostedStatus[$data['check_no']][$claimNumber] = "Yes";
								$postedClaim[$fileCheckNo]['posted'][$timeStamp]['status'] = "Posted";
								$postedClaim[$fileCheckNo]['posted'][$timeStamp]['claim_no'] = $claimNumber;
							}else{
								$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['error'][] = $errorMsg[$timeStamp];	
								$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['patient_name'] = (isset($data['patient_name']) ? $data['patient_name'] : "-" );
								$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['patient_acct'] = (isset($data['patient_acct']) ? $data['patient_acct'] : "-" );
								$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['dos_from'] = @$data['dos_from'];
								$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['cpt_billed_amt'] = @$data['cpt_billed_amt'];
								$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['cpt_allowed_amt'] = @$data['cpt_allowed_amt'];
								$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['co_ins'] = @$data['co_ins'];
								$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['co_pay'] = @$data['co_pay'];
								$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['deductable'] = @$data['deductable'];
								$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['adj_reson_amount'] = @$data['adj_reson_amount'];
								$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['pat_policyId'] = @$data['pat_policyId'];
								$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['resp'] = @$data['resp'];
								$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['paid_amt'] = @$data['paid_amt'];
								$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['cpt'] = @$data['cpt'];
								$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['claim_id'] = @$data['claim_id'];
								$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['patient_id'] = @$data['patient_id'];
								$claimPostedStatus[$data['check_no']][$claimNumber] = "No";
								$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['status'] = "Unposted";
								$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['claim_no'] = $claimNumber;
							}	
						}
					
					}
				}
				
				foreach($claimPostedStatus as $key => $value){
					Eras::where('check_no',$key)->update(['claim_nos'=>json_encode($value),'status'=>'Yes']);
				}
				DB::commit();
			} catch (\Exception $e) {
                DB::rollback();
                \Log::info("Auto Posting Error Log : ". $e);
            }
		}
		
		$pageType = "Era Posting Popup";
		return Response::view('payments/payments/autoPostResponse', compact('postedClaim', 'pageType'));
	}
	
	/* 
		DESC : Showing Era posted and new era status in pop up
		Author : selvakumar V
		Date : 04/01/2019
	*/
	
	public function getAutoPostStatus($checkNo,$id){
		$checkNo = str_replace('XXOOXX',' ',$checkNo);
		$insert_data = $basic_info = $postedClaim = $unpostedClaim = array();
		$erasInfo = Eras::where('check_no', $checkNo)->where('id',$id)->get()->first();
		$filename = $erasInfo->pdf_name;
		$fileCheckNo = $erasInfo->check_no;
		$fileCheckNo = preg_replace("/[^a-zA-Z0-9]/", "", $fileCheckNo);
		$eraFileName = explode('.',$filename);

		$orgERAFolderName = $eraFileName[0];
		$orgERAFileName = str_replace('STATUS','835',$eraFileName[0]).'.835';
		$path_medcubic = public_path() . '/';
		$local_path = $path_medcubic . 'media/era_files/' . Session::get('practice_dbid') . '/'.$orgERAFolderName.'/';
		$glossary = $basic_info = $insert_data = [];
		$fileReceivedDate = $erasInfo->receive_date;
		foreach (glob($local_path . $orgERAFileName) as $list) {

			$file_content = file($list);
			$file_full_content = explode('~', $file_content[0]);

			$symb_check = implode('', $file_full_content);
			$first_segment = $file_full_content[0];
			if (count(explode('|', $symb_check)) > 5) {
				$separate = "|";
			} elseif (count(explode('*', $symb_check)) > 1) {
				$separate = "*";
			}
			$spl_symb = explode($separate, $first_segment);
			$spl_separate = $spl_symb[16];
			$basic_info['payment_method'] = $basic_info['type'] = 'Insurance';
			$basic_info['card_type'] = $basic_info['next_insurance_id'] = $basic_info['insurance_cat'] = $basic_info['eob_id'] = $basic_info['checkexist'] = $basic_info['reference'] =  $basic_info['next'] = $basic_info['resubmit'] = $basic_info['adjustment_reason'] = $basic_info['content'] = $basic_info['payment_hold_reason'] = $basic_info['status'] = '';
			$basic_info['patient_paid'] = '0.00';
			$basic_info['deposite_date'] = date('m/d/Y');
			$basic_info['payment_mode'] = 'EFT';
			$basic_info['payment_type'] = 'Payment';
			
			foreach ($file_full_content as $key => $segment) {
				if (substr($segment, 0, 3) == 'ST' . $separate) {
					$CLPCount = -1;
					$claim_cpt_count = 0;
				}
				
				if (substr($segment, 0, 6) == 'N1' . $separate . 'PR' . $separate) {
					$temp = explode($separate, $segment);
					if (!empty($temp[2]))
						$basic_info['insurance_company'] = $temp[2];
				}
				
				/* Getting check no */
				if (substr($segment, 0, 4) == 'BPR' . $separate) {
					$temp = explode($separate, $segment);
					if (!empty($temp[16]))
						$basic_info['check_date'] = date('Y-m-d', strtotime($temp[16]));
					if (!empty($temp[2]))
						$basic_info['check_amount'] = $basic_info['payment_amt'] = $basic_info['check_paid_amount'] = $temp[2];
					else
						$basic_info['check_amount'] = $basic_info['payment_amt'] = $basic_info['check_paid_amount'] = 0;
					if((!empty($temp[4])) && $temp[4] != 'CHK')
						$basic_info['payment_mode'] = 'EFT';
					else
						$basic_info['payment_mode'] = 'Check';
						
				}
				
				/* Getting check no */
				if (substr($segment, 0, 4) == 'TRN' . $separate) {
					$temp = explode($separate, $segment);
					if (!empty($temp[2])){
						// Remove remove special characters from cheque no in era
						// Revision 1 : MR-2799 : 6 Sep 2019 : Selva
						$temp[2] = preg_replace("/[^a-zA-Z0-9]/", "", $temp[2]);
						$basic_info['check_no'] = $temp[2];
					}
					
					if($basic_info['payment_mode'] == 'Check'){
						$checkNo = $basic_info['check_no'];
						$checkDetails = PMTInfoV1::with('checkDetails')
									->whereHas('checkDetails', function ($q) use ($checkNo) {
										$q->where('check_no', '=', $checkNo);
									})
									->where('pmt_method', '=', 'Insurance')
									->where('pmt_mode', '=', 'Check')
									->whereNull('void_check');
						$checkInfo = $checkDetails->get()->first();
						$checkCount = $checkDetails->count();
						if($checkCount > 0)
							$basic_info['payment_detail_id'] =  Helpers::getEncodeAndDecodeOfId($checkInfo->id,'encode');
						else
							$basic_info['payment_detail_id'] = '';
					}elseif($basic_info['payment_mode'] == 'EFT'){
						$checkNo = $basic_info['check_no'];
						$eftDetails = PMTInfoV1::with('eftDetails')
										->whereHas('eftDetails', function ($q) use ($checkNo) {
											$q->where('eft_no', '=', $checkNo);
										})->where('pmt_method', '=', 'Insurance')
										->where('pmt_mode', '=', 'EFT')
										->whereNull('void_check');
						$eftInfo = $eftDetails->get()->first();
						$eftCount = $eftDetails->count();
						if($eftCount > 0)
							$basic_info['payment_detail_id'] =  Helpers::getEncodeAndDecodeOfId($eftInfo->id,'encode');
						else
							$basic_info['payment_detail_id'] = '';
					}
				}
				

				if (substr($segment, 0, 4) == 'CLP' . $separate) {
					$temp = explode($separate, $segment);
					$CLPCount ++;
					$tempClaimStatus = '';
					$claim_cpt_count = -1;
					if (!empty($temp[1])){
						$basic_info['claim_number'] = Helpers::getEncodeAndDecodeOfId($temp[1], 'encode');
						$claimDetails = ClaimInfoV1::with('insurance_details')->where('claim_number',$temp[1])
										->get()
										->first(); 
						if(isset($claimDetails->patient_id)){
							$patientInfo = Patient::where('id',$claimDetails->patient_id)->get()->first();
							$insert_data[$basic_info['check_no']][$CLPCount]['patient_name'] = @$patientInfo->last_name.", ".@$patientInfo->first_name;
							$insert_data[$basic_info['check_no']][$CLPCount]['patient_acct'] = @$patientInfo->account_no;
							$insert_data[$basic_info['check_no']][$CLPCount]['claim_current_insurance_id'] = @$claimDetails->insurance_id;
							$insert_data[$basic_info['check_no']][$CLPCount]['resp'] = (isset($claimDetails->insurance_details->short_name)) ? $claimDetails->insurance_details->short_name : "Patient" ;
						} else{
							$insert_data[$basic_info['check_no']][$CLPCount]['claim_current_insurance_id'] = $insert_data[$basic_info['check_no']][$CLPCount]['patient_acct'] = $insert_data[$basic_info['check_no']][$CLPCount]['resp'] = '-';
						}
						$insert_data[$basic_info['check_no']][$CLPCount]['claim_id'] = Helpers::getEncodeAndDecodeOfId(@$claimDetails->id, 'encode');
						$insert_data[$basic_info['check_no']][$CLPCount]['patient_id'] = Helpers::getEncodeAndDecodeOfId(@$claimDetails->patient_id, 'encode');
						$insert_data[$basic_info['check_no']][$CLPCount] = array_merge($basic_info,$insert_data[$basic_info['check_no']][$CLPCount]);
					}
					if (!empty($temp[2])){
						$processData = $this->processData($temp[2]);
						$insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] = $processData['type'];
						if($insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Denied')
							$insert_data[$basic_info['check_no']][$CLPCount]['status'] = 'Denied';
					}
					if (!empty($temp[3]))
						$insert_data[$basic_info['check_no']][$CLPCount]['charge_amount'] = abs($temp[3]);
					if (!empty($temp[4]))
						$insert_data[$basic_info['check_no']][$CLPCount]['claim_paid_amt'] = $insert_data[$basic_info['check_no']][$CLPCount]['charge_paid_amount'] = $temp[4];
					else
						$insert_data[$basic_info['check_no']][$CLPCount]['claim_paid_amt'] = $insert_data[$basic_info['check_no']][$CLPCount]['charge_paid_amount'] = 0;
					
					if($insert_data[$basic_info['check_no']][$CLPCount]['claim_paid_amt'] == 0)
						$insert_data[$basic_info['check_no']][$CLPCount]['status'] = 'Denied';
					
					
					if (!empty($temp[7]))
						$insert_data[$basic_info['check_no']][$CLPCount]['patient_icn'] = $temp[7];
				}
				if (substr($segment, 0, 7) == 'NM1' . $separate . 'QC' . $separate) {
					$temp = explode($separate, $segment);
					
					if (!empty($temp[3]))
                        $insert_data[$basic_info['check_no']][$CLPCount]['patient_lastname'] = $temp[3];
                    if (!empty($temp[4]))
                        $insert_data[$basic_info['check_no']][$CLPCount]['patient_firstname'] = $temp[4];
					if(!isset($insert_data[$basic_info['check_no']][$CLPCount]['patient_name']))
						$insert_data[$basic_info['check_no']][$CLPCount]['patient_name'] = $insert_data[$basic_info['check_no']][$CLPCount]['patient_lastname'].", ".$insert_data[$basic_info['check_no']][$CLPCount]['patient_firstname'];
					
					if(isset($temp[8]) && ($temp[8] == 'MI' || $temp[8] == 'MR' || $temp[8] == 'HN') && isset($temp[9]) && !empty($temp[9]))
						$insert_data[$basic_info['check_no']][$CLPCount]['insurance_id'] = $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_id'] = $this->findInsuranceID($temp[9]);	
					
					if(isset($temp[9]) && !empty($temp[9]))
						$insert_data[$basic_info['check_no']][$CLPCount]['pat_policyId'] = $temp[9];
				}
				
				if ((substr($segment, 0, 7) == 'NM1' . $separate . '74' . $separate) && (empty(@$insert_data[$basic_info['check_no']][$CLPCount]['insurance_id']))) {
					$temp = explode($separate, $segment);
					if(isset($temp[8]) && $temp[8] == 'C' && isset($temp[9]) && !empty($temp[9]))
						$insert_data[$basic_info['check_no']][$CLPCount]['insurance_id'] = $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_id'] = $this->findInsuranceID($temp[9]);
					if(isset($temp[9]) && !empty($temp[9]))
						$insert_data[$basic_info['check_no']][$CLPCount]['pat_policyId'] = $temp[9];
				}
				
				if ((substr($segment, 0, 7) == 'NM1' . $separate . 'IL' . $separate) && (empty(@$insert_data[$basic_info['check_no']][$CLPCount]['insurance_id']))) {
					$temp = explode($separate, $segment);
					if(isset($temp[8]) && ($temp[8] == 'MI' || $temp[8] == 'MR' || $temp[8] == 'HN') && isset($temp[9]) && !empty($temp[9]))
						$insert_data[$basic_info['check_no']][$CLPCount]['insurance_id'] = $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_id'] = $this->findInsuranceID($temp[9]);	
					if(isset($temp[9]) && !empty($temp[9]))
						$insert_data[$basic_info['check_no']][$CLPCount]['pat_policyId'] = $temp[9];
				}

				

				if (substr($segment, 0, 4) == 'MOA' . $separate) {
					$temp = explode($separate, $segment);
					if (!empty($temp[3]))
						$insert_data[$basic_info['check_no']][$CLPCount]['patient_moa'] = $temp[3];
				}

				if (substr($segment, 0, 4) == 'DTM' . $separate) {
					$temp = explode($separate, $segment);
					if (!empty($temp[2]) && $temp[1] == 232)
						$insert_data[$basic_info['check_no']][$CLPCount]['start_date'] = $temp[2];
					if (!empty($temp[2]) && $temp[1] == 233)
						$insert_data[$basic_info['check_no']][$CLPCount]['end_date'] = $temp[2];
					if (!empty($temp[2]) && $temp[1] == 050)
						$insert_data[$basic_info['check_no']][$CLPCount]['patient_statement_date'] = date('d/m/Y', strtotime($temp[2]));
				}

				if (substr($segment, 0, 4) == 'SVC' . $separate) {
					$claim_cpt_count ++;
					$remarks_codes = '';
					/* Global Declaration Cpt Variable  */
					$insert_data[$basic_info['check_no']][$CLPCount]['adjustment'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['remarkcode'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['adj_reson_amount'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['ids'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['paid_amt'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['deductable'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['cpt_billed_amt'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['cpt_allowed_amt'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['co_pay'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['co_ins'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['with_held'][$claim_cpt_count] = 0;
					
					$insert_data[$basic_info['check_no']][$CLPCount]['adj_reason'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['adj_reson_amount'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['active_lineitem'][$claim_cpt_count] = array();
					
					$temp = explode($separate, $segment);
					$temp_proc = explode($spl_separate, $temp[1]);
					
					if (!empty($temp_proc[1])){
						$insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count] = $temp_proc[1];
						$insert_data[$basic_info['check_no']][$CLPCount]['active_lineitem'][$claim_cpt_count] = 1;
						
					}
					if (!empty($temp[2]))
						$insert_data[$basic_info['check_no']][$CLPCount]['cpt_billed_amt'][$claim_cpt_count] = $temp[2];
					if (!empty($temp[3]))
						$insert_data[$basic_info['check_no']][$CLPCount]['paid_amt'][$claim_cpt_count] = $temp[3];
					
					/* Claim date Only received in era means it will take claim date other wise receive DTM 472 date will take  */
					if (isset($insert_data[$basic_info['check_no']][$CLPCount]['start_date']) && !empty($insert_data[$basic_info['check_no']][$CLPCount]['start_date'])){
					   $insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['start_date'];
						if($claim_cpt_count >= 0 && isset($insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count]) && !empty($insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count]) && isset($insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count]) && !empty($insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count])){ 
							$claimCptDetails = 	ClaimCPTInfoV1::where('claim_id',Helpers::getEncodeAndDecodeOfId($insert_data[$basic_info['check_no']][$CLPCount]['claim_id'],'decode'))
													->where('cpt_code',$insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count])
													->where('dos_from',date('Y-m-d',strtotime($insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count])))
													->get()
													->first();
							if(isset($claimCptDetails->id))
								$insert_data[$basic_info['check_no']][$CLPCount]['id'][$claim_cpt_count] = $claimCptDetails->id; 
							$insert_data[$basic_info['check_no']][$CLPCount]['ids'][$claim_cpt_count] = Helpers::getEncodeAndDecodeOfId(@$claimCptDetails->id,'encode'); 
								
						}
					}
				}

				if (substr($segment, 0, 4) == 'DTM' . $separate && $claim_cpt_count != -1) {
					$temp = explode($separate, $segment);
					if (!empty($temp[2]) && $temp[1] == 472)
					   $insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count] = $temp[2];
					if (!empty($temp[2]) && $temp[1] == 150)
					   $insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count] = $temp[2];
				   
					if($claim_cpt_count >= 0 && isset($insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count]) && !empty($insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count]) && isset($insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count]) && !empty($insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count])){ 
						$claimCptDetails = 	ClaimCPTInfoV1::where('claim_id',Helpers::getEncodeAndDecodeOfId($insert_data[$basic_info['check_no']][$CLPCount]['claim_id'],'decode'))
												->where('cpt_code',$insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count])
												->where('dos_from',date('Y-m-d',strtotime($insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count])))
												->get()
												->first();
						if(isset($claimCptDetails->id))
							$insert_data[$basic_info['check_no']][$CLPCount]['id'][$claim_cpt_count] = $claimCptDetails->id; 
						$insert_data[$basic_info['check_no']][$CLPCount]['ids'][$claim_cpt_count] = Helpers::getEncodeAndDecodeOfId(@$claimCptDetails->id,'encode'); 	
					}
				}

				if (substr($segment, 0, 4) == 'CAS' . $separate && $claim_cpt_count != -1) {
					$temp = explode($separate, $segment);
					//$temp = array_filter($temp, function($value) { return $value !== ''; });
					$adjCount = 0;
					
					foreach($temp as $tempData){ 
						$adjTempArr = ['CO45'];
						if($tempData != 'CAS' && $tempData != 'CO' && $tempData != 'OA' && $tempData != 'PI' && $tempData != 'PR' ){
							if($adjCount == 0){
								$prevAdjCode = $temp[1].$tempData;
								$exemptArr = ['PR1', 'PR2','PR3', 'CO1','CO2','CO3', 'OA1','OA2','OA3','PI1','PI2','PI3','CO45'];
								if(!in_array($prevAdjCode, $exemptArr)){
									if($insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Primary' || $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Primary Fwd' || $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Denied'){ 
										$insert_data[$basic_info['check_no']][$CLPCount]['adj_reason'][$claim_cpt_count][] = $this->checkAdjReason($temp[1],$tempData);
										/* if($tempData !='253')
											$remarks_codes .= $temp[1].$tempData.','; */
									}else{
										/* if($tempData !='253')
											$remarks_codes .= $temp[1].$tempData.','; */
										$insert_data[$basic_info['check_no']][$CLPCount]['adj_reason'][$claim_cpt_count][] = '';
									}
								}
								$adjCount ++;
							}elseif($adjCount == 2){
								$adjCount = 0;
							}elseif($adjCount == 1){
								$adjCount ++; 
								if($prevAdjCode == 'PR1' || $prevAdjCode == 'CO1' || $prevAdjCode == 'OA1' || $prevAdjCode == 'PI1'){
									$insert_data[$basic_info['check_no']][$CLPCount]['deductable'][$claim_cpt_count] = $tempData;
									$prevAdjCode = '';
									if($insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] != "Denied")
										$insert_data[$basic_info['check_no']][$CLPCount]['status'] = '';
								}elseif($prevAdjCode == 'PR2' || $prevAdjCode == 'CO2' || $prevAdjCode == 'OA2' || $prevAdjCode == 'PI2'){
									$insert_data[$basic_info['check_no']][$CLPCount]['co_ins'][$claim_cpt_count] = $tempData;
									$prevAdjCode = '';
								}elseif($prevAdjCode == 'PR3' || $prevAdjCode == 'CO3' || $prevAdjCode == 'OA3' || $prevAdjCode == 'PI3'){
									$insert_data[$basic_info['check_no']][$CLPCount]['co_pay'][$claim_cpt_count] = $tempData;
									$prevAdjCode = '';
									if($insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] != "Denied")
										$insert_data[$basic_info['check_no']][$CLPCount]['status'] = '';
								}/* elseif(in_array($prevAdjCode, $adjTempArr) && ($insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Primary' || $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Primary Fwd')){
									$insert_data[$basic_info['check_no']][$CLPCount]['adjustment'][$claim_cpt_count] = $tempData;
									$prevAdjCode = '';
								} */elseif(!empty($tempData)){
									if($insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Primary' || $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Primary Fwd')
										$insert_data[$basic_info['check_no']][$CLPCount]['adj_reson_amount'][$claim_cpt_count][] = $tempData;
									else
										$insert_data[$basic_info['check_no']][$CLPCount]['adj_reson_amount'][$claim_cpt_count][] = 0;
								}
							}
						}
					}
					//$insert_data[$basic_info['check_no']][$CLPCount]['remarkcode'][$claim_cpt_count] = $remarks_codes;
				}
				if (substr($segment, 0, 7) == 'AMT' . $separate . 'B6' . $separate && $claim_cpt_count != -1) {
					$temp = explode($separate, $segment);
					if (!empty($temp[2]))
						$insert_data[$basic_info['check_no']][$CLPCount]['cpt_allowed_amt'][$claim_cpt_count] = $temp[2];
						if($insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Primary' || $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Primary Fwd' || $tempClaimStatus == 'Denied')
							$insert_data[$basic_info['check_no']][$CLPCount]['adjustment'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['cpt_billed_amt'][$claim_cpt_count] - $insert_data[$basic_info['check_no']][$CLPCount]['cpt_allowed_amt'][$claim_cpt_count];
						else
							$insert_data[$basic_info['check_no']][$CLPCount]['adjustment'][$claim_cpt_count] = 0;
				}
			}
		}
		$insertFinalData = $insert_data[$fileCheckNo];
		DB::beginTransaction();
		try {
			$counts= 0;
			foreach($insertFinalData as $data){ 
				
				$counts++;
				$checkNo = $data['check_no'];
				if($data['payment_mode'] == 'Check'){
					$checkDetails = PMTInfoV1::with('checkDetails')
								->whereHas('checkDetails', function ($q) use ($checkNo) {
									$q->where('check_no', '=', $checkNo);
								})
								->where('pmt_method', '=', 'Insurance')
								->where('pmt_mode', '=', 'Check')
								->whereNull('void_check');
					$checkInfo = $checkDetails->get()->first();
					$checkCount = $checkDetails->count();
					if($checkCount > 0)
						$data['payment_detail_id'] =  Helpers::getEncodeAndDecodeOfId($checkInfo->id,'encode');
					else
						$data['payment_detail_id'] = '';
				}elseif($data['payment_mode'] == 'EFT'){
					$eftDetails = PMTInfoV1::with('eftDetails')
									->whereHas('eftDetails', function ($q) use ($checkNo) {
										$q->where('eft_no', '=', $checkNo);
									})->where('pmt_method', '=', 'Insurance')
									->where('pmt_mode', '=', 'EFT')
									->whereNull('void_check');
					$eftInfo = $eftDetails->get()->first();
					$eftCount = $eftDetails->count();
					if($eftCount > 0)
						$data['payment_detail_id'] =  Helpers::getEncodeAndDecodeOfId($eftInfo->id,'encode');
					else
						$data['payment_detail_id'] = '';
				}
				
				$timeStamp = microtime();
				$errorMsg = $this->autoPostValidation($data,$timeStamp);
				$claimNumber = Helpers::getEncodeAndDecodeOfId($data['claim_number'],'decode');
				$postedClaim[$fileCheckNo]['insurance_company'] = $data['insurance_company'];
				$postedClaim[$fileCheckNo]['check_no'] = $data['check_no'];
				$postedClaim[$fileCheckNo]['check_date'] = $data['check_date'];
				$postedClaim[$fileCheckNo]['check_amount'] = $data['check_amount'];
				if(count($errorMsg) == 0){
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['patient_name'] = (isset($data['patient_name']) ? $data['patient_name'] : "-" );
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['patient_acct'] = (isset($data['patient_acct']) ? $data['patient_acct'] : "-" );
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['dos_from'] = @$data['dos_from'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['cpt_billed_amt'] = @$data['cpt_billed_amt'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['cpt_allowed_amt'] = @$data['cpt_allowed_amt'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['co_ins'] = @$data['co_ins'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['co_pay'] = @$data['co_pay'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['deductable'] = @$data['deductable'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['adj_reson_amount'] = @$data['adj_reson_amount'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['pat_policyId'] = @$data['pat_policyId'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['resp'] = @$data['resp'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['paid_amt'] = @$data['paid_amt'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['cpt'] = @$data['cpt'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['claim_id'] = @$data['claim_id'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['patient_id'] = @$data['patient_id'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['status'] = "Ready for Posting";
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['claim_no'] = $claimNumber;
					$claimPostedStatus[$data['check_no']][$claimNumber] = "Yes";
				}else{  
					$eras_details = Eras::where('check_no',$data['check_no'])->get()->first();
					$claims = json_decode($eras_details->claim_nos,true);
					if(isset($claims[$claimNumber]) && $claims[$claimNumber] == 'Yes'){
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['error'][] = $errorMsg[$timeStamp];	
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['patient_name'] = (isset($data['patient_name']) ? $data['patient_name'] : "-" );
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['patient_acct'] = (isset($data['patient_acct']) ? $data['patient_acct'] : "-" );
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['dos_from'] = @$data['dos_from'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['cpt_billed_amt'] = @$data['cpt_billed_amt'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['cpt_allowed_amt'] = @$data['cpt_allowed_amt'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['co_ins'] = @$data['co_ins'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['co_pay'] = @$data['co_pay'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['deductable'] = @$data['deductable'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['adj_reson_amount'] = @$data['adj_reson_amount'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['pat_policyId'] = @$data['pat_policyId'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['resp'] = @$data['resp'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['paid_amt'] = @$data['paid_amt'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['cpt'] = @$data['cpt'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['claim_id'] = @$data['claim_id'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['patient_id'] = @$data['patient_id'];
						$claimPostedStatus[$data['check_no']][$claimNumber] = "Yes";
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['status'] = "Posted";
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['claim_no'] = $claimNumber;
					}else{
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['error'][] = $errorMsg[$timeStamp];	
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['patient_name'] = (isset($data['patient_name']) ? $data['patient_name'] : "-" );
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['patient_acct'] = (isset($data['patient_acct']) ? $data['patient_acct'] : "-" );
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['dos_from'] = @$data['dos_from'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['cpt_billed_amt'] = @$data['cpt_billed_amt'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['cpt_allowed_amt'] = @$data['cpt_allowed_amt'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['co_ins'] = @$data['co_ins'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['co_pay'] = @$data['co_pay'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['deductable'] = @$data['deductable'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['adj_reson_amount'] = @$data['adj_reson_amount'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['pat_policyId'] = @$data['pat_policyId'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['resp'] = @$data['resp'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['paid_amt'] = @$data['paid_amt'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['cpt'] = @$data['cpt'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['claim_id'] = @$data['claim_id'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['patient_id'] = @$data['patient_id'];
						$claimPostedStatus[$data['check_no']][$claimNumber] = "No";
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['status'] = "Unposted";
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['claim_no'] = $claimNumber;
					}
					
				}	
			}		
			DB::commit();
		} catch (\Exception $e) {
			DB::rollback();
			\Log::info("Get Auto Posting Status Error Log : ". $e);
		}
		$pageType = "Era Status Popup";
		return Response::view('payments/payments/autoPostResponse', compact('postedClaim','pageType'));
	}
	
	
	
	public function autoPostValidation($data,$timeStamp){
		//echo "<pre>";print_r($data);die;
		$errorMsg = array();
		$negCount = 0;
		$claim_number = Helpers::getEncodeAndDecodeOfId($data['claim_number'],'decode');
		
		// Added one more validation for era auto post for future cheque
		// Revision 1 : Ref : MR-2758 : 28 Aug 2019 : Selva
		$current = time();
		$practiceTZDate = Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
		\Log::info('ERA Auto post current and Check Date');
		\Log::info($current);
		\Log::info(strtotime(trim($data['check_date'])));
		if($current < strtotime($data['check_date'])){ 
			$errorMsg[$timeStamp][] = "Check date cannot be in the future";
		}
		
		if(isset($data['check_no'])){
			$checkDupilicateInfo = Eras::where('check_no',$data['check_no'])->where('status','Yes')->get()->first();
			if(isset($checkDupilicateInfo)){
				if($checkDupilicateInfo->check_paid_amount != $data['check_amount']){
					$errorMsg[$timeStamp][] = "Check number already been used";
				}
			}
			
		}
		
		if(empty($data['claim_number']))
			$errorMsg[$timeStamp][] = "Claim not found";
		
		if(count($data['cpt']) != 0){
			foreach($data['cpt'] as $cptList){
				$claimCptCount = ClaimCPTInfoV1::where('claim_id',Helpers::getEncodeAndDecodeOfId($data['claim_id'],'decode'))->where('cpt_code',$cptList)->get()->count();
				if($claimCptCount == 0)
					$errorMsg[$timeStamp][] = "Invalid claim CPT";
			}
			
		}
		
		if(empty($data['patient_id']))
			$errorMsg[$timeStamp][] = "Patient not found";
		
		if(empty($data['insurance_id']) && empty($data['claim_insurance_id']))
			$errorMsg[$timeStamp][] = "Insured policy ID mismatch";
		
		if(!empty($data['insurance_id']) && !empty($data['claim_insurance_id'])){ 
			$claimDetails = ClaimInfoV1::where('id',Helpers::getEncodeAndDecodeOfId($data['claim_id'],'decode'))
							->where('patient_id',Helpers::getEncodeAndDecodeOfId($data['patient_id'],'decode'))
							->where('insurance_id',$data['insurance_id'])
							->where('insurance_category',$data['claim_insurance_type'])
							->get()
							->toArray();
						
			if($data['claim_current_insurance_id'] != $data['claim_insurance_id'] || $data['claim_current_insurance_id'] == 0)
				$errorMsg[$timeStamp][] = "Claim responsibility mismatch";
			
			// Added condition for negative payment auto post in e-remittance module
			// Revision 1: MR-27 : 28 Aug 2019 : Selva
			
			if(isset($data['cpt_billed_amt']) && !empty($data['cpt_billed_amt'])){
				foreach($data['cpt_billed_amt'] as $list){
					if($list < 0){
						if($negCount == 0){
							$errorMsg[$timeStamp][] = "Negative payment not allowed";
							$negCount++;
						}
					}
				}
			}
			if(isset($data['co_ins']) && !empty($data['co_ins'])){
				foreach($data['co_ins'] as $list){
					if($list < 0){
						if($negCount == 0){
							$errorMsg[$timeStamp][] = "Negative payment not allowed";
							$negCount++;
						}
					}
				}
			}
			if(isset($data['co_pay']) && !empty($data['co_pay'])){
				foreach($data['co_pay'] as $list){
					if($list < 0){
						if($negCount == 0){
							$errorMsg[$timeStamp][] = "Negative payment not allowed";
							$negCount++;
						}
					}
				}
			}
			if(isset($data['deductable']) && !empty($data['deductable'])){
				foreach($data['deductable'] as $list){
					if($list < 0){
						if($negCount == 0){
							$errorMsg[$timeStamp][] = "Negative payment not allowed";
							$negCount++;
						}
					}
				}
			}
			if(isset($data['paid_amt']) && !empty($data['paid_amt'])){
				foreach($data['paid_amt'] as $list){
					if($list < 0){
						if($negCount == 0){
							$errorMsg[$timeStamp][] = "Negative payment not allowed";
							$negCount++;
						}
					}
				}
			}
			/* if(isset($data['adj_reson_amount']) && !empty($data['adj_reson_amount'])){
				foreach($data['adj_reson_amount'] as $adjlist){
					foreach($adjlist as $list){
						if($list < 0){
							if($negCount == 0){
								$errorMsg[$claim_number][] = "Negative payment not allowed";
								$negCount++;
							}
						}
					}
				}
			} */
			
			if(count($claimDetails) != 0 && $data['charge_amount'] != $claimDetails[0]['total_charge'])
				$errorMsg[$timeStamp][] = "Billed amount mismatch";
		}
		
		if(!empty($data['payment_detail_id'])){
			$eras_details = Eras::where('check_no',$data['check_no'])->get()->first();
			if(isset($eras_details) && !empty($eras_details)){
				$claims = json_decode($eras_details->claim_nos,true);
				$claimnumber = Helpers::getEncodeAndDecodeOfId($data['claim_number'],'decode');
				if($claims[$claimnumber] == 'Yes')
					$errorMsg[$timeStamp][] = "Check already posted for this claim";
			}
		}
		return $errorMsg;
	}
	
	public function findInsuranceID($patientPolicyId){
		// Added condition for insurance category 
        //Revision 1 - Ref: MR-2727 22 Aug 2019: Selva
		$patientInsurance = PatientInsurance::where('policy_id',$patientPolicyId)->whereIn('category',['Primary','Secondary','Tertiary'])->get()->first();
		$patientInsId = (isset($patientInsurance->insurance_id)) ? $patientInsurance->insurance_id : '';
		return $patientInsId;
	}
	
	
	
	public function findPaymentInsurance($payerId){
		$insurance = Insurance::where('payerid', 'like', '%' . substr($payerId,1) . '%');
		$insuranceCount = $insurance->count();
		if($insuranceCount == 1){
			$insuranceinfo = $insurance->get()->first();
			return $insuranceinfo->id;
		}else{
			return '';
		}
	}
	
	public function checkAdjReason($adjtype, $code){
		if($adjtype.$code != 'CO253'){
			$adjustment = AdjustmentReason::where('adjustment_shortname',$adjtype.$code);		
			$adjCount = $adjustment->count();
			if($adjCount == 0){
				$codeDetails = Code::where('transactioncode_id',$code)->get()->first();
				$dataAdjArr['adjustment_type'] = 'Insurance';
				$dataAdjArr['adjustment_reason'] = (isset($codeDetails->description)) ? $codeDetails->description : $adjtype.$code ;
				$dataAdjArr['adjustment_shortname'] = $adjtype.$code;
				$dataAdjArr['status'] = 'Active';
				$adjDetails = AdjustmentReason::create($dataAdjArr);
				$id = $adjDetails->id;
			}else{
				$adjustmentDetails = $adjustment->get()->first();
				$id = $adjustmentDetails->id;
			}
		}else{
			$id = 'CO253';
		}
		return $id;
		
	}
	
	// Check and Add the remark code in codes table
	// Revision 1 : MR-2774 : 12 Sep 2019 : Selva
	public function checkRemarkCode($remarkCode){
		$RemarkCodeInfo = Code::where('codecategory_id',2)->where('transactioncode_id',$remarkCode)->whereNull('deleted_at')->count();
		if($RemarkCodeInfo == 0){
			$codeDataArr['codecategory_id'] = 2;
			$codeDataArr['transactioncode_id'] = $remarkCode;
			$codeDataArr['description'] = $remarkCode;
			$codeDataArr['status'] = 'Active';
			Code::create($codeDataArr);
		}
	}
	
	
	
	/* Navicure Reports File Downloading function */
	/* Author : Selvakumar */
	public function EraResponseFile($id){
		//Handling Error and file not available in clearing house means showing error msg
        //Revision 1 - Ref: Billing Team Feedback 20 Aug 2019: Selva
		try{
			$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
			$checkInfo = Eras::where('id',$id)->get()->first();
			$path_medcubic = public_path() . '/';
			$local_path = $path_medcubic . 'media/era_reports/' . Session::get('practice_dbid') . '/';
			if (!file_exists($local_path)) {
				mkdir($local_path, 0777, true);
			}
			$fileName = str_replace('-STATUS.835','.rpt',$checkInfo->pdf_name);
			$downloadFileName = str_replace('-STATUS.835','.txt',$checkInfo->pdf_name);
			if(file_exists($local_path.$fileName)){
				$headers = array('Content-Type: text/plain');
				return Response::download($local_path.$fileName, $downloadFileName, $headers);
			}elseif(!file_exists($local_path.$fileName)){
				$clearing_house_details = ClearingHouse::where('status', 'Active')->where('practice_id', Session::get('practice_dbid'))->first();
				if (count($clearing_house_details) > 0) {
					$ClearingHouseType = $clearing_house_details->name;
					$ftp_server = $clearing_house_details->ftp_address;
					$ftp_username = $clearing_house_details->ftp_user_id;
					$ftp_password = $clearing_house_details->ftp_password;
					$ftp_port = $clearing_house_details->ftp_port;
					if($ClearingHouseType == "Navicure"){
						$destination_file = $clearing_house_details->edi_report_folder."/REPORTS";
						if (!function_exists("ssh2_connect")) {
							$status = 'error';
							$error_code = 'Function ssh2_connect not found, you cannot use ssh2 here';
						} elseif (!$connection = ssh2_connect($ftp_server, $ftp_port)) {
							$status = 'error';
							$error_code = 'Connection cannot be made to clearing house. Please contact administrator';
						} elseif (!ssh2_auth_password($connection, $ftp_username, $ftp_password)) {
							$status = 'error';
							$error_code = 'Connection cannot be made to clearing house. Please contact administrator';
						} elseif (!$stream = ssh2_sftp($connection)) {
							$status = 'error';
							$error_code = 'Connection cannot be made to clearing house. Please contact administrator';
						} elseif (!$dir = opendir("ssh2.sftp://" . intval($stream) . "/{$destination_file}/./")) {
							$status = 'error';
							$error_code = 'ssh2.sftp://' . $stream . $destination_file . 'Could not open the directory';
						}
						$file_content = file_get_contents("ssh2.sftp://" . intval($stream) . "/{$destination_file}/" . $fileName);
						$myerafileTxt = fopen($local_path . $fileName, "w+");
						fwrite($myerafileTxt, $file_content);
						
						$headers = array('Content-Type: text/plain');
						return Response::download($local_path.$fileName, $downloadFileName, $headers);
					}
					
				}
			}
		}catch(\Exception $e) {
			return Redirect::to('payments/get-e-remittance')->with('error', 'EOB file not available in clearing house. Please contact administrator');
		}
	}
	
	public function getCodeStatus($code){
		$prefixCode = substr($code,0,2);
		$suffixCode = substr($code,2);
		$codeInfo = CodesRuleEngine::where('transactioncode_id',$suffixCode)->where('code_type',$prefixCode)->get()->first();
		if(isset($codeInfo)){
			return $codeInfo->claim_status;
		}else{
			return 'Adjustment';
		}
	}
	

	/* Testing 835 file data in this function  */
	
	
	public function get835Data($checkNo,$id){
		
		$insert_data = $basic_info = $postedClaim = $unpostedClaim = array();
		$erasInfo = Eras::where('check_no', $checkNo)->where('id',$id)->get()->first();
		$filename = $erasInfo->pdf_name;
		$fileCheckNo = $erasInfo->check_no;
		$fileCheckNo = preg_replace("/[^a-zA-Z0-9]/", "", $fileCheckNo);
		$eraFileName = explode('.',$filename);

		$orgERAFolderName = $eraFileName[0];
		$orgERAFileName = str_replace('STATUS','835',$eraFileName[0]).'.835';
		$path_medcubic = public_path() . '/';
		$local_path = $path_medcubic . 'media/era_files/' . Session::get('practice_dbid') . '/'.$orgERAFolderName.'/';
		$glossary = $basic_info = $insert_data = [];
		$fileReceivedDate = $erasInfo->receive_date;
		foreach (glob($local_path . $orgERAFileName) as $list) {

			$file_content = file($list);
			$file_full_content = explode('~', $file_content[0]);

			$symb_check = implode('', $file_full_content);
			$first_segment = $file_full_content[0];
			if (count(explode('|', $symb_check)) > 5) {
				$separate = "|";
			} elseif (count(explode('*', $symb_check)) > 1) {
				$separate = "*";
			}
			$spl_symb = explode($separate, $first_segment);
			$spl_separate = $spl_symb[16];
			$basic_info['payment_method'] = $basic_info['type'] = 'Insurance';
			$basic_info['card_type'] = $basic_info['next_insurance_id'] = $basic_info['insurance_cat'] = $basic_info['eob_id'] = $basic_info['checkexist'] = $basic_info['reference'] =  $basic_info['next'] = $basic_info['resubmit'] = $basic_info['adjustment_reason'] = $basic_info['content'] = $basic_info['payment_hold_reason'] = $basic_info['status'] = '';
			$basic_info['patient_paid'] = '0.00';
			$basic_info['deposite_date'] = date('m/d/Y');
			$basic_info['payment_mode'] = 'EFT';
			$basic_info['payment_type'] = 'Payment';
			\Log::info('File segment :');
			\Log::info($file_full_content);
			foreach ($file_full_content as $key => $segment) {
				if (substr($segment, 0, 3) == 'ST' . $separate) {
					$CLPCount = -1;
					$claim_cpt_count = 0;
				}
				
				if (substr($segment, 0, 6) == 'N1' . $separate . 'PR' . $separate) {
					$temp = explode($separate, $segment);
					if (!empty($temp[2]))
						$basic_info['insurance_company'] = $temp[2];
				}
				
				/* Getting check no */
				if (substr($segment, 0, 4) == 'BPR' . $separate) {
					$temp = explode($separate, $segment);
					if (!empty($temp[16]))
						$basic_info['check_date'] = date('Y-m-d', strtotime($temp[16]));
					if (!empty($temp[2]))
						$basic_info['check_amount'] = $basic_info['payment_amt'] = $basic_info['check_paid_amount'] = $temp[2];
					else
						$basic_info['check_amount'] = $basic_info['payment_amt'] = $basic_info['check_paid_amount'] = 0;
					if((!empty($temp[4])) && $temp[4] != 'CHK')
						$basic_info['payment_mode'] = 'EFT';
					else
						$basic_info['payment_mode'] = 'Check';
						
				}
				
				/* Getting check no */
				if (substr($segment, 0, 4) == 'TRN' . $separate) {
					$temp = explode($separate, $segment);
					if (!empty($temp[2])){
						// Remove remove special characters from cheque no in era
						// Revision 1 : MR-2799 : 6 Sep 2019 : Selva
						$temp[2] = preg_replace("/[^a-zA-Z0-9]/", "", $temp[2]);
						$basic_info['check_no'] = $temp[2];
					}
					
					if($basic_info['payment_mode'] == 'Check'){
						$checkNo = $basic_info['check_no'];
						$checkDetails = PMTInfoV1::with('checkDetails')
									->whereHas('checkDetails', function ($q) use ($checkNo) {
										$q->where('check_no', '=', $checkNo);
									})
									->where('pmt_method', '=', 'Insurance')
									->where('pmt_mode', '=', 'Check')
									->whereNull('void_check');
						$checkInfo = $checkDetails->get()->first();
						$checkCount = $checkDetails->count();
						if($checkCount > 0)
							$basic_info['payment_detail_id'] =  Helpers::getEncodeAndDecodeOfId($checkInfo->id,'encode');
						else
							$basic_info['payment_detail_id'] = '';
					}elseif($basic_info['payment_mode'] == 'EFT'){
						$checkNo = $basic_info['check_no'];
						$eftDetails = PMTInfoV1::with('eftDetails')
										->whereHas('eftDetails', function ($q) use ($checkNo) {
											$q->where('eft_no', '=', $checkNo);
										})->where('pmt_method', '=', 'Insurance')
										->where('pmt_mode', '=', 'EFT')
										->whereNull('void_check');
						$eftInfo = $eftDetails->get()->first();
						$eftCount = $eftDetails->count();
						if($eftCount > 0)
							$basic_info['payment_detail_id'] =  Helpers::getEncodeAndDecodeOfId($eftInfo->id,'encode');
						else
							$basic_info['payment_detail_id'] = '';
					}
				}
				

				if (substr($segment, 0, 4) == 'CLP' . $separate) {
					$temp = explode($separate, $segment);
					$CLPCount ++;
					$tempClaimStatus = '';
					$claim_cpt_count = -1;
					if (!empty($temp[1])){
						$basic_info['claim_number'] = Helpers::getEncodeAndDecodeOfId($temp[1], 'encode');
						$claimDetails = ClaimInfoV1::with('insurance_details')->where('claim_number',$temp[1])
										->get()
										->first(); 
						if(isset($claimDetails->patient_id)){
							$patientInfo = Patient::where('id',$claimDetails->patient_id)->get()->first();
							$insert_data[$basic_info['check_no']][$CLPCount]['patient_name'] = @$patientInfo->last_name.", ".@$patientInfo->first_name;
							$insert_data[$basic_info['check_no']][$CLPCount]['patient_acct'] = @$patientInfo->account_no;
							$insert_data[$basic_info['check_no']][$CLPCount]['claim_current_insurance_id'] = @$claimDetails->insurance_id;
							$insert_data[$basic_info['check_no']][$CLPCount]['resp'] = (isset($claimDetails->insurance_details->short_name)) ? $claimDetails->insurance_details->short_name : "Patient" ;
						} else{
							$insert_data[$basic_info['check_no']][$CLPCount]['claim_current_insurance_id'] = $insert_data[$basic_info['check_no']][$CLPCount]['patient_acct'] = $insert_data[$basic_info['check_no']][$CLPCount]['resp'] = '-';
						}
						$insert_data[$basic_info['check_no']][$CLPCount]['claim_id'] = Helpers::getEncodeAndDecodeOfId(@$claimDetails->id, 'encode');
						$insert_data[$basic_info['check_no']][$CLPCount]['patient_id'] = Helpers::getEncodeAndDecodeOfId(@$claimDetails->patient_id, 'encode');
						$insert_data[$basic_info['check_no']][$CLPCount] = array_merge($basic_info,$insert_data[$basic_info['check_no']][$CLPCount]);
					}
					if (!empty($temp[2])){
						$processData = $this->processData($temp[2]);
						$insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] = $processData['type'];
						if($insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Denied')
							$insert_data[$basic_info['check_no']][$CLPCount]['status'] = 'Denied';
					}
					if (!empty($temp[3]))
						$insert_data[$basic_info['check_no']][$CLPCount]['charge_amount'] = abs($temp[3]);
					if (!empty($temp[4]))
						$insert_data[$basic_info['check_no']][$CLPCount]['claim_paid_amt'] = $insert_data[$basic_info['check_no']][$CLPCount]['charge_paid_amount'] = $temp[4];
					else
						$insert_data[$basic_info['check_no']][$CLPCount]['claim_paid_amt'] = $insert_data[$basic_info['check_no']][$CLPCount]['charge_paid_amount'] = 0;
					
					if($insert_data[$basic_info['check_no']][$CLPCount]['claim_paid_amt'] == 0)
						$insert_data[$basic_info['check_no']][$CLPCount]['status'] = 'Denied';
					
					
					if (!empty($temp[7]))
						$insert_data[$basic_info['check_no']][$CLPCount]['patient_icn'] = $temp[7];
				}
				if (substr($segment, 0, 7) == 'NM1' . $separate . 'QC' . $separate) {
					$temp = explode($separate, $segment);
					
					if (!empty($temp[3]))
                        $insert_data[$basic_info['check_no']][$CLPCount]['patient_lastname'] = $temp[3];
                    if (!empty($temp[4]))
                        $insert_data[$basic_info['check_no']][$CLPCount]['patient_firstname'] = $temp[4];
					if(!isset($insert_data[$basic_info['check_no']][$CLPCount]['patient_name']))
						$insert_data[$basic_info['check_no']][$CLPCount]['patient_name'] = $insert_data[$basic_info['check_no']][$CLPCount]['patient_lastname'].", ".$insert_data[$basic_info['check_no']][$CLPCount]['patient_firstname'];
					
					if(isset($temp[8]) && ($temp[8] == 'MI' || $temp[8] == 'MR' || $temp[8] == 'HN') && isset($temp[9]) && !empty($temp[9]))
						$insert_data[$basic_info['check_no']][$CLPCount]['insurance_id'] = $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_id'] = $this->findInsuranceID($temp[9]);	
					
					if(isset($temp[9]) && !empty($temp[9]))
						$insert_data[$basic_info['check_no']][$CLPCount]['pat_policyId'] = $temp[9];
				}
				
				if ((substr($segment, 0, 7) == 'NM1' . $separate . '74' . $separate) && (empty(@$insert_data[$basic_info['check_no']][$CLPCount]['insurance_id']))) {
					$temp = explode($separate, $segment);
					if(isset($temp[8]) && $temp[8] == 'C' && isset($temp[9]) && !empty($temp[9]))
						$insert_data[$basic_info['check_no']][$CLPCount]['insurance_id'] = $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_id'] = $this->findInsuranceID($temp[9]);
					if(isset($temp[9]) && !empty($temp[9]))
						$insert_data[$basic_info['check_no']][$CLPCount]['pat_policyId'] = $temp[9];
				}
				
				if ((substr($segment, 0, 7) == 'NM1' . $separate . 'IL' . $separate) && (empty(@$insert_data[$basic_info['check_no']][$CLPCount]['insurance_id']))) {
					$temp = explode($separate, $segment);
					if(isset($temp[8]) && ($temp[8] == 'MI' || $temp[8] == 'MR' || $temp[8] == 'HN') && isset($temp[9]) && !empty($temp[9]))
						$insert_data[$basic_info['check_no']][$CLPCount]['insurance_id'] = $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_id'] = $this->findInsuranceID($temp[9]);	
					if(isset($temp[9]) && !empty($temp[9]))
						$insert_data[$basic_info['check_no']][$CLPCount]['pat_policyId'] = $temp[9];
				}

				

				if (substr($segment, 0, 4) == 'MOA' . $separate) {
					$temp = explode($separate, $segment);
					if (!empty($temp[3]))
						$insert_data[$basic_info['check_no']][$CLPCount]['patient_moa'] = $temp[3];
				}

				if (substr($segment, 0, 4) == 'DTM' . $separate) {
					$temp = explode($separate, $segment);
					if (!empty($temp[2]) && $temp[1] == 232)
						$insert_data[$basic_info['check_no']][$CLPCount]['start_date'] = $temp[2];
					if (!empty($temp[2]) && $temp[1] == 233)
						$insert_data[$basic_info['check_no']][$CLPCount]['end_date'] = $temp[2];
					if (!empty($temp[2]) && $temp[1] == 050)
						$insert_data[$basic_info['check_no']][$CLPCount]['patient_statement_date'] = date('d/m/Y', strtotime($temp[2]));
				}

				if (substr($segment, 0, 4) == 'SVC' . $separate) {
					$claim_cpt_count ++;
					$remarks_codes = '';
					/* Global Declaration Cpt Variable  */
					$insert_data[$basic_info['check_no']][$CLPCount]['adjustment'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['remarkcode'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['adj_reson_amount'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['ids'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['paid_amt'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['deductable'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['cpt_billed_amt'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['cpt_allowed_amt'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['co_pay'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['co_ins'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['with_held'][$claim_cpt_count] = 0;
					
					$insert_data[$basic_info['check_no']][$CLPCount]['adj_reason'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['adj_reson_amount'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['active_lineitem'][$claim_cpt_count] = array();
					
					$temp = explode($separate, $segment);
					$temp_proc = explode($spl_separate, $temp[1]);
					
					if (!empty($temp_proc[1])){
						$insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count] = $temp_proc[1];
						$insert_data[$basic_info['check_no']][$CLPCount]['active_lineitem'][$claim_cpt_count] = 1;
						
					}
					if (!empty($temp[2]))
						$insert_data[$basic_info['check_no']][$CLPCount]['cpt_billed_amt'][$claim_cpt_count] = $temp[2];
					if (!empty($temp[3]))
						$insert_data[$basic_info['check_no']][$CLPCount]['paid_amt'][$claim_cpt_count] = $temp[3];
					
					/* Claim date Only received in era means it will take claim date other wise receive DTM 472 date will take  */
					if (isset($insert_data[$basic_info['check_no']][$CLPCount]['start_date']) && !empty($insert_data[$basic_info['check_no']][$CLPCount]['start_date'])){
					   $insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['start_date'];
						if($claim_cpt_count >= 0 && isset($insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count]) && !empty($insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count]) && isset($insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count]) && !empty($insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count])){ 
							$claimCptDetails = 	ClaimCPTInfoV1::where('claim_id',Helpers::getEncodeAndDecodeOfId($insert_data[$basic_info['check_no']][$CLPCount]['claim_id'],'decode'))
													->where('cpt_code',$insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count])
													->where('dos_from',date('Y-m-d',strtotime($insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count])))
													->get()
													->first();
							if(isset($claimCptDetails->id))
								$insert_data[$basic_info['check_no']][$CLPCount]['id'][$claim_cpt_count] = $claimCptDetails->id; 
							$insert_data[$basic_info['check_no']][$CLPCount]['ids'][$claim_cpt_count] = Helpers::getEncodeAndDecodeOfId(@$claimCptDetails->id,'encode'); 
								
						}
					}
				}

				if (substr($segment, 0, 4) == 'DTM' . $separate && $claim_cpt_count != -1) {
					$temp = explode($separate, $segment);
					if (!empty($temp[2]) && $temp[1] == 472)
					   $insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count] = $temp[2];
					if (!empty($temp[2]) && $temp[1] == 150)
					   $insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count] = $temp[2];
				   
					if($claim_cpt_count >= 0 && isset($insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count]) && !empty($insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count]) && isset($insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count]) && !empty($insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count])){ 
						$claimCptDetails = 	ClaimCPTInfoV1::where('claim_id',Helpers::getEncodeAndDecodeOfId($insert_data[$basic_info['check_no']][$CLPCount]['claim_id'],'decode'))
												->where('cpt_code',$insert_data[$basic_info['check_no']][$CLPCount]['cpt'][$claim_cpt_count])
												->where('dos_from',date('Y-m-d',strtotime($insert_data[$basic_info['check_no']][$CLPCount]['dos_from'][$claim_cpt_count])))
												->get()
												->first();
						if(isset($claimCptDetails->id))
							$insert_data[$basic_info['check_no']][$CLPCount]['id'][$claim_cpt_count] = $claimCptDetails->id; 
						$insert_data[$basic_info['check_no']][$CLPCount]['ids'][$claim_cpt_count] = Helpers::getEncodeAndDecodeOfId(@$claimCptDetails->id,'encode'); 	
					}
				}

				if (substr($segment, 0, 4) == 'CAS' . $separate && $claim_cpt_count != -1) {
					$temp = explode($separate, $segment);
					//$temp = array_filter($temp, function($value) { return $value !== ''; });
					$adjCount = 0;
					
					foreach($temp as $tempData){ 
						$adjTempArr = ['CO45'];
						if($tempData != 'CAS' && $tempData != 'CO' && $tempData != 'OA' && $tempData != 'PI' && $tempData != 'PR' ){
							if($adjCount == 0){
								$prevAdjCode = $temp[1].$tempData;
								$exemptArr = ['PR1', 'PR2','PR3', 'CO1','CO2','CO3', 'OA1','OA2','OA3','PI1','PI2','PI3','CO45'];
								if(!in_array($prevAdjCode, $exemptArr)){
									if($insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Primary' || $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Primary Fwd' || $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Denied'){ 
										$insert_data[$basic_info['check_no']][$CLPCount]['adj_reason'][$claim_cpt_count][] = $this->checkAdjReason($temp[1],$tempData);
										
									}else{
										
										$insert_data[$basic_info['check_no']][$CLPCount]['adj_reason'][$claim_cpt_count][] = '';
									}
								}
								$adjCount ++;
							}elseif($adjCount == 2){
								$adjCount = 0;
							}elseif($adjCount == 1){
								$adjCount ++; 
								if($prevAdjCode == 'PR1' || $prevAdjCode == 'CO1' || $prevAdjCode == 'OA1' || $prevAdjCode == 'PI1'){
									$insert_data[$basic_info['check_no']][$CLPCount]['deductable'][$claim_cpt_count] = $tempData;
									$prevAdjCode = '';
									if($insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] != "Denied")
										$insert_data[$basic_info['check_no']][$CLPCount]['status'] = '';
								}elseif($prevAdjCode == 'PR2' || $prevAdjCode == 'CO2' || $prevAdjCode == 'OA2' || $prevAdjCode == 'PI2'){
									$insert_data[$basic_info['check_no']][$CLPCount]['co_ins'][$claim_cpt_count] = $tempData;
									$prevAdjCode = '';
								}elseif($prevAdjCode == 'PR3' || $prevAdjCode == 'CO3' || $prevAdjCode == 'OA3' || $prevAdjCode == 'PI3'){
									$insert_data[$basic_info['check_no']][$CLPCount]['co_pay'][$claim_cpt_count] = $tempData;
									$prevAdjCode = '';
									if($insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] != "Denied")
										$insert_data[$basic_info['check_no']][$CLPCount]['status'] = '';
								}elseif(!empty($tempData)){
									if($insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Primary' || $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Primary Fwd')
										$insert_data[$basic_info['check_no']][$CLPCount]['adj_reson_amount'][$claim_cpt_count][] = $tempData;
									else
										$insert_data[$basic_info['check_no']][$CLPCount]['adj_reson_amount'][$claim_cpt_count][] = 0;
								}
							}
						}
					}
					
				}
				if (substr($segment, 0, 7) == 'AMT' . $separate . 'B6' . $separate && $claim_cpt_count != -1) {
					$temp = explode($separate, $segment);
					if (!empty($temp[2]))
						$insert_data[$basic_info['check_no']][$CLPCount]['cpt_allowed_amt'][$claim_cpt_count] = $temp[2];
						if($insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Primary' || $insert_data[$basic_info['check_no']][$CLPCount]['claim_insurance_type'] == 'Primary Fwd' || $tempClaimStatus == 'Denied')
							$insert_data[$basic_info['check_no']][$CLPCount]['adjustment'][$claim_cpt_count] = $insert_data[$basic_info['check_no']][$CLPCount]['cpt_billed_amt'][$claim_cpt_count] - $insert_data[$basic_info['check_no']][$CLPCount]['cpt_allowed_amt'][$claim_cpt_count];
						else
							$insert_data[$basic_info['check_no']][$CLPCount]['adjustment'][$claim_cpt_count] = 0;
				}
			}
		}
		$insertFinalData = $insert_data[$fileCheckNo];
		\Log::info('Segment Data : ');
		\Log::info($insertFinalData);
		DB::beginTransaction();
		try {
			$counts= 0;
			foreach($insertFinalData as $data){ 
				
				$counts++;
				$checkNo = $data['check_no'];
				if($data['payment_mode'] == 'Check'){
					$checkDetails = PMTInfoV1::with('checkDetails')
								->whereHas('checkDetails', function ($q) use ($checkNo) {
									$q->where('check_no', '=', $checkNo);
								})
								->where('pmt_method', '=', 'Insurance')
								->where('pmt_mode', '=', 'Check')
								->whereNull('void_check');
					$checkInfo = $checkDetails->get()->first();
					$checkCount = $checkDetails->count();
					if($checkCount > 0)
						$data['payment_detail_id'] =  Helpers::getEncodeAndDecodeOfId($checkInfo->id,'encode');
					else
						$data['payment_detail_id'] = '';
				}elseif($data['payment_mode'] == 'EFT'){
					$eftDetails = PMTInfoV1::with('eftDetails')
									->whereHas('eftDetails', function ($q) use ($checkNo) {
										$q->where('eft_no', '=', $checkNo);
									})->where('pmt_method', '=', 'Insurance')
									->where('pmt_mode', '=', 'EFT')
									->whereNull('void_check');
					$eftInfo = $eftDetails->get()->first();
					$eftCount = $eftDetails->count();
					if($eftCount > 0)
						$data['payment_detail_id'] =  Helpers::getEncodeAndDecodeOfId($eftInfo->id,'encode');
					else
						$data['payment_detail_id'] = '';
				}
				
				$timeStamp = microtime();
				$errorMsg = $this->autoPostValidation($data,$timeStamp);
				\Log::info('Error Msg : ');
				\Log::info($errorMsg);
				$claimNumber = Helpers::getEncodeAndDecodeOfId($data['claim_number'],'decode');
				$postedClaim[$fileCheckNo]['insurance_company'] = $data['insurance_company'];
				$postedClaim[$fileCheckNo]['check_no'] = $data['check_no'];
				$postedClaim[$fileCheckNo]['check_date'] = $data['check_date'];
				$postedClaim[$fileCheckNo]['check_amount'] = $data['check_amount'];
				if(count($errorMsg) == 0){
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['patient_name'] = (isset($data['patient_name']) ? $data['patient_name'] : "-" );
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['patient_acct'] = (isset($data['patient_acct']) ? $data['patient_acct'] : "-" );
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['dos_from'] = @$data['dos_from'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['cpt_billed_amt'] = @$data['cpt_billed_amt'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['cpt_allowed_amt'] = @$data['cpt_allowed_amt'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['co_ins'] = @$data['co_ins'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['co_pay'] = @$data['co_pay'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['deductable'] = @$data['deductable'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['adj_reson_amount'] = @$data['adj_reson_amount'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['pat_policyId'] = @$data['pat_policyId'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['resp'] = @$data['resp'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['paid_amt'] = @$data['paid_amt'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['cpt'] = @$data['cpt'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['claim_id'] = @$data['claim_id'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['patient_id'] = @$data['patient_id'];
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['status'] = "Ready for Posting";
					$postedClaim[$fileCheckNo]['posted'][$timeStamp]['claim_no'] = $claimNumber;
					$claimPostedStatus[$data['check_no']][$claimNumber] = "Yes";
				}else{  
					$eras_details = Eras::where('check_no',$data['check_no'])->get()->first();
					$claims = json_decode($eras_details->claim_nos,true);
					if(isset($claims[$claimNumber]) && $claims[$claimNumber] == 'Yes'){
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['error'][] = $errorMsg[$timeStamp];	
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['patient_name'] = (isset($data['patient_name']) ? $data['patient_name'] : "-" );
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['patient_acct'] = (isset($data['patient_acct']) ? $data['patient_acct'] : "-" );
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['dos_from'] = @$data['dos_from'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['cpt_billed_amt'] = @$data['cpt_billed_amt'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['cpt_allowed_amt'] = @$data['cpt_allowed_amt'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['co_ins'] = @$data['co_ins'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['co_pay'] = @$data['co_pay'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['deductable'] = @$data['deductable'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['adj_reson_amount'] = @$data['adj_reson_amount'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['pat_policyId'] = @$data['pat_policyId'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['resp'] = @$data['resp'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['paid_amt'] = @$data['paid_amt'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['cpt'] = @$data['cpt'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['claim_id'] = @$data['claim_id'];
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['patient_id'] = @$data['patient_id'];
						$claimPostedStatus[$data['check_no']][$claimNumber] = "Yes";
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['status'] = "Posted";
						$postedClaim[$fileCheckNo]['posted'][$timeStamp]['claim_no'] = $claimNumber;
					}else{
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['error'][] = $errorMsg[$timeStamp];	
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['patient_name'] = (isset($data['patient_name']) ? $data['patient_name'] : "-" );
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['patient_acct'] = (isset($data['patient_acct']) ? $data['patient_acct'] : "-" );
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['dos_from'] = @$data['dos_from'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['cpt_billed_amt'] = @$data['cpt_billed_amt'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['cpt_allowed_amt'] = @$data['cpt_allowed_amt'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['co_ins'] = @$data['co_ins'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['co_pay'] = @$data['co_pay'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['deductable'] = @$data['deductable'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['adj_reson_amount'] = @$data['adj_reson_amount'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['pat_policyId'] = @$data['pat_policyId'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['resp'] = @$data['resp'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['paid_amt'] = @$data['paid_amt'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['cpt'] = @$data['cpt'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['claim_id'] = @$data['claim_id'];
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['patient_id'] = @$data['patient_id'];
						$claimPostedStatus[$data['check_no']][$claimNumber] = "No";
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['status'] = "Unposted";
						$postedClaim[$fileCheckNo]['unposted'][$timeStamp]['claim_no'] = $claimNumber;
					}
					
				}	
			}		
			DB::commit();
		} catch (\Exception $e) {
			DB::rollback();
			\Log::info("Get 835 Error Log : ". $e);
		}
		$pageType = "Era Status Popup";
		dd($postedClaim);
		return Response::view('payments/payments/autoPostResponse', compact('postedClaim','pageType'));
	}

}
