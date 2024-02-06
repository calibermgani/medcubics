<?php
//try { 
?>
<?xml version="1.0" encoding="UTF-8"?>
<billflashdoc version="1">
	<statements>		
	 @if(count((array)@$claims)>0) 				
		<?php						
			$CC = 0;
			 // 1=Visa, 2=MasterCard, 4=Discover, 8=AmEx (sum any 2 or more #’s together for any combo) Ex: 3=Visa/Mcard, 
			 // maestro_card => Discover, gift_card => AmEx   => Have to change accordingly in all placed with the gateway provided by NexTrust.
			if($psettings->visa_card != 0) {
				$CC +=1;
			}
			if($psettings->mc_card != 0) {
				$CC +=2;
			}
			if($psettings->maestro_card != 0) {
				$CC +=4;
			}  
			if($psettings->gift_card != 0) {
				$CC +=8;
			} 
			$CCSecCode = '';
			$get_last_result = count((array)$claims)-1; 
            $patient_zip4 = (@$patients->zip4!='')?' - '.@$patients->zip4:'';  
			$patient_other_zip4 = (@$patient_other_address->zip4!='')?'-'.@$patient_other_address->zip4:'';  
            $guarantor_zip4 = (count((array)$patient_guarantor_address)>0 && @$patient_guarantor_address->guarantor_zip4!='')?' - '.@$patient_guarantor_address->guarantor_zip4:'';
            $check_zip4  =  (@$psettings->check_zip4!='')?' - '.@$psettings->check_zip4:''; 
			$practice_zip4   =  (@$practice->pay_zip4!='')?' - '.@$practice->pay_zip4:'';  
            $facility_zip4   =  (@$claims[$get_last_result]->facility_detail->facility_address->pay_zip4!='')?' - '.@$claims[$get_last_result]->facility_detail->facility_address->pay_zip4:'';  
            $totalbalance = 0;//@$insurance_balance + @$patient_balance;  
            $insurance_bal = $patient_bal = '';			
			$patient_age = date_diff(date_create(@$patients->dob), date_create('today'))->y;
			
			if($psettings->servicelocation=='Facility') {
				$RetName = strtoupper(@$claims[$get_last_result]->facility_detail->facility_name);	
				$RetAddress1 =  strtoupper(@$claims[$get_last_result]->facility_detail->facility_address->address1);
				$RetAddress2 = strtoupper(@$claims[$get_last_result]->facility_detail->facility_address->address2);
				$RetCity = strtoupper(@$claims[$get_last_result]->facility_detail->facility_address->city);
				$RetST = strtoupper(@$claims[$get_last_result]->facility_detail->facility_address->state);
				$RetZip = @$claims[$get_last_result]->facility_detail->facility_address->pay_zip5.$facility_zip4;
			} else {  // RPG changes
				$RetName =  ($practice->id == 40 ) ? 'Rural Physicians Group' : strtoupper(@$practice->practice_name);
				$RetAddress1 = strtoupper(@$practice->pay_add_1);
				$RetAddress2 =  strtoupper(@$practice->pay_add_2);
				$RetCity =  strtoupper(@$practice->pay_city);
				$RetST =  strtoupper(@$practice->pay_state);
				$RetZip =  @$practice->pay_zip5.$practice_zip4;
			}
			$SvcMsg1 = '';
			$SvcMsg2 = '';
			$SvcMsg3 = '';			
			$patEmail = $patients->email;
			$RecName = ($patients->title) ? @$patients->title."." : "";
			$RecName .= strtoupper(App\Http\Helpers\Helpers::getNameformat($patients->last_name,$patients->first_name,$patients->middle_name));
			$guarantor_name = (count((array)$patient_guarantor_address)>0) ? App\Http\Helpers\Helpers::getNameformat($patient_guarantor_address->guarantor_last_name,$patient_guarantor_address->guarantor_first_name,$patient_guarantor_address->guarantor_middle_name) : "";
			
			if($patient_age <= 18) {
				$RecName = ($guarantor_name != '') ? strtoupper($guarantor_name) : strtoupper($pat_name);
			}
			$RecName2 = $RecAddress1 = $RecAddress2 = $fullAddr = '';			
			if(count((array)$patient_other_address)>0 && $patient_other_address->address1 !='') {
				$RecAddress1 = strtoupper(@$patient_other_address->address1);
				$RecAddress2 = strtoupper(@$patient_other_address->address2);
				$RecCity = strtoupper(@$patient_other_address->city);
				$RecST = strtoupper(@$patient_other_address->state);
				$RecZip = strtoupper(@$patient_other_address->zip5).$patient_other_zip4;
			} else {
				if($patient_age < 18 && count((array)$patient_guarantor_address)>0 && $patient_guarantor_address->guarantor_address1!='') {
					$RecAddress1 = strtoupper(@$patient_guarantor_address->guarantor_address1);
					$RecAddress2 = strtoupper(@$patient_guarantor_address->guarantor_address2);
					$RecCity = strtoupper(@$patient_guarantor_address->guarantor_city);
					$RecST = strtoupper(@$patient_guarantor_address->guarantor_state);
					$RecZip = strtoupper(@$patient_guarantor_address->guarantor_zip5.$guarantor_zip4);
				} else {
					$RecAddress1 = strtoupper(@$patients->address1);
					$RecAddress2 = strtoupper(@$patients->address2);
					$RecCity = strtoupper(@$patients->city);
					$RecST = strtoupper(@$patients->state);
					$RecZip = @$patients->zip5.$patient_zip4;
				}
			}
			
			$AcctNoNM = 'Acc No';  // Account Number Name box , Also printed on statement detail line
			$AcctNo = @$patients->account_no;	// Account Number Data box, Also printed on statement detail line
			
			$PayBoxNM1 = ''; 	//	Payment Box Name 1 (if blank, box doesn’t print)
			$PayBox1 = ''; 		//  Payment Box Data 1
			$PayBoxNM2 = ''; 	//	Payment Box Name 1 (if blank, box doesn’t print) 
			$PayBox2 = '';		//	Payment Box Data 1 
			
			$PayBoxNM3 = 'Payment Due'; 	//	Payment Box Name 1 (if blank, box doesn’t print) 
			$PayBox3 = (count((array)$patients->patient_budget)>0)? ($get_budgetamount->budget_balance == 0)? App\Http\Helpers\Helpers::priceFormat($get_budgetamount->budget_amt,'yes'): App\Http\Helpers\Helpers::priceFormat($get_budgetamount->budget_balance,'yes') : App\Http\Helpers\Helpers::priceFormat($patient_balance,'yes'); 		// 	Payment Box Data 1			
			// Patient Budget amount;
			$patBudget = [];
			if(isset($patients->patient_budget)) {
				$patBudget = App\Http\Helpers\Helpers::getPatientBudgetBalence($patients->id);
				if($patBudget['budgetDueTotalAmount'] > 0 && $patBudget['budgetDueTotalAmount'] > 0) {
					$PayBox3 = App\Http\Helpers\Helpers::priceFormat($patBudget['budgetDueTotalAmount']);
				}
			}	
			
			$RemName = 'Mailing Address';
			$RemAddress1 = ($psettings->check_add_1!='')? strtoupper(@$psettings->check_add_1) : '';
			$RemAddress2 = ($psettings->check_add_2!='')? strtoupper(@$psettings->check_add_2) : '';
			$RemCity = strtoupper(@$psettings->check_city);
			$RemST = strtoupper(@$psettings->check_state);
			$RemZip = strtoupper(@$psettings->check_zip5.$check_zip4);
			
			$RemCheckDigit = '';			
		    $TopMsg = '';
			if($psettings->rendering_provider == 1) {
				$TopMsg = "Rendering Provider:".@$claims[$get_last_result]->rendering_provider->provider_name." ".@$claims[$get_last_result]->rendering_provider->degrees->degree_name;
			}
		    $DetailBarNM = '';	// Name printed on detail bar (doesn't print if blank)
		    $DetailBar1 = '';	// Field 1 for statement detail header line (doesn’t print if blank) 
		    $DetailBar2 = '';	// Field 2 for statement detail header line (doesn’t print if blank)
		    $DetailNM = '';
		    $DetailNMx = '';			
			$DetailNMH[] = 'Visit Date';
			if($psettings->displaypayment == 'Payments')
				$DetailNMH[] = 'Activity Date';
			
			$DetailNMH[] = 'Description of Service';
			$DetailNMH[] = 'Charges';
			if($psettings->displaypayment == 'Payments')
				$DetailNMH[] = 'Payments';
			if($psettings->displaypayment == 'InsPatient') {
				$DetailNMH[] = 'Insurance Payments';
				$DetailNMH[] = 'Patient Payments';
			}	
			$DetailNMH[] = 'Adjustments';
			$DetailNMH[] = 'Balance';
			
			//905 - 9 Columns / 805 - 8 Columns / 705 - 7 Columns / 706 - 7 Columns with word wrap.
			$TableID = '401';
			if(count((array)$DetailNMH) == 9)
				$TableID = 905;
			elseif(count((array)$DetailNMH) == 8)
				$TableID = 805;
			elseif(count((array)$DetailNMH) == 7)
				$TableID = ($psettings->displaypayment == 'Payments') ? 704 : 705; 	
			else	
				$TableID = 401;	
					
			//Turn account summary on Y/N (if N, all acct summary is eliminated on statement)  If Acct Summary is to print, the first SumTtl defines if the box will print or not.  If SumTtlA is blank, the entire box will be eliminated.
			
			$UseAcctSummary = (@$psettings->latestpaymentinfo == '1' && count((array)$patient_latestpayment)>0) ? 'Y' : 'N';
			
			$SumNMa		= 'Last Statement'; // Name for the Account Summary boxes - line 1
			$SumNMb		= ''; // Name for the Account Summary boxes - line 2
			$SumNM1a	= 'Payment Date'; // Name for summary field 1 (Use logic defined above)
			$SumNM1b	= ''; // Name for summary field 1 (Use logic defined above)
			$Sum1		= App\Http\Helpers\Helpers::dateFormat(@$patient_latestpayment->created_at,'date'); // Data for summary field 1 (Use logic defined above)
			$SumNM2a	= 'Paid Amount'; // Name for summary field 2 (Use logic defined above)
			$SumNM2b	= ''; // Name for summary field 2 (Use logic defined above)
			$Sum2		= App\Http\Helpers\Helpers::priceFormat(@$patient_latestpayment->patient_paid,'yes'); // Data for summary field 2 (Use logic defined above)
			$SumNM3a	= 'Payment Method'; // Name for summary field 3 (Use logic defined above)
			$SumNM3b	= ''; // Name for summary field 3 (Use logic defined above)
			$Sum3		= @$patient_latestpayment->pmt_mode; // Data for summary field 3 (Use logic defined above)
			$SumNM4a	= ''; // Name for summary field 4 (Use logic defined above)
			$SumNM4b	= ''; // Name for summary field 4 (Use logic defined above)
			$Sum4		= ''; // Data for summary field 4 (Use logic defined above)
			$SumNM5a	= ''; // Name for Summary field 5 (Use logic defined above)
			$SumNM5b	= ''; // Name for Summary field 5 (Use logic defined above)
			$Sum5		= ''; // Data for summary field 5 (Use logic defined above)
			
			$DueNM1a	= 'Payment Due'; // Name for Due field 2 First line (If this field is blank, the box doesn't print)
			$DueNM1b	= ''; // Name for Due field 2 Second line
			//Data field for Due field 2
			$Due1 	 = (count((array)$patients->patient_budget)>0)? ($get_budgetamount->budget_balance == 0)? App\Http\Helpers\Helpers::priceFormat($get_budgetamount->budget_amt,'yes'): App\Http\Helpers\Helpers::priceFormat($get_budgetamount->budget_balance,'yes') : App\Http\Helpers\Helpers::priceFormat($patient_balance,'yes'); 
			
			if(isset($patients->patient_budget)) {
				if($patBudget['budgetDueTotalAmount'] > 0 && $patBudget['budgetDueTotalAmount'] > 0) {
					$Due1 	 = App\Http\Helpers\Helpers::priceFormat($patBudget['budgetDueTotalAmount']);
				}
			}
			$DueNM2a	= 'Pay by Date'; // Name for Due field 1 First line (If this field is blank, the box doesn't print)
			$DueNM2b	= ''; 			// Name for Due field 1 Second line
			$paybydate = date('Y-m-d',strtotime(date('Y-m-d') . ' +'.@$psettings->paybydate.' day'));
			$Due2		= App\Http\Helpers\Helpers::dateFormat($paybydate,'date'); // Data field for Due1
			
			//	Use the Aging boxes Y/N (if N, all aging is eliminated on statement)
			$UseAging = (isset($psettings->aging_bucket) && $psettings->aging_bucket == 1) 	? 'Y' : 'N';
			
			$AgeNM	= 'Aging'; 	//	Name for the Aging  boxes 
			$AgeNM1	= '0-30'; 	//	Aging Name1 (if Name blank, doesn’t print)
			$Age1	= ''; 		//	Aging Amount 1 (if Name blank, doesn’t print)
			$AgeNM2 = '31-60'; 	//	Aging Name2 (if Name blank, doesn’t print)
			$Age2 = ''; 		//	Aging Amount 2 (if Name blank, doesn’t print)
			$AgeNM3	= '61-90'; 	//	Aging Name3 (if Name blank, doesn’t print)
			$Age3 = ''; 		//	Aging Amount 3 (if Name blank, doesn’t print)
			$AgeNM4	= '91-120'; //	Aging Name 4 (if Name blank, doesn’t print)
			$Age4 = ''; 		//	Aging Amount 4 (if Name blank, doesn’t print)
			$AgeNM5	= '120+'; 	//	Aging Name5 (if Name blank, doesn’t print)
			$Age5	= ''; 		//	Aging Amount 5 (if Name blank, doesn’t print)
						
			$ageBk = [];
			//dd($patients->aging);
			$ageData = $patients->aging;
			foreach($ageData['Insurance'] as $key=>$claimval) {				
				$get_monthclaimvalue = (is_float($claimval))? (App\Http\Helpers\Helpers::priceFormat($claimval)) : $claimval;					
				if($key < 5)
					$ageBk[$key+1] = (isset($claimval) && ( $claimval!='0'))? $get_monthclaimvalue :'0.00';
			}
			//	Free-form text msg1–with wrapping(if no msgs are sent in any of the BottomMessage(s),the msg box will not print)
			if($practice->id == 40 ){
				$Msg1	= 'This bill is for services provided at '.strtoupper(@$claims[$get_last_result]->facility_detail->facility_name);
				$Msg2   = 'For Billing Inquiries Please Call:'.@$psettings->callbackphone.' with your Account Number';	
				$Msg3   = 'Please visit us at https://ruralphysiciansgroup.com to pay by credit card';
			} else {
				$Msg1	= '';
				$Msg2	= '';
				$Msg3	= '';
			}
			if(isset($patients->patient_statement_note->content) && $patients->patient_statement_note->content != '') {
				$Msg4   = $patients->patient_statement_note->content;	//	Free-form text message 4 – with wrapping
				$Msg5   = ($paymentmessage == '') ? @$psettings->paymentmessage_1 : $paymentmessage;	//	Free-form text message 4 – with wrapping
				$Msg6   = @$psettings->spacial_message_1;												//	Free-form text message 5 – with wrapping
				$Msg7	= $Msg8	= $Msg9	= $Msg10 = '';	//	Collection Leter and Standard Letter use only
			} else {
				$Msg4   = ($paymentmessage == '') ? @$psettings->paymentmessage_1 : $paymentmessage;	//	Free-form text message 4 – with wrapping
				$Msg5   = @$psettings->spacial_message_1;												//	Free-form text message 5 – with wrapping
				$Msg6	= $Msg7	= $Msg8	= $Msg9	= $Msg10 = '';	//	Collection Leter and Standard Letter use only
			}
			
			// Details Start //
			$detailsBlk = []; 
			foreach($claims as $claim_detail) {	
				$cptTx = $claim_detail->cpttransactiondetails;			
				$claim_details = App\Models\Payments\ClaimInfoV1::getClaimTransactionList($claim_detail->id);            
				$claim_count = 1; 
				if(!empty($claim_details)) {
					$idx = 0;
					foreach($claim_details as $claim) {											
						$insBal = (!is_null($claim['ins_balance'])) ? str_replace(",","",strip_tags($claim['ins_balance'])) : 0;
						$patBal = (!is_null($claim['pat_balance'])) ? str_replace(",","",strip_tags($claim['pat_balance'])) : 0;
						$totalbalance = $insBal+$patBal;  //$claim['total_balance'];	
						$insurance_bal = $claim['ins_balance'];
						$patient_bal = $claim['pat_balance'];                
						/* Claim status check  */
					
						$claim_fin_ipmt = App\Http\Helpers\Helpers::priceFormat(@$claim['pmt_fins']['insurance_paid']);
						$claim_fin_ppmt = App\Http\Helpers\Helpers::priceFormat(@$claim['pmt_fins']['patient_paid']);
						$claim_fin_pmt = App\Http\Helpers\Helpers::priceFormat(@$claim['pmt_fins']['patient_paid']+ @$claim['pmt_fins']['insurance_paid']);
						$claim_fin_adj = App\Http\Helpers\Helpers::priceFormat(@$claim['pmt_fins']['patient_adj'] + @$claim['pmt_fins']['insurance_adj']+ @$claim['pmt_fins']['withheld']);						
						$claim_fin_bal = App\Http\Helpers\Helpers::priceFormat(@$claim['pmt_fins']['total_charge'] - (@$claim['pmt_fins']['patient_paid']+@$claim['pmt_fins']['insurance_paid'] + @$claim['pmt_fins']['patient_adj'] + @$claim['pmt_fins']['insurance_adj'] + @$claim['pmt_fins']['withheld']));   
				
						/* Claim denied status also included in the list */
						if($claim['txn_details']['claim_info']->status == 'Denied' && $claim['txn_details']['claim_info']->deleted_at == "1" ) {
							$detailsRow = [];
							$detailsRow[] = ''; 
							if(@$psettings->displaypayment == 'Payments') {
								$detailsRow[] = '';
							}						
							$detailsRow[] = $claim['txn_details']['claim_info']->status;
							$detailsRow[] = '';
							if(@$psettings->displaypayment == 'Payments') {
								$detailsRow[] = 0.00;
							}
							if($psettings->displaypayment == 'InsPatient') {
								$detailsRow[] = 0.00;
								$detailsRow[] = '';
							}
							$detailsRow[] = '';
							$detailsRow[] = '';
							$detailsBlk[] = $detailsRow;
						} else {
							$balance_amt = App\Http\Helpers\Helpers::priceFormat($totalbalance) ;		/* Claim Balance amount */
							/* check all insurance & patient claim will show or not */											
							if(@$balance_amt != '0.00' && @$psettings->insserviceline==0 && @$psettings->patserviceline==0 || @$psettings->insserviceline==1 || @$psettings->patserviceline==1) {				
											
								$totalpayment = count((array)$claim['pmt_trans']); 
								$paycount = 1;
							
								/* List claim vise */
								if(@$psettings->cpt_shortdesc == 'Claim') {
									
									$lastCoinsDet = App\Models\Payments\ClaimInfoV1::getClaimLastCopayDetails($claim['txn_details']['claim_info']->id);
									$detailsRow = [];
										
									if($idx == 0) {
										$detailsRow[] = App\Http\Helpers\Helpers::dateFormat(@$claim['txn_details']['claim_info']->date_of_service,'date');
									}
								
									if(@$psettings->displaypayment == 'Payments') {
										$detailsRow[] = App\Http\Helpers\Helpers::dateFormat(@$claim['txn_details']['claim_info']->created_at,'date');
									}							
									
									/* CPT Transaction Showed here */ 
									$tempDesc = '';
									foreach(@$claim['cpt_transaction'] as $show_cpt) {										
										$cpt_desc = App\Models\Cpt::cpt_shot_desc($show_cpt['cpt_code']);
										$tempDesc .= @strtoupper($show_cpt['cpt_code']) ." ".(($cpt_desc != '')?'- '.strtoupper($cpt_desc):'');
										$tempDesc .= ' ';
									}
									
									if($lastCoinsDet != ""){
										$tempDesc = $tempDesc." ".$lastCoinsDet;	
									}
									$detailsRow[] = $tempDesc;
									$detailsRow[] = @$claim['txn_details']['claim_info']->total_charge;
									if($psettings->displaypayment == 'Payments') {
										$detailsRow[] = @$claim_fin_pmt;
									}
									if($psettings->displaypayment == 'InsPatient') {
										$detailsRow[] = @$claim_fin_ipmt;
										$detailsRow[] = @$claim_fin_ppmt;
									}
									$detailsRow[] = @$claim_fin_adj;
									$detailsRow[] = $claim_fin_bal;
									$detailsBlk[] = $detailsRow;

									/* List ICD detail with description in claim vise */
									if(@$psettings->primary_dx == '1') {
										/* Split ICD code useing comma(,) based */
										$get_icd_detail = explode(',',$claim['txn_details']['claim_info']->icd_codes);
										$detailsRow = [];										   
										$detailsRow[] = App\Http\Helpers\Helpers::dateFormat($claim['txn_details']['claim_info']->date_of_service,'date');
										if(@$psettings->displaypayment == 'Payments') {
											$detailsRow[] = App\Http\Helpers\Helpers::dateFormat($claim['txn_details']['claim_info']->created_at,'date');
										}
										$tempDesc = '';
										foreach($get_icd_detail as $show_icd) {												
											$icd_det = App\Models\Icd::getIcdCodeAndDesc($show_icd); 
											$icd_shot_desc = isset($icd_det['icd_code']) ? $icd_det['icd_code'] : App\Models\Icd::icd_shot_desc($show_icd);
											$icd_code = isset($icd_det['short_description']) ? $icd_det['short_description'] : App\Models\Icd::icd_code($show_icd);										
											$tempDesc .= @$icd_code .' - '.@$icd_shot_desc. "<br>";
										}
										$detailsRow[] = $tempDesc;										
										$detailsRow[] = '';										
										if($psettings->displaypayment == 'Payments') {
											$detailsRow[] = '';
										}
										if($psettings->displaypayment == 'InsPatient') {
											$detailsRow[] = '';
											$detailsRow[] = '';
										}
										$detailsRow[] = '';
										$detailsRow[] = '';
										$detailsBlk[] = $detailsRow;
									}
								} else {
									 /* Transcation wise show the details */  
									$totalclaim = count((array)$claim['pmt_trans']); 
									$claimcount = 1;
									$cpt_transaction = $cptTx;  //$claim['cpt_transaction'];
									
									/* List line item wise */
									foreach(@$cpt_transaction as $cptKey => $show_cpt) { 
										$tcpt_id = @$show_cpt->id; 			
										$tClaim_id = @$show_cpt->claim_id; 
										$tcpt_code = @$show_cpt->cpt_code;
										$cptFins = @$show_cpt->claimCptFinDetails;										
										$cpt_cfin_ipmt = App\Http\Helpers\Helpers::priceFormat(@$cptFins->insurance_paid);
										$cpt_cfin_ppmt = App\Http\Helpers\Helpers::priceFormat(@$cptFins->patient_paid);
										$cpt_cfin_pmt = App\Http\Helpers\Helpers::priceFormat(@$cptFins->patient_paid+@$cptFins->insurance_paid);
										$cpt_cfin_adj = App\Http\Helpers\Helpers::priceFormat(@$cptFins->patient_adjusted + @$cptFins->insurance_adjusted+ @$cptFins->with_held);										
										$cpt_cfin_bal = App\Http\Helpers\Helpers::priceFormat(@$cptFins->cpt_charge - (@$cptFins->patient_paid + @$cptFins->insurance_paid + @$cptFins->with_held+ @$cptFins->patient_adjusted + @$cptFins->insurance_adjusted));
										$lastCptCoinsDet = App\Models\Payments\ClaimInfoV1::getClaimLastCopayDetails($tClaim_id, $tcpt_id);
																				
										$detailsRow = [];
																	
										if($cptKey == 0) {
											$detailsRow[] = App\Http\Helpers\Helpers::dateFormat(@$claim['txn_details']['claim_info']->date_of_service,'date');
										} else {
											$detailsRow[] = '';
										}
										
										if(@$psettings->displaypayment == 'Payments') {
											$detailsRow[] = App\Http\Helpers\Helpers::dateFormat(@$claim['txn_details']['claim_info']->created_at,'date');
										}
																				
										$cpt_desc = App\Models\Cpt::cpt_shot_desc(@$tcpt_code);
										$cpt_des = strtoupper(@$tcpt_code).' - '.strtoupper(@$cpt_desc);
										
										$tempDesc =  @$cpt_des;
										if($lastCptCoinsDet != '') {
											$tempDesc .= $lastCptCoinsDet;
										}
										$detailsRow[] = $tempDesc;

										$detailsRow[] = @$show_cpt->charge;

										if($psettings->displaypayment == 'Payments') {
											$detailsRow[] = $cpt_cfin_pmt;
										}
										if($psettings->displaypayment == 'InsPatient') {
											$detailsRow[] =  @$cpt_cfin_ipmt;
											$detailsRow[] =  @$cpt_cfin_ppmt;
										}
										$detailsRow[] = @$cpt_cfin_adj;
											  
										$get_totalbalanceclaim = ($totalpayment == 0)? @$balance_amt : '' ;
										$get_totalbalanceclaim = ($totalclaim == $claimcount)? $get_totalbalanceclaim :'';
									
										$detailsRow[] = @$cpt_cfin_bal." ".$get_totalbalanceclaim;
									
										$detailsBlk[] = $detailsRow;

										/* List ICD detail with description in Line item vise */
										if($psettings->primary_dx == '1'){
											$get_icd_detail = explode(',',$claim['txn_details']['claim_info']->icd_codes);

											if(count((array)$get_icd_detail) > 1){
												$detailsRow = [];
												$detailsRow[] =  App\Http\Helpers\Helpers::dateFormat($claim_detail->date_of_service,'date');
												if($psettings->displaypayment == 'Payments'){
													$detailsRow[] = App\Http\Helpers\Helpers::dateFormat($claim_detail->created_at,'date');
												}
												$tempDesc = '';
												foreach($get_icd_detail as $show_icd) {													
													$icd_shot_desc = App\Models\Icd::icd_shot_desc($show_icd);
													$icd_code = App\Models\Icd::icd_code($show_icd);
													$tempDesc .= @$icd_code .' - '.trim(@$icd_shot_desc)."<br>";
												}
												$detailsRow[] = $tempDesc;

												$detailsRow[]  = '';
												if($psettings->displaypayment == 'Payments') {
													$detailsRow[]  = '';
												}

												if($psettings->displaypayment == 'InsPatient'){
													$detailsRow[]  = '';
													$detailsRow[]  = '';
												}
												$detailsRow[]  = '';
												$detailsRow[]  = '';
												$detailsBlk[] = $detailsRow;
											}
										}
										$claimcount++;
									}
								}		
							}
						}					
					}				
				}					
			}	// Details Ends 
			
			// Patient budget pland set then show it in description.
			if(isset($patients->patient_budget) && isset($patBudget['budgetTotalAmount']) ) {
				$detailsBlk[] = ['', '', 'Patient Balance: '.App\Http\Helpers\Helpers::priceFormat(@$patient_balance,'yes')];				
				$patBudTotalBal = App\Http\Helpers\Helpers::priceFormat($patBudget['budgetTotalAmount']);
				$detailsBlk[] = ['', '', 'Budget Amount: $'.App\Http\Helpers\Helpers::priceFormat(@$patBudTotalBal)];
			}
		?>		
		<statement>
		  <field name="Color" value="Red"></field>
		  <field name="CC" value="{{$CC}}"></field>
		  <field name="CCSecCode" value="{{$CCSecCode}}"></field>
		  <field name="TableID" value="{{ $TableID}}"></field>
		  <field name="EmailAddress" value="{{$patEmail}}"></field>		  
		  <field name="RecName" value="{{$RecName}}"></field>
		  <field name="RecAddress1" value="{{$RecAddress1}}"></field>
		  <field name="RecAddress2" value="{{ $RecAddress2 }}"></field>
		  <field name="RecCity" value="{{ $RecCity }}"></field>
		  <field name="RecST" value="{{ $RecST }}"></field>
		  <field name="RecZip" value="{{ $RecZip}}"></field>		  
		  <field name="SvcMsg">
			<field value="{{ $SvcMsg1 }}"></field>
			<field value="{{ $SvcMsg2 }}"></field>
			<field value="{{ $SvcMsg3 }}"></field>
		  </field>		  
		  <field name="RetName" value="{{$RetName}}"></field>
		  <field name="RetAddress1" value="{{$RetAddress1}}"></field>
		  <field name="RetAddress2" value="{{$RetAddress2}}"></field>
		  <field name="RetCity" value="{{$RetCity}}"></field>
		  <field name="RetST" value="{{$RetST}}"></field>
		  <field name="RetZip" value="{{$RetZip}}"></field>	
		  <field name="AcctNoNM" value="{{$AcctNoNM}}"></field>
		  <field name="AcctNo" value="{{ $AcctNo}}"></field>
		  <field name="PayBoxNM1" value="{{ $PayBoxNM1 }}"></field>
		  <field name="PayBox1" value="{{ $PayBox1 }}"></field>		  		  
		  <field name="PayBoxNM2" value="{{ $PayBoxNM2 }}"></field>
		  <field name="PayBox2" value="{{ $PayBox2 }}"></field>		  
		  <field name="PayBoxNM3" value="{{ $PayBoxNM3 }}"></field>
		  <field name="PayBox3" value="{{ strip_tags($PayBox3) }}"></field>		  
		  <field name="RemName" value="{{$RemName}}"></field>
		  <field name="RemAddress1" value="{{ $RemAddress1 }}"></field>
		  <field name="RemAddress2" value="{{ $RemAddress2 }}"></field>
		  <field name="RemCity" value="{{ $RemCity }}"></field>
		  <field name="RemST" value="{{ $RemST }}"></field>
		  <field name="RemZip" value="{{ $RemZip }}"></field>		  
		  <field name="RecName2" value=" {{ $RecName2 }}"></field>
		  <field name="RemCheckDigit" value=" {{ $RemCheckDigit }}"></field>
		  <field name="TopMsg" value=" {{ $TopMsg }}"></field>
		  <field name="DetailBarNM" value="{{ $DetailBarNM }}"></field>
		  <field name="DetailBar1" value="{{ $DetailBar1 }}"></field>
		  <field name="DetailBar2" value="{{ $DetailBar2 }}"></field>
		  <field name="DetailNM" value="{{ @$DetailNM }}"></field>
		  <field name="DetailNMx" value="{{ $DetailNMx }}"></field>
		  
		  <field name="DetailNM">		  
			@foreach($DetailNMH as $det_name)
			<field value="{{ @$det_name }}"></field>
			@endforeach
			@if(COUNT((array)$DetailNMH)<12)
			@for($i=COUNT((array)$DetailNMH); $i<12; $i++ )	
			<field value=""></field>
			@endfor 	
			@endif
		  </field>
		  
		  <field name="UseAcctSummary" value="{{ $UseAcctSummary }}"></field>
		  <field name="SumNMa" value="{{ $SumNMa }}"></field>
		  <field name="SumNMb" value="{{ $SumNMb }}"></field>
		  <field name="SumNM1a" value="{{ $SumNM1a }}"></field>
		  <field name="SumNM1b" value="{{ $SumNM1b }}"></field>
		  <field name="Sum1" value="{{ $Sum1 }}"></field>
		  <field name="SumNM2a" value="{{ $SumNM2a }}"></field>
		  <field name="SumNM2b" value="{{ $SumNM2b }}"></field>
		  <field name="Sum2" value="{{ $Sum2 }}"></field>
		  <field name="SumNM3a" value="{{ $SumNM3a }}"></field>
		  <field name="SumNM3b" value="{{ $SumNM3b }}"></field>
		  <field name="Sum3" value="{{ $Sum3 }}"></field>
		  <field name="SumNM4a" value="{{ $SumNM4a }}"></field>
		  <field name="SumNM4b" value="{{ $SumNM4b }}"></field>
		  <field name="Sum4" value="{{ $Sum4 }}"></field>
		  <field name="SumNM5a" value="{{ $SumNM5a }}"></field>
		  <field name="SumNM5b" value="{{ $SumNM5b }}"></field>
		  <field name="Sum5" value="{{ $Sum5 }}"></field>
		  <field name="DueNM1a" value="{{ $DueNM1a }}"></field>
		  <field name="DueNM1b" value="{{ $DueNM1b }}"></field>
		  <field name="Due1" value="{{ strip_tags($Due1) }}"></field>
		  <field name="DueNM2a" value="{{ $DueNM2a }}"></field>
		  <field name="DueNM2b" value="{{ $DueNM2b }}"></field>
		  <field name="Due2" value="{{ strip_tags($Due2) }}"></field>
		  
		  <field name="UseAging" value="{{ $UseAging }} "></field>
		  <field name="AgeNM" value="{{ $AgeNM }}"></field>
		  <field name="AgeNM1" value="{{ $AgeNM1 }}"></field>		  
		  <field name="AgeNM2" value="{{ $AgeNM2 }}"></field>		  
		  <field name="AgeNM3" value="{{ $AgeNM3 }}"></field>		  
		  <field name="AgeNM4" value="{{ $AgeNM4 }}"></field>
		  <field name="AgeNM5" value="{{ $AgeNM5 }}"></field>		  
		  
		  @foreach($ageBk as $akey => $agedata)
		  <field name="Age{{$akey}}" value="{{ strip_tags($agedata) }}"></field>
		  @endforeach	
		  
		  <field name="Msg">
			<field value="{{ $Msg1 }}"></field>
			<field value="{{ $Msg2 }}"></field>
			<field value="{{ $Msg3 }}"></field>
			<field value="{{ $Msg4 }}"></field>
			<field value="{{ $Msg5 }}"></field>
			<field value="{{ $Msg6 }}"></field>
			<field value="{{ $Msg7 }}"></field>
			<field value="{{ $Msg8 }}"></field>
			<field value="{{ $Msg9 }}"></field>
			<field value="{{ $Msg10 }}"></field>
		  </field>
		<details>			
		@foreach($detailsBlk as $det_row)
		<detail>
			@foreach($det_row as $data)
			<field value="{{ strip_tags($data) }}"></field>
			@endforeach
			@if(COUNT((array)$det_row)<13)
			@for($i=COUNT((array)$det_row); $i<13;$i++ )	
			<field value=""></field>
			@endfor 	
			@endif
		</detail>
		@endforeach			
		  </details>
		</statement>
	@endif	
	</statements>
</billflashdoc>
<?php 
/*} catch(Exception $e){
	$trace = $e->getTrace();
    $eMSg = $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine().' called from '.@$trace[0]['file'].' on line '.@$trace[0]['line'];
	\Log::info($eMSg);
}
*/
?>