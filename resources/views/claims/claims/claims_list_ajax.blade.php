<?php $count = 1;    
	$insurances = App\Http\Helpers\Helpers::getInsuranceNameLists(); 
	$patient_insurances = App\Models\Patients\PatientInsurance::getAllPatientInsuranceList();  
	$payment_claimed_det = App\Models\Payments\PMTInfoV1::getAllpaymentClaimDetails('payment');  
	$billed_amounts_list = App\Models\Payments\ClaimCPTInfoV1::getAllBilledAmountByActiveLineItem();
?>  

@if(isset($claims))	
@foreach($claims as $key => $claim)
<?php 
	$facility = @$claim->facility_detail;
	$provider = @$claim->rendering_provider; 
	$patient = @$claim->patient;
	$patient_name = App\Http\Helpers\Helpers::getNameformat(@$claim->patient->last_name,@$claim->patient->first_name,@$claim->patient->middle_name); 
	if (@$claim->insurance_details->payerid != '' && @$claim->self_pay == 'No')
		$class_name = 'cls-electronic';
	else if (@$claim->status == 'Patient' || $claim->self_pay == 'Yes')
		$class_name = 'cls-patient';
	else
		$class_name = 'cls-paper';
	$claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claim->id);
	$billed_amount = (!empty($billed_amounts_list[$claim->id])) ? $billed_amounts_list[$claim->id] : 0;
	$billed_amount = App\Http\Helpers\Helpers::priceFormat($billed_amount);
	$insurance_payment_count = (!empty($payment_claimed_det[$claim->id])) ? $payment_claimed_det[$claim->id] : 0;

	$insurance_name = "";
	if ($claim->self_pay == 'Yes')
		$insurance_name = "Self";
	else
		$insurance_name = !empty($insurances[@$claim->insurance_details->id]) ? $insurances[@$claim->insurance_details->id] : App\Http\Helpers\Helpers::getInsuranceName(@$claim->insurance_details->id);
	// Edit link new function call included
	$detArr = ['patient_id' => @$claim->patient->id, 'status' => @$claim->status, 'charge_add_type' => @$claim->charge_add_type, 'claim_submit_count' => @$claim->claim_submit_count];
	$edit_link = App\Http\Helpers\Helpers::getChargeEditLinkByDetails(@$claim->id, @$insurance_payment_count, "Charge", $detArr);

	$patient_ins_name = '';
	if (isset($patient_insurances['all'][@$claim->patient->id])) {
		$patient_ins_name = $patient_insurances['all'][@$claim->patient->id];
	}
?>
<tr data-url="{{$edit_link}}" class="js-table-click-billing">
	@if($claim->self_pay == 'Yes')
	<td style="cursor:not-allowed;">
		{!! Form::checkbox("claim_ids[]",$claim_id,null,["class" => "no-margin js-select-all-sub-checkbox $class_name","disabled" => "disabled"]) !!}
	</td>
	@else
	<td>
		{!! Form::checkbox("claim_ids[]",$claim_id,null,["class" => "no-margin js-select-all-sub-checkbox $class_name"]) !!}
	</td>
	@endif                                                                    
	<td>{{$claim->claim_number}}</td>
	<td>
		<?php $popupurl = url('patients/payment/popuppayment/'.$claim_id) ?> 
		<a href="#" class="js-prevent-redirect" claim_number="{{$claim->claim_number}}" data-toggle="modal" data-target="#js-model-popup-payment" data-url="{{$popupurl}}" class="claimbilling">{{App\Http\Helpers\Helpers::dateFormat($claim->date_of_service,'claimdate')}}</a></td>
	<td>@include ('layouts/patient_hover')</td>
	<td>
		<?php /*
		  @if($claim->self_pay == 'No'){!!App\Http\Helpers\Helpers::getInsuranceName(@$claim->insurance_details->id)!!} @elseif($claim->self_pay == 'Yes') Self @endif
		 */ ?>
		{{ $insurance_name }}
	</td>
	<td>
	{{ $claim->insurance_category }}
	</td>
	<td>@if($claim->self_pay == 'No'){{@$claim->insurance_details->payerid}}@endif</td>
	<td>
		<a id="someelem{{hash('sha256','p_'.@$claim->rendering_provider->id.$key)}}" class="someelem" data-id="{{hash('sha256','p_'.@$claim->rendering_provider->id.$key)}}" href="javascript:void(0);"> {{str_limit(@$claim->rendering_provider->short_name,15,' ...')}}</a> 
		<?php @$provider->id = 'p_'.@$claim->rendering_provider->id.$key; ?> 
		@include ('layouts/provider_hover')                                        
	</td>
	<?php $provider = $claim->billing_provider; ?>
	<td>
		<a id="someelem{{hash('sha256','p_'.@$claim->billing_provider->id.$key)}}" class="someelem" data-id="{{hash('sha256','p_'.@$claim->billing_provider->id.$key)}}" href="javascript:void(0);"> {{str_limit(@$claim->billing_provider->short_name,15,' ...')}}</a> 
		<?php @$provider->id = 'p_'.@$claim->billing_provider->id.$key; ?> 
		@include ('layouts/provider_hover')                                        
	</td>
	<td>
		<a id="someelem{{hash('sha256','f_'.@$claim->facility_detail->id.$key)}}" class="someelem" data-id="{{hash('sha256','f_'.@$claim->facility_detail->id.$key)}}" href="javascript:void(0);"> {{str_limit(@$claim->facility_detail->short_name,15,' ...')}}</a> 
			<?php @$facility->id = 'f_'.@$claim->facility_detail->id.$key; ?> 
			@include ('layouts/facility_hover')
	</td>
	<td  class="text-right">{{$billed_amount}}</td>                    
	<td>{{ App\Http\Helpers\Helpers::dateFormat($claim->created_at,'date')}}</td>
	<td>{{ App\Http\Helpers\Helpers::dateFormat($claim->filed_date,'date')}}</td>
	<td>
		<a class="js-not-click" onClick="window.open('{{url('/getcmsform/'.$claim_id)}}', '_blank')">  <i class="fa {{Config::get('cssconfigs.patient.cms_icon')}}" data-placement="bottom" data-toggle="tooltip" data-original-title="CMS 1500"></i></a>
		@if($type == 'electronic')
			
		@elseif($type == 'paper')
			@if(@$claim->insurance_details->payerid != '')
				<a class="js-change-electronic-click" data-page-url="claims/status/paper" data-claim-id="{{ $claim_id }}"><i class="fa fa-exclamation-triangle" data-placement="bottom" data-toggle="tooltip" data-original-title="Electronic"></i></a>
			@endif
		@endif
	</td>
</tr>
<?php $count++;   ?> 
@endforeach     
@endif	