<?php
try { 

?>
<style>
    table, table tr, table tr td{font-size:10px;font-family:sans-serif;color:#646464;margin-bottom:20px;}
    .border-none tr td{border:none !important;padding:5px 10px 2px 10px;}
    table tr td{border:1px solid #e3e6e6;padding:2px 10px 2px 10px;}
    .statement table tr td{border:1px solid #e3e6e6;padding:3px 10px 4px 10px;}
    .statement table tr th{border:1px solid #e3e6e6;padding:3px 10px 4px 10px;}
    .statement table tr:nth-of-type(odd){background-color: rgba(244,244,244,1);}
    .no-padding{padding-bottom: 0px !important; }
    .pull-right { float:right;text-align:right}
    .med-green { color:#00877f;}
    .med-orange { color:#F07D08;}
    h3 {font-size:12px;font-family:sans-serif;margin: 20px 0px;clear: both;margin-bottom:20px;}
    h4 {color:#F07D08;font-size:12px;font-family:sans-serif;margin: 20px 0px;clear: both;margin-bottom:20px;}
    .bg-gray{border:1px solid #e3e6e6; background: #f4f4f4; width:100%;margin-top:-13px;}
    .tb-green{width:100%;margin-top:-10px;border: 1px solid #00877f; margin-bottom:25px !important;}
    .bg-green{background-color: #00877f !important;padding:2px 10px 2px 10px; font-weight:400; color: #fff !important; text-align: center; font-size:10px;font-family:sans-serif;border-right: 1px solid #e3e6e6;}
    .bg-white{background-color: #fff;padding:2px 10px 2px 10px; font-weight:400; color: #00877f; text-align: center; font-size:10px;font-family:sans-serif;}
    .right-text{text-align: right !important;}
    header {
        position: absolute;
        font-family:sans-serif;
        transform:rotate(315deg);
        -webkit-transform:rotate(315deg);
        margin-top: 0%;
        transform-origin: 90% 15%;       
        opacity: 0.1;
        font-size: 150px;
        z-index: 11111;
        text-align: center !important; 
    }
    .footer {bottom: 0px; position: absolute;}
</style>
<div style="margin-top:-30px; margin-right:-20px;">
    <?php /* Preview option set in */ ?>    
    @if($mode == 'preview') 
        <header>Preview</header>
    @endif   
    <div  style="color:#00877f; font-size:11px;font-family:sans-serif; font-weight:600; border-bottom: 1px solid #a7e9e1;  margin-left:-25px; background: #e4f8f6; padding:2px 5px 2px 4px; margin-bottom:5px;margin-top:-7px;">
        STATEMENT 
	</div>
    <div style="@if(count(@$claims)>0) position:absolute; @endif margin-left:300px; ">
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
							{{ App\Http\Helpers\Helpers::timezone(date('m/d/y H:i:s'),'m/d/y') }}
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
						
						@if(isset($patients->patient_budget)) 
							@php $data = App\Http\Helpers\Helpers::getPatientBudgetBalence($patients->id); @endphp
							@if($data['budgetDueTotalAmount'] > 0 && $data['budgetTotalAmount'] > 0)
								{{ App\Http\Helpers\Helpers::priceFormat($data['budgetDueTotalAmount'])	}}
							@else
								{{ App\Http\Helpers\Helpers::priceFormat($patient_balance,'yes') }}
							@endif
						@else
							{{ App\Http\Helpers\Helpers::priceFormat($patient_balance,'yes') }}
						@endif
                      <!--  {!! (count((array)$patients->patient_budget)>0)? ($get_budgetamount->budget_balance == 0)? App\Http\Helpers\Helpers::priceFormat($get_budgetamount->budget_amt,'yes'): App\Http\Helpers\Helpers::priceFormat($get_budgetamount->budget_balance,'yes') : App\Http\Helpers\Helpers::priceFormat($patient_balance,'yes') !!} -->
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

					<div style="color:#00877f; font-size:12px;font-family:sans-serif; margin-top:20px;">
						
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
						
						<p style="color:#868686; margin-top: 6px; margin-bottom:5px; font-size:11px;font-family:sans-serif;">
							{{ $addr1 }}
						</p>
						
						@if( $addr2 != '' )
							<p style="color:#868686; margin-top: 6px; margin-bottom:5px; font-size:11px;font-family:sans-serif;">
								{{ $addr2 }}
							</p>
						@else
							<?php $tempAddr =1; ?>
						@endif
						
						<p style="color:#868686; margin-top: 0px; font-size:11px;font-family:sans-serif;">
							{{ $fullAddr  }}
						</p>
						
						@if($tempAddr > 0)
							<p style="color:#868686; margin-top: 0px; font-size:11px;font-family:sans-serif;">&nbsp; </p>
						@endif
					</div>
				</div>    
			</div>
			<?php 
				/* Facility Details show  End*/
				/* Patient Details show here */
			?>
			<table class="table table-striped-view tb-green border-none" style="background:#eafbf9; margin-top:10px; border-collapse: collapse; margin-bottom:10px !important;">   
				<tr style="margin-top:-30px !important;">
					<td>
						<div style="font-size:11px;font-family:sans-serif;margin-top:0px;" class="med-green">
							Patient Name : <span style="color:#868686">{{ strtoupper($pat_name) }}</span> 
						</div>
						<div style="margin-top:3px; font-size:11px;font-family:sans-serif;" class="med-green">
							Acc No : <span style="color:#868686">{{ $patients->account_no }}</span>
						</div>
						@if($psettings->rendering_provider == 1)   
						<div style="margin-top:3px; font-size:11px;font-family:sans-serif;" class="med-green">Rendering Provider : 
							<span style="color:#868686"> {{ strtoupper(@$claims[$get_last_result]->rendering_provider->provider_name) }} {{ @$claims[$get_last_result]->rendering_provider->degrees->degree_name }}</span>
						</div>
						@endif
					</td>
					<td style="border-left:1px solid #00877f !important; ">
						<div style="font-size:11px;font-family:sans-serif;margin-top:0px;" class="med-green">Mailing Address </div>
						<div style="margin-top:3px; font-size:11px;font-family:sans-serif;"><span style="color:#868686">{{ ($psettings->check_add_1!='')? strtoupper(@$psettings->check_add_1.', '.@$psettings->check_add_2) : '' }}</span></div>
						<div style="margin-top:3px; font-size:11px;font-family:sans-serif;" class="med-green">
							<span style="color:#868686">{{ ($psettings->check_add_1!='')? strtoupper(@$psettings->check_city.', '.@$psettings->check_state.' '.@$psettings->check_zip5.$check_zip4): '' }} </span>
						</div>
					</td>
				</tr>
			</table>
			<?php 
				/* Patient details end here */
				/* Payment transaction details show here */
			?>
			<p style="color:#646464; border-bottom:1px dashed #ccc; padding-bottom:4px; font-size:8px;font-family:sans-serif; margin-top:-10px;"><span style="background:#fff; color:#fff; padding:1px;border:1px solid #ccc;"> .. </span> Check if your billing information has changed. Provide update(s) above or on reverse side <span style="margin-left:200px;">Please detach and return top portion with payment</span></p>
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
							<td class="right-text">0.00</td>
							@endif

							@if($psettings->displaypayment == 'InsPatient')
							<td class="right-text">0.00</td>
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
										<?php /* CPT Transaction Showed here */ ?>
										
										@if(isset($file_type) && strtolower($file_type) == 'csv')
											<?php $descTxt = ''; ?>
											@foreach(@$claim['cpt_transaction'] as $show_cpt)
												<?php 
													$cpt_desc = App\Models\Cpt::cpt_shot_desc($show_cpt['cpt_code']); 
													$descTxt = strtoupper($show_cpt['cpt_code']).($cpt_desc != '')?'-'.strtoupper($cpt_desc):'';
												?>
											@endforeach
											@if($lastCoinsDet != "") 
												<?php $descTxt .= " (".$lastCoinsDet.")"; ?>
											@endif
											<?php $descTxt = str_limit($descTxt, 55, '...'); ?>												
											{{ $descTxt }}
										@else
											@foreach(@$claim['cpt_transaction'] as $show_cpt) 
												<?php $cpt_desc = App\Models\Cpt::cpt_shot_desc($show_cpt['cpt_code']); ?>
												{{ @strtoupper($show_cpt['cpt_code']) }} {{($cpt_desc != '')?'-'.strtoupper($cpt_desc):'' }}
												<br>
											@endforeach
											@if($lastCoinsDet != "")
												{{$lastCoinsDet}}
											@endif
										@endif
									</td>
									<td class="right-text">{{ @$claim['txn_details']['claim_info']->total_charge }}</td>
									@if($psettings->displaypayment == 'Payments')
									<td class="right-text">{!! @$claim_fin_pmt !!}</td>
									@endif

									@if($psettings->displaypayment == 'InsPatient')
									<td class="right-text">{{ @$claim_fin_ipmt }}</td>
									<td class="right-text">{!! @$claim_fin_ppmt !!}</td>
									@endif
									<td class="right-text">{{ @$claim_fin_adj }}</td>
									<td class="right-text">
										{!! $claim_fin_bal !!}
										<?php /*($totalpayment == 0)? @$balance_amt : ''  */ ?>  
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
													$icd_code = isset($icd_det['short_description']) ? $icd_det['short_description'] : App\Models\Icd::icd_code($show_icd)
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
									$cpt_transaction = $cptTx;  //$claim['cpt_transaction'];
									//\Log::info("\n\n Claim CPT wise");
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
											?>
											@if(isset($file_type) && strtolower($file_type) == 'csv')
												<?php 
													$cpt_des .= ($lastCptCoinsDet != '') ? " (".$lastCptCoinsDet.")" : '';
													$cpt_des = str_limit($cpt_des, 55, '...');
												?>
												{{ @$cpt_des }}
											@else 
												{{ @$cpt_des }}	@if($lastCptCoinsDet != '') <br>{{ $lastCptCoinsDet }} @endif
											@endif
										</td>
										<td class="right-text">{{@$show_cpt->charge}}</td>
										@if($psettings->displaypayment == 'Payments')
										<td class="right-text">{{ $cpt_cfin_pmt }}</td>
										@endif

										@if($psettings->displaypayment == 'InsPatient')
										<td class="right-text">{{ @$cpt_cfin_ipmt }}</td>
										<td class="right-text">{!! @$cpt_cfin_ppmt !!}</td>
										@endif
										<td class="right-text">{{ @$cpt_cfin_adj }}</td>
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
							<?php  
								// Temporary cpt txn don't shown, have to add separate setting for show description.
								// $cpt_tran = App\Models\Payments\ClaimInfoV1::getClaimTxnList($claim['txn_details']['claim_info']->id, '', '');
							 ($cpt_tran_id = 0);
								$cpt_tran = [];
								/* No need to show the txn description.
							?>
							@if(count(@$cpt_tran) > 0)  
							@foreach($cpt_tran as $payment_detail)
							<!-- check show only unbilled claim or all claim -->                          
							   @if((@$psettings->patserviceline==1 && @$claim['txn_details']['claim_info']->status == 'Paid' && @$claim['txn_details']['claim_txn']->pmt_method =='Patient') || (@$psettings->insserviceline==1 && @$claim['txn_details']['claim_info']->status == 'Paid' && @$claim['txn_details']['claim_txn']->pmt_method =='Insurance') || @$claim['txn_details']['claim_info']->status != 'Paid'  )
									<?php 
										$cpt_balance_amt = App\Http\Helpers\Helpers::priceFormat(@$claim['pmt_tra_amt']->cpt_charge -(@$claim['pmt_tra_amt']->patient_paid)) ;
										$pmt_check_no = App\Models\Payments\ClaimInfoV1:: getCheckNo((@$claim['payment_trans']->pmt_mode == 'Check'), @$claim['payment_trans']->pmt_mode_id);
										// Replace PR01, PR02, PR03
										$desc = @$payment_detail['description'];
										$desc = str_replace("PR01", "Deductible", $desc);
										$desc = str_replace("PR02", "Co-Insurance", $desc);
										$desc = str_replace("PR03", "Copay", $desc);
										if(isset($payment_detail['payment_type'])) // Payment type shown in statement
											$desc = $desc."\n".@$payment_detail['payment_type'];
									?>
									@if(((@$payment_detail['txn_details']['claim_txn']->pmt_method=='Insurance') || (@$payment_detail['txn_details']['claim_txn']->pmt_method == 'Patient' && @$balance_amt>=0)))
										<?php
											$checkname = (@$claim['txn_details']['claim_txn']->pmt_method=='Patient')?'':', Check ';  
											$checkno = (@$claim['payment_trans']->pmt_mode == 'Check' && @$pmt_check_no!='')? $checkname.'No : '.@$pmt_check_no : '';										
										?>
										@if(@$claim['pmt_tra_amt']->patient_paid == '0.00' && @$claim['pmt_tra_amt']->insurance_paid =='0.00' && (@$claim['pmt_tra_amt']->patient_adjusted + @$claim['pmt_tra_amt']->insurance_adjusted)=='0.00' && $totalpayment != $paycount)

										@else
											<tr>  
												<td>
												<?php
												// App\Http\Helpers\Helpers::dateFormat(@$claim['txn_details']['claim_info']->date_of_service,'date')
												?>
												</td>
												@if($psettings->displaypayment == 'Payments')
													<td>{{ App\Http\Helpers\Helpers::dateFormat(@$payment_detail['txn_details']->created_at,'date') }}</td>
												@endif
												
												<td>{!! nl2br($desc) !!}</td>
												<td class="right-text"></td>
												@if($psettings->displaypayment == 'Payments')
												<td class="right-text"> {{ ($payment_detail['payments']!=0) ? @$payment_detail['payments'] :'' }}
												</td>
												@endif

												@if($psettings->displaypayment == 'InsPatient')
												<td class="right-text">
													@if(@$payment_detail['txn_details']['claim_txn']->pmt_method =='Insurance')
														{{ ($payment_detail['payments']!=0) ? @$payment_detail['payments'] :''}}
													@endif
												</td>
												<td class="right-text">
													@if(@$payment_detail['txn_details']['claim_txn']->pmt_method =='Patient')
														{{ ($payment_detail['payments']!=0) ? @$payment_detail['payments'] :''}}
													@endif
												</td>
												@endif

												<td class="right-text">{{(@$payment_detail['adjustment'] != 0 )? strip_tags(@$payment_detail['adjustment']) : ''}} 
												</td>
												<td class="right-text">
													<?php // Don't show balance in transaction list.
													{{ App\Http\Helpers\Helpers::priceFormat(@$payment_detail['pat_balance'] + @$payment_detail['ins_balance'])}}
													 ?>
													<?php // {{ ($totalpayment == $paycount)? @$balance_amt :'' }}   ?>
												</td>
											</tr>
										@endif
										
									@else
									  
									@endif

								@endif
								<?php  $paycount++;  ?>
								
								<?php $cpt_tran_id++; $idx++; ?>

							@endforeach
							@endif
							<?php */ ?>
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
								$patFin = App\Models\Patients\Patient::getPatientFinDetailsSTMT(@$patients->id);
								// App\Http\Helpers\Helpers::priceFormat(App\Models\Patients\Patient::getPatienttabARData(@$patients->id))
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
							<td class="right-text" style="background: #fff !important;">
								<?php /* strip_tags($patFin['total_insurance_due'])	*/ ?>
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
							<td class="right-text" style="background: #fff !important;">{!! ($financial_charges!='0') ? App\Http\Helpers\Helpers::priceFormat($financial_charges) : '0.00' !!}</td>
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
						<td class="right-text bg-green">{!! ($patient_balance!='')? App\Http\Helpers\Helpers::priceFormat($patient_balance) : '0.00' !!}</td>
					</tr>
					
					@if(isset($patients->patient_budget)) 
						@php $data = App\Http\Helpers\Helpers::getPatientBudgetBalence($patients->id); @endphp
					@if($data['budgetDueTotalAmount'] > 0 && $data['budgetTotalAmount'] > 0)
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
						<td colspan="1" class="right-text bg-green" style="border:1px solid #ccc;">Budget Amount</td>
						<td class="right-text bg-green">{!! ($data['budgetTotalAmount'] !='' )? App\Http\Helpers\Helpers::priceFormat($data['budgetTotalAmount']) : '0.00' !!}</td>
					</tr>
					
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
						<td colspan="1" class="right-text bg-green" style="border:1px solid #ccc;">Budget Due</td>
						<td class="right-text bg-green">{!! ($data['budgetDueTotalAmount'] !='') ? App\Http\Helpers\Helpers::priceFormat($data['budgetDueTotalAmount']) : '0.00' !!}</td>
					</tr>
					@endif
					@endif
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
							<td class="no-padding" style="padding-top:0px;"><span class="med-green">Payment Date</span> : {{ App\Http\Helpers\Helpers::dateFormat(@$patient_latestpayment->created_at,'date') }}</td>
							<td style="border-left:1px solid #00877f !important; padding-top:0px;" class="no-padding" ><span class="med-green">Paid Amount </span> : {!! App\Http\Helpers\Helpers::priceFormat(@$patient_latestpayment->patient_paid,'yes') !!}</td>
							<td style="border-left:1px solid #00877f !important; padding-top:0px;" class="no-padding"><span class="med-green">Payment Mode</span> : {{ @$patient_latestpayment->pmt_mode }}</td>
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
			<div>
				<div style="width:40%;">
					<div style="color:#00877f; font-size:12px;font-family:sans-serif;">
						<p class="font600" style="">&nbsp; </p>
					</div>
				</div>
			</div>	
		</div>
	@endif
	
    <?php /*  Aging day calculation */?>
	@if(isset($psettings->aging_bucket) && $psettings->aging_bucket == 1) 		
		<div style="page-break-after: auto; page-break-inside: avoid; margin-left:-20px">
			<table class="table-striped-view table" style="border-collapse:collapse;margin-bottom: 20px; margin-top:5px; width:100%;">
				<tr>
					<td class="bg-white" style="border-left:0px solid #ccc; border-right:0px solid #ccc;text-align:left; border-top:0px solid #ccc; border-bottom: 2px solid #00877f;font-weight:600">Aging</td>
					<td class="bg-white" style="border-left:0px solid #ccc; border-right:0px solid #ccc; border-top:0px solid #ccc; border-bottom: 2px solid #00877f;font-weight:600">0-30</td>
					<td class="bg-white" style="border-left:0px solid #ccc; border-right:0px solid #ccc; border-top:0px solid #ccc; border-bottom: 2px solid #00877f;font-weight:600">31-60</td>
					<td class="bg-white" style="border-left:0px solid #ccc; border-right:0px solid #ccc; border-top:0px solid #ccc; border-bottom: 2px solid #00877f;font-weight:600">61-90</td>
					<td class="bg-white" style="border-left:0px solid #ccc; border-right:0px solid #ccc; border-top:0px solid #ccc; border-bottom: 2px solid #00877f;font-weight:600">91-120</td>
					<td class="bg-white" style="border-left:0px solid #ccc; border-right:0px solid #ccc; border-top:0px solid #ccc; border-bottom: 2px solid #00877f;font-weight:600">120+</td> 
					<td class="bg-white" style="border-left:0px solid #ccc; border-right:0px solid #ccc; border-top:0px solid #ccc; border-bottom: 2px solid #00877f;font-weight:600">Total($)</td>        
				</tr>

				@foreach($patients->aging as $key=>$claimval)
					@if($key != 'total_aging')
						<tr style="border-collapse:collapse;">
							<td style="border-collapse:collapse;border:1px solid #ccc;border-left-color: #00877f;color:#fff;text-align:left;font-size:10px;font-family:sans-serif; background:#00877f">{{ @$key }}($)</td>

							@foreach($claimval as $key=>$monthclaimval)
							<?php 
								$get_monthclaimvalue = (is_float($monthclaimval))? (App\Http\Helpers\Helpers::priceFormat($monthclaimval)) : $monthclaimval;
							?>
							<td class="right-text">{!! (isset($monthclaimval) && ( $monthclaimval!='0'))? $get_monthclaimvalue :'0.00' !!}</td>
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
							<td style="border-collapse:collapse;border:1px solid #ccc;color:#00877f; font-weight:600;text-align:right;font-size:11px;padding-right:10px;font-family:sans-serif;">{!! (isset($claimval) && ( $claimval!='0'))? App\Http\Helpers\Helpers::priceFormat($claimval, 'export'):'0.00' !!}</td>
						</tr>
					@endif
				@endforeach 

			</table>
		</div>
	@endif
	
	<div class="footer" style="color:#646464; border:1px solid #ccc; margin-top:100px; padding:6px 4px; font-size:10px;font-family:sans-serif; line-height: 24px;margin-left: -20px;">
		<p style="margin-top:-17px; margin-bottom:0px;"><span style="background:#fff; padding: 0px 4px; color:#00877f;">Important Message</span></p>
		
		@if(isset($patients->patient_statement_note->content) && $patients->patient_statement_note->content != '')
		<p style="margin-top:-17px; margin-bottom:0px; padding-bottom: 10px;">
			<span style="background:#fff; padding: 0px 0px;">{{ $patients->patient_statement_note->content }}</span>
		</p>
		@endif	
		
		@if($paymentmessage == '')
			{{ @$psettings->paymentmessage_1 }}
		@else
			{{ $paymentmessage }}
		@endif
		<?php /* Spacial message show here */ ?>
		<p style="margin-top:-17px; margin-bottom:0px; padding-bottom: 10px;">
			<span style="background:#fff; padding: 0px 0px;">{{ @$psettings->spacial_message_1 }}</span>
		</p>            
		<p style="margin-top:-17px; margin-bottom:0px; font-weight:600;">
			For Billing Inquiries Please Call : <span style="color:#00877f;">{{ @$psettings->callbackphone }}</span> with your Account Number
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