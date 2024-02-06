<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiConfig as ApiConfig;
use App\Models\Patients\Patient;
use App\Models\Practice as Practice;
use App\Models\Claims\EdiTransmission as EdiTransmission;
use App\Models\Claims\TransmissionClaimDetails as TransmissionClaimDetails;
use App\Models\Claims\TransmissionCptDetails as TransmissionCptDetails;
use App\Models\Payments\ClaimInfoV1 as ClaimInfo;
use App\Http\Helpers\Helpers as Helpers;
use Illuminate\Support\Facades\Storage as Storage;
use Illuminate\Support\Facades\File;
use App\Models\Payments\ClaimTXDESCV1;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\PMTClaimTXV1;
use App\Models\Payments\PMTClaimFINV1;
use App\Models\Payments\PMTClaimCPTFINV1;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Traits\ClaimUtil;
use DB;
use Auth;
use Request;
use App;
use Config;
use SSH;
use Session;
use Log;

class EdiApiController extends Controller {
	
	use ClaimUtil;
	
    public $ediFileName;
    public $spReturnValue;
    public $HLCnt = 1;
    public $HLCntPtr = 1;
    public $HLSubscriberPtr = 1;
    public $totalLines;
    public $tempString;
    public $edi_file_content_string;

    /**
     * Create EDI file
     *
     * @param  $claim_details
     * @param  $clearing_house_details
     * @return void
     */
    public function createEDIFile($claim_details, $clearing_house_details) {
        //If there is a professional file extension value set, use it as the file extension
        //else use '.txt' as the file extension

		
        $file_extension = $clearing_house_details->ftp_file_extension_professional;
        if ($file_extension != '')
            $extension = '.' . $file_extension;
        else
            $extension = '.txt';

        //Get the path to the upload folder 
        $upload_folder_path = $clearing_house_details->ftp_folder;
        //EDI file are created in the format YearMonthDayTimeP.txt (ex: 201705120500P.txt)
        $filename = date("Ymd") . time() . 'P';
        $this->filename = $filename . $extension;
        //Get the directory path for creating the EDI file
        
        $path_medcubic = public_path() . '/';
        $this->path = $path_medcubic . 'media/clearing_house/' . Session::get('practice_dbid') . '/';
        if (!file_exists($this->path)) {
            mkdir($this->path, 0777, true);
        }
        DB::beginTransaction();
        try {
            // Starts - Insert claim transmission into tables ///
            $total_billed_amount = 0;
            $edi_transmission['transmission_type'] = 'Electronic';
            $edi_transmission['total_claims'] = count($claim_details);
            $edi_transmission['file_path'] = $this->path . $this->filename;
            $edi_transmission['created_by'] = Auth::user()->id;
            $edi_transmission = EdiTransmission::create($edi_transmission);
            $edi_transmission_id = $edi_transmission->id;
            // Ends - Insert claim transmission into tables ///
            // Get Practice Name and call the Stored Procedure LoopISAHeader to get the 
            // header for EDI file. The header file consist of 
            // ISA01 - Authorization Information,
            // ISA02 - User ID,
            // ISA03 = 01, If User ID exist else ISA03 = 00,
            // ISA04 - Password,
            // ISA05 (Default Value: ZZ) Interchange ID Qualifier, 
            // ISA06 - Submitter ID, 
            // ISA07,
            // ISA08 - Reciever ID, 
            // ISA14 - Acknowledgement Request, 
            // ISA15 - Submission Mode, 
            // Contact Name, Contact Fax, Contact Phone
            $practice_name = Practice::value('practice_name');
            $practice_id = Practice::value('id');
            $sp_return_result = DB::select('call LoopISAHeader("' . $practice_id . '", "' . $practice_name . '")');
            $spReturnValue = $sp_return_result[0]->varReturnValue;
            $this->writeMultipleSegments($spReturnValue);
            $this->totalLines = 6;

            //The following is the claim Loop. Loop for all the selected claims

            foreach ($claim_details as $claims) {
                $this->writeHLLevelCount();
                $claim_id = $claims['claim_id'];
                $sp_return_result = DB::select('call Loop2010AA(' . $claim_id . ')');
                $spReturnValue = $sp_return_result[0]->varReturnValue;
                $this->writeMultipleSegments($spReturnValue);
				
				if(Session::has('practice_dbid')){
					$practiceInfo = Practice::where('id',Session::get('practice_dbid'))->first();
					if(!empty($practiceInfo->pay_add_1) && !empty($practiceInfo->pay_city) && !empty($practiceInfo->pay_state) && !empty($practiceInfo->pay_zip5) && !empty($practiceInfo->pay_zip4)){ 
						$sp_return_result = DB::select('call Loop2010AB(' . Session::get('practice_dbid') . ')');
						$spReturnValue = $sp_return_result[0]->varReturnValue;
						$this->writeMultipleSegments($spReturnValue);	
					}
				}
				

                //Look bottom for the function body
                $this->writeSubScriberLEVEL($claims['patient_details']['relationship']);

                $insurance_category = $claims['claim_section']['insurance_category'];
                $insurance_id = $claims["patient_insurance_details"]["insurance_id"];
				$patient_insurance_id = $claims["patient_insurance_details"]["patient_insurance_id"];
                $patient_id = $claims['patient_details']["patient_id"];


                /// Starts - Add submitted entry in claim transaction table ///
                $data['claim_info_id'] = $claim_id;
                $data['patient_ins_id'] = $patient_insurance_id;
                $data['pat_ins_category'] = $insurance_category;
				$data['resp'] = @$insurance_id;
                //$claim_insurance = ClaimTXDESCV1::where('claim_id', $claim_id);
                $claimFinData = PMTClaimFINV1::select(['id','patient_due','insurance_due'])
                                ->where('claim_id',$claim_id)->get()->first();
                $data['pat_bal'] = $claimFinData['patient_due'];
                $data['ins_bal'] = $claimFinData['insurance_due'];
                //$claim_submit_count = $claim_insurance->where('transaction_type',"Submitted")->count();
                
                /* if ($claim_submit_count > 0) {
					$claimTxnDesc = $this->storeClaimTxnDesc('Resubmitted', $data);
                } else {
					$claimTxnDesc = $this->storeClaimTxnDesc('Submitted', $data);
                } */
				$dataArr['claim_info_id'] = $claim_id;
				$dataArr['resp'] = @$insurance_id;
				$sp_return_result = DB::select('call Loop2000B("' . $insurance_id . '", "' . $insurance_category . '", "' . $patient_id . '", "' . $patient_insurance_id . '")');
				
                $spReturnValue = $sp_return_result[0]->varReturnValue;
                $this->writeMultipleSegments($spReturnValue);
				if ($claims['patient_details']['relationship'] == 'Self'){
					$sp_return_result = DB::select('call SelfLoop2010BA("' . $insurance_id . '", "' . $insurance_category . '", "' . $patient_id . '", "' . $patient_insurance_id . '")');
					$spReturnValue = $sp_return_result[0]->varReturnValue;
					$this->writeMultipleSegments($spReturnValue);
				}else{
					$sp_return_result = DB::select('call Loop2010BA("' . $insurance_id . '", "' . $insurance_category . '", "' . $patient_id . '", "' . $patient_insurance_id . '")');
					$spReturnValue = $sp_return_result[0]->varReturnValue;
					$this->writeMultipleSegments($spReturnValue);
				}

                $sp_return_result = DB::select('call Loop2010BB("' . $insurance_id . '")');
                $spReturnValue = $sp_return_result[0]->varReturnValue;
                $this->writeMultipleSegments($spReturnValue);

                if ($claims['patient_details']['relationship'] == 'Spouse')
                    $relationship = "01";
                elseif ($claims['patient_details']['relationship'] == 'Child')
                    $relationship = 19;
                elseif ($claims['patient_details']['relationship'] == 'Self')
                    $relationship = 18;
                else
                    $relationship = 21;

                if ($relationship != 18) {
                    $this->writePatientHLevel($relationship);
                    $this->writeLoop2000C($relationship);
                    $sp_return_result = DB::select('call Loop2010CA(' . $claim_id . ',1, 1)');
                    $spReturnValue = $sp_return_result[0]->varReturnValue;
                    $this->writeMultipleSegments($spReturnValue);
                }

                // 2300 - Claim Information Page No 170
				
				$claim_insurance = ClaimTXDESCV1::where('claim_id', $claim_id);			
				$claim_submit_count = $claim_insurance->whereIn('transaction_type',["Submitted","Submitted Paper"])->get();
				$submitted_count = 0;
				foreach($claim_submit_count as $subCount){
					$descDet = json_decode($subCount->value_1, true);
					if (json_last_error() === JSON_ERROR_NONE && is_array($descDet)) {
						$patient_insurance_id = PatientInsurance::where('id',$descDet['patient_insurance_id'])->get()->first();
						// Handling non object error in patient insurance
						// Revision 1 : MR-2810 : 09 Sep 2019 : Selva
						if(isset($patient_insurance_id) && !empty($patient_insurance_id)){
							if($patient_insurance_id->insurance_id == $claims['claim_section']['insurance_id'] && $claims['claim_section']['insurance_category'] == $descDet['insurance_category']){
								$submitted_count++;
							}
						}
					}
				}
				$patient_insurance_id = $claims["patient_insurance_details"]["patient_insurance_id"];
				if(!isset($claims['submission'])){
					if($submitted_count == 0){
						$claimSubmissionType = 1;
						$claimTxnDesc = $this->storeClaimTxnDesc('Submitted', $data);
					}else{
						$claimSubmissionType = 7;
						$claimTxnDesc = $this->storeClaimTxnDesc('Resubmitted', $data);
					}
				}
                $sp_return_result = DB::select('call Loop2300(' . $claim_id .')');
                $spReturnValue = $sp_return_result[0]->varReturnValue;
                $this->writeMultipleSegments($spReturnValue);

                if (count($claims['refering_provider']) > 0) {
                    $sp_return_result = DB::select('call Loop2310A(' . $claim_id . ')');
                    $spReturnValue = $sp_return_result[0]->varReturnValue;
                    $this->writeMultipleSegments($spReturnValue);
                }

                // 2310B - RENDERING PROVIDER NAME Page No 290
                $sp_return_result = DB::select('call Loop2310B(' . $claim_id . ')');
                $spReturnValue = $sp_return_result[0]->varReturnValue;
                $this->writeMultipleSegments($spReturnValue);

                // Loop2310C - Facility 
				$posType = $claims['facility_detail']['pos_id'];
                $sp_return_result = DB::select('call Loop2310C(' . $claim_id . ',' .$patient_id.','.$posType.')');
                $spReturnValue = $sp_return_result[0]->varReturnValue;
                $this->writeMultipleSegments($spReturnValue);

                if ($claims['claim_section']['is_send_paid_amount'] == 'No' && $insurance_category != 'Primary') {
                    
					// Old Payment Table refering 
					
					/* $posted_insurance_details = PaymentClaimDetail::has('claimdoscptdetails')->whereHas('claimdoscptdetails', function($q) use($claim_id){$q->where('claim_id', $claim_id)->where('is_active',1);})->where('claim_id', $claim_id)->where('patient_id', $patient_id)->where('payment_type','Insurance')->groupBy(['insurance_id','insurance_category'])->get(); */
					
                    /* $posted_insurance_details = PaymentClaimDetail::has('claimdoscptdetails')->whereHas('claimdoscptdetails', function($q) use($claim_id) {
                                $q->where('claim_id', $claim_id)->where('insurance_paid', '>', 0)->where('is_active', 1);
                            })->where('claim_id', $claim_id)->where('patient_id', $patient_id)->where('payment_type', 'Insurance')->where('insurance_paid_amt', '>', 0)->groupBy(['insurance_id', 'insurance_category'])->get();*/
							
							
					// New Table Follow		
					// ->where('total_paid','>',0) removed this condition form below query
					$posted_insurance_details  = 	PMTClaimTXV1::has('claimcptinfoV1')
													->whereHas('claimcptinfoV1' , function($q) use($claim_id){$q->where('claim_id', $claim_id)->where('is_active',1);})
													->where('claim_id', $claim_id)
													->where('patient_id', $patient_id)
													->where('pmt_method','Insurance')
													->where('pmt_type','Payment')
													->where('claim_insurance_id','!=','0')
													->groupBy(['claim_insurance_id'])
													->get();

                    foreach ($posted_insurance_details as $previous_insurance) {
                        $previous_insurance_id = $previous_insurance->claim_insurance_id;
                        $previous_insurance_category = $previous_insurance->ins_category;
						if(empty($previous_insurance_category))
							$previous_insurance_category = "Primary";
                        $sp_return_result = DB::select('call Loop2320Sec(' . $claim_id . ',' . $previous_insurance_id . ',"' . $previous_insurance_category . '",' . $patient_id . ',' . $patient_insurance_id . ')');
                        $spReturnValue = $sp_return_result[0]->varReturnValue;
                        $this->writeMultipleSegments($spReturnValue);

                        $sp_return_result = DB::select('call Loop2330ASec(' . $previous_insurance_id . ',"' . $previous_insurance_category . '",' . $patient_id . ',' . $patient_insurance_id . ')');
                        $spReturnValue = $sp_return_result[0]->varReturnValue;
                        $this->writeMultipleSegments($spReturnValue);

                        $sp_return_result = DB::select('call Loop2330BSec(' . $previous_insurance_id . ')');
                        $spReturnValue = $sp_return_result[0]->varReturnValue;
                        $this->writeMultipleSegments($spReturnValue);
                    } 
                }

                /// Starts - Insert edi transmission claim details into tables ///
                $transmission_claim['edi_transmission_id'] = $edi_transmission_id;
                $transmission_claim['claim_id'] = $claim_id;
                $transmission_claim['claim_type'] = 'Primary';
                $transmission_claim['insurance_id'] = $claims['claim_section']['insurance_id'];
                $transmission_claim['icd'] = implode(',', $claims['claim_section']['diagnosis_details']['selected_icd']);
                $transmission_claim['referring_provider_id'] = @$claims['claim_section']['referring_provider_id'];
                $transmission_claim_create = TransmissionClaimDetails::create($transmission_claim);
                $transmission_claim_id = $transmission_claim_create->id;
                /// Ends - Insert edi transmission claim details into tables ///		

                $s = 1;
                $claim_billed_amount = 0;
                foreach ($claims['claim_section']['line_item'] as $line_item) {
                    $line_item_id = $line_item['line_item_id'];
					$dataArr['claim_cpt_info_id'] = $line_item_id;
                    $cptFinData = PMTClaimCPTFINV1::select(['id','patient_balance','insurance_balance'])
                        ->where('claim_cpt_info_id',$line_item_id)->get()->first();
                    $dataArr['pat_bal'] = $cptFinData['patient_balance'];
                    $dataArr['ins_bal'] = $cptFinData['insurance_balance'];
					if(!isset($claims['submission'])){
						$dataArr['claim_tx_desc_id'] = $claimTxnDesc;
						if ($submitted_count > 0) {
							$this->storeClaimCptTxnDesc('Resubmitted', $dataArr);
						} else {
							$this->storeClaimCptTxnDesc('Submitted', $dataArr);
						}
					}
                    $sp_return_result = DB::select('call Loop2400(' . $claim_id . ',' . $line_item_id . ',' . $s . ')');
                    $spReturnValue = $sp_return_result[0]->varReturnValue;
                    $this->writeMultipleSegments($spReturnValue);
                    if ($claims['claim_section']['is_send_paid_amount'] == 'No' && $insurance_category != 'Primary') {
                        /// Write if we are submit claim to secondary/Ter/ Not to primary ///
						
						// Old Payment Table Follow 
						
                        /* $posted_line_item_insurance_details = PaymentClaimDetail::has('paymentcptdetailclaim')->whereHas('paymentcptdetailclaim', function($q) use($line_item_id) {
                                    $q->where('claimdoscptdetail_id', $line_item_id)->where('insurance_paid', '>', 0)->where('posting_type', 'Insurance');
                                })->where('claim_id', $claim_id)->where('patient_id', $patient_id)->where('payment_type', 'Insurance')->where('insurance_paid_amt', '>', 0)->groupBy(['insurance_id', 'insurance_category'])->get();*/
                        
						
						// New Table Follow
						// ->where('total_paid','>',0) removed this line from below query for insurance paid check condition
						$posted_line_item_insurance_details = 	PMTClaimTXV1::has('pmtclaimcpttxV1')
																->whereHas('pmtclaimcpttxV1' , function($q) use($claim_id,$line_item_id){$q->where('claim_cpt_info_id', $line_item_id);})
																->where('claim_id', $claim_id)
																->where('patient_id', $patient_id)
																->where('pmt_method','Insurance')
																->where('pmt_type','Payment')
																->where('claim_insurance_id','!=','0')
																->groupBy(['claim_insurance_id'])
																->get();
						
						foreach ($posted_line_item_insurance_details as $line_item_posting) {
                            $sp_return_result = DB::select('call Loop2430Sec(' . $claim_id . ',' . $line_item_posting->claim_insurance_id . ',' . $line_item_id . ',' . $patient_id . ')');
                            $spReturnValue = $sp_return_result[0]->varReturnValue;
                            $this->writeMultipleSegments($spReturnValue);
                        } 
                    }

                    /// Starts - Insert edi transmission claim details into tables ///
                    $transmission_cpt['edi_transmission_id'] = $edi_transmission_id;
                    $transmission_cpt['transmission_claim_id'] = $transmission_claim_id;
                    $transmission_cpt['cpt'] = $line_item['cpt'];
                    $transmission_cpt['icd_pointers'] = $line_item['icd_pointers'];
                    $transmission_cpt['billed_amount'] = $line_item['billed_amount'][0] . '.' . $line_item['billed_amount'][1];
                    TransmissionCptDetails::create($transmission_cpt);
                    $claim_billed_amount = $claim_billed_amount + $transmission_cpt['billed_amount'];
                    /// Ends - Insert edi transmission claim details into tables ///
                    $s++;
                }
                $transmission_claim_create->total_billed_amount = $claim_billed_amount;
                $transmission_claim_create->save();
                $total_billed_amount = $total_billed_amount + $claim_billed_amount;

                /// Starts - Update Claim table details ///
                $cur_date = date("Y-m-d H:i:s");
                $claims_update = '';
                $claims_update = ClaimInfo::find($claim_id);
                $claims_update->status = 'Submitted';
                if ($claims['claim_section']['claim_submit_count'] == 0) {
                    $claims_update->submited_date = $cur_date;
                    $claims_update->last_submited_date = $cur_date;
                } else {
                    $claims_update->last_submited_date = $cur_date;
                }
                $submission_count = $claims['claim_section']['claim_submit_count'] + 1;
                $claims_update->claim_submit_count = $submission_count;
                $claims_update->save();
                /// Ends - Update Claim table details ///

                /* /// Starts - Add submitted entry in claim transaction table ///
                  $request['claim_id'] = $claim_id;
                  $request['patient_id'] = $patient_id;
                  $request['description'] = "Claim has been submitted to ".$claims["insurance_details"]["insurance_name"];
                  PaymentClaimDetail::saveClaimTransaction($request);
                  /// Ends - Add submitted entry in claim transaction table /// */
            }
            $edi_transmission->total_billed_amount = $total_billed_amount;
            $edi_transmission->save();
            $this->writeFooterLoops();
            //DB::commit();

            if (App::environment() == 'local') {
                $path = $this->path;
                $ftp_server = $clearing_house_details->ftp_address;
                $ftp_username = $clearing_house_details->ftp_user_id;
                $ftp_password = $clearing_house_details->ftp_password;
                $ftp_port = $clearing_house_details->ftp_port;

                $source_file = $path . $this->filename;
                $destination_file = $clearing_house_details->ftp_folder . '/' . $this->filename;

                // set up basic connection
                set_time_limit(0);
                $connection = ssh2_connect($ftp_server, $ftp_port);
                if (ssh2_auth_password($connection, $ftp_username, $ftp_password)) {
                    // initialize sftp
                    $sftp = ssh2_sftp($connection);
                    $contents = file_get_contents($source_file);
                    //echo "<pre>";print_r($contents);
                    if (file_put_contents("ssh2.sftp://" . intval($sftp) . "/{$destination_file}", $contents)) {
                        DB::commit();
                        $edi_transmission['is_transmitted'] = 'Yes';
                        $edi_transmission->save();
                    } else
                        DB::rollback();
                }
                else {
                    DB::rollback();
                }
            } else {
                DB::commit();
                $edi_transmission['is_transmitted'] = 'Yes';
                $edi_transmission->save();
            }
        } catch (\Exception $e) {
            DB::rollback();
			\Log::info("EDI ERR:".$e->getMessage());
            dd($e);
        }
    }

    /*     * * Ends - EDI file creation ** */

    public function writeFooterLoops() {
        //$tempString = '';
        $tempString = "SE*" . $this->totalLines . "*0001~";
        $this->writeToFile($tempString, true);

        // Going to Write GE Segment
        // http://emrpms.blogspot.in/2012/08/edi-5010-documentation-837-professional.html
        // Sample : GE*1*0001

        $tempString = "GE*1*000000000~";
        $this->writeToFile($tempString, true);

        // Going to Write IEA Segment
        // http://emrpms.blogspot.in/2012/09/edi-5010-documentation-837-professional_12.html
        // Sample : IEA*1*0001

        $tempString = "IEA*1*000000000~";
        $this->writeToFile($tempString, true);
    }

    public function writeLoop2000C($relationShip) {
        if ($relationShip == 1)
            $relationShip = "01";
        $tempString = '';
        $tempString = "PAT";
        $tempString = $tempString . "*" . $relationShip . "~";
        $this->writeToFile($tempString, true);
    }

    public function writePatientHLevel($relationShip) {
        $tempString = '';
        $tempString = "HL";
        $tempString = $tempString . "*" . $this->HLCnt;
        $this->HLCnt = $this->HLCnt + 1;
        $tempString = $tempString . "*" . $this->HLSubscriberPtr;
        $tempString = $tempString . "*" . "23";
        $tempString = $tempString . "*0~";
        $this->writeToFile($tempString, true);
    }

    public function writeSubScriberLEVEL($relationShip) {
        $tempString = '';
        $tempString = "HL" . "*" . $this->HLCnt;
        $this->HLCnt = $this->HLCnt + 1;
        $tempString = $tempString . "*" . $this->HLCntPtr;
        $tempString = $tempString . "*22";
        if ($relationShip == "Self") {
            $tempString = $tempString . "*0";
        } else {
            $tempString = $tempString . "*1";
            $this->HLSubscriberPtr = $this->HLCnt - 1;
        }
        $tempString = $tempString . "~";
        $this->writeToFile($tempString, true);
    }

    public function writeHLLevelCount() {
        $tempString = '';
        //HL*1**20*1
        $tempString = "HL*" . $this->HLCnt . "**20*1~";
        $this->writeToFile($tempString, true);
        $this->HLCntPtr = $this->HLCnt;
        $this->HLCnt = $this->HLCnt + 1;
    }

    public function writeMultipleSegments($spReturnValue) {
        // From the SP, the return value may come as follows
        // xxxxxx~xxxxxx~xxxxxxx~.  i.e that multiple segments. But here we will split that and write one by one as follows.

        $tempString = '';
        $parts = explode("~", $spReturnValue);
        $parts_length = count($parts) - 1;
        for ($i = 0; $i < $parts_length; $i++) {
            $tempString = $parts[$i] . "~";
            $this->writeToFile($tempString, true);
        }
    }

    public function writeToFile($ediLine, $linecount) {
        
        $path_medcubic = public_path() . '/';
        $path = $path_medcubic . 'media/clearing_house/' . Session::get('practice_dbid') . '/';

        //$path = $this->path;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $myfile = $path . $this->filename;

        if (!file_exists($myfile))
            fopen($myfile, "w");

        if ($linecount == true)
            $this->totalLines = $this->totalLines + 1;

        $current = file_get_contents($myfile);
        $current .= $ediLine;
        file_put_contents($myfile, $current);
    }
	
	public function edi_connection_check(){
			try{
				$ftp_server = 'ftp.officeally.com';
				$ftp_port = 22;
				$ftp_username = 'ambdaniel';
				$ftp_password = 'Teec6V96';
				$connection = ssh2_connect($ftp_server, $ftp_port);
                ssh2_auth_password($connection, $ftp_username, $ftp_password);
				$stream = ssh2_sftp($connection);
				$source_dir = "outbound";
				$dir = opendir("ssh2.sftp://" . intval($stream) . "/{$source_dir}/./");
				while (false !== ($file = readdir($dir))) {
					\Log::info($file);
				}
				
				
			}catch(Exception $e){
				dd($e->getMessage());
			}
	}
	
	
	public function check277Segment(){
		$file_content = "";
		$fileSegmentContent = explode('~', $file_content);
		$claim_count = $Claimcount = 0;
		foreach ($fileSegmentContent as $key => $segmentList) {
			if(substr($segmentList, 0, 4) == 'ISA*'){ 
				$temp = explode('*', $segmentList);
				$seprator = $temp[16];
			 
			} 
			
			if(substr($segmentList, 0, 6) == 'TRN*2*'){
					$temp = explode('*', $segmentList);
					if(isset($temp[2]) && !empty($temp[2])){
							$claim_details = ClaimInfoV1::where('claim_number', trim($temp[2]))->first();

							if(isset($claim_details) && !empty($claim_details->claim_number)){
									$multiCode = "";
									$claimNo = $dataArr[$claim_details->claim_number]['CLAIM#'] = $claim_details->claim_number;
									$claim_count++;
									$Claimcount = 1;
							}else{
								$Claimcount = 0;	
							}
					}
			}

			if(substr($segmentList, 0, 4) == 'STC*' && $Claimcount == 1){
					$temp = explode('*', $segmentList);
					echo $segmentList."<br>";
					$tempSegment = explode($seprator, $temp[1]);
					echo $tempSegment[0]."<br>";
					if(!empty($claimNo) && isset($tempSegment[0]) && isset($tempSegment[1])){
							if(isset($temp[12])){
								$multiCode .= "<li><span class='med-orange font600'>".$tempSegment[0].$tempSegment[1]."</span> - ".$temp[12]."</li>";
								$dataArr[$claimNo]['ERROR'] = $multiCode;
							}else{
								$dataArr[$claimNo]['ERROR'] = '';
							}
							if(trim($tempSegment[0]) == 'A0' || trim($tempSegment[0]) == 'A1' || trim($tempSegment[0]) == 'A2' || trim($tempSegment[0]) == 'A5'){
								$dataArr[$claimNo]['Code'] = $tempSegment[0];
								if($tempSegment[0] == 'A0'){
									$dataArr[$claimNo]['STATUS'] = 'ACCEPTED';
									$dataArr[$claimNo]['TYPE'] = 'Clearing';
								}else{
									$dataArr[$claimNo]['STATUS'] = 'ACCEPTED';
									$dataArr[$claimNo]['TYPE'] = 'Payer';
								}
							}else{
								$dataArr[$claimNo]['Code'] = $tempSegment[0];
								$dataArr[$claimNo]['STATUS'] = 'REJECTED';
								$dataArr[$claimNo]['TYPE'] = 'Clearing';
							}
					}
			}
		}
		echo "<pre>";print_r($dataArr);
	}

}
