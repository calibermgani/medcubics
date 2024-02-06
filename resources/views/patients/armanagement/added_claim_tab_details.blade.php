<?php
try{
?>
@if(count(@$claim_detail)>0)

@foreach($claim_detail as $claim_detail_val)  
@if(@$tab_type!='indivual')
<div class="tab-pane" id="claim-tab-info_{{@$claim_detail_val->claim_number}}">
    @endif
    <?php 
		$encode_patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claim_detail_val->patient_id,'encode');
    	$encode_claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claim_detail_val->id,'encode'); 
	?>	
    <div class="box-view no-shadow no-border"><!--  Box Starts -->                        
        <div class="box-body form-horizontal no-padding"> 
                <?php 
                    if(!isset($get_default_timezone)){
                       $get_default_timezone = \App\Http\Helpers\Helpers::getdefaulttimezone();
                    }                  
               ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-20 margin-t-20 p-l-0">

                <div class="payment-links pull-left hide">
                    <ul class="nav nav-pills">
                        <li><a data-toggle = "collapse" data-target = "#view_transaction" class="p-l-0"> <input type="checkbox" checked="checked" class="flat-red"> Transaction</a></li>
                        <li><a data-toggle="collapse" data-target="#notes_{{$claim_detail_val->claim_number}}"> <input type="checkbox" checked="checked" class="flat-red"> Notes</a></li>  
                    </ul>
                </div>

                <div class="payment-links pull-right">
                    <ul class="nav nav-pills">
                        <?php
							$insurance_payment_count = App\Models\Payments\PMTInfoV1::checkpaymentDone(@$claim_detail_val->id, 'any');
							$insurances = App\Http\Helpers\Helpers::getInsuranceNameLists();
                        
							$edit_ch_url = url('patients/'.$encode_patient_id.'/billing/create/'.$encode_claim_id);
                        	$url=$_SERVER['HTTP_REFERER']; 
						
							if (strpos($url, 'patient') !== FALSE) {
								$url_value = url('payments/armanagement/' . $encode_claim_id . '/patient');
								$edit_link = App\Http\Helpers\Helpers::getChareEditLink(@$claim_detail_val->id, @$insurance_payment_count, "Billing");
							} else {
								$url_value = url('payments/armanagement/' . $encode_claim_id);
								$edit_link = App\Http\Helpers\Helpers::getChareEditLink(@$claim_detail_val->id, @$insurance_payment_count, "Charge");
							}
                        ?>						

                        <li class="documents-view" >
                            <a href="#view_ar_document_{{$claim_detail_val->claim_number}}" data-url="" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.Practicesmaster.document_open')}}"></i> Documents</a>
                        </li>   

                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa {{Config::get('cssconfigs.charges.charges')}}"></i>  Edit<b class="caret"></b></a>
                            <ul class="dropdown-menu" style="margin-left:-35px;">                                 
                                <li class="form-cursor"><a data-url="{{$edit_link}}" class="js_claim_edit_charge_link_new1 font600" id="claim-charge_{{$claim_detail_val->claim_number}}"><i class="fa {{Config::get('cssconfigs.charges.charges')}}"></i> Visit</a></li>

                                <li class="form-cursor">
                                    @if($claim_detail_val->status == "Hold")
                                    <a class="font600"  href ="#" data-toggle="tooltip" data-original-title="Payment can't be done for hold claims"> <i class="fa fa-money"></i>Insurance Payments</a>
                                    @else
                                    <a data-url="{{$url_value}}"  data-type="ins_pmt" class="js_claim_edit_charge_link_new1 font600" id="claim-payment_{{$claim_detail_val->claim_number}}"> <i class="fa fa-money"></i>Insurance Payments</a>
                                    @endif
                                    
                                </li>
								<li class="form-cursor">
									<a data-url="{{$url_value}}" data-type="pat_pmt" data-claim="{{$claim_detail_val->claim_number}}" class="js_claim_edit_charge_link_new1 font600" id="claim-payment_{{$claim_detail_val->claim_number}}"> <i class="fa fa-user"></i>Patient Payments</a>
								</li>
								
								<!-- Todo for next version -->
								<li class="form-cursor hide"><a class="font600"> <i class="fa fa-shield"></i> Authorization</a></li>
                                <li class="form-cursor hide"><a class="font600"> <i class="fa fa-institution"></i> Insurance</a></li>
                            </ul>
                        </li>   

                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#"> More<b class="caret"></b></a>
                            <ul class="dropdown-menu" style="margin-left:-70px;"> 
                                <li class="form-cursor hide"><a data-toggle="modal" data-target="#eligibility_details_{{$claim_detail_val->claim_number}}"><i class="fa {{Config::get('cssconfigs.common.check')}}"></i> Eligibility</a></li>

                                <li class="form-cursor"><a class="js-billingdet-popup-link font600" data-id="{{$claim_detail_val->claim_number}}" data-toggle="modal" data-target="#billing_details_{{$claim_detail_val->claim_number}}"> <i class="fa {{Config::get('cssconfigs.patient.file_text')}}"></i> Billing Details</a></li>                                                                
                                <li class="hide"><a data-toggle = "collapse" data-target = "#view_era" class="font600"> <i class="fa {{Config::get('cssconfigs.charges.claim_preview')}}"></i> View ERA</a></li>
                                <li><a class="font600" href= "javascript:void()" onClick="window.open('{{url('/getcmsform/'.$encode_claim_id)}}', '_blank')"><i class="fa {{Config::get('cssconfigs.patient.cms_icon')}}"></i> CMS 1500</a></li>
                                <li class="form-cursor">
                                    <a class="cob font600"> <i class="fa {{Config::get('cssconfigs.common.insurance')}}"></i> <span data-patient-id="{{ $encode_patient_id }}" class="js-show-ins">COB</span></a>
                                </li>
                            </ul>
                        </li>   
                    </ul>
                </div>
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-white padding-10">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border margin-t-5 bg-white tabs-border border-radius-4">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                        <?php
							$acc_no = $claim_detail_val->patient->account_no;
							$patient_name = @$claim_detail_val->patient->last_name . ", " . @$claim_detail_val->patient->first_name . " " . @$claim_detail_val->patient->middle_name;
							if (strripos($_SERVER['HTTP_REFERER'], "patients") > 0) {
								$display_text = "Claim Details";
							} else {
								$display_text = $acc_no . " - " . $patient_name;
							}
                        ?>
                        <span class="med-orange padding-0-4 font13 margin-l-10 bg-white font600"> {{$display_text}}</span>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" >
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 table-responsive" >
                            <table class="popup-table-claims table">                    
                                <tbody>

                                    <tr class="tab-r-b-1 green-b-c">
                                        <td class="font600">Rendering Provider</td>
                                        <td>{{ str_limit(@$claim_detail_val->rendering_provider->provider_name." ".@$claim_detail_val->rendering_provider->degrees->degree_name,85,'...') }}</td>
                                    </tr>
                                    <tr class="tab-r-b-1 green-b-c">
                                        <td class="font600">Billing Provider</td>
                                        <td>{{ str_limit(@$claim_detail_val->billing_provider->provider_name." ".@$claim_detail_val->billing_provider->degrees->degree_name,85,'...') }}</td>
                                    </tr>
                                    <tr class="tab-r-b-1 green-b-c">
                                        <td class="font600">Facility</td>
                                        <td class="">{{@$claim_detail_val->facility_detail->facility_name}}</td>
                                    </tr>
                                    <tr class="tab-r-b-1 green-b-c">
                                        <td class="font600">Referring Provider</td>
                                        <td class="">{{ (!empty($claim_detail_val->refering_provider))?str_limit(@$claim_detail_val->refering_provider->provider_name." ".@$claim_detail_val->refering_provider->degrees->degree_name,85,'...'): "- Nil - "}}</td>                                  
                                    </tr>                                        

                                </tbody>
                            </table>
                        </div>

                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 table-responsive" >
                            <table class="popup-table-claims table">                    
                                <tbody>
                                    <tr class="tab-r-b-1 green-b-c">									
                                        <?php 
											if (isset($claim_detail_val->claim_details->illness_box14) && $claim_detail_val->claim_details->illness_box14 != "0000-00-00") {
												$doi = App\Http\Helpers\Helpers::dateFormat($claim_detail_val->claim_details->illness_box14, 'date');
											} elseif (isset($claim_detail_val->doi) && $claim_detail_val->doi != "0000-00-00 00:00:00" && $claim_detail_val->doi != "0000-00-00" && $claim_detail_val->doi != "1970-01-01") {
												$doi = App\Http\Helpers\Helpers::dateFormat($claim_detail_val->doi, 'date');
											} else {
												$doi = "- Nil -";
											}	
                                        ?>
                                        <td class="font600">DOI</td>
                                        <td><span class="@if($doi == '- Nil -') bg-white @else bg-date @endif">{{$doi}}</span></td>
                                    </tr>
                                    <tr class="tab-r-b-1 green-b-c">
                                        <?php
											if (isset($claim_detail_val->submited_date) && $claim_detail_val->submited_date != "0000-00-00" && $claim_detail_val->submited_date != "1970-01-01")
												$claims_submited_date = App\Http\Helpers\Helpers::dateFormat($claim_detail_val->submited_date, 'date');
											else
												$claims_submited_date = "- Nil -";
                                        ?>
                                        <td class="font600">First Submission Date</td>
                                        <td><span class="@if($claims_submited_date == '- Nil -') bg-white @else bg-date @endif">{{$claims_submited_date}}</span></td>                            
                                    </tr>  
                                    <tr class="tab-r-b-1 green-b-c">
                                        <?php
											if (isset($claim_detail_val->last_submited_date) && $claim_detail_val->last_submited_date != "0000-00-00")
												$claims_last_submited_date = App\Http\Helpers\Helpers::dateFormat($claim_detail_val->last_submited_date, 'date');
											else
												$claims_last_submited_date = "- Nil -";
                                        ?>
                                        <td class="font600">Last Submission Date</td>
                                        <td><span class="@if($claims_last_submited_date == '- Nil -') bg-white @else bg-date @endif">{{$claims_last_submited_date}}</span></td>
                                    </tr>
                                    <tr class="tab-r-b-1 green-b-c">
                                        <td class="font600">Auth No</td>
                                        <?php
											if (isset($claim_detail_val->claim_details->box_23) && !empty($claim_detail_val->claim_details->box_23) && isset($claim_detail_val->claim_details->box23_type) && !empty($claim_detail_val->claim_details->box23_type) && $claim_detail_val->claim_details->box23_type=='referal_number')
												$auth_number = $claim_detail_val->claim_details->box_23;
											elseif (!empty($claim_detail_val->auth_no))
												$auth_number = $claim_detail_val->auth_no;
											else
												$auth_number = "- Nil -";
                                        ?>
                                        <td><span class="@if($claims_submited_date == '- Nil -') bg-white @else bg-date @endif">{{$auth_number}}</span></td>
                                    </tr>                                   
                                </tbody>
                            </table>
                        </div>

                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 table-responsive" >
                            <table class="popup-table-claims table">                    
                                <tbody>
                                    <tr>
                                        <td class="font600">Aging Days</td>
                                        <?php
											/*$now = time(App\Http\Helpers\Helpers::timezone(date('Y-m-d H:i:s'),'Y-m-d')); // or your date as well
											$your_date = strtotime($claim_detail_val->date_of_service);
											$datediff = $now - $your_date;
											$count = floor($datediff / (60 * 60 * 24));*/
											$datetime1 = new DateTime($claim_detail_val->date_of_service);
											$datetime2 = new DateTime(App\Http\Helpers\Helpers::timezone(date('Y-m-d H:i:s'),'Y-m-d'));
											$interval = $datetime1->diff($datetime2);
											$count = $interval->format('%a');//now do whatever you like with $days
											$days = ($count == 1 || $count == 0) ? ($count == 0 ? "0" : $count . " Day") : $count . " Days";
                                        ?>
                                        <td>{{$days}}</td>                                
                                    </tr>
                                    <tr>
                                        <?php
                                        if (!empty(@$claim_detail_val->insurance_details)) {
                                            $insurance_detail = !empty($insurances[@$claim_detail_val->insurance_details->id]) ? $insurances[@$claim_detail_val->insurance_details->id] : App\Http\Helpers\Helpers::getInsuranceName(@$claim_detail_val->insurance_details->id);
                                        } else {
                                            $insurance_detail = "Self";
                                        }
                                        ?>
                                        <td class="font600">Payer</td>
                                        <td>{!!@$insurance_detail!!}</td>                                
                                    </tr>
                                    <tr>
                                        <td class="font600">Patient Balance</td>
                                        <td>{!!App\Http\Helpers\Helpers::priceFormat(@$claim_detail_val->patient_due)!!}</td>
                                    </tr>  
                                    <tr>
                                        <td class="font600">Insurance Balance</td>
                                        <td>{!!App\Http\Helpers\Helpers::priceFormat(@$claim_detail_val->insurance_due)!!}</td> 
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mobile-scroll table-responsive margin-t-m-13">
                        <table class="popup-table-transaction table mobile-width">                    
                            <thead>
                                <tr>    
                                    <th style="background: #96dcd8" class="med-green">From</th>
                                    <th style="background: #96dcd8" class="med-green">To</th>
                                    <th style="background: #96dcd8" class="med-green">CPT</th>
                                    <th style="background: #96dcd8" class="med-green">Units</th>
                                    <th style="background: #96dcd8" class="med-green">Modifiers</th>
                                    <th style="background: #96dcd8" class="med-green">ICD</th>
                                    <th style="background: #96dcd8" class="med-green text-right">Charges</th>
                                    <th style="background: #96dcd8" class="med-green text-right">Allowed</th>
                                    <th style="background: #96dcd8" class="med-green text-right">Adj</th>
                                    <th style="background: #96dcd8" class="med-green text-right">AR Due</th>
                                </tr>
                            </thead>
                            <tbody>
							
                                @foreach(@$claim_detail_val->dosdetails as $trans_detail)
                                <?php
									$modifier = $trans_detail->modifier1 . ',' . $trans_detail->modifier2 . ',' . $trans_detail->modifier3 . ',' . $trans_detail->modifier4;
									$modifier_val = array_filter(explode(',', $modifier));
									$modifier_data = (!empty($modifier_val)) ? implode(',', $modifier_val) : "";
									if(!empty($trans_detail->claim_cpt_fin_details)){
										 $finDetails = $trans_detail->claim_cpt_fin_details;
										$latestCpt_tx_Allowed_Amount = \App\Models\Payments\PMTClaimCPTTXV1::getLastCptAllowedAmountFromClaimTx($trans_detail->claim_id);
									} else {
										$finDetails = [];
										$latestCpt_tx_Allowed_Amount = '0.00';
									}
									$total_adj = $finDetails->patient_adjusted + $finDetails->insurance_adjusted + $finDetails->with_held;
									$totalPaid = $finDetails->patient_paid + $finDetails->insurance_paid; 
									$balance_amt = (@$trans_detail->charge) - ($totalPaid + $total_adj);
								?>                               
                                <tr>
                                    <td>@if(@$trans_detail->dos_from !='' && @$trans_detail->dos_from !='0000-00-00'){{ App\Http\Helpers\Helpers::dateFormat(@$trans_detail->dos_from,'claimdate') }} @endif</td>
                                    <td>@if(@$trans_detail->dos_to !='' && @$trans_detail->dos_to !='0000-00-00'){{ App\Http\Helpers\Helpers::dateFormat(@$trans_detail->dos_to,'claimdate') }} @endif</td>
                                    <td class="">{{ @$trans_detail->cpt_code }}</td>
                                    <td class="">{{ @$trans_detail->unit }}</td>
                                    <td class="">{{ @$modifier_data }}</td>                                   
                                    <td class="">
										<?php $cptArr = explode(',',@$trans_detail->cpt_icd_code);  ?>
										<?php echo @$cptArr[0]; if(!empty(@$cptArr[1])) echo ",".@$cptArr[1]; if(!empty(@$cptArr[2])) echo ",".@$cptArr[2];if(!empty(@$cptArr[3])) echo ",".@$cptArr[3];  ?>
									</td>                                   
                                    <td class="text-right">{{App\Http\Helpers\Helpers::priceFormat(@$trans_detail->charge) }}</td>
                                    <td class="text-right">{{App\Http\Helpers\Helpers::priceFormat(@$latestCpt_tx_Allowed_Amount) }}</td>
                                    <td class="text-right">{{ App\Http\Helpers\Helpers::priceFormat($total_adj) }}</td>
                                    <td class="text-right">{!!  App\Http\Helpers\Helpers::priceFormat(@$balance_amt) !!}</td>
                                </tr>                               
                                @endforeach                               
                            </tbody>
                        </table>                    
                    </div>
                </div>
            </div>

            <?php 
				$claim_transaction = $claim_detail_val->claim_tx_list;
				$cpt_transaction = $claim_detail_val->cpt_tx_list; 
			?>
            <div id = "view_transaction" class="collapse in col-lg-12 col-md-12 col-sm-12 col-xs-12 p-b-25">
                <div class="box-body-block no-padding margin-t-15 bg-transparent"><!--Background color for Inner Content Starts -->
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive no-b-b bg-transparent no-padding">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">                            
                            <h4 class="med-darkgray margin-t-5 margin-b-15"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> Transaction Details</h4>
                        </div>         
                       <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive yes-border tabs-border p-b-15" >
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10 margin-b-6">
								<span class="bg-f0f0f0 med-orange padding-0-4 font600">Claim Transaction</span>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding claim-transaction-scroll">
								<table class="popup-table-wo-border table table-responsive margin-b-5">                    
									<thead>
										<tr> 
											<th></th>
											<th>Trans Date</th>
											<th>Responsibility</th>
											<th>Description</th>
											<th>Payment Type</th>
											<th class="text-right">Charges</th>
											<th class="text-right">Payments</th>
											<th class="text-right">Adj</th>
											<th class="text-right">Pat Bal</th>
											<th class="text-right">Ins Bal</th>  
										</tr>
									</thead>                              
									<tbody >  

										@if(!empty($claim_transaction))            
										<?php $j = 1; ?>        
										@foreach($claim_transaction as $key => $txn)
										<tr>    
											<td><a href="#" class="font600" data-prod-cat="{{$j}}"><span style="position: absolute"> &emsp;</span></a></td>
											<td> {{App\Http\Helpers\Helpers::dateFormat($txn->txn_date,'date')}}</td>
											<td>
												{!! $txn->responsibility !!}
												@if(isset($txn->resp_category) && $txn->resp_category != '')
													<span class="{{@$txn->resp_cat_class}}">{{ substr(@$txn->resp_category, 0, 1) }}</span>
												@endif
											</td>                            
											<td>{!! nl2br($txn->description) !!}</td> 
											<td>{!! $txn->payment_type!!}</td>
											<td class="text-right">{!! $txn->charges!!}</td>
											<td class="text-right">{!! $txn->payments!!}</td>
											<td class="text-right">{!! $txn->adjustment!!}</td>
											<td class="text-right">{!! $txn->pat_balance!!}</td>
											<td class="text-right">{!! $txn->ins_balance!!}</td>
										</tr>
										
										@endforeach
										<tr> 
											<td colspan="5"></td>
											<td class="med-green-wo-span font600 text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim_detail_val->total_charge)!!}</td>
											<td class="med-green-wo-span font600 text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim_detail_val->total_paid)!!}</td>
											<td class="med-green-wo-span font600 text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim_detail_val->total_adjusted)!!}</td>
											<td class="med-green-wo-span font600 text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim_detail_val->patient_due)!!}</td>
											<td class="med-green-wo-span font600 text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim_detail_val->insurance_due)!!}</td> 
										</tr>               
										@else
										<tr>	
											<td colspan="8"><p class="text-center no-bottom med-gray margin-t-10">No payment has been done</p></td>
										</tr>	
										@endif                                
									</tbody>                           
								</table>
							</div>
						</div>     
                    </div>
                </div>

				<div id = "view_transaction" class=" "><!-- Inner Content for full width Starts -->

					<div class="box-body-block no-padding bg-transparent"><!--Background color for Inner Content Starts -->
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive tabs-border no-b-t">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
								<span class="bg-f0f0f0 med-orange padding-0-4 font600"> CPT Transaction</span>
							</div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding claim-transaction-scroll">

							<table class="popup-table-wo-border table table-responsive margin-t-15 margin-b-5">
								<thead>
									<tr>
										<th></th>
										<th>CPT</th>
										<th>Trans Date</th>
										<th>Responsibility</th>
										<th>Description</th>
										<th class="text-right">Charges</th>
										<th class="text-right">Payments</th>
										<th class="text-right">Adj</th>
										<th class="text-right">Pat Bal</th>
										<th class="text-right">Ins Bal</th>
									</tr>
								</thead>
								<tbody>
								<?php $j = 1; $payment = 1; ?>
								@foreach($cpt_transaction as $cptKey => $cpttx)
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
											<td>{{@$fTxn->cpt_code}}</td>
											<td>{{@date('m/d/y', strtotime($lTxn->txn_date))}}</td>
											<td>{!! $lTxn->responsibility !!}
												@if(isset($lTxn->resp_category) && $lTxn->resp_category != '')
													<span class="{{ @$lTxn->resp_bg_class }}">{{ substr(@$lTxn->resp_category, 0, 1) }}</span>
												@endif
											</td>
											<td>{!! nl2br($lTxn->description) !!}</td>
											<td class="text-right">{!! $fTxn->charges !!}</td>
											<td class="text-right">{!! $lTxn->payments !!}</td>
											<td class="text-right">{!! $lTxn->adjustment!!}</td>
											<td class="text-right">{!! $lTxn->pat_balance!!}</td>
											<td class="text-right">{!! $lTxn->ins_balance!!}</td>
										</tr>
										<!-- Dummy block end -->
									@endif
									@foreach($cpttx as $ctxn)
										<?php
										//$cpt_transaction_count = count($cpttx);
											$payment= 0;
										?>
										<tr class="blk_{{$j}} med-l-green-bg" {{$style}}>
											<td>
												@if($i == 0)
													<a href="#" class="txtoggler font600 {{ $toggler }}" data-prod-cat="{{$j}}"><span style="position: absolute"> &emsp;</span></a>
												@endif
											</td>
											<td>@if($i == 0){{@$ctxn->cpt_code}}@endif</td>
											<td>{{@date('m/d/y', strtotime($ctxn->txn_date))}}</td>
											<td>
												{!! $ctxn->responsibility !!}
												@if(isset($ctxn->resp_category) && $ctxn->resp_category != '')
													<span class="{{ @$ctxn->resp_bg_class }}">{{ substr(@$ctxn->resp_category, 0, 1) }}</span>
												@endif											
											</td>
											<td>{!! nl2br($ctxn->description) !!}</td>
											<td class="text-right">{!! $ctxn->charges !!}</td>
											<td class="text-right">{!! $ctxn->payments !!}</td>
											<td class="text-right">{!! $ctxn->adjustment!!}</td>
											<td class="text-right">{!! $ctxn->pat_balance!!}</td>
											<td class="text-right">{!! $ctxn->ins_balance!!}</td>
										</tr>
									<?php $i++;?>
									@endforeach
		 
								<?php $j++;?>
								@endforeach								

								<tr> 
									<td colspan="5"></td>
									<td class="med-green-wo-span font600 text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim_detail_val->total_charge)!!}</td>
									<td class="med-green-wo-span font600 text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim_detail_val->total_paid)!!}</td>
									<td class="med-green-wo-span font600 text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim_detail_val->total_adjusted)!!}</td>
									<td class="med-green-wo-span font600 text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim_detail_val->patient_due)!!}</td>
									<td class="med-green-wo-span font600 text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim_detail_val->insurance_due)!!}</td>
								</tr>
								@if(@$payment)
								<td colspan="10">
									<p class="text-center no-bottom med-gray margin-t-10">No payment has been done</p>
								</td>
								@endif
							   
								</tbody>
							</table>
                            </div>
						</div>
					</div><!-- Inner Content for full width Ends -->
				</div><!--Background color for Inner Content Ends -->
            </div><!--Background color for Inner Content Ends --> 

            <div class="collapse in" id = "notes_{{$claim_detail_val->claim_number}}">
                <div  class=" col-lg-12 col-md-12 col-sm-12 col-xs-12  bg-white p-b-12 ">
                    <div  class=" col-lg-8 col-md-8  col-sm-12 col-xs-12 p-l-0 p-b-0 p-t-0 p-r-0"><!-- Notes Col starts -->
                        <div class=" box-view no-shadow  margin-t-13 tabs-border border-radius-4 no-bottom "><!-- VOB Box starts -->
                            <p class="med-orange no-bottom  font13 margin-l-10 margin-t-m-10"> <span class="bg-white padding-0-4 font600 ">Notes </span></p>
							<?php $notes_count = 0; ?>
							
							@foreach(@$claim_detail_val->claim_notes_details as $notes_detail)
								@if($notes_detail->patient_notes_type == 'claim_notes' || $notes_detail->patient_notes_type == 'claim_denial_notes')
								<?php  $notes_count ++;  ?>
								@endif
							@endforeach
                            
                            <?php 
								if($notes_count >0 || count($patient_notes)>0) {
									$ar_notes  = 'ar-notes'; 
									$notes_no_datas  = 'hide';
									$full_notes_icon  = 'show';
								} else {
									$ar_notes  = '';
									$notes_no_datas  = 'show';
									$full_notes_icon  = 'hide';
								}
							?>

                            <div id="notes-details_{{$claim_detail_val->claim_number}}" class="box-body {{$ar_notes}} chat margin-t-m-5"><!-- Notes Box Body starts -->
                                @if($notes_count >0 || count($patient_notes)>0)
									 @include ('patients/armanagement/notes_details',['claim_detail_val_arr'=>$claim_detail_val->claim_notes_details, 'patient_notes' => $patient_notes])
                                @endif
                                <div id="js-notes-no-datas_{{$claim_detail_val->claim_number}}" class="{{$notes_no_datas}} col-lg-12 col-md-12 col-sm-12 col-xs-12 med-gray text-center font600">No data available</div>
                            </div><!-- Notes box-body Ends-->

                            <div class="box-body no-padding hide" style="border-top: 1px solid #8ce5bb"><!-- Notes Box Body starts -->
                                <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 table-responsive margin-t-5 p-b-0">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group-billing no-bottom">                                
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10 no-padding" id="js-claim-notes-div_{{$claim_detail_val->claim_number}}">
                                                {!! Form::textarea('claim_notes',null,['class'=>'form-control ar-notes-minheight js-claim-notes-txt p-l-0','placeholder'=>'Type your Notes','id'=>'js-claim-notes-txt_'.@$claim_detail_val->claim_number]) !!}
                                                <span id="js-claim-notes-err_{{$claim_detail_val->claim_number}}" class="hide"><small class="help-block">Please enter note</small></span>
                                            </div>                                
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 table-responsive margin-t-5 no-padding left-border">
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                                        <p class="no-bottom font600 form-cursor"><a class="js_claim_denial_form_link" id="claim-denial-form-link_{{$claim_detail_val->claim_number}}"><i class="fa {{Config::get('cssconfigs.common.denials')}}"></i> Denials</a></p>
                                        <p class="no-bottom margin-t-5 font600 form-cursor"><a><i class="fa {{Config::get('cssconfigs.charges.voice')}}"></i> Voice</a></p>
                                        <p class="no-bottom margin-t-5 font600 form-cursor"><a class="js_claim_assign_form_link" id="claim-assign-form-link_{{$claim_detail_val->claim_number}}"><i class="fa {{Config::get('cssconfigs.Practicesmaster.problemlist')}}"></i> Workbench</a></p>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-8 col-xs-12 ">
                                        <div class="form-group-billing">                                                   
                                            <div id="js-claim-notes-submitbtn-footer_{{$claim_detail_val->claim_number}}" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: right;margin-top: 35%;">
                                                <button class="btn btn-medcubics-small margin-t-m-5 pull-right js-claim-notes-submitbtn" id="js-claim-notes-submitbtn_{{$claim_detail_val->claim_number}}">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- Notes box-body Ends-->

                            <p id="full-notes-icon_{{$claim_detail_val->claim_number}}" class="{{$full_notes_icon}} no-bottom margin-t-m-18 p-r-15 pull-right font600 form-cursor"><a data-toggle="modal" data-id="{{$claim_detail_val->claim_number}}" data-target="#full-notes_{{$claim_detail_val->claim_number}}" data-title="Full View" class="js_arfullnotes_link"><i class="med-orange fa {{Config::get('cssconfigs.common.full-view')}}" style="position: absolute; z-index: 999;" title="Full View"></i></a></p>
                        </div><!-- Notes box Ends -->           
                    </div><!-- Notes COl Ends --> 

                    <div class="col-lg-4 col-md-4 col-sm-3">
                        <form>
                            
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group margin-t-10"> 
                                    {!! Form::label('next_responsible', 'Next Responsible', ['class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 control-label med-green font600']) !!}
                                    <div class="col-lg-8 col-md-7 col-sm-12 col-xs-10">  
										@if($claim_detail_val->status == 'Hold' || $claim_detail_val->total_charge == 0.00)
										   {!! Form::select('next_responsible', array('' => '-- Select --')+ (array)@$patient_insurance+array('0'=>'Patient'),null,['class'=>'form-control select2 js_Ar_insurance','id'=>'js_denial_frm_denial_insurances_'.@$claim_detail_val->claim_number,'disabled'=>'disabled']) !!}
										@else
										   {!! Form::select('next_responsible', array('' => '-- Select --')+ (array)@$patient_insurance+array('0'=>'Patient'),null,['class'=>'form-control select2 js_Ar_insurance','id'=>'js_denial_frm_denial_insurances_'.@$claim_detail_val->claim_number]) !!}
										@endif
                                    </div>                                
                                </div>
								<span id="next_resp_ar" style="color:red" class="hide">Select insurance</span>
                            </div>
							
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hide">
                                <div class="form-group margin-t-10"> 
                                    {!! Form::label('authorization', 'Category', ['class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 control-label med-green font600']) !!}
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-10">  
                                        {!! Form::text('auth_no',null,['maxlength'=>'25','id'=>'authorization','class'=>'form-control ']) !!}
                                    </div>                                
                                </div>
                            </div>
                        </form>
						@if($claim_detail_val->status == 'Hold' && $claim_detail_val->total_charge == 0.00)
							<a href= "javascript:void(0);" data-claimNo="{{$claim_detail_val->claim_number}}" data-patientId="{{@$claim_detail_val->patient_id}}" data-claimId="{{$claim_detail_val->id}}" data-claim-no="{{$claim_detail_val->claim_number}}" class="btn btn-medcubics margin-l-5" id="js_next_ar_responsb" data-error-msg="You cannot change responsibility when charge amount is '0'" style="">Submit</a>
						@else
							<a href= "javascript:void(0);" data-claimNo="{{$claim_detail_val->claim_number}}" data-patientId="{{@$claim_detail_val->patient_id}}" data-claimId="{{$claim_detail_val->id}}" data-claim-no="{{$claim_detail_val->claim_number}}" class="btn btn-medcubics margin-l-5" id="js_next_ar_responsb" style="">Submit</a>
						@endif
                    </div>

                </div>
            </div>

            <div class="payment-links-hold pull-right margin-t-10">              
                <p class="ar-claim-status">
					<span class="font600">Status : </span> 
                    <span class="med-orange font600"> 
						{{ @$claim_detail_val->status }}
						<?php /*
                        @if(@$claim_detail_val->claim_armanagement_status!='') 
                        @if(@$claim_detail_val->claim_armanagement_status=='Claim Nis')
                        Claim NIS
                        @else
                        {{@$claim_detail_val->claim_armanagement_status}} 
                        @endif
                        @else 
                        - Nil - 
                        @endif
						*/ 
						?>
                    </span> 
					<span class="margin-l-10 margin-r-10">|</span>
					<span class="font600"> 
						<?php 
							$sub_status = App\Models\ClaimSubStatus::getClaimSubStatusList(); 
							$subStatusID = ($claim_detail_val->sub_status_id) ? $claim_detail_val->sub_status_id : null;
						?>
						<a class="js_claimsubstasg_link form-cursor" alt="{{$claim_detail_val->claim_number}}">Claim Sub Status:</a>
					</span>
					<span class="med-orange font600">
						@if($claim_detail_val->sub_status_id) 
							@if(isset($claim_detail_val->claim_sub_status->sub_status_desc))
								{{ $claim_detail_val->claim_sub_status->sub_status_desc  }} 
							@else 
								{{ $sub_status[$claim_detail_val->sub_status_id]  }} 
							@endif
						@else 
							- Nil -
						@endif
					</span>
					{!! Form::hidden('claim_num', $claim_detail_val->claim_number, ['id' => 'claim_num']) !!}
					{!! Form::hidden('claim_sub_status', $subStatusID, ['id' => 'claim_sub_status_'.$claim_detail_val->claim_number]) !!}
					<i class="fa fa-edit fa-claimsubstatus form-cursor margin-l-5" alt="{{$claim_detail_val->claim_number}}" ></i>
									
                    <span class="margin-l-10 form-cursor hide"><a class="js_claim_status_notes_form_link" id="claim-status-notes-form-link_{{$claim_detail_val->claim_number}}" data-id="{{@$claim_detail_val->patient_id}}" ><i class="fa {{Config::get('cssconfigs.common.edit')}}" data-placement="right"  data-toggle="tooltip" data-original-title="Edit"></i> </a></span>
					@if(isset($claim_detail_val->problem_list) && !empty(@$claim_detail_val->problem_list))
						<?php 
							$prob_count = count($claim_detail_val->problem_list) - 1 ;
							$status = $claim_detail_val->problem_list{$prob_count}->status; 
							$assiged_user = @$claim_detail_val->problem_list{$prob_count}->user->short_name; 
							$date = $claim_detail_val->problem_list{$prob_count}->fllowup_date; 
						?>	
						<span class="margin-l-10 margin-r-10">|</span> <a class="js_claim_assign_form_link font600 cur-pointer" id="claim-assign-form-link_{{$claim_detail_val->claim_number}}"><i class="fa {{Config::get('cssconfigs.Practicesmaster.problemlist')}} font600" title="Workbench"></i> WB : </a>  {{ $assiged_user }} / {{ App\Http\Helpers\Helpers::dateFormat(@$date,'date') }} / <span class="med-red">{{ $status }}</span>
					@else
						<span class="margin-l-10 margin-r-10">|</span> <a class="claim_assign_ar_link font600 cur-pointer" id="claim-assign-form-link_{{$claim_detail_val->claim_number}}" data-id="{{$claim_detail_val->claim_number}}" ><i class="fa {{Config::get('cssconfigs.Practicesmaster.problemlist')}} font600" title="Workbench"></i> WB : </a>  <span class="med-orange"> - Nil - </span> </span>
					@endif	
				</p>
                <ul class="nav nav-pills">
                    <?php 
						$claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claim_detail_val->id, 'encode'); $url = url('patients/payment/popuppayment/'.$claim_id); 
					?>
                    <li class="dropdown hide"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa {{Config::get('cssconfigs.common.plus')}}"></i> Followup Template <b class="caret"></b></a>
                        <ul class="dropdown-menu followup-dropdown">
                            <li><a class="js_claim_followup_notes_form_link cur-pointer font600" id="claim-status-notes-form-link_{{$claim_detail_val->claim_number}}" data-id="{{@$claim_detail_val->patient_id}}"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i>Create Followup</a></li>
                            <li><a data-toggle="modal" data-id="{{$claim_detail_val->claim_number}}" data-target="#followup-notes_{{$claim_detail_val->claim_number}}" class="js_arfullnotes_link cur-pointer font600"><i class="fa {{Config::get('cssconfigs.common.medicalhistory')}}"></i>Followup History</a></li>
                        </ul>
                    </li>

                    <li class="hide"><a class="font600"><i class="fa {{Config::get('cssconfigs.Practicesmaster.patientstatement')}}"></i> Appeal Letters</a></li>
                    <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa {{Config::get('cssconfigs.Practicesmaster.patientstatement')}}"></i> Statements <b class="caret"></b></a>
                        <ul class="dropdown-menu">
							@if($claim_detail_val->patient->statements == 'Hold')
								<li><a href="javascript:void(0);" data-patient-id="{{ $encode_patient_id }}" class="js-ar-unhold"><i class="fa {{Config::get('cssconfigs.Practicesmaster.holdoption')}}"></i>Unhold</a></li>
							@else
								<li><a href="javascript:void(0);" data-patient-id="{{ $encode_patient_id }}" class="js_statement_hold"><i class="fa {{Config::get('cssconfigs.Practicesmaster.holdoption')}}"></i> Hold</a></li> 
                            <?php /*
								<li><a href="#" data-toggle='modal' data-target="#statement_hold" data-url="" data-patient-id="{{ $encode_patient_id }}" class="js_statement_hold"><i class="fa {{Config::get('cssconfigs.Practicesmaster.holdoption')}}"></i> Hold</a></li>
							*/ ?>	
							@endif
							<li><a href="javascript:void(0);" data-name="preview" data-unique="{{$claim_detail_val->patient_id}}" data-id="{{ $encode_patient_id }}" class="js_submit_type"><i class="fa {{Config::get('cssconfigs.charges.claim_preview')}}"></i> Preview</a></li>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa {{Config::get('cssconfigs.common.plus')}}"></i>  Notes<b class="caret"></b></a>
                        <ul class="dropdown-menu" style="margin-left:-35px;">         
                            <li class="form-cursor"><a href="#" data-toggle = 'modal' data-target="#create_notes" data-notes-type="claim_notes" data-notes-claim="{{$claim_detail_val->claim_number}}" data-url="patients/{{App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claim_detail_val->patient_id, 'encode')}}/notes/create" class="js-notes font600"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> Claim Notes</a></li>
					
                            <li class="form-cursor"><a href="#" data-toggle = 'modal' data-target="#create_notes" data-notes-type="patient_notes" data-url="patients/{{App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claim_detail_val->patient_id, 'encode')}}/notes/create" class="js-notes font600"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> Patient Notes</a></li>
							<li class="hide"><a class="font600"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> Voice Notes</a></li>
                        </ul>
                    </li>  

                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa {{Config::get('cssconfigs.common.plus')}}"></i>  Add<b class="caret"></b></a>
                        <ul class="dropdown-menu" style="margin-left:-35px;">                                 
                            <li class="form-cursor"><a class="js_claim_denial_form_link" id="claim-denial-form-link_{{$claim_detail_val->claim_number}}"><i class="fa {{Config::get('cssconfigs.common.denials')}}"></i> Denials</a></li>
                            <li class="form-cursor hide"><a class="js_claim_followup_notes_form_link cur-pointer" id="claim-status-notes-form-link_{{$claim_detail_val->claim_number}}" data-id="{{@$claim_detail_val->patient_id}}"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> Followup Templates</a></li>
							<li class="form-cursor hide"><a data-toggle="modal" data-id="{{$claim_detail_val->claim_number}}" data-target="#followup-notes_{{$claim_detail_val->claim_number}}" class="js_arfullnotes_link cur-pointer font600"><i class="fa {{Config::get('cssconfigs.common.medicalhistory')}}"></i>Followup History</a></li>
                        </ul>
                    </li>  
					
                    @if(@$claim_detail_val->status!='Pending' || @$claim_detail_val->patient->statements!='Hold')
                    <li class="dropdown hide"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa {{Config::get('cssconfigs.Practicesmaster.holdoption')}}"></i> Hold <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            @if(@$claim_detail_val->status!='Pending')
                            <li><a href="javascript:void(0);" data-id="{{@$claim_detail_val->id}}" data-url="claim_hold" class="js_hold_common_link" id="claimhold-info_{{@$claim_detail_val->claim_number}}"><i class="fa {{Config::get('cssconfigs.charges.claim_preview')}}"></i> Claim</a></li>
                            @endif
                            @if(@$claim_detail_val->patient->statements!='Hold')
                            <li><a href="javascript:void(0);" data-id="{{@$claim_detail_val->patient_id}}" data-url="statement_hold" class="js_hold_common_link" id="statementhold-info_{{@$claim_detail_val->claim_number}}"><i class="fa {{Config::get('cssconfigs.charges.statement')}}"></i> Statement</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa {{Config::get('cssconfigs.common.calendar')}}"></i>  Followup Template <b class="caret"></b></a>
                        <ul class="dropdown-menu" style="margin-left:35px;">
                            <li class="form-cursor"><a class="js_claim_followup_notes_form_link cur-pointer" id="claim-status-notes-form-link_{{$claim_detail_val->claim_number}}" data-id="{{@$claim_detail_val->patient_id}}"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i>Create Followup</a></li>
							<li class="form-cursor"><a  class="js_arfullnotes_link cur-pointer font600 js_showing_history" data-url="{{ url('patients/armanagement/followup/history') }}/{{$claim_detail_val->claim_number}}" data-claimno="{{$claim_detail_val->claim_number}}"><i class="fa {{Config::get('cssconfigs.common.medicalhistory')}}"></i>Followup History</a></li>
                        </ul>
                    </li>
                </ul>
            </div>		
			<!-- Start Add Option -->
						
			<div class="js-add-new-select substatus-option hide col-lg-6 no-padding" style="margin-top:30px;" id= "js-review-substatus-type_{{$claim_detail_val->claim_number}}">
				<div class="form-group js_common_ins no-margin">
					<?php $sub_status = App\Models\ClaimSubStatus::getClaimSubStatusList(); ?>
				
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-8 p-r-0 p-l-0 m-t-sm-5 m-t-xs-5 @if($errors->first('insurancetype_id')) error @endif ">	
						{!! Form::select('sub_status_id', array('' => '-- Select Sub Status --') + (array)@$sub_status+ array('-NA-' => '-- NIL --', '0' => '-- Add New --'),  @$claim_detail_val->sub_status_id, ['class'=>'form-control select2 input-sm-modal-billing js-add-new-select-opt js-ar-review-substatus','id' =>'js-claim-review-substatus_'.@$claim_detail_val->claim_number, 'data-id' =>@$claim_detail_val->claim_number]) !!}			
					</div>
					<div class="col-sm-12 col-xs-12">
						{!!Form::hidden('sub_status_exist',null)!!}
					</div>
				</div>
				<div class="form-group hide no-margin" id="add_new_span">                   
					<div class="col-lg-11 col-md-11 col-sm-9 col-xs-8 p-r-0  no-margin">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 m-t-sm-5 m-t-xs-5 no-padding hold-option-reason"> 
							{!! Form::text('other_substatus',null,['id'=>'newadded','class'=>'form-control','maxlength'=>'25','placeholder'=>'Add New','data-label-name'=>'Sub Status','data-field-name'=>'sub_status_desc', 'data-table-name' => 'claim_sub_status']) !!}
						</div>
						<a href="javascript:void(0)" class="font600" id="add_new_save"><i class="fa {{Config::get('cssconfigs.common.save')}}"></i> Save</a> | 
						<a href="javascript:void(0)" class="font600" id="add_new_cancel"><i class="fa {{Config::get('cssconfigs.common.cancel')}}"></i> Cancel</a>
					</div>
				</div>
			</div>			
			<!-- End Add Option -->
			
        </div>        
    </div><!-- /.box-body -->

    <div id="full-notes_{{$claim_detail_val->claim_number}}" class="modal fade in">
        <div class="modal-md-650">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close normal_popup_form" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"> Notes</h4>
                </div>
                <div class="modal-body">
                    <div class="box box-view no-shadow no-border no-bottom"><!--  Box Starts -->
                        <div class="box-body ar-full-notes form-horizontal ar-notes-scroll" style="border: 1px solid #8ce5bb;" id="full-notes-details_{{$claim_detail_val->claim_number}}">
                            @if(count($claim_detail_val->claim_notes_details)>0 || count($patient_notes)>0)
                            @include ('patients/armanagement/fullnotes_details',['claim_detail_val_arr'=>$claim_detail_val->claim_notes_details,'patient_notes' => $patient_notes])
                            @endif
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box Ends Contact Details-->
                <div class="modal-footer p-t-0">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="payment-links pull-left hide">

                            <ul class="nav nav-pills">
                                <li class="med-orange font600">Add :</li>
                                <li><a><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i>  Claim Notes</a></li>
                                <li><a><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i>  Patient Notes</a></li>  
                                <li><a><i class="fa {{Config::get('cssconfigs.common.plus_circle')}} hide"></i>  Voice Notes</a></li>  
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->	

    <div id="eligibility_details_{{$claim_detail_val->claim_number}}" class="modal fade in">
        <div class="modal-md-650">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Eligibility Details</h4>
                </div>
                <div class="modal-body no-padding" >
                    <div class="box box-view no-shadow no-border"><!--  Box Starts -->
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="example2" class="table table-bordered table-striped table-separate">         
                                    <thead>
                                        <tr>
                                            <th>DOS From</th>
                                            <th>DOS To</th>
                                            <th>Insurance</th>
                                            <th>Policy ID</th>
                                            <th>EDI</th>
                                            <th>Benefit Verification</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($claim_detail_val->eligibility_list)>0)
                                        @foreach($claim_detail_val->eligibility_list as $eligibility)
                                        <?php 
											$encode_eligibility_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$eligibility->id,'encode');
											$encode_eligibility_patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$eligibility->patients_id,'encode'); 
										?>
                                        <tr class="clsCursor">
                                            <td>@if(@$eligibility->dos_from !="0000-00-00"){{ App\Http\Helpers\Helpers::dateFormat(@$eligibility->dos_from,'dob')}}@endif</td>
                                            <td> @if(@$eligibility->dos_to !="0000-00-00") {{ App\Http\Helpers\Helpers::dateFormat(@$eligibility->dos_to,'dob') }} @endif</td>
                                            <td>
                                                <?php
													$ins_name = !empty($insurances[@$eligibility->insurance_details->id]) ? $insurances[@$eligibility->insurance_details->id] : App\Http\Helpers\Helpers::getInsuranceName(@$eligibility->insurance_details->id);
                                                ?>
                                                {!! $ins_name !!}
                                            </td>
                                            <td>
                                                @if(@$eligibility->is_edi_atatched == 1) 
													{{@$eligibility->get_eligibilityinfo->policy_id }} 
                                                @else
													{{ App\Http\Controllers\Patients\Api\PatientEligibilityApiController::get_policy_id(@$eligibility->patient_insurance_id,@$eligibility->patients_id) }}
                                                @endif
                                            </td>
                                            <td>@if($eligibility->is_edi_atatched == 1) <a style="cursor: pointer;" onclick="window.open('{{ url('patients/getEligibilityMoreInfo/'.@$encode_eligibility_patient_id.'/'.@$eligibility->edi_filename) }}', '_blank')" data-toggle="tooltip" data-original-title="View more" data-placement="bottom"><i class="fa {{Config::get('cssconfigs.patient.file_text')}}"></i></a> @endif</td> 
                                            <td>@if($eligibility->is_manual_atatched == 1) <a style="cursor: pointer;" target = "_blank" href= "{{ url($eligibility->bv_file_path) }}" data-toggle="tooltip" data-original-title="View more" data-placement="bottom"><i class="fa {{Config::get('cssconfigs.patient.file_text')}}"></i></a> @endif</td>
                                        </tr>
                                        @endforeach  
                                        @else
                                        <tr class="clsCursor">
                                            <td colspan="6"> <p class="med-gray no-bottom margin-t-10 text-center">No data available</p></td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>      
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box Ends Contact Details-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  
    <div id="billing_details_{{$claim_detail_val->claim_number}}" class="modal fade in">
        <div class="modal-md-550">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Billing Details</h4>
                </div>
                <div class="modal-body no-padding" >
                    <div class="box box-view no-shadow no-border"><!--  Box Starts -->

                        <div class="box-body form-horizontal" id="js-billingdet-popup_{{$claim_detail_val->claim_number}}">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-10">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                    <span class="bg-white med-orange padding-0-4 margin-l-10 font600"> Practice Info</span>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 table-responsive m-b-m-10" >
                                    <table class="popup-table-wo-border table">                    
                                        <tbody>
                                            <tr>
                                                <td class="font600">Name</td>
                                                <td>
                                                    {{(@$practice_det->practice_name != '')?@$practice_det->practice_name:'-- Nil --'}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="font600">Tax ID</td>
                                                <td>
                                                    {{(@$practice_det->tax_id != '' && @$practice_det->tax_id != 0)?@$practice_det->tax_id:'-- Nil --'}}
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td class="font600">NPI</td>
                                                <td>
                                                    {{(@$practice_det->npi != '' && @$practice_det->npi != 0)?@$practice_det->npi:'-- Nil --'}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="font600">Specialty</td>
                                                <td>
                                                    {{(@$practice_det->speciality_details->speciality != '')?@$practice_det->speciality_details->speciality:'-- Nil --'}}
                                                </td>
                                            </tr>  
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 table-responsive  m-b-m-10" >
                                    <table class="popup-table-wo-border table">                    
                                        <tbody>
                                            <tr>
                                                <td></td>
                                                <td class="font600 med-green">Pay to Address</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>
                                                    {{@$practice_det->pay_add_1}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>
                                                    {{@$practice_det->pay_city}} {{(@$practice_det->pay_state != '')?'- '.@$practice_det->pay_state:''}} {{@$practice_det->pay_zip5}} {{(@$practice_det->pay_zip4 != '')?'- '.@$practice_det->pay_zip4:''}}
                                                </td>
                                            </tr>    
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-15">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                    <span class="bg-white med-orange padding-0-4 margin-l-10 font600"> Facility Info</span>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 table-responsive  m-b-m-10">
                                    <table class="popup-table-wo-border table">                    
                                        <tbody>
                                            <tr>
                                                <td class="font600">Name</td>
                                                <td>
                                                    {{(@$claim_detail_val->facility_detail->facility_name != '')?@$claim_detail_val->facility_detail->facility_name:'-- Nil --'}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="font600">NPI</td>
                                                <td>
                                                    {{(@$claim_detail_val->facility_detail->facility_npi != '')?@$claim_detail_val->facility_detail->facility_npi:'-- Nil --'}}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 table-responsive  m-b-m-10">
                                    <table class="popup-table-wo-border table">                    
                                        <tbody>
                                            <tr>
                                                <td></td>
                                                <td class="font600 med-green">Address</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>{{@$claim_detail_val->facility_detail->facility_address->address1}}</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>
													{{@$claim_detail_val->facility_detail->facility_address->city}} {{(@$claim_detail_val->facility_detail->facility_address->state != '')?'- '.@$claim_detail_val->facility_detail->facility_address->state:''}} {{@$claim_detail_val->facility_detail->facility_address->pay_zip5}} {{(@$claim_detail_val->facility_detail->facility_address->pay_zip5 != '')?'- '.@$claim_detail_val->facility_detail->facility_address->pay_zip5:''}}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-15">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                    <span class="bg-white med-orange padding-0-4 margin-l-10 font600"> Provider Info</span>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 table-responsive  m-b-m-10">
                                    <table class="popup-table-wo-border table">                    
                                        <tbody>
                                            <tr>
                                                <td class="font600">Rendering Prov</td>
                                                <td>
                                                    {{(@$claim_detail_val->rendering_provider->provider_name != '')?str_limit(@$claim_detail_val->rendering_provider->provider_name." ".@$claim_detail_val->rendering_provider->degrees->degree_name,85,'...'):'-- Nil --'}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="font600">Tax ID</td>
                                                <td>
												<?php
													if(@$claim_detail_val->rendering_provider->etin_type == 'TAX ID'){
														if(!empty($claim_detail_val->rendering_provider->etin_type_number))
															echo $claim_detail_val->rendering_provider->etin_type_number;
														else
															echo "-- Nil --";
													}else{
														echo "-- Nil --";
													}
												?>
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td class="font600">NPI</td>
                                                <td>
                                                    {{(@$claim_detail_val->rendering_provider->npi != '')?@$claim_detail_val->rendering_provider->npi:'-- Nil --'}}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 table-responsive m-b-m-10">
                                    <table class="popup-table-wo-border table">                    
                                        <tbody>
                                            <tr>
                                                <td class="font600">Referring Prov</td>
                                                <td>
                                                    {{(@$claim_detail_val->refering_provider->provider_name != '')?str_limit(@$claim_detail_val->refering_provider->provider_name." ".@$claim_detail_val->refering_provider->degrees->degree_name,85,'...'):'-- Nil --'}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="font600">NPI</td>
                                                <td>
                                                    {{(@$claim_detail_val->refering_provider->npi != '')?@$claim_detail_val->refering_provider->npi:'-- Nil --'}}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-15">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                    <span class="bg-white med-orange padding-0-4 margin-l-10 font600"> Insurance Info</span>
                                </div>

                                @if(@$claim_detail_val->patient->is_self_pay=='Yes' || count(@$claim_detail_val->patient->insured_detail)==0)
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ar-bottom-border">
                                    <div style="text-align: center;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive m-b-m-15">
                                        <table class="popup-table-wo-border table">                    
                                            <tbody>
                                                <tr>
                                                    <td class="font600">Self Pay</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @else
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive m-b-m-10">
                                    <table class="popup-table-wo-border table">                    
                                        <tbody>
                                            <tr>
                                                <td class="font600">Billed To</td>
                                                <td>
                                                    {!!@$insurance_detail!!}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <?php /*
                                  @foreach(@$claim_detail_val->patient->insured_detail as $patient_insurance_det)
                                  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ar-bottom-border">
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 table-responsive m-b-m-15">
                                  <table class="popup-table-wo-border table">
                                  <tbody>
                                  <tr>
                                  <td class="font600">{{@$patient_insurance_det->category}} Ins</td>
                                  <td>{!!App\Http\Helpers\Helpers::getInsuranceName(@$patient_insurance_det->insurance_details->id)!!}</td>
                                  </tr>
                                  <tr>
                                  <td class="font600">Policy ID</td>
                                  <td>{{@$patient_insurance_det->policy_id}}</td>
                                  </tr>
                                  </tbody>
                                  </table>
                                  </div>
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 table-responsive m-b-m-15">
                                  <table class="popup-table-wo-border table">
                                  <tbody>
                                  <tr>
                                  <td class="font600">Group Name</td>
                                  <td>{{(@$patient_insurance_det->group_name != '')?@$patient_insurance_det->group_name:'-- Nil --'}}</td>
                                  </tr>
                                  <tr>
                                  <td class="font600">Relationship</td>
                                  <td>{{@$patient_insurance_det->relationship}}</td>
                                  </tr>
                                  </tbody>
                                  </table>
                                  </div>
                                  </div>
                                  @endforeach
                                  <?php */ ?>
                                @endif                                                        
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box Ends Contact Details-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    <!-- Modal Payment Details ends here -->

    <div id="denial_details_{{$claim_detail_val->claim_number}}" class="modal fade in js-denied-popup-div">
        <div class="modal-md-650">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close js_denial_close" aria-label="Close" data_claim_number="{{$claim_detail_val->claim_number}}"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Denials : {{$claim_detail_val->claim_number}} </h4>
                </div>
                <div class="modal-body no-padding" >
                    {!! Form::open(['method'=>'POST','name'=>'claim_denial_form','id'=>'bootstrap-validator-denial_'.@$claim_detail_val->claim_number,'class'=>'js-bootstrap-validator-denial popupmedcubicsform']) !!}
                    <input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.denial") }}' />
                    <div class="box box-view no-shadow no-border"><!--  Box Starts -->
                        {!!Form::hidden('checkexist', null)!!}
                        <div class="box-body form-horizontal p-b-0">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-5">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                    <span class="bg-white med-orange margin-l-10 padding-0-4 font600"> Denial Info</span>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive margin-b-10">
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal">
                                        <div class="form-group">
                                            {!! Form::label('denial_date_lbl', 'Denial Date', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600 star']) !!}
                                            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2">
                                                <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('denial_date')"></i>
                                                {!! Form::text('denial_date', null,['placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control input-sm-header-billing dm-date js_claim_denial_date js_denial_frm_denail_date_'.@$claim_detail_val->claim_number,'autocomplete'=>'off' ,'id'=>'claim_denial_date-'.@$claim_detail_val->claim_number]) !!}
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('check_no_lbl', 'Check No', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600 star']) !!} 
                                            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2">
                                                <!-- MR-2911 : Change cheque limit -->
                                                {!! Form::text('check_no', null,['class'=>'form-control js-check-number input-sm-header-billing js_denial_frm_check_no_'.@$claim_detail_val->claim_number,'maxlength'=>'50', 'data-type' => "Insurance"]) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal">
                                        <div class="form-group">
                                            {!! Form::label('insurance_lbl', 'Insurance', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600 star']) !!}
                                            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 select2-white-popup">
                                                <?php $insurance_arr = App\Models\Patients\Patient::getARPatientInsurance($claim_detail_val->patient_id); ?>
                                                {!! Form::select('denial_insurance', array('' => '-- Select --')+ (array)@$patient_insurance,null,['class'=>'form-control select2 js_denial_insurance','id'=>'js_denial_frm_denial_insurance_'.@$claim_detail_val->claim_number]) !!}
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('next_responsibility', 'Next Responsibility', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!}
                                            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 select2-white-popup">
                                                <?php $insurance_arr = App\Models\Patients\Patient::getARPatientInsurance($claim_detail_val->patient_id); ?>
                                                {!! Form::select('next_responsibility', array('' => '-- Select --')+ (array)@$patient_insurance+array('0'=>'Patient'),null,['class'=>'form-control select2 js_denial_insurance','id'=>'js_denial_frm_denial_insurance_'.@$claim_detail_val->claim_number]) !!}
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('reference_lbl', 'Reference', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!}
                                            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2 ">
                                                {!! Form::text('reference', null, ['class'=>'form-control input-sm-header-billing js_denial_frm_reference_'.@$claim_detail_val->claim_number, 'maxlength'=>'25']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-header-view bg-white  margin-t-10">    
                            <div class="input-group input-group-sm">
                                <input name="denial_search_str" type="text" class="form-control" placeholder="Search Denial Codes">
                                <span class="input-group-btn">
                                    <button class="btn btn-flat btn-medgreen js_denial_search_btn" id="denial_search_btn-{{@$claim_detail_val->claim_number}}" type="button">Search</button>
                                </span>
                            </div>   
                        </div><!-- /.box-header -->

                        <div id="ar-denials-new_{{$claim_detail_val->claim_number}}" class="box-body table-responsive no-padding  modal-ins-scroll-50"><!-- Notes Box Body starts -->

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">
                                <ul class="no-padding line-height-26 no-bottom" style="list-style-type:none;" id="">
                                    <li class="denials">
                                        <table class="table-striped table table-borderless">
                                            <tbody class="denail_codes_list_part_{{$claim_detail_val->claim_number}}">                                                
                                            </tbody>
                                        </table>
                                    </li>	
                                </ul>
                            </div>

                        </div><!-- Notes box-body Ends-->
                        <div id="denial-form-footer_{{@$claim_detail_val->claim_number}}" class="box-header-view-white ar-bottom-border text-center">   
                            {!! Form::submit('Save', ['class'=>'btn btn-medcubics-small padding-2-8 js-denail-form-submit','id'=>'denail-form-submit_'.@$claim_detail_val->claim_number]) !!}
                            {!! Form::button('Cancel', ['class'=>'btn btn-medcubics-small padding-2-8' ,'data-dismiss'=>"modal"]) !!}
                        </div><!-- /.box-header -->

                    </div><!-- /.box-body --> 
                    {!! Form::close() !!}
                </div><!-- /.box Ends Contact Details-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

    <div id="assign_details_{{$claim_detail_val->claim_number}}" class="modal fade in">
        <div class="modal-md-650">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close " aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title "> Claim No: {{@$claim_detail_val->claim_number}}</h4>
                </div>
                <div class="modal-body no-padding" >
                    {!! Form::open(['method'=>'POST','name'=>'claim_assign_form','id'=>'bootstrap-validator-assign_'.@$claim_detail_val->claim_number,'class'=>'popupmedcubicsform']) !!}
                    <div class="box box-view no-shadow no-border"><!--  Box Starts -->
 
                        <div id="popupassign_details_{{$claim_detail_val->claim_number}}"  class="">
						@if(!empty($claim_detail_val->problem_list))
                            @foreach(@$claim_detail_val->problem_list as $problemlist)
                            <div class="margin-t-5 border-radius-4" style="padding:10px;">
                                <div class="border-bottom-dotted">
                                    <p class="no-bottom"><span class="med-green">{{@$problemlist->created_by->short_name}}</span>  <span class="pull-right med-orange">{{date("m/d/y", strtotime($problemlist->created_at))}}</span></p>
                                    <p class="no-bottom"><span class="med-gray-dark">{{@$problemlist->description}}</span></p>
                                    <p class="no-bottom">Assigned To : <span class="med-green font600">{{@$problemlist->user->name}}</span></p>
                                    <p class="no-bottom">Followup date : <span class="med-orange font600">{{date("m/d/y", strtotime($problemlist->fllowup_date))}}</span></p>
                                    <p>  
                                        Status : <span class="{{@$problemlist->status}}">{{@$problemlist->status}}</span> | Priority : <span class="{{@$problemlist->priority}}" data-toggle="tooltip" data-original-title="{{@$problemlist->priority}}">
										@if($problemlist->priority == 'High')
										<i class="fa fa-arrow-up" aria-hidden="true"></i>
										@elseif($problemlist->priority == 'Low')
										<i class="fa fa-arrow-down" aria-hidden="true"></i>
										@else
										<i class="fa fa-arrows-h" aria-hidden="true"></i>
										@endif</span>
                                    </p>
                                </div>
                            </div>
                            @endforeach
						@else
							<div class="margin-t-5 border-radius-4" style="padding:10px;">
							No workbench found
							</div>
						@endif


                        </div>
                        <input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.assign") }}' />
						@if(@$problemlist->assign_user_id == Auth::user ()->id || @$problemlist->created_by->id == Auth::user ()->id)
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                            <p class="padding-4 med-orange bg-aqua">Create New </p>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-5" ><!-- Left side Content Starts -->
                            <div class="form-horizontal"><!-- Box Starts -->
                                <div class="form-group-billing">                                                     
                                    <div class="col-lg-12 col-md-12 col-sm-8 col-xs-10"> 
                                        {!! Form::textarea('indivual_description',null,['class'=>'form-control input-sm-modal-billing problem_desc','placeholder'=>'Description','id'=>'js_indivual_description_'.@$claim_detail_val->claim_number]) !!}
                                    </div>
                                </div> 

                                <div class="form-group-billing">					         
                                    <div class="col-lg-3 col-md-3 col-sm-5 col-xs-6 select2-white-popup">  
                                        {!! Form::select('indivual_assign_user_id', [''=>'-- Assigned To --']+(array)$user_list,null,['class'=>'select2 form-control js_indivual_assign_to','id'=>'js_indivual_assign_user_id_'.@$claim_detail_val->claim_number]) !!}
                                    </div>                                    
                                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-4"> 
                                        <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('indivual_followup_date')"></i>
                                        {!! Form::text('indivual_followup_date',null,['placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control input-sm-modal-billing form-cursor dm-date js_follow_up_date','id'=>'js_indivual_followup_date_'.@$claim_detail_val->claim_number]) !!}
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-5 col-xs-6 select2-white-popup">  
                                        {!! Form::select('indivual_status', [''=>'-- Status --','Assigned' => 'Assigned','Inprocess' => 'Inprocess','Completed'=>'Completed'],null,['class'=>'select2 form-control js_indivual_status','id'=>'js_indivual_status_'.@$claim_detail_val->claim_number]) !!}
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-4 select2-white-popup"> 
                                        {!! Form::select('indivual_priority', [''=>'-- Priority --','High' => 'High','Moderate' => 'Moderate','Low'=>'Low'],null,['class'=>'select2 form-control js_indivual_priority','id'=>'js_indivual_priority_'.@$claim_detail_val->claim_number]) !!}
                                    </div>
                                </div>   
                            </div>
                        </div>
                        <div id="assign-form-footer_{{@$claim_detail_val->claim_number}}" class="modal-footer m-b-m-15">
                            {!! Form::submit('Save', ['class'=>'btn btn-medcubics-small padding-2-8 js-assign-form-submit','id'=>'assign-form-submit_'.@$claim_detail_val->claim_number]) !!}
                            <button class="btn btn-medcubics-small" data-dismiss="modal" type="button">Cancel</button>
                        </div>
					@endif
                    </div><!-- /.box-body -->   
                    {!! Form::close() !!}
                </div><!-- /.box Ends Contact Details-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
	
	<div id="view_ar_document_{{$claim_detail_val->claim_number}}" class="modal fade in">
        <div class="modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{ $claim_detail_val->claim_number }} - Claim Documents </h4>
                </div>
                <div class="modal-body no-padding" >
					<div class="box-body">
					@if(!empty($claim_detail_val->documents))
					<table id="documents" class="table table-bordered table-striped table-collapse" data-category="ssn">
						<thead>
							<tr>
								<th>Created On</th> 
								<th>Title</th>
								<th>Category</th>
								<th>User</th>
								<th>Assigned To</th>
								<th>Status</th>
								<th>Follow up Date</th>
								<th></th>
								<th></th>
								<th class="td-c-8"></th>
							</tr>
						</thead>
						<tbody>
							@foreach($claim_detail_val->documents as $list)
							 <tr data-toggle="modal" data-target="#show_document_assigned_list" class="cur-pointer js_show_document_assigned_list" data-document-id="{{ @$list->id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$list->id.'/show')}}" data-document-show="js_update_row_{{ @$list->id }}">
								<td>{{ App\Http\Helpers\Helpers::dateFormat($list->created_at,'date')}}</td>
								<td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
								<td>{{ @$list->document_categories->category_value }}</td>
								<td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
								<td class="jsuser">{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
								<td class="jsstatus font600"><span class="{{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>
								<td class="jsfollowup">
									<?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
									@if(date("m/d/y") == $fllowup_date)
									<span class="med-orange">{{$fllowup_date}}</span>
									@elseif(date("m/d/y") >= $fllowup_date)
									<span class="med-red">{{$fllowup_date}}</span>
									@else
									<span class="med-gray">{{$fllowup_date}}</span>
									@endif
								</td>
								<td>{{ $list->page }}</td>
								<td class="jspriority">
									<span class="{{@$list->document_followup->priority}}">
										@if(@$list->document_followup->priority == 'High')
										<span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
										@elseif(@$list->document_followup->priority == 'Low')
										<span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
										@elseif(@$list->document_followup->priority == 'Moderate')
										<span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
										@endif
									</span>
								</td>
								<td><span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')"><i class="fa  {{Config::get('cssconfigs.common.view')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="View"></i></a></span></td>
							 </tr>   
							@endforeach
						</tbody>
					</table>
					@else
						No Documents Found 
					@endif
					</div>
                </div><!-- /.box Ends Contact Details-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
	
	<!-- Show Problem list start-->
	<div id="show_document_assigned_list" class="modal fade in js_model_show_document_assigned_list"></div><!-- /.modal-dialog -->
	<!-- Show Problem list end-->
	
	<!-- Showing Followup history in the popup using ajax -->
	<div id="show_followup_history" class="modal fade in">
		<div class="modal-md-650">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"> Claim No : CHR401</h4>
				</div>
				<div class="modal-body no-padding" >
				</div>
			</div>
		</div>
	</div>

@if(@$tab_type!='indivual')
</div>
@endif
@endforeach

@endif
<?php
} catch (Exception $e){
	//echo "Error ".$e->getMessage();
}
?>
@push('view.scripts')
<script type="text/javascript">
    $('#authorization').attr('autocomplete','off');
   	<?php if(isset($get_default_timezone)){ ?> 
        var get_default_timezone = '<?php echo $get_default_timezone; ?>';
    <?php }else{?>
        var get_default_timezone = '';
   	<?php }?>
</script>
@endpush