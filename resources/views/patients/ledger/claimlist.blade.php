<div class="box-body no-bottom no-padding margin-t-10">
    <div class="box box-view-border no-shadow no-border no-bottom bg-f6fdfd"><!--  Box Starts -->
        <div class="box-header no-padding " style="border-radius: 4px 4px 0px 0px; background: #96dcd8">
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2">
                <h3 class="box-title padding-6-4 med-green">From</h3>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs hidden-md hidden-sm visible-print">
                <h3 class="box-title padding-6-4 med-green">To</h3>
            </div> 
            <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs">
                <h3 class="box-title padding-6-4 med-green">Claim No</h3>
            </div>  
            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">
                <h3 class="box-title padding-6-4 med-green">Billed To</h3>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-2 col-xs-3">
                <h3 class="box-title padding-6-0 med-green pull-right">Charges($)</h3>
            </div> 
            <div class="col-lg-1 col-md-1 col-sm-2 hidden-sm hidden-xs visible-print">
                <h3 class="box-title padding-6-0 med-green pull-right">Allowed($)</h3>
            </div>                    
            <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs">
                <h3 class="box-title padding-6-0 med-green pull-right">Paid($)</h3>
            </div> 
            <div class="col-lg-1 col-md-1 col-sm-2 hidden-md hidden-sm hidden-xs visible-print">
                <h3 class="box-title padding-6-0 med-green pull-right">Adj($)</h3>
            </div> 
            <div class="col-lg-1 col-md-1 col-sm-2 col-xs-3">
                <h3 class="box-title padding-6-0 med-green pull-right">Balance($)</h3>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-2">
                <h3 class="box-title padding-6-0 med-green">Status</h3>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body p-b-0 m-b-m-8" style="padding: 4px;">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-15"><!--  Left side Content Starts -->     <?php $claims_count =1 ?>  
            @if(count($patients->claims)>0)
                @foreach($patients->claims as $claims)
                <?php 
					$claimsIdent = $claims_id = @$claims->claim_id;
					$claims->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claims->id, 'encode'); 				
					$claims->patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claims->patient_id, 'encode'); 
				 
					if($claims->charge_add_type == 'esuperbill' && $claims->status == "E-bill"|| $claims->charge_add_type == 'ehr') {
						$url = url('patients/'.$claims->patient_id.'/billing/create/'.$claims->id);
					} elseif($claims->status == 'Submitted')  {
						$url = url('patients/'.$claims->patient_id.'/billing/edit/'.$claims->id);
					} elseif($claims->status == 'Paid') {
						$url = 'javascript:void(0)';
					} else {
						$url = url('patients/'.$claims->patient_id.'/billing/create/'.$claims->id);
					}
				
					if (!empty($claims->insurance_details)) {
						$insurance_detail = $claims->insurance_details->short_name;
					} else {
						$insurance_detail = "Self";
					}
				?> 
				
                <div class="box box-view-border no-shadow collapsed-box no-border-radius  yes-border border-green m-b-m-1 no-b-l no-b-r"><!--  Box Starts -->
                    <div class="box-header-view-white no-border-radius">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2">
                            <h3 class="box-title font12 font-normal">
								@if(@$claims->cpttransactiondetails[0]->dos_from !='0000-00-00'){{ App\Http\Helpers\Helpers::dateFormat(@$claims->cpttransactiondetails[0]->dos_from,'claimdate') }} @endif
							</h3>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs hidden-md hidden-sm visible-print">
                            <h3 class="box-title font12 font-normal">
								@if(@$claims->cpttransactiondetails[0]->dos_to !='0000-00-00'){{ App\Http\Helpers\Helpers::dateFormat(@$claims->cpttransactiondetails[0]->dos_to,'claimdate') }} @endif
							</h3>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 hidden-xs">
                            <h3 class="box-title font12 font-normal">{{ @$claims->claim_number }}</h3>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">
                            <h3 class="box-title font12 med-gray-dark font-normal">
                                {{ str_limit( @$insurance_detail, 20, '...') }}
                            </h3>
                        </div>

                        <div class="col-lg-1 col-md-1 col-sm-2 col-xs-3 text-right">
                            <h3 class="box-title font12 med-gray-dark font-normal">{!! App\Http\Helpers\Helpers::priceFormat($claims->total_charge) !!}</h3>
                            <?php $amt_total = @$claims->balance_summary;?>
                        </div>
						<?php 
							$claim_total_amt = App\Http\Helpers\Helpers::getClaimwiseAmt($claims->id,$claims->patient_id);
                           // $tot_ar = App\Http\Helpers\Helpers::priceFormat($claims->total_charge - ($claim_total_amt['total_paid'] + $claim_total_amt['total_adj']));  
							if(is_null($amt_total)) $tot_ar = 0; else  $tot_ar = $amt_total;                        
                        ?>
                        <div class="col-lg-1 col-md-1 col-sm-2 hidden-sm hidden-xs text-right visible-print">
                            <h3 class="box-title font12 med-gray-dark font-normal">{!! App\Http\Helpers\Helpers::priceFormat(@$claim_total_amt['total_allowed']) !!}</h3>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs text-right">
                            <h3 class="box-title font12 med-gray-dark font-normal"> {!! App\Http\Helpers\Helpers::priceFormat(@$claim_total_amt['total_paid']) !!}</h3>
                        </div>

                        <div class="col-lg-1 col-md-1 col-sm-1 hidden-sm hidden-md hidden-xs text-right visible-print">
                            <h3 class="box-title font12 med-gray-dark font-normal">{!! App\Http\Helpers\Helpers::priceFormat(@$claim_total_amt['total_adj']) !!}
                                </h3>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-2 col-xs-3 text-right">
                            <h3 class="box-title font12 med-gray-dark font-normal">{!! App\Http\Helpers\Helpers::priceFormat(@$tot_ar) !!}</h3>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-3">
                            <h3 class="box-title font12 font-normal @if($claims->status == 'Ready') ready-to-submit @elseif($claims->status == 'Partial Paid') c-ppaid @else {{ $claims->status }} @endif"> {{ $claims->status}}</h3>
                        </div>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa {{Config::get('cssconfigs.common.plus')}}"></i></button>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body form-horizontal" style="margin-top: 12px;">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding tabs-border no-b-b">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                <span class="bg-white med-orange margin-l-10 font13 padding-0-4 font600"> Claim Details</span>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive  p-l-0 ledger-sm-bottom-border">
                                    <table class="popup-table-wo-border table margin-t-5 no-sm-bottom">
                                        <tbody>
                                            <tr>
                                                <td class="font600">DOS</td>
                                                <td><span class="bg-date">@if(@$claims->date_of_service !='' && @$claims->date_of_service !='0000-00-00'){{ App\Http\Helpers\Helpers::dateFormat(@$claims->date_of_service,'claimdate') }} @endif
                                                    </span>
                                                </td> 
                                            </tr>
                                            <tr>
                                                <td class="font600">First Submission</td>
                                                <?php if (isset($claims->submited_date) && $claims->submited_date != "0000-00-00"&& $claims->submited_date != "1970-01-01") { ?>
                                                    <td><span class="bg-date">{{ App\Http\Helpers\Helpers::timezone($claims->submited_date,'m/d/y') }}</span></td>
                                                <?php } else { ?>
                                                    <td><span class="nil">- Nil - </span></td>
                                                <?php } ?>
                                            </tr>
                                            <tr>
                                                <td class="font600">Last Submission</td>
                                                <?php if (isset($claims->last_submited_date) && $claims->last_submited_date != "0000-00-00") { ?>
                                                    <td><span class="bg-date">{{ App\Http\Helpers\Helpers::timezone($claims->last_submited_date,'m/d/y') }}</span></td>
                                                <?php } else { ?>
                                                    <td><span class="nil">- Nil - </span></td>
                                                <?php } ?>
                                            </tr>   
                                            <!--tr>
                                                    <td class="font600">Claim Type</td>
                                                    <td>{{ucwords(@$claims->claim_type)}}</td>
                                            </tr-->
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 table-responsive  p-l-0 ledger-sm-bottom-border">
                                    <table class="popup-table-wo-border table tab-l-b-1 border-b4f7f7 margin-t-5 no-sm-bottom">                    
                                        <tbody>
                                            <tr>
                                                <td class="font600">Billed To</td>
                                                <td>{{$insurance_detail}}</td>                                              
                                            </tr>
                                            <tr>
                                                <td class="font600">Rendering Provider</td>
                                                <td><?php $provider_name = @$claims->rendering_provider->short_name;   ?> 
                                                    {{ @$provider_name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="font600">Billing Provider</td>
                                                <td>
                                                    {{ @$claims->billing_provider->short_name }} 
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="font600">Facility</td>
                                                <td>{{@$claims->facility_detail->short_name}} </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>                                
                                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 table-responsive p-l-0 ">
                                    <table class="popup-table-wo-border table tab-l-b-2 border-b4f7f7 margin-t-5 no-sm-bottom">                    
                                        <tbody>
                                            <tr>
                                                <td class="font600">Wallet Balance</td>
                                                <?php
                                                    $patient_id = $claims->patient_id;
                                                    $credit_balance = '';
                                                    $insurance_refund = '';
                                                    $patient_refund = '';
                                                    $insurance_overpayment = 0; // need to check for this
                                                     //TO do Payment 
                                                    $get_data = App\Models\Patients\Patient::getPatienttabData($claims->patient_id);
                                                    $c_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claims->id, 'decode');
                                                    //TO do Payment
                                                    $insurance_refund = App\Models\Payments\ClaimInfoV1::getRefund($c_id, 'insurance_paid_amt');
                                                    $patient_refund = App\Models\Payments\ClaimInfoV1::getRefund($c_id, 'patient_paid_amt');													
                                                ?>
                                                <td class="med-orange font600 text-right"> {!!App\Http\Helpers\Helpers::priceFormat($get_data['wallet_balance'])!!}</td>
                                            </tr>
                                            <tr>
                                                <td class="font600">Ins Overpayment</td>
                                                <?php $ins_overpayment = App\Models\Payments\ClaimInfoV1::InsuranceOverPayment($c_id);?>
                                                <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$ins_overpayment)!!}</td>
                                            </tr>
                                            <tr>
                                                <td class="font600">Ins Refund</td>
                                                <td class="text-right">{!!($insurance_refund == 0)?"0.00":App\Http\Helpers\Helpers::priceFormat(@$insurance_refund)!!}</td>
                                            </tr>
                                            <tr>
                                                <td class="font600">Pat Refund</td>
                                                <td class="text-right">{!!($patient_refund == 0)?"0.00":App\Http\Helpers\Helpers::priceFormat(@$patient_refund)!!}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>                                
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border tabs-border p-b-15" >
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10 margin-b-6">
                                <span class="bg-white med-orange padding-0-4 font600">Claim Transaction</span>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding claim-transaction-scroll">
                                <table class="popup-table-wo-border1 table margin-b-5 " style="border-top:1px solid #E4FAFD;">                    
                                    <thead>
                                        <tr>    
                                            <th class="med-green font600" style="background: #96dcd8;">From</th>
                                            <th class="med-green font600" style="background: #96dcd8;">To</th>
                                            <th class="med-green font600" style="background: #96dcd8;">CPT/HCPCS</th> 
                                            <th class="med-green font600" style="background: #96dcd8;">Modifiers</th>
                                            <th class="med-green font600" style="background: #96dcd8;">Units</th>
                                            <th class="text-right med-green font600" style="background: #96dcd8;">Charges($)</th>
                                            <th class="text-right med-green font600" style="background: #96dcd8;">Allowed($)</th>
                                            <th class="text-right med-green font600" style="background: #96dcd8;">Paid($)</th>
                                            <th class="text-right med-green font600" style="background: #96dcd8;">Co-Ins($)</th>
                                            <th class="text-right med-green font600" style="background: #96dcd8;">Co-Pay($)</th>
                                            <th class="text-right med-green font600" style="background: #96dcd8;">Deductible($)</th>
											<th class="text-right med-green font600" style="background: #96dcd8;">Adj($)</th>
                                            <!--th class="text-right">Denial Code</th>
                                            <th class="text-right">Status</th-->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach((array)@$claims->cpttransactiondetails as $trans_detail)
                                        @if(@$trans_detail->cpt_code !='' && @$trans_detail->cpt_code !='Patient')
										<?php 
											$paid_amt = @$trans_detail->claim_cpt_fin_details->patient_paid + @$trans_detail->claim_cpt_fin_details->insurance_paid;  
											$adjustment = @$trans_detail->claim_cpt_fin_details->patient_adjusted + @$trans_detail->claim_cpt_fin_details->insurance_adjusted + @$trans_detail->claim_cpt_fin_details->with_held;
                                            $latestCpt_tx_Allowed_Amount = \App\Models\Payments\PMTClaimCPTTXV1::getLastCptAllowedAmountFromClaimTx($trans_detail->claim_id);
										?>
                                        <tr>
                                            <td>@if(@$trans_detail->dos_from !='' && @$trans_detail->dos_from !='0000-00-00'){{ App\Http\Helpers\Helpers::dateFormat(@$trans_detail->dos_from,'claimdate') }} @endif
											</td>
                                            <td>@if(@$trans_detail->dos_to !='' && @$trans_detail->dos_to !='0000-00-00'){{ App\Http\Helpers\Helpers::dateFormat(@$trans_detail->dos_to,'claimdate') }} @endif</td>
                                            <td>{{	@$trans_detail->cpt_code }}</td>
                                            <td>@if(@$trans_detail->modifier1 !='') {{	@$trans_detail->modifier1 }}  @if(@$trans_detail->modifier2 !='') , {{	@$trans_detail->modifier2 }}@endif  @if(@$trans_detail->modifier3 !='') , {{	@$trans_detail->modifier3 }}@endif  @if(@$trans_detail->modifier4 !='') , {{ @$trans_detail->modifier4 }} @endif @else - @endif</td>
                                            <td>{{	@$trans_detail->unit }}</td>
                                            <td class="text-right">{{ @$trans_detail->charge }}</td>
                                            <td class="text-right">{{ @$latestCpt_tx_Allowed_Amount}}</td>
                                            <td class="text-right">{{ App\Http\Helpers\Helpers::priceFormat(@$paid_amt) }}</td>
                                            <td class="text-right">{{ @$trans_detail->claim_cpt_fin_details->co_ins }}</td>
                                            <td class="text-right">{{ @$trans_detail->claim_cpt_fin_details->co_pay }}</td>
                                            <td class="text-right">{{ @$trans_detail->claim_cpt_fin_details->deductable }}</td>	
                                            <td class="text-right">{{ App\Http\Helpers\Helpers::priceFormat(@$adjustment) }}</td>
											<?php /*
                                            <td class="text-right">@if(@$trans_detail->denial_code =='') {{ $trans_detail->denial_code }} @else {{	@$trans_detail->denial_code }} @endif</td>
                                            <td class="text-right"><span class="@if(@$trans_detail->status == 'Ready') ready-to-submit @elseif(@$trans_detail->status == 'Partial Paid') c-ppaid @else {{ @$trans_detail->status }} @endif">{{ @$trans_detail->status }}</span></td>
											*/?>
                                        </tr>
                                        @endif
                                        @endforeach
                                    </tbody>
                                </table> 
                            </div>
                        </div>    
						<?php 
							$cpttxn = App\Models\Payments\ClaimInfoV1::getClaimCptTxnList($claims_id);
						?>						
						<div id = "view_transaction{{$claims_count}}" class="collapse out col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><!-- Inner Content for full width Starts -->
                            <div class="box-body-block no-padding"><!--Background color for Inner Content Starts -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive tabs-border no-b-t">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                        <span class="bg-white med-orange padding-0-4 font600"> CPT Transaction</span>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding claim-transaction-scroll margin-t-10">

                                        <table class="popup-table-wo-border table table-responsive no-bottom">                    
                                            <thead>
                                                <tr> 
                                                    <th></th>
                                                    <th>CPT</th>
                                                    <th>Trans Date</th>                                
                                                    <th>Responsibility</th>                                
                                                    <th>Description</th>                                    
                                                    <th class="text-right">Charges($)</th>
                                                    <th class="text-right">Payments($)</th>
                                                    <th class="text-right">Adj($)</th>
                                                    <th class="text-right">Pat Bal($)</th>
                                                    <th class="text-right">Ins Bal($)</th>
                                                </tr>
                                            </thead>                                           
                                        <tbody>
										<?php $j = 1; ?>
										@if(!empty($cpttxn))
											@foreach($cpttxn as $cptKey => $cpttx)
												<?php
													$cpt_code = $cpttx;
													$style = $dmStyle = "";
													if($j == 1 && count($cpttx) > 1){
														$toggler = "toggle-minus";
														$dmStyle = "style = display:none";
													} elseif($j != 1 && count($cpttx) >1) {
														$toggler = "toggle-plus";
														$style =  "style = display:none";
													} else{
														$toggler = "";
													}
													$i = 0;
													$lTxn = end($cpttx);
													$fTxn = isset($cpttx[0]) ? $cpttx[0] : [];
												?>  
												
												@if(count($cpttx) > 1)
													<!-- Dummy block start -->
													<tr class="blk_{{$j}} med-l-green-bg" {{$dmStyle}} >
														<td><!-- Don't remove this inline style. It will affect in safari browser for + icon.  -->		  
															<a href="#" class="txtoggler font600 {{ $toggler }}" data-prod-cat="{{$j}}"><span style="position: absolute"> &emsp;</span></a>
														</td>
														<td>{{@$fTxn['cpt_code']}}</td>
														<td>{{App\Http\Helpers\Helpers::dateFormat(@$lTxn['txn_date'])}}</td>
														<td>{!! @$lTxn['responsibility'] !!}
															@if(isset($lTxn['resp_category']) && $lTxn['resp_category'] != '')
																<span class="{{ @$lTxn['resp_bg_class'] }}">{{ substr(@$lTxn['resp_category'], 0, 1) }}</span>
															@endif
														</td>
														<td>{!! nl2br(@$lTxn['description']) !!}</td>
														<td class="text-right">{!! @$fTxn['charges'] !!}</td>
														<td class="text-right">{!! @$lTxn['payments'] !!}</td>
														<td class="text-right">{!! @$lTxn['adjustment'] !!}</td>
														<td class="text-right">{!! @$lTxn['pat_balance'] !!}</td>
														<td class="text-right">{!! @$lTxn['ins_balance'] !!}</td>
													</tr>
													<!-- Dummy block end -->
												@endif
																
												@foreach($cpttx as $ctxn)
													<?php 
														//$cpt_transaction_count = count($cpttx);
													?> 
													<tr class="blk_{{$j}} med-l-green-bg" {{$style}}>
														<td> 
															@if($i == 0)
																<a href="#" class="txtoggler font600 {{ $toggler }}" data-prod-cat="{{$j}}"><span style="position: absolute"> &emsp;</span></a>
															@endif
														</td>
														<td>@if($i == 0){{@$ctxn['cpt_code']}}@endif</td>
														<td>{{App\Http\Helpers\Helpers::dateFormat(@$ctxn['txn_date'])}}</td>
														<td>
															{!! @$ctxn['responsibility'] !!}
															@if(isset($ctxn['resp_category']) && $ctxn['resp_category'] != '')
																<span class="{{ @$ctxn['resp_bg_class'] }}">{{ substr(@$ctxn['resp_category'], 0, 1) }}</span>
															@endif
														</td>
														<td>{!! nl2br(@$ctxn['description']) !!}</td>
														<td class="text-right">{!! @$ctxn['charges'] !!}</td>
														<td class="text-right">{!! @$ctxn['payments'] !!}</td>
														<td class="text-right">{!! @$ctxn['adjustment']!!}</td>
														<td class="text-right">{!! @$ctxn['pat_balance']!!}</td>
														<td class="text-right">{!! @$ctxn['ins_balance'] !!}</td>
													</tr>
												<?php $i++;?>                                          
												@endforeach
											<?php $j++;?>  
											@endforeach
										@else										
											<td colspan="10"><p class="text-center med-gray margin-t-10 margin-b-10">No payment has been done</p></td>										
										@endif										
									</tbody>    
									</table>
								</div>
							</div>
						</div>
					</div><!-- Inner Content for full width Ends -->

                        <div class="payment-links pull-right margin-t-5 margin-b-10 no-print">
                            <ul class="nav nav-pills">
                                <li><a data-toggle = "collapse" data-target = "#view_transaction{{$claims_count}}" > <i class="fa {{Config::get('cssconfigs.charges.view_trans')}}"></i> View Transaction</a></li>
                                <!--<li><a class="js_claim_edit_charge_link" id="claim-charge_{{ @$claims->claim_number }}"> <i class="fa {{Config::get('cssconfigs.charges.charges')}}"></i> Edit Charges</a></li>-->
                                <?php 
                                    $insurance_payment_count = App\Models\Payments\PMTInfoV1::checkpaymentDone($claims_id, 'payment');
                                    $edit_link = App\Http\Helpers\Helpers::getChareEditLink(@$claims_id, @$insurance_payment_count, "Billing");
                                 ?>
                                <li><a @if($edit_link <> "" ) href="{{$edit_link}}" @endif target = "_blank"><i class="fa {{Config::get('cssconfigs.charges.charges')}}"></i> Edit Charges</a></li>

                                <li><a data-index="ledger" data-id ="{{ @$claims->claim_number }}" class="claim_assign_all_link form-cursor claimotherdetail font600 p-l-10"><i class="fa {{Config::get('cssconfigs.Practicesmaster.problemlist')}}"></i> Workbench</a></li>
                                <li><a href= "javascript::void()" @if($claims->charge_add_type != 'esuperbill') onClick="window.open('{{url('/getcmsform/'.$claims->id)}}', '_blank')" @endif> <i class="fa {{Config::get('cssconfigs.patient.cms_icon')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="CMS 1500"></i> CMS 1500</a></li>                                        
                                <!--li><a><i class="fa {{Config::get('cssconfigs.charges.resubmit')}}"></i>Re-Submit</a></li-->                   
                            </ul>
                        </div>
                    </div><!-- /.box Ends-->
                </div>
                <?php $claims_count++; ?>
                @endforeach
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding dataTables_info">
                        Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! $pagination->pagination_prt !!}</div>
                </div>
            @else
                <p class="text-center med-gray-dark">{{ trans("common.validation.not_found_msg") }}</p> 
            @endif
            </div>    
        </div>
    </div>
</div><!-- /.box -->