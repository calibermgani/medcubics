@if(!empty($claim_detail))
<?php
    $claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claim_detail->id, 'encode'); 
    $url = url('patients/payment/popuppayment/'.$claim_id.'/mainpopup');
?>
<div class="box box-view no-shadow no-border"><!--  Box Starts -->
    <div class="box-body form-horizontal">
        <?php //dd($claim_detail)?>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-5 margin-b-10">
            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 no-padding">
                <h6>Status : <span class="@if(@$claim_detail->status == 'Ready') ready-to-submit @elseif(@$claim_detail->status == 'Partial Paid') c-ppaid @else {{ @$claim_detail->status}} @endif"> {{@$claim_detail->status}}</span></h6>                                
            </div>
            <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12 p-r-0">                              
                <h6 class="">Charge Amt : <span class="med-orange">{{App\Http\Helpers\Helpers::priceFormat($claim_detail->total_charge)}}</span>&emsp;  Paid : <span class="med-orange"> {!!App\Http\Helpers\Helpers::priceFormat($claim_detail->total_paid)!!}</span>&emsp; Balance : <span class="med-orange"> {!!App\Http\Helpers\Helpers::priceFormat($claim_detail->balance_amt)!!}</span>
                    <?php /* <a href="javascript:void(0);" class="js-print-popup pull-right hidden-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a>  */ ?>
                    <a class="pull-right" href="{{ url($url.'/pdf') }}" data-url="{{ url($url.'/pdf') }}" data-option = "pdf" >
                        <i class="fa fa-file-pdf-o" data-placement="bottom" data-toggle="tooltip" data-original-title="Download PDF"></i>
                    </a>
                </h6>
            </div>
        </div>     

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding tabs-border no-b-b">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                <span class="bg-white med-orange margin-l-10 font13 padding-0-4 font600"> Claim Details</span>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding " >                                                                
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive margin-b-15">
                    <table class="popup-table-wo-border table margin-b-1">                    
                        <tbody>
                            <tr>
                                <td class="font600" style="width:50%">DOS</td>
                                <td><span class="bg-date"> {{App\Http\Helpers\Helpers::dateFormat($claim_detail->date_of_service,'dob')}}</span></td> 
                            </tr>                            
                            <tr>
                                <td class="font600">First Submission </td>
                                <?php if (!empty($claim_detail->submited_date) && $claim_detail->submited_date != "0000-00-00" && $claim_detail->submited_date != "1970-01-01") { ?>
                                    <td><span class="bg-date">{{App\Http\Helpers\Helpers::dateFormat($claim_detail->submited_date,'date')}}</span></td>
                                <?php } else { ?>
                                    <td><span class="">- Nil -</span></td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td class="font600">Last Submission</td>
                                <?php if (!empty($claim_detail->last_submited_date) && $claim_detail->last_submited_date != "0000-00-00") { ?>
                                    <td><span class="bg-date">{{App\Http\Helpers\Helpers::dateFormat($claim_detail->last_submited_date,'date')}}</span></td>
                                <?php } else { ?>
                                    <td><span class="">- Nil -</span></td>
                                <?php } ?>                                
                            </tr>  
                            <tr>
                                <td class="font600">Claim Type</td>
                                <td>{{ App\Models\Payments\ClaimInfoV1::getPayerIdbilledToInsurance(@$claim_detail->insurance_id)}}</td>
                            </tr>                                  
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 table-responsive tab-l-b-1 p-l-0  md-display tabs-lightgreen-border">
                    <table class="popup-table-wo-border table margin-b-1">                    
                        <tbody>      
                            <tr>
                                <td class="font600">Billed To</td>
                                <?php
									if (!empty($claim_detail->insurance_details)) {
										$insurance_detail = App\Http\Helpers\Helpers::getInsuranceName(@$claim_detail->insurance_details->id);
									} else {
										$insurance_detail = "Self";
									}
                                ?>
                                <td>{!!$insurance_detail!!}</td>                                              
                            </tr>
                            <tr>                                               
                                <td class="font600">Rendering Provider</td>
                                <td>{{@$claim_detail->rendering_provider->provider_name.' '.@$claim_detail->rendering_provider->degrees->degree_name}} </td>
                            </tr>
                            <tr>
                                <td class="font600">Billing Provider</td>
                                <td>{{@$claim_detail->billing_provider->provider_name.' '.@$claim_detail->billing_provider->degrees->degree_name}} </td>
                            </tr>
                            <tr>
                                <td class="font600">Facility</td>
                                <td>{{@$claim_detail->facility_detail->facility_name}} </td>                                              
                            </tr>                            
                        </tbody>
                    </table>
                </div>                                
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 table-responsive p-l-0 tab-l-b-1  md-display tabs-lightgreen-border">
                    <table class="popup-table-wo-border table margin-b-1">                    
                        <tbody>
                            <tr>
                                <td class="font600">Wallet Balance</td>
                                <?php
									$credit_balance = App\Models\Patients\Patient::getPatienttabData($claim_detail->patient->id);
									$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claim_detail->patient->id, 'encode');
									$insurance_refund = '';
									$patient_refund = '';
									$ins_overpayment = App\Models\Payments\ClaimInfoV1::InsuranceOverPayment($claim_detail->id);
									$insurance_refund =  App\Models\Payments\ClaimInfoV1::getRefund($claim_detail->id, 'insurance_paid_amt');
									$patient_refund = @App\Models\Payments\ClaimInfoV1::getRefund($claim_detail->id, 'patient_paid_amt');
                                ?>
                                <td class="med-orange font600 text-right"> 
                                    {{($credit_balance['wallet_balance'] == 0)?"0.00":App\Http\Helpers\Helpers::priceFormat($credit_balance['wallet_balance'])}}
                                </td>                                              
                            </tr>
							
                            <tr>
                                <td class="font600">Ins Overpayment</td>                                
                                <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat($ins_overpayment)!!}</td>
                            </tr>
                            <tr>
                                <td class="font600">Insurance Refund</td>
                                <td class="text-right">{{($insurance_refund == 0)?"0.00":App\Http\Helpers\Helpers::priceFormat($insurance_refund)}}</td>
                            </tr>
                            <tr>
                                <td class="font600">Patient Refund</td>
                                <td class="text-right">{{($patient_refund == 0)?"0.00":App\Http\Helpers\Helpers::priceFormat($patient_refund)}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>                                
            </div>
        </div>
		
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive yes-border tabs-border p-b-15" >
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10 margin-b-6 pr-m-t-10">
                <span class="bg-white med-orange padding-0-4 font600">Claim Transaction</span>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding claim-transaction-scroll">
                <table class="popup-table-wo-border table table-responsive margin-b-5">                    
                    <thead>
                        <tr> 
							<?php /*
                            <th></th>
							*/ ?>
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
							<?php /*
                            <td><a href="#" class="font600" data-prod-cat="{{$j}}"><span style="position: absolute"> &emsp;</span></a></td>
							 */ ?>
                            <td> {{ App\Http\Helpers\Helpers::dateFormat(@$txn->txn_date) }}</td>
                            <td>
								{!! $txn->responsibility !!}
								@if(isset($txn->resp_category) && $txn->resp_category != '')
									<span class="{{@$txn->resp_cat_class}}">{{ substr(@$txn->resp_category, 0, 1) }}</span>
								@endif
							</td>                            
                            <td>{!! nl2br($txn->description) !!}</td> 
                            <td>{!! $txn->payment_type!!}</td>
                            <td class="text-right">{!! $txn->charges!!}</td>                                           
                            <td class="text-right"> {!! $txn->payments!!}</td>                                            
                            <td class="text-right">{!! $txn->adjustment!!}</td>
                            <td class="text-right">{!! $txn->pat_balance!!}</td>
                            <td class="text-right">{!! $txn->ins_balance!!}</td>
                        </tr>
                        @endforeach
                        <tr style="background: #f3fffe"> 
                            <td colspan="4"></td>
                            <td class="med-green-wo-span font600 text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim_detail->total_charge)!!}</td>
                            <td class="med-green-wo-span font600 text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim_detail->total_paid)!!}</td>
                            <td class="med-green-wo-span font600 text-right">
							<?php /* 
								App\Http\Helpers\Helpers::getCalculatedAdjustment(@$claim_detail->totalAdjustment, @$claim_detail->withheld )
							*/?>
							{!!App\Http\Helpers\Helpers::priceFormat(@$claim_detail->totalAdjustment)!!}</td>
                            <td class="med-green-wo-span font600 text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim_detail->patient_due)!!}</td>
                            <td class="med-green-wo-span font600 text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim_detail->insurance_due)!!}</td> 
                        </tr>               
                        @else
                    <td colspan="8"><p class="text-center no-bottom med-gray margin-t-10">No payment has been done</p></td>
                    @endif                                
                    </tbody>                           
                </table>
            </div>
        </div>     
        
		<div id = "view_transaction" class=" out col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding collapse"><!-- Inner Content for full width Starts -->
			<div class="box-body-block no-padding"><!--Background color for Inner Content Starts -->
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive tabs-border no-b-t">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10 pr-m-t-10">
						<span class="bg-white med-orange padding-0-4 font600"> CPT Transaction</span>
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
									<td>{{ App\Http\Helpers\Helpers::dateFormat(@$lTxn->txn_date)}}</td>
									<td>{!! @$lTxn->responsibility !!}
										@if(isset($lTxn->resp_category) && $lTxn->resp_category != '')
											<span class="{{ @$lTxn->resp_bg_class }}">{{ substr(@$lTxn->resp_category, 0, 1) }}</span>
										@endif
									</td>
									<td>{!! nl2br(@$lTxn->description) !!}</td>
									<td class="text-right">{!! @$fTxn->charges !!}</td>
									<td class="text-right">{!! @$lTxn->payments !!}</td>
									<td class="text-right">{!! @$lTxn->adjustment!!}</td>
									<td class="text-right">{!! @$lTxn->pat_balance!!}</td>
									<td class="text-right">{!! @$lTxn->ins_balance!!}</td>
								</tr>
								<!-- Dummy block end -->
							@endif
							@foreach($cpttx as $ctxnIndex => $ctxn)
								<?php
									$cpt_transaction_count = count($cpttx);
									$payment= 0;
								?>														
								<tr class="blk_{{$j}} med-l-green-bg" {{$style}}>
									<td>
										@if($i == 0)
											<a href="#" class="txtoggler font600 {{ $toggler }}" data-prod-cat="{{$j}}"><span style="position: absolute"> &emsp;</span></a>
										@endif
									</td>
									<td>@if($i == 0){{@$ctxn->cpt_code}}@endif</td>
									<td>{{App\Http\Helpers\Helpers::dateFormat($ctxn->txn_date)}}</td>
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
						
						@if($payment)
						<td colspan="10">
							<p class="text-center no-bottom med-gray margin-t-10">No payment has been done</p>
						</td>
						@endif
						<?php $patient_paid_amt = App\Http\Helpers\Helpers::getClaimPatPaidAmt($claim_detail->id);?>
						@if($patient_paid_amt != 0)
						<p class="no-bottom margin-t-15 font600"><span class="med-green">Patient Paid : </span> <span class="med-orange">{{$patient_paid_amt}}</span></p>
						@endif
						</tbody>
					</table>
                    </div>
				</div>
			</div><!-- Inner Content for full width Ends -->
        </div><!--Background color for Inner Content Ends -->
         
        <?php
			$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claim_detail->patient->id, 'encode');
			$claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claim_detail->id, 'encode');
        ?>       
        @if(!empty($patient_id) )
        <div class="payment-links pull-right margin-t-10 hidden-print">
            <ul class="nav nav-pills">
				
                <li><a href="#view_transaction"data-toggle = "collapse" > <i class="fa {{Config::get('cssconfigs.charges.claim_preview')}}"></i> View Transaction</a></li>  
                <?php 
                 $hold_data = App\Http\Helpers\Helpers::checkForPaymnetRequirement($claim_detail->id,$claim_detail->status, 'Patient');
                ?>
                <?php $url = @$_SERVER['HTTP_REFERER']; ?>            
                @if(strpos($url, 'patientpayment') == FALSE && strpos($url, 'claims') == FALSE && strpos($url, 'armanagement') == FALSE)
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="">
                        <i class="fa {{Config::get('cssconfigs.payments.payments')}}"></i> Post Payments <b class="caret"></b>
                    </a>                         
                    <ul class="dropdown-menu"> 
                        <?php $type_patient = ""; $type_insurance = ""; ?>
                        @if(isset($type) && $type == "mainpopup")
                        <?php $type_patient = "Patient"; $type_insurance = "Insurance";  ?>
                        @endif
                        <li>
							@if($hold_data)                            
								<a href= "#" data-payment-info = "Post Patient Payment" data-toggle="modal" data-payment-type = "{{$type_patient}}" data-target="#post_payments" data-url = "{{url('patients/'.$patient_id.'/paymentinsurance/patient/'.$claim_id)}}" class=" form-cursor js-modalboxopen font600 p-r-10 p-l-10"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}}"></i> Patient Payments</a>
                            @else
								<a class="font600"  href ="#" data-toggle="tooltip" data-original-title="Payment can't be done due to insufficient info"> <i class="fa fa-money"></i> Patient Payments</a>
                            @endif 
                        </li>
                        <li>
                            @if($claim_detail->status == "Hold")
								<a class="font600"  href ="#" data-toggle="tooltip" data-original-title="Payment can't be done for hold claims"> <i class="fa fa-money"></i> Insurance Payments</a>
                            @else
								<a href= "#" data-payment-info = "Post Insurance Payment" data-toggle="modal" data-payment-type = "{{$type_insurance}}" data-target="#post_payments" data-url = "{{url('patients/'.$patient_id.'/paymentinsurance/insurance/'.$claim_id)}}"class=" form-cursor js-modalboxopen font600 p-r-10 p-l-10"><i class="fa {{Config::get('cssconfigs.common.insurance')}}"></i> Insurance Payments</a> 
                        @endif
                        </li>                                                          
                    </ul>                
                </li> 
                @endif 				
                <li>
                    <a class="someelem" data-id="val" id="someelem{{'val'}}" data-toggle = "collapse" data-target = "#view_cob" > <i class="fa {{Config::get('cssconfigs.Practicesmaster.insurance')}}"></i> <span >COB</span></a>
                    <span class="p-b-0 p-l-0">
						<?php $patient_insurances = App\Models\Patients\Patient::getPopupPatientInsurance($claim_detail->patient->id); ?>            
                        <div class="on-hover-content js-tooltip_val" style="display:none;">
                            <span class="med-orange font600"> Insurances</span> 
                            <p class="no-bottom hover-color">
                                @if(count($patient_insurances) > 0)                       
									@foreach($patient_insurances as $patient_insurance)
										<span class="font600">{{@$patient_insurance->category}}:</span> {!!App\Http\Helpers\Helpers::getInsuranceName(@$patient_insurance->insurance_details->id)!!}<br>
									@endforeach 
                                @else
									No insurances available
                                @endif                  
                            </p>
                        </div>
                    </span>
                </li>

                <li class="dropdown">
                    @if(empty($attachments))
						<span class="med-gray margin-l-10">
							<i class="fa {{Config::get('cssconfigs.Practicesmaster.patientstatement')}}"></i> View ERA
						</span>
                    @else
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">
							<i class="fa {{Config::get('cssconfigs.common.attachment')}}"></i> View ERA <b class="caret"></b>
						</a>    
                    @endif
                    <?php// dd($attachments);?>     
                    <ul class="dropdown-menu"> 

                        @if(!empty($attachments))
                        @foreach($attachments as $attach)
                        <?php                      
							$url = url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($attach->attachment_detail->type_id,'encode').'/'.$attach->attachment_detail->document_type.'/'.$attach->attachment_detail->filename);
                        ?>                       
                        <li><a href="{{$url}}" target = "_blank"><i class="fa {{Config::get('cssconfigs.charges.claim_preview')}}"></i> Img Attached</a></li>
                        @endforeach
                        @endif
                    </ul>                
                </li>       
            </ul>
        </div>     
        @endif
    </div>
</div><!-- /.box-body -->    
<div id="post_payments" class="modal fade in">
    <div class="modal-md-800">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close_popup" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Posting</h4>
            </div>
            <div class="modal-body no-padding" >
            </div><!-- /.box Ends Contact Details-->
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->    
@endif