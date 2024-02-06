<?php
	if (@$payment_details->pmt_method  == "Patient") {
		$link = 'patient';
		$popup_class = "";
		$insurance_name = App\Http\Helpers\Helpers::getNameformat($payment_details->patient->last_name, $payment_details->patient->first_name, $payment_details->patient->middle_name);
	} else {
		$link = 'insurance';
		$popup_class = "js-show-patientsearch";
		$insurance_name = @$payment_details->insurancedetail->insurance_name;
		if($insurance_name == "" && isset($payment_details->insurance_id) && $payment_details->insurance_id != 0){		
			$insurance_name = App\Http\Helpers\Helpers::getInsuranceFullName(@$payment_details->insurance_id);
		}
	}
	$created_date = (!empty($payment_details->created_at)) ? App\Http\Helpers\Helpers::timezone($payment_details->created_at,'m/d/y') : '';
	$check_date = (!empty($payment_details->check_date)) ? App\Http\Helpers\Helpers::checkAndDisplayDateInInput($payment_details->check_date, '','-') : '-Nil-';
	$payment_detail_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$payment_details->id, 'encode');
	$balance_amt = @$payment_details->balance;
	$paymode = @$payment_details->pmt_mode ;
	if (@$payment_details->pmt_mode  == "Check" || @$payment_details->pmt_mode  == "EFT" || @$payment_details->pmt_mode  == "Money Order" ) {
		$check_no = $payment_details->check_no."/".$paymode;
	} elseif (@$payment_details->pmt_mode  == "Cash") {
		$check_no = "Cash";
	} elseif (@$payment_details->pmt_mode  == "Credit") {
		$check_no = ((isset($payment_details->card_last_4)) ? @$payment_details->card_last_4 : ' - ')."/".$paymode;
		$check_date = '-Nil-';
	}
?>
<span data-patient ="@if($payment_details->pmt_type == 'Payment' && @$payment_details->pmt_method == 'Patient'){{'Creditbalance'}}@endif"  class="js_show_content" style="display: none;">{{@$payment_details->pmt_type}}</span>
<div class="box box-view no-shadow no-border"><!--  Box Starts -->
    <div class="box-body form-horizontal">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0">
            <?php $url=@$_SERVER['HTTP_REFERER']; ?>	            
            @if(strpos($url, 'patientpayment') == FALSE)  
            <p class="margin-t-m-5 pull-right margin-b-5">
                           
                <a href = "#" data-toggle="modal" data-target="#choose_claim" data-url = "{{url('payments/paymentinsurance/'.$link.'/'.$payment_detail_id)}}"
                   class="{{$popup_class}} claimdetail form-cursor font600 p-l-10"><i class="fa {{Config::get('cssconfigs.common.plus')}}"></i> New Claim</a> 
				<!-- For credit card payment edit option disabled start -->	
				
					<span class="margin-r-05 margin-l-5">|</span> 
					<td><a href = "#" data-toggle="modal" data-url = "{{url('payments/editcheck/'.$payment_detail_id)}}" class = "js-modalboxopen font600" data-target="#payment_editpopup"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a></td>           
				
				<!-- For credit card payment edit option disabled end -->	
				@if($payment_details->amt_used == 0 && empty($payment_details->payment_claim_detail)) 
					<span class="margin-r-05 margin-l-5">|</span>  
					<a href = "#" class="claimdetail form-cursor font600 js_void_claim" data-url = "{{url('payments/delete/'.$payment_detail_id)}}"><i class="fa {{Config::get('cssconfigs.common.delete')}}"></i> Delete </a>
				@elseif($payment_details->amt_used == 0 && !empty($payment_details->payment_claim_detail) && $payment_details->pmt_method  != "Patient")
					<span class="margin-r-05 margin-l-5">|</span>  
					<a href = "#" class="claimdetail form-cursor font600 js_void_claim" data-url = "{{url('payments/delete/'.$payment_detail_id.'/void')}}"><i class="fa {{Config::get('cssconfigs.common.delete')}}"></i> Void Check</a>
				@elseif(!empty($payment_details->payment_claim_detail) && $payment_details->pmt_method  == "Patient")
					<span class="margin-r-05 margin-l-5">|</span>  
					<a href = "#" class="claimdetail form-cursor font600 js_void_claim" data-url = "{{url('payments/voidpatientpayment/'.$payment_detail_id)}}"><i class="fa {{Config::get('cssconfigs.common.delete')}}"></i> Delete</a>	
				@elseif($payment_details->pmt_method  == "Patient" && $payment_details->source  == "refundwallet")
					<span class="margin-r-05 margin-l-5">|</span>  
					<a href = "#" class="claimdetail form-cursor font600 js_void_claim" data-url = "{{url('payments/voidpatientpayment/'.$payment_detail_id)}}"><i class="fa {{Config::get('cssconfigs.common.delete')}}"></i> Delete</a>				
				@elseif(!empty($payment_details->payment_claim_detail) && $payment_details->pmt_method  == "Insurance" && $payment_details->pmt_type == "Refund")
					<!-- Show Delete Link for Insurance Refund Payment -->
					<span class="margin-r-05 margin-l-5">|</span>  
					<a href="#" class="claimdetail form-cursor font600 js_void_claim" data-url = "{{url('payments/delete/'.$payment_detail_id)}}"><i class="fa {{Config::get('cssconfigs.common.delete')}}"></i> Delete</a>				
				@endif         
				
				@if($payment_details->pmt_method  == "Patient" && $payment_details->pmt_type== "Payment" && !empty($payment_details->payment_claim_detail) && $payment_details->amt_used != 0)
				<!-- <span class="margin-l-5 margin-r-05">|</span>    
				<a href = "#" data-toggle="modal" data-target="#choose_claim" data-url = "{{url('/payments/getpaidclaimdata/'.$payment_detail_id)}}" class="{{$popup_class}} claimdetail form-cursor font600"><i class="fa {{Config::get('cssconfigs.payments.payments')}}"></i> Payment Takeback</a>
				 -->  
				@endif         

				<?php $url = url($payment_detail_id . '/document/get/patients' . '/' . @$payment_details->attachment_detail->filename); ?>
				@if(!empty($payment_details->attachment_detail) && !empty(@$payment_details->attachment_detail->filename))
				<span class="margin-r-05 margin-l-5">|</span>  
				<td><a href = "{{$url}}" target = "_blank" class = "js_change_attchurl font600"><i class="fa {{Config::get('cssconfigs.common.attachment')}}"></i> Attachment</a></td>
				@endif
            </p>
            @endif 
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">            
            <div class="box box-info no-shadow tabs-border">
                <div class="box-body border-radius-4">                    
                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 billing-create-tab-b margin-t-m-8 m-b-m-8">               
                        <p class=""><span class="med-green font600">Payer : </span><span class="med-orange font600">{{$insurance_name}}</span></p>                                               
                        <p class="space-m-t-7"><span class="med-green font600">Posted Date  </span> <span class="bg-date pull-right">{{$created_date}}</span></p>     
                        <!--<p class="space-m-t-7"><span class="med-green font600">Check No/Mode  </span> <span class="pull-right">{{$check_no." / ".$paymode }}</span></p>-->
                        <p class="space-m-t-7"><span class="med-green font600">Check No/Mode  </span>
                        	<?php
                        		$firstArray = explode('/', $check_no);
	                        	$title = @$firstArray[0];
	                        	$secondArray = (isset($firstArray[1]) && $firstArray[1]!='')?@$firstArray[1]:'-Nil-';
	                        	$array = str_split(@$firstArray[0]);
	                        	$convertArray = array_slice($array, 0, 14);
	                        	$convertString = implode('', $convertArray); 
                        	if(count($array) <= 15 ){
                        	?>
                        		<span class="pull-right" title="{{$title}}">
								{{$convertString}}/{{$secondArray}}</span>
							<?php	
                        	}else if(count($array) > 15 ){
                        	?>
                        		<span class="pull-right" title="{{$title}}">
								{{$convertString}}.../{{$secondArray}}</span>	
							<?php
                        	} ?>
                        </p>
                        <p class="space-m-t-7"><span class="med-green font600">Check Date  </span> <span class="@if($check_date !='-Nil-') bg-date @endif pull-right">{{$check_date}}</span></p>
                    </div>
                    @if(!empty(@$payment_details->providerdetail))                    
                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 billing-create-tab-b m-b-m-8 margin-t-m-8 tab-l-b-2 md-display">               
                        <p class=" "><span class="med-orange font600">Pay to Address </span></p>                                              
                        <p class="space-m-t-7"><span class="med-green">Address :</span><span> {{@$payment_details->providerdetail->address_1}}</span></p>     
                        <p class="space-m-t-7"><span class="med-green">City :</span><span> {{@$payment_details->providerdetail->city}} {!! HTML::decode( !empty($payment_details->providerdetail->state)? "- <span class='bg-state'>".@$payment_details->providerdetail->state.'</span>':"")!!}</span></p>
                        <p class="space-m-t-7 "><span class="med-green">Zip Code :</span><span> {{@$payment_details->providerdetail->zipcode5}} {{ !empty($payment_details->providerdetail->zipcode4)?'- '.$payment_details->providerdetail->zipcode4:""}}</span></p>
                    </div>
                    @endif                    
                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 m-b-m-8  tab-l-b-3 margin-t-m-8 md-display">
                        <p ><span class="med-green font600">Check Amount </span><span class="pull-right font600 js-check-amt"> {!!App\Http\Helpers\Helpers::priceFormat(@$payment_details->pmt_amt)!!}</span></p>
                        <p class="space-m-t-7"><span class="med-green font600">Posted Amount </span> <span class="pull-right font600 js-amt-used"> {!!App\Http\Helpers\Helpers::priceFormat(@$payment_details->amt_used)!!}</span></p>
                        <p class="space-m-t-7"><span class="med-green font600">Un Posted </span> <span class="pull-right font600 js-unapplied-amt">{!!App\Http\Helpers\Helpers::priceFormat(@$payment_details->balance)!!}</span></p>
                        <p class="space-m-t-7"><span class="med-green font600">Check Type </span> <span class="pull-right font600 js-check-type">{!!@$payment_details->pmt_type!!}</span></p>
                    </div>                    
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-b-10">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                <span class="bg-white med-orange padding-0-4 margin-l-10 font600"> Transaction Details</span>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive mobile-scroll margin-t-10">
                <table class="popup-table-wo-border table margin-b-5 mobile-width"> 
                    <thead>
                        <tr>  
                            <th>DOS</th>
                            <th>Claim No</th>
                            <th>Patient Name</th>
                            <th>CPT</th>                               
                            <th class="text-right">Billed</th>
                            <th class="text-right">Allowed</th>
                            <th class="text-right">Ded</th>
                            <th class="text-right">Co-Pay</th>
                            <th class="text-right">Co-Ins</th>
                            <th class="text-right">Adjustments</th>
                            <th class="text-right">Writeoff</th>
                            <th class="text-right">Paid</th>
                            <!--
							<th class="text-right">Bal</th>
							-->
                        </tr>
                    </thead>
                    <tbody> 
						<?php $i = 0; ?>
                        @if(!empty($payment_details->payment_claim_detail))    
                        @foreach($payment_details->payment_claim_detail as $payment_claim_detail) 
                        @if(!empty($payment_claim_detail->claim))        
							<?php	
								$claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($payment_claim_detail->claim->id, 'encode'); 
								$url = url('patients/payment/popuppayment/'.$claim_id.'/mainpopup'); 
								//$getpayment_data = App\Http\Helpers\Helpers::getClaimPaymentData(@$payment_claim_detail->claim->id); 
								$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$payment_claim_detail->claim->patient->id, 'encode'); 
								$i +=1;
							?>
							<tr>																		
								<td> 
									<a href="#" claim_number = "{{$payment_claim_detail->claim->claim_number}}" data-payment-info = "{{'Claim No:'.$payment_claim_detail->claim->claim_number}}" data-toggle="modal" data-target="#js-model-payment" data-url="{{$url}}" class="js-modalboxopen">{{@date('m/d/Y',strtotime($payment_claim_detail->claim->date_of_service))}}</a>
								</td>
								<td>{{@$payment_claim_detail->claim->claim_number}}</td>
								<?php 
									$patient_name = App\Http\Helpers\Helpers::getNameformat(@$payment_claim_detail->claim->patient->last_name, @$payment_claim_detail->claim->patient->first_name, @$payment_claim_detail->claim->patient->middle_name); 
								?>
								<td>
									<span>                               
										<a href="{{ url('patients/'.@$patient_id.'/ledger') }}" target="_blank"> <span class="someelem" data-id="{{@$payment_claim_detail->claim->patient->id}}" id="someelem{{@$payment_claim_detail->claim->patient->id}}">@if(@$payment_claim_detail->claim->patient->title){{ @$payment_claim_detail->claim->patient->title }}. @endif{{ str_limit(@$patient_name,25,'...') }}</span></a> 
									</span>
									<div class="on-hover-content js-tooltip_{{$payment_claim_detail->claim->patient->id}}" style="display:none;">
										<span class="med-orange font600">@if(@$payment_claim_detail->claim->patient->title){{ @$payment_claim_detail->claim->patient->title }}. @endif{{ @$patient_name }}</span> 
										<p class="no-bottom hover-color">
											<span class="font600">Acc No :</span> {{ @$payment_claim_detail->claim->patient->account_no }}
											<br>
											@if(@$payment_claim_detail->claim->patient->dob !='' && @$payment_claim_detail->claim->patient->dob != "0000-00-00" && @$payment_claim_detail->claim->patient->dob != "1901-01-01" )
											<span class="font600">DOB :</span>{{ App\Http\Helpers\Helpers::dateFormat(@$payment_claim_detail->claim->patient->dob,'claimdate') }}
											<span class="font600">Age :</span> {{ App\Http\Helpers\Helpers::dob_age(@$payment_claim_detail->claim->patient->dob) }}
											@endif
											<span class="font600">Gender :</span> {{ $payment_claim_detail->claim->patient->gender }}<br>
											<span class="font600">Ins :</span> {{ App\Models\Patients\PatientInsurance::CheckAndReturnInsuranceName(@$payment_claim_detail->claim->patient->id)}}<br>
											<span class="font600">Address :</span> {{ $payment_claim_detail->claim->patient->address1 }}<br>
											{{ $payment_claim_detail->claim->patient->city}}, {{ $payment_claim_detail->claim->patient->state}}, {{ $payment_claim_detail->claim->patient->zip5}}-{{ $payment_claim_detail->claim->patient->zip4}}<br>
											@if(@$payment_claim_detail->claim->patient->phone)<span class="font600">Home Phone :</span>{{$payment_claim_detail->claim->patient->phone}} <br>@endif
											@if(@$payment_claim_detail->claim->patient->work_phone)<span class="font600">Work Phone :</span> {{$payment_claim_detail->claim->patient->work_phone}}@endif
										</p>
									</div>
								</td>
								<?php /*
								<td>{{@$payment_claim_detail->claim->primary_cpt_code}}</td>
								<td class="text-right">{{@$payment_claim_detail->claim->total_charge}}</td>							
								<td class="text-right">{{@$payment_claim_detail->total_allowed}}</td>							
								<td class="text-right">{{@$getpayment_data->deductable}}</td>
								<td class="text-right">{{@$getpayment_data->copay}}</td>
								<td class="text-right">{{@$getpayment_data->coinsurance}}</td>							
								<td class="text-right">{{@$getpayment_data->withheld}}</td>	
								@$payment_claim_detail->total_adjusted
								App\Http\Helpers\Helpers::priceFormat(@$payment_claim_detail->total_paid)
								App\Http\Helpers\Helpers::priceFormat(@$payment_claim_detail->balance_amt)
								*/?>
								<td>{{@$payment_claim_detail->claimcpt->cpt_code}}</td>
								<td class="text-right">{{@$payment_claim_detail->claimcpt->charge}}</td>							
								<td class="text-right">{{@$payment_claim_detail->allowed}}</td>							
								<td class="text-right">{{@$payment_claim_detail->deduction}}</td>						
								<td class="text-right">{{@$payment_claim_detail->copay}}</td>
								<td class="text-right">{{@$payment_claim_detail->coins}}</td>							
								<td class="text-right">{{@$payment_claim_detail->withheld}}</td>
								<td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$payment_claim_detail->writeoff)!!}</td>
								<td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$payment_claim_detail->paid)!!}</td>
							</tr>
                        @endif
                        @endforeach
                        @endif
						
						@if($i < 1) 
							<tr><td colspan="12" class="text-center"><span class="med-gray-dark">No payments has been done</span> </td></tr>
						@endif
                    </tbody>
                </table>                    
            </div>
        </div>
    </div>
</div><!-- /.box-body -->
<!-- Modal Payment Details ends here -->
<div id="choose_claim" class="modal fade in">
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