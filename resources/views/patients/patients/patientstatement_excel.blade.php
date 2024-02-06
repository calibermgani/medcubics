<?php
try { 
?>
<div style="margin-top:-30px; margin-right:-20px;">
    <?php /* Preview option set in */ ?>    
    @if($mode == 'preview') 
        <header>Preview</header>         
    @endif   
    <div  style="color:#00877f; font-size:16px;font-family:sans-serif; font-weight:600; border-bottom: 3px solid #a7e9e1;  margin-left:-25px; background: #e4f8f6; padding:5px 5px 5px 4px; margin-bottom:20px;">
        STATEMENT 
	</div>
    <div style="position:absolute; margin-left:300px; ">
        <table style="border-collapse: collapse; width: 100%;">
            <tr style="background: #eafbf9">
                <td colspan="4" style="border-color: #a7e9e1 !important;"><span style="color:#00877f;">By Check</span></td>
            </tr>
            <tr>
                <td colspan="2" style="padding-bottom:20px; border-color: #a7e9e1 !important;"><span>Check Payable To</span></td>        
                <td colspan="2" style="padding-bottom:20px; border-color: #a7e9e1 !important;"><span>Check No</span></td>       
            </tr>
            @if(($psettings->visa_card != 0) || ($psettings->mc_card != 0) || ($psettings->maestro_card != 0) || ($psettings->gift_card != 0)) 
                <tr>
                    <td colspan="4" style="border-color: #a7e9e1 !important; background: #eafbf9;"><span style="color:#00877f; border-color: #a7e9e1 !important;">By Card</span></td>
                </tr>
                <tr>
                    <td style="width:30%; border-color: #a7e9e1 !important;"><span>Card Type</span></td>        
                    <td colspan="3" style="border-color: #a7e9e1 !important;" style="padding:0;">
                        @if($psettings->visa_card != 0)
							<span style="margin-left:-5px;"><input type="text" style="width:10px; height:10px; background: #fff; border-color:#ccc; margin-left:5px;">VISA</span> 
                        @endif
                        @if($psettings->mc_card != 0)
							<span style="margin-left:5px;"><input type="text" style="width:10px; height:10px; background: #fff; border-color:#ccc; margin-left:5px;">MasterCard</span> 
                        @endif  
                        @if($psettings->maestro_card != 0) 
							<span style="margin-left:5px;"><input type="text" style="width:10px; height:10px; background: #fff; border-color:#ccc; margin-left:5px;">MaestroCard</span> 
                        @endif  
                        @if($psettings->gift_card != 0) 
							<span style="margin-left:5px;"><input type="text" style="width:10px; height:10px; background: #fff; border-color:#ccc; margin-left:5px;">GiftCard</span>       
                        @endif  
                    </td>     
                </tr>
                <tr>
                    <td colspan="2" style="padding-bottom:20px; border-color: #a7e9e1 !important;"><span>Card No</span></td> 
                    <td style="padding-bottom:20px; border-color: #a7e9e1 !important;"><span>Exp Date</span></td>  
                    <td style="padding-bottom:20px; border-color: #a7e9e1 !important;"><span>Signature</span></td>       
                </tr>
            @endif            

            <tr style="background:#d7fef9">
                <td style="padding-bottom:20px; text-align: center;border-color: #a7e9e1 !important;">
					<span style="color:#00877f">Statement Date</span>
                    <div style="position:absolute; @if(($psettings->visa_card != 0) || ($psettings->mc_card != 0) || ($psettings->maestro_card != 0) || ($psettings->gift_card != 0)) margin-left: -55px; @else margin-left:-40px; @endif margin-top:16px;">
                        @if($mode == 'preview') 
                        {{ '####' }}
                        @else
                        {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}
                        @endif
                    </div>
                </td> 
                
                <td style="padding-bottom:20px; text-align: center;border-color: #a7e9e1 !important;">
					<span style="color:#00877f;">Pay by Date</span>
					<?php 
						$today = App\Http\Helpers\Helpers::timezone(date('m/d/y H:i:s'),'Y-m-d');
						$paybydate = date('Y-m-d',strtotime($today . ' +'.@$psettings->paybydate.' day')); 
					?>
                    <div style="position:absolute; @if(($psettings->visa_card != 0) || ($psettings->mc_card != 0) || ($psettings->maestro_card != 0) || ($psettings->gift_card != 0)) margin-left: -51px; @else margin-left:-38px; @endif margin-top:16px;">
                        @if($mode == 'preview') 
                        {{ '####' }}
                        @else
                        {{ App\Http\Helpers\Helpers::dateFormat($paybydate,'date') }}
                        @endif
                    </div>
                </td>  
                <td colspan="2" style="padding-bottom:20px;text-align: center;border-color: #a7e9e1 !important;">
					<span style="color:#00877f;">Payment Due</span>
                    <div style="position:absolute;@if(($psettings->visa_card != 0) || ($psettings->mc_card != 0) || ($psettings->maestro_card != 0) || ($psettings->gift_card != 0)) margin-left: -2px; @else margin-left:-34px; @endif  margin-top:16px;">
                        {!! (count((array)$patients->patient_budget)>0)? ($get_budgetamount->budget_balance == 0)? App\Http\Helpers\Helpers::priceFormat($get_budgetamount->budget_amt,'yes'): App\Http\Helpers\Helpers::priceFormat($get_budgetamount->budget_balance,'yes') : App\Http\Helpers\Helpers::priceFormat($patient_balance,'yes') !!} 
                    </div>
                </td>       
            </tr>
        </table>
    </div>
<?php /* Claim count check greaterthan Zero  Start */	?>
    @if(count(@$claims)>0)          
        <?php 
            $get_last_result = count((array)$claims)-1; 
            $patient_zip4 = (@$patients->zip4!='')?'-'.@$patients->zip4:'';  
			$patient_other_zip4 = (@$patient_other_address->zip4!='')?'-'.@$patient_other_address->zip4:'';  
            $guarantor_zip4 = (count((array)$patient_guarantor_address)>0 && @$patient_guarantor_address->guarantor_zip4!='')?'-'.@$patient_guarantor_address->guarantor_zip4:'';
            $check_zip4  =  (@$psettings->check_zip4!='')?'-'.@$psettings->check_zip4:'';  
            $practice_zip4   =  (@$practice->pay_zip4!='')?'-'.@$practice->pay_zip4:'';  
            $facility_zip4   =  (@$claims[$get_last_result]->facility_detail->facility_address->pay_zip4!='')?'-'.@$claims[$get_last_result]->facility_detail->facility_address->pay_zip4:'';  
            $totalbalance = [];//@$insurance_balance + @$patient_balance;  
            $insurance_bal = $patient_bal = '';			
			$patient_age = date_diff(date_create(@$patients->dob), date_create('today'))->y;
			$guarantor_name = (count((array)$patient_guarantor_address)>0) ? App\Http\Helpers\Helpers::getNameformat($patient_guarantor_address->guarantor_last_name,$patient_guarantor_address->guarantor_first_name,$patient_guarantor_address->guarantor_middle_name) : "";
			$pat_name = (($patients->title) ? @$patients->title."." : ""). App\Http\Helpers\Helpers::getNameformat($patients->last_name,$patients->first_name,$patients->middle_name);
        ?>  
		<div style="margin-left:-20px">		<?php /* Facility Details show here */?>
			<div>    
				<div style="width:40%;">

					<div style="color:#00877f; font-size:12px;font-family:sans-serif;">

						<p class="font600" style="color:#868686; margin-top: 6px; margin-bottom:5px; font-size:11px;font-family:sans-serif;">
							<b>{{ (@$psettings->servicelocation=='Facility')? strtoupper(@$claims[$get_last_result]->facility_detail->facility_name) : strtoupper(@$practice->practice_name)  }}</b>
						</p>
						<p style="color:#868686; margin-top: 6px; margin-bottom:5px; font-size:11px;font-family:sans-serif;">
							{{ (@$psettings->servicelocation=='Facility')?  strtoupper(@$claims[$get_last_result]->facility_detail->facility_address->address1.','.@$claims[$get_last_result]->facility_detail->facility_address->address2) : strtoupper(@$practice->pay_add_1.','.@$practice->pay_add_2) }}
						</p>
						<p style="color:#868686; margin-top: 0px; font-size:11px;font-family:sans-serif;">
							{{ ($psettings->servicelocation=='Facility')?  strtoupper(@$claims[$get_last_result]->facility_detail->facility_address->city.', '.@$claims[$get_last_result]->facility_detail->facility_address->state.' '.@$claims[$get_last_result]->facility_detail->facility_address->pay_zip5.$facility_zip4) : strtoupper(@$practice->pay_city.', '.@$practice->pay_state.' '.@$practice->pay_zip5.$practice_zip4) }}  
						</p>
					</div>
					
					<div style="color:#00877f; font-size:11px;font-family:sans-serif;">
						For Billing Inquiries Please Call  
						<p style="color:#868686; margin-top: 6px; margin-bottom:5px; font-size:11px;font-family:sans-serif;">{{ @$psettings->callbackphone }}</p> with your Account Number         
					</div>
					
					<div style="color:#00877f; font-size:12px;font-family:sans-serif; margin-top:20px;">
						Patient Address
						<?php $tempAddr =0; ?>
						<p style="margin-top: 0px; margin-bottom:5px; font-size:11px;font-family:sans-serif; color:#f07d08 !important; font-weight:600;"><span style="color:#868686">
						@if($patient_age > 18) 
							{{ strtoupper($pat_name) }} 
						@else 
							@if($guarantor_name != '') {{ strtoupper($guarantor_name) }} @else {{ strtoupper($pat_name) }} @endif
						@endif
						</span> </p>		
						
						<?php
							$addr1 = $addr2 = $fullAddr = '';
							if(count((array)$patient_other_address)>0 && $patient_other_address->address1 !='') {
								$addr1 = strtoupper(@$patient_other_address->address1);
								$addr2 = strtoupper(@$patient_other_address->address2);
								$fullAddr = strtoupper(@$patient_other_address->city.', '.@$patient_other_address->state.' '.@$patient_other_address->zip5.$patient_other_zip4);
							} else {
								if($patient_age < 18 && count((array)$patient_guarantor_address)>0 && $patient_guarantor_address->guarantor_address1!='') {
									$addr1 = strtoupper(@$patient_guarantor_address->guarantor_address1);
									$addr2 = strtoupper(@$patient_guarantor_address->guarantor_address2);
									$fullAddr = strtoupper(@$patient_guarantor_address->guarantor_city.', '.@$patient_guarantor_address->guarantor_state.' '. @$patient_guarantor_address->guarantor_zip5.$guarantor_zip4);
								} else {
									$addr1 = strtoupper(@$patients->address1);
									$addr2 = strtoupper(@$patients->address2);
									$fullAddr = strtoupper(@$patients->city.', '.@$patients->state.' '.@$patients->zip5.$patient_zip4);
								}
							}							
						?>
						<p style="color:#868686; margin-top: 6px; margin-bottom:5px; font-size:11px;font-family:sans-serif;">{{ $addr1 }}</p>		
						
						@if( (count((array)$patient_guarantor_address)>0 && (trim($patient_guarantor_address->guarantor_address2)!='' )) || trim($patients->address2) !='' || (isset($patient_other_address->address2) && trim($patient_other_address->address2) !=''))
							<p style="color:#868686; margin-top: 6px; margin-bottom:5px; font-size:11px;font-family:sans-serif;">
								{{ $addr2 }}
							</p>
						@else
							<?php $tempAddr =1; ?>	
						@endif
						
						<p style="color:#868686; margin-top: 0px; font-size:11px;font-family:sans-serif;">
							{{ $fullAddr }}
						</p>
						
						@if($tempAddr >0)
							<p style="color:#868686; margin-top: 0px; font-size:11px;font-family:sans-serif;">&nbsp; </p>
						@endif
					</div>
				</div>    
			</div>
			<?php 
				/* Facility Details show End*/
				/* Patient Details show here */
			?>
			<table class="table table-striped-view tb-green border-none" style="background:#eafbf9; margin-top:-2px; border-collapse: collapse;">   
				<tr style="margin-top:-30px !important;">        
					<td>
						<div style="font-size:11px;font-family:sans-serif;margin-top:3px;" class="med-green">
							Patient Name : <span style="color:#868686">{{ strtoupper($pat_name) }} </span> 
						</div>
						<div style="margin-top:6px; font-size:11px;font-family:sans-serif;" class="med-green">
							Acc No : <span style="color:#868686">{{ $patients->account_no }}</span>
						</div>
						@if($psettings->rendering_provider == 1)   
						<div style="margin-top:6px; font-size:11px;font-family:sans-serif;" class="med-green">Rendering Provider : 
							<span style="color:#868686"> {{ strtoupper(@$claims[$get_last_result]->rendering_provider->provider_name) }} {{ @$claims[$get_last_result]->rendering_provider->degrees->degree_name }}</span>
						</div>
						@endif
					</td>
					<td style="border-left:1px solid #00877f !important; ">
						<div style="font-size:11px;font-family:sans-serif;margin-top:3px;" class="med-green">Mailing Address </div>
						<div style="margin-top:6px; font-size:11px;font-family:sans-serif;"><span style="color:#868686">{{ ($psettings->check_add_1!='')? strtoupper(@$psettings->check_add_1.', '.@$psettings->check_add_2) : '' }}</span></div>
						<div style="margin-top:6px; font-size:11px;font-family:sans-serif;" class="med-green">
							<span style="color:#868686">{{ ($psettings->check_add_1!='')?strtoupper(@$psettings->check_city.', '.@$psettings->check_state.' '.@$psettings->check_zip5.$check_zip4): '' }} </span>
						</div>
					</td>       
				</tr>
				<tr>        
					<td></td>
					<td></td>       
				</tr>
			</table>
			<?php 
				/* Patient details end here */
				/* Payment transaction details show here */
			?>
			<p style="color:#646464; border-bottom:1px dashed #ccc; padding-bottom:4px; font-size:8px;font-family:sans-serif; margin-top:0px;"><span style="background:#fff; color:#fff; padding:1px;border:1px solid #ccc;"></span> Check if your billing information has changed. Provide update(s) above or on reverse side <span style="margin-left:200px;">Please detach and return top portion with payment</span></p>
			<div class="statement">
				<table class="table table-striped-view" style="border-collapse: collapse; width:100%;">   
					<tr>        
						<th class="bg-green">Visit Date</th>
						<!-- display only payment or patient and insurance payments -->
						@if($psettings->displaypayment == 'Payments')
						<th class="bg-green">Activity Date</th>
						@endif
						<th class="bg-green" style="width:30%;">Description of Service</th>
						<th class="bg-green">Charges($)</th>
						@if($psettings->displaypayment == 'Payments')
						<th class="bg-green">Payments($)</th>
						@endif

						@if($psettings->displaypayment == 'InsPatient')
						<th class="bg-green">Insurance Payments($)</th>
						<th class="bg-green">Patient Payments($)</th>
						@endif

						<th class="bg-green">Adjustments($)</th>
						<th class="bg-green">Balance($)</th>
					</tr>
					<!-- Claim wise LOOP Start -->
				@foreach($claims as $claim_detail)
				<?php  					
					$cptTx = $claim_detail->cpttransactiondetails;
					$claim_details = App\Models\Payments\ClaimInfoV1::getClaimTransactionList($claim_detail->id);            
					$claim_count = 1; 
					if(!empty($claim_details)) {
					$idx = 0;	
				?>				
				@foreach($claim_details as $claim)
					<?php  
						$totalbalance = str_replace(",", "",strip_tags($claim['ins_balance']))+str_replace(",", "",strip_tags($claim['pat_balance']));  //$claim['total_balance'];		
						$insurance_bal = $claim['ins_balance'];
						$patient_bal = $claim['pat_balance'];                
						/* Claim status check  */						
						$claim_fin_ipmt = App\Http\Helpers\Helpers::priceFormat($claim['pmt_fins']['insurance_paid']);
						$claim_fin_ppmt = App\Http\Helpers\Helpers::priceFormat($claim['pmt_fins']['patient_paid']);
						$claim_fin_pmt = App\Http\Helpers\Helpers::priceFormat($claim['pmt_fins']['patient_paid']+$claim['pmt_fins']['insurance_paid']);
						$claim_fin_adj = App\Http\Helpers\Helpers::priceFormat($claim['pmt_fins']['patient_adj'] + $claim['pmt_fins']['insurance_adj']+$claim['pmt_fins']['withheld']);						
						$claim_fin_bal = App\Http\Helpers\Helpers::priceFormat($claim['pmt_fins']['total_charge'] - ($claim['pmt_fins']['patient_paid']+$claim['pmt_fins']['insurance_paid'] + $claim['pmt_fins']['patient_adj'] + $claim['pmt_fins']['insurance_adj']+$claim['pmt_fins']['withheld']));   
					?>
					<!-- Claim denied status also included in the list -->
					@if($claim['txn_details']['claim_info']->status == 'Denied' && $claim['txn_details']['claim_info']->deleted_at == "1" )
						<tr>  
							<td></td>
							@if(@$psettings->displaypayment == 'Payments')
							<td></td>
							@endif
							<td>{{ $claim['txn_details']['claim_info']->status }}</td>
							<td class="right-text"></td>
							@if(@$psettings->displaypayment == 'Payments')
							<td class="right-text" data-format="0.00">0.00</td>
							@endif

							@if($psettings->displaypayment == 'InsPatient')
							<td class="right-text" data-format="0.00">0.00</td>
							<td class="right-text"></td>
							@endif
							<td class="right-text"></td>
							<td class="right-text"></td>
						</tr>
					@else
						<?php $balance_amt = App\Http\Helpers\Helpers::priceFormat($totalbalance) ;		/* Claim Balance amount */?>
						<!-- check all insurance & patient claim will show or not -->											
						
						@if(@$balance_amt != '0.00' && @$psettings->insserviceline==0 && @$psettings->patserviceline==0 || @$psettings->insserviceline==1 || @$psettings->patserviceline==1)				
						
							<?php
								$totalpayment = count((array)$claim['pmt_trans']); 
								$paycount = 1;
							?>
							<!-- List claim vise -->
							@if(@$psettings->cpt_shortdesc == 'Claim')
								<?php
									$lastCoinsDet = App\Models\Payments\ClaimInfoV1::getClaimLastCopayDetails($claim['txn_details']['claim_info']->id);
								?>
								<tr>   
									<td>
										@if($idx == 0)
										{{ App\Http\Helpers\Helpers::dateFormat(@$claim['txn_details']['claim_info']->date_of_service,'date') }}
										@endif									
									</td>
									@if(@$psettings->displaypayment == 'Payments')
									<td>{{ App\Http\Helpers\Helpers::dateFormat(@$claim['txn_details']['claim_info']->created_at,'date') }}</td>
									@endif
									<td> 
										<?php /* CPT Transaction Showed here */ 
											$descTxt = ''; 
										?>
										@foreach(@$claim['cpt_transaction'] as $show_cpt)
											<?php 
												$cpt_desc = App\Models\Cpt::cpt_shot_desc($show_cpt['cpt_code']); 
												$descTxt = strtoupper($show_cpt['cpt_code']).($cpt_desc != '')?'-'.strtoupper($cpt_desc):'';
											?>												
										@endforeach
										@if($lastCoinsDet != "") 
											<?php $descTxt .= " (".$lastCoinsDet.")"; ?>
										@endif
										<?php 
											$descTxt = str_limit($descTxt, 55, '...'); 
											$descTxt = str_replace("-"," ", str_replace("=","",@$descTxt));
										?>												
										{{ $descTxt }}											
										
									</td>
									<td class="right-text" data-format="0.00">{{ @$claim['txn_details']['claim_info']->total_charge }}</td>
									@if($psettings->displaypayment == 'Payments')
									<td class="right-text" data-format="0.00">{{ @$claim_fin_pmt }}</td>
									@endif

									@if($psettings->displaypayment == 'InsPatient')
									<td class="right-text" data-format="0.00">{{ @$claim_fin_ipmt }}</td>
									<td class="right-text" data-format="0.00">{!! @$claim_fin_ppmt !!}</td>
									@endif
									<td class="right-text" data-format="0.00">{{ @$claim_fin_adj }}</td>
									<td class="right-text" data-format="0.00">
										{!! $claim_fin_bal !!}
									</td>
								</tr>
								<!-- List ICD detail with description in claim vise -->
								@if(@$psettings->primary_dx == '1')
									<?php /* Split ICD code useing comma(,) based */
									$get_icd_detail = explode(',',$claim['txn_details']['claim_info']->icd_codes); ?>
									<tr>   
										<td>{{ App\Http\Helpers\Helpers::dateFormat($claim['txn_details']['claim_info']->date_of_service,'date') }}</td>
										@if(@$psettings->displaypayment == 'Payments')
											<td>{{ App\Http\Helpers\Helpers::dateFormat($claim['txn_details']['claim_info']->created_at,'date') }}</td>
										@endif
										<td> 
											@foreach($get_icd_detail as $show_icd) 
												<?php 													
													$icd_det = App\Models\Icd::getIcdCodeAndDesc($show_icd); $show_icd;
													$icd_shot_desc = isset($icd_det['icd_code']) ? $icd_det['icd_code'] : App\Models\Icd::icd_shot_desc($show_icd);
													$icd_code = isset($icd_det['short_description']) ? $icd_det['short_description'] : App\Models\Icd::icd_code($show_icd);
													$icd_code = isset($icd_code) ? str_replace("-", " ",str_replace("=","",@$icd_code)) : '';
												?>
												{{ @$icd_code .' - '.@$icd_shot_desc}}<br>
											@endforeach
										</td>
										<td class="right-text"></td>
										@if($psettings->displaypayment == 'Payments')
											<td class="right-text"></td>
										@endif

										@if($psettings->displaypayment == 'InsPatient')
											<td class="right-text"></td>
											<td class="right-text"></td>
										@endif
										<td class="right-text"></td>
										<td class="right-text"></td>
									</tr>
								@endif
							@else
								<?php /* Transcation wise show the details */  
									$totalclaim = count((array)$claim['pmt_trans']); 
									$claimcount = 1;
									$cpt_transaction = $cptTx; 
								?>   
								<!-- List line item wise -->
								@foreach(@$cpt_transaction as $cptKey => $show_cpt)  
									<?php
										$tcpt_id = @$show_cpt->id; 			
										$tClaim_id = @$show_cpt->claim_id; 
										$tcpt_code = @$show_cpt->cpt_code;
										$cptFins = @$show_cpt->claimCptFinDetails;										
										$cpt_cfin_ipmt = App\Http\Helpers\Helpers::priceFormat($cptFins->insurance_paid);
										$cpt_cfin_ppmt = App\Http\Helpers\Helpers::priceFormat($cptFins->patient_paid);
										$cpt_cfin_pmt = App\Http\Helpers\Helpers::priceFormat($cptFins->patient_paid+$cptFins->insurance_paid);
										$cpt_cfin_adj = App\Http\Helpers\Helpers::priceFormat($cptFins->patient_adjusted + $cptFins->insurance_adjusted+$cptFins->with_held);										
										$cpt_cfin_bal = App\Http\Helpers\Helpers::priceFormat($cptFins->cpt_charge - ($cptFins->patient_paid + $cptFins->insurance_paid + $cptFins->with_held+ $cptFins->patient_adjusted + $cptFins->insurance_adjusted));
										$lastCptCoinsDet = App\Models\Payments\ClaimInfoV1::getClaimLastCopayDetails($tClaim_id, $tcpt_id);
									?>																		
									<tr>
										<td>	
											@if($cptKey == 0) 
												{{ App\Http\Helpers\Helpers::dateFormat(@$claim['txn_details']['claim_info']->date_of_service,'date') }}
											@endif
										</td>
										@if(@$psettings->displaypayment == 'Payments')
											<td>{{ App\Http\Helpers\Helpers::dateFormat(@$claim['txn_details']['claim_info']->created_at,'date') }}</td>
										@endif
										<td> 
											<?php											
												$cpt_desc = App\Models\Cpt::cpt_shot_desc(@$tcpt_code);
												$cpt_des = strtoupper($tcpt_code).'-'.strtoupper(@$cpt_desc);
												$cpt_des .= ($lastCptCoinsDet != '') ? " (".$lastCptCoinsDet.")" : '';
												$cpt_des = str_limit($cpt_des, 55, '...');
												$cpt_des = str_replace("=","",@cpt_des);
											?>
											{{ @$cpt_des }}												
										</td>
										<td class="right-text">{{@$show_cpt->charge}}</td>
										@if($psettings->displaypayment == 'Payments')
										<td class="right-text">{{ $cpt_cfin_pmt }}</td>
										@endif

										@if($psettings->displaypayment == 'InsPatient')
										<td class="right-text" data-format="0.00">{{ @$cpt_cfin_ipmt }}</td>
										<td class="right-text" data-format="0.00">{!! @$cpt_cfin_ppmt !!}</td>
										@endif
										<td class="right-text" data-format="0.00">{{ @$cpt_cfin_adj }}</td>
										<td class="right-text">
											{!! @$cpt_cfin_bal !!}
											<?php  
												$get_totalbalanceclaim = ($totalpayment == 0)? @$balance_amt : '' ;
												$get_totalbalanceclaim = ($totalclaim == $claimcount)? $get_totalbalanceclaim :'';
											?>
											{{ $get_totalbalanceclaim }}
										</td>
									</tr>
									
									<!-- List ICD detail with description in Line item vise -->
									@if($psettings->primary_dx == '1')
										<?php $get_icd_detail = explode(',',$claim['txn_details']['claim_info']->icd_codes); ?>
										@if(count((array)$get_icd_detail) > 1)
										<tr>  
											<td>{{ App\Http\Helpers\Helpers::dateFormat($claim_detail->date_of_service,'date') }}</td>
											@if($psettings->displaypayment == 'Payments')
											<td>{{ App\Http\Helpers\Helpers::dateFormat($claim_detail->created_at,'date') }}</td>
											@endif
											<td> 
												@foreach($get_icd_detail as $show_icd) 
													<?php 
														$icd_shot_desc = App\Models\Icd::icd_shot_desc($show_icd);
														$icd_code = App\Models\Icd::icd_code($show_icd);
													?>
													{{ @$icd_code .' - '.trim(@$icd_shot_desc)}}
													<br>
												@endforeach  
											</td>
											<td class="right-text"> <?php //print_r(count($get_icd_detail));?></td>
											@if($psettings->displaypayment == 'Payments')
											<td class="right-text"></td>
											@endif

											@if($psettings->displaypayment == 'InsPatient')
											<td class="right-text"></td>
											<td class="right-text"></td>
											@endif
											<td class="right-text"></td>
											<td class="right-text"></td>
										</tr>
										@endif  
									@endif
								<?php $claimcount++ ?>
								@endforeach
							@endif							
						@endif
					@endif					
				@endforeach
				<?php
					}					
				?>
				@endforeach
					@if(isset($psettings->insurance_balance) && $psettings->insurance_balance == '1')
						<tr class="border-none bg-white"> 
							<td style="border:0px solid #ccc; background: #fff !important;"></td>
							@if($psettings->displaypayment == 'Payments')
							<td style="border:0px solid #ccc; background: #fff !important;"></td>
							@endif
							<td style="border:0px solid #ccc; background: #fff !important;"></td>
							<td style="border:0px solid #ccc; background: #fff !important;"></td>
							@if($psettings->displaypayment == 'Payments')
							<td style="border:0px solid #ccc; background: #fff !important;"></td>  
							@endif
							@if($psettings->displaypayment == 'InsPatient')
							<td style="border:0px solid #ccc; background: #fff !important;"></td> 
							<td style="border:0px solid #ccc; background: #fff !important;"></td> 
							@endif

							<td colspan="1" class="right-text" style="background:#fff;">Total</td>
							<td class="right-text" style="background: #fff !important;">
							<?php
								$patFin = App\Models\Patients\Patient::getPatientFinDetails(@$patients->id);
							?>
							{!! $patFin['total_ar'] !!}</td>
						</tr>
					
					
						<tr class="border-none bg-white">        
							<td  style="border:0px solid #ccc; background: #fff !important;"></td>
							@if($psettings->displaypayment == 'Payments')
								<td style="border:0px solid #ccc;  background: #fff !important;"></td>
							@endif
							<td style="border:0px solid #ccc; background: #fff !important;"></td>
							<td style="border:0px solid #ccc; background: #fff !important;"></td>
							@if($psettings->displaypayment == 'Payments')
								<td style="border:0px solid #ccc; background: #fff !important;"></td>
							@endif
							
							@if($psettings->displaypayment == 'InsPatient')
								<td style="border:0px solid #ccc; background: #fff !important;"></td> 
								<td style="border:0px solid #ccc; background: #fff !important;"></td> 
							@endif					
							
							<td colspan="1" class="right-text" style="background: #fff !important;">Insurance<br>Balance</td>
							<td class="right-text" style="background: #fff !important;" data-format="0.00">
								{!! App\Http\Helpers\Helpers::priceFormat($insurance_balance) !!}
							</td>						
						</tr>
					@endif	
					
					@if($psettings->financial_charge == '1')
						<tr class="border-none bg-white">        
							<td  style="border:0px solid #ccc; background: #fff !important;"></td>
							@if($psettings->displaypayment == 'Payments')
								 <td style="border:0px solid #ccc;  background: #fff !important;"></td>
							@endif
							<td style="border:0px solid #ccc; background: #fff !important;"></td>
							<td style="border:0px solid #ccc; background: #fff !important;"></td>
							@if($psettings->displaypayment == 'Payments')
								<td style="border:0px solid #ccc; background: #fff !important;"></td>
							@endif
							@if($psettings->displaypayment == 'InsPatient')
								<td style="border:0px solid #ccc; background: #fff !important;"></td> 
								<td style="border:0px solid #ccc; background: #fff !important;"></td> 
							@endif
							<td colspan="1" class="right-text" style="background: #fff !important;">Financial Charges</td>
							<td class="right-text" style="background: #fff !important;" data-format="0.00">{!! ($financial_charges!='0') ? App\Http\Helpers\Helpers::priceFormat($financial_charges) : '0.00' !!}</td>
						</tr>					
					   <?php $patient_balance = $financial_charges + $patient_bal; ?>
					@else
						<?php // $patient_balance = $patient_bal; ?>
					@endif 
					
					<tr class="border-none bg-white">        
						<td style="border:0px solid #ccc; background: #fff !important;"></td>
						@if($psettings->displaypayment == 'Payments')
						<td style="border:0px solid #ccc; background: #fff !important;"></td>
						@endif
						<td style="border:0px solid #ccc; background: #fff !important;"></td>
						<td style="border:0px solid #ccc; background: #fff !important;"></td>
						@if($psettings->displaypayment == 'Payments')
						<td style="border:0px solid #ccc; background: #fff !important;"></td>  
						@endif
						@if($psettings->displaypayment == 'InsPatient')
						<td style="border:0px solid #ccc; background: #fff !important;"></td> 
						<td style="border:0px solid #ccc; background: #fff !important;"></td> 
						@endif
						<td colspan="1" class="right-text bg-green" style="border:1px solid #ccc;">Patient Balance</td>
						<td class="right-text bg-green" data-format="0.00">{!! ($patient_balance!='')? App\Http\Helpers\Helpers::priceFormat($patient_balance) : '0.00' !!}</td>
					</tr>
					
				</table>	
			</div>
			@if(@$psettings->latestpaymentinfo == '1' && count((array)$patient_latestpayment)>0)			
				<div style="">
					<div style="font-family:sans-serif; color:#00877f; font-size: 12px; margin-top:-10px;">Last Statement</div>
					<table class="table table-striped-view tb-green border-none" style="background:#eaf6f5; margin-top:0px; margin-bottom:-40px; border-collapse: collapse;">   
						<tr style="margin-top:-30px !important;">        
							<td colspan="3"></td>
						</tr>
						<tr>        
							<td  class="no-padding" style="padding-top:0px;" data-format="0.00"><span class="med-green">Payment Date</span> : {{ App\Http\Helpers\Helpers::dateFormat(@$patient_latestpayment->created_at,'date') }}</td>
							<td  style="border-left:1px solid #00877f !important; padding-top:0px;" class="no-padding" data-format="0.00"><span class="med-green">Paid Amount </span> : {!! App\Http\Helpers\Helpers::priceFormat(@$patient_latestpayment->patient_paid,'yes') !!}</td>
							<td  style="border-left:1px solid #00877f !important; padding-top:0px;" class="no-padding" data-format="0.00"><span class="med-green">Payment Mode</span> : {{ @$patient_latestpayment->pmt_mode }}</td>
						</tr>
						<tr>     
							<td colspan="3"></td>
						</tr>
					</table>
				</div>
			@endif
			
		</div>
    @else
		<div style="margin-left:-20px">
		</div>
	@endif    
	
    <?php /*  Aging day calculation */?>	
	@if(isset($psettings->aging_bucket) && $psettings->aging_bucket == 1) 		
		<div style="page-break-after: auto; page-break-inside: avoid;">
			<table class="table-striped-view table" style="border-collapse:collapse;margin-bottom: 20px; margin-top:5px; width:100%;">
				<tr>
					<td class="bg-white" style="border-left:0px solid #ccc; border-right:0px solid #ccc;text-align:left; border-top:0px solid #ccc; border-bottom: 2px solid #00877f;font-weight:600">Aging</td>
					<td class="bg-white" style="border-left:0px solid #ccc; border-right:0px solid #ccc; border-top:0px solid #ccc; border-bottom: 2px solid #00877f;font-weight:600">0-30</td>
					<td class="bg-white" style="border-left:0px solid #ccc; border-right:0px solid #ccc; border-top:0px solid #ccc; border-bottom: 2px solid #00877f;font-weight:600">31-60</td>
					<td class="bg-white" style="border-left:0px solid #ccc; border-right:0px solid #ccc; border-top:0px solid #ccc; border-bottom: 2px solid #00877f;font-weight:600">61-90</td>
					<td class="bg-white" style="border-left:0px solid #ccc; border-right:0px solid #ccc; border-top:0px solid #ccc; border-bottom: 2px solid #00877f;font-weight:600">91-120</td>
					<td class="bg-white" style="border-left:0px solid #ccc; border-right:0px solid #ccc; border-top:0px solid #ccc; border-bottom: 2px solid #00877f;font-weight:600">120+</td> 
					<td class="bg-white" style="border-left:0px solid #ccc; border-right:0px solid #ccc; border-top:0px solid #ccc; border-bottom: 2px solid #00877f;font-weight:600">Total</td>        
				</tr>

				@foreach($patients->aging as $key=>$claimval)
					@if($key != 'total_aging')
						<tr style="border-collapse:collapse;">
							<td style="border-collapse:collapse;border:1px solid #ccc;border-left-color: #00877f;color:#fff;text-align:left;font-size:10px;font-family:sans-serif; background:#00877f">{{ @$key }}</td>
							@foreach($claimval as $key=>$monthclaimval)
							<?php 
								$get_monthclaimvalue = (is_float($monthclaimval))? (App\Http\Helpers\Helpers::priceFormat($monthclaimval)) : $monthclaimval;     
							?>
							<td class="right-text" data-format="0.00">{!! (isset($monthclaimval) && ( $monthclaimval!='0'))? $get_monthclaimvalue :'0.00' !!}</td>
							@endforeach 
						</tr>				
					@else
						<tr style="border-collapse:collapse;border:0px solid #ccc;">
							<td style="border-collapse:collapse;border:0px solid #ccc;color:#fff;font-size:11px;text-align:center;font-family:sans-serif;"></td>
							<td style="border-collapse:collapse;border:0px solid #ccc;color:#646464;font-size:11px;padding-right:5px;text-align:right;font-family:sans-serif;"></td>
							<td style="border-collapse:collapse;border:0px solid #ccc;color:#646464;font-size:11px;padding-right:5px;text-align:right;font-family:sans-serif; text-transform: capitalize; line-height: 18px;"> </td>
							<td style="border-collapse:collapse;border:0px solid #ccc;color:#646464;font-size:11px;padding-right:5px;text-align:right;font-family:sans-serif; text-transform: capitalize; line-height: 18px;"> </td>
							<td style="border-collapse:collapse;border:0px solid #ccc;color:#646464;font-size:11px;padding-right:5px;text-align:right;font-family:sans-serif; text-transform: capitalize; line-height: 18px;"></td>
							<td style="border-collapse:collapse;border:0px solid #ccc;color:#00877f;text-align:right;font-size:11px;padding-right:5px;font-family:sans-serif;">Total</td>   
							<td style="border-collapse:collapse;border:1px solid #ccc;color:#00877f; font-weight:600;text-align:right;font-size:11px;padding-right:10px;font-family:sans-serif;" data-format="0.00">{!! (isset($claimval) && ( $claimval!='0'))? App\Http\Helpers\Helpers::priceFormat($claimval, 'export'):'0.00' !!}</td>
						</tr>
					@endif
				@endforeach 

			</table>
		</div>    
	@endif
	
	<div class="footer" style="color:#646464; border:1px solid #ccc; margin-top:100px; padding:6px 4px; font-size:10px;font-family:sans-serif; line-height: 24px;">
		<p style="margin-top:-17px; margin-bottom:0px;"><span style="background:#fff; padding: 0px 4px; color:#00877f;">Important Message</span></p>
		@if(isset($patients->patient_statement_note->content) && $patients->patient_statement_note->content != '')
		<p style="margin-top:-17px; margin-bottom:0px;">
			<span style="background:#fff; padding: 0px 0px;">{{ $patients->patient_statement_note->content }}</span>
		</p>	
		@endif
		@if($paymentmessage == '')
			{{ @$psettings->paymentmessage_1 }}
		@else
			{{ $paymentmessage }}
		@endif
		<?php /* Spacial message show here */ ?>
		<p style="margin-top:-17px; margin-bottom:0px;">
			<span style="background:#fff; padding: 0px 0px;">{{ @$psettings->spacial_message_1 }}</span>
		</p>            
		<p style="margin-top:-17px; margin-bottom:0px; font-weight:600;">
			For Billing Inquiries Please Call : <span style="color:#00877f;">{{ @$psettings->callbackphone }} with your Account Number</span>
		</p>
	</div>
</div>
<?php 
} catch(Exception $e){
	$trace = $e->getTrace();
    $eMSg = $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine().' called from '.@$trace[0]['file'].' on line '.@$trace[0]['line'];
	\Log::info($eMSg);
}
?>