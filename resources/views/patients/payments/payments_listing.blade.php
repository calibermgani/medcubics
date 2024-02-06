<?php $count = 1;   ?>  
@if(!empty($claims_lists))  
@foreach($claims_lists as $claims_list)
	<?php
		$facility = $claims_list;
		$provider = @$claims_list->rendering_provider;
		$claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claims_list->id, 'encode');
		// Edit charge link started.                
		//$insurance_payment_count = (!empty($payment_claimed_det[$claims_list->id])) ? $payment_claimed_det[$claims_list->id] : 0;
		$insurance_payment_count = App\Models\Payments\PMTInfoV1::checkpaymentDone($claims_list->id, 'any');
		$detArr = ['patient_id'=> @$claims_list->patient_id, 'status' => @$claims_list->status, 'charge_add_type' => @$claims_list->charge_add_type, 'claim_submit_count' => @$claims_list->claim_submit_count];
		$edit_link = App\Http\Helpers\Helpers::getChargeEditLinkByDetails(@$claims_list->id, @$insurance_payment_count, "Billing", $detArr);
		$url = url('patients/payment/popuppayment/'.$claim_id); 
	?>
	<tr>
		<td><a href="#" claim_number = "{{$claims_list->claim_number}}"data-toggle="modal" data-target="#js-model-popup-payment" data-url="{{$url}}" class="claimbilling">{{ App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$claims_list->date_of_service) }}</a></td>
		<td>
			<a href="{{ $edit_link }}">{{@$claims_list->claim_number}}</a>
		</td>
		<td>
			<a id="someelem{{hash('sha256','p_'.@$claims_list->rendering_provider_id.$count)}}" class="someelem" data-id="{{hash('sha256','p_'.@$claims_list->rendering_provider_id.$count)}}" href="javascript:void(0);"> {{str_limit(@$claims_list->rendering_short_name,15,' ...')}}</a> 
			<?php
				@$provider->id = 'p_'.@$claims_list->rendering_provider_id.$count; 
				@$provider->provider_name = @$claims_list->rendering_short_name;
				@$provider->provider_dob = @$claims_list->rendering_dob;
				@$provider->gender = @$claims_list->rendering_gender;
				@$provider->etin_type = @$claims_list->rendering_etin_type;
				@$provider->etin_type_number = @$claims_list->rendering_etin_no;
				@$provider->npi = @$claims_list->rendering_npi;
				$provider->type_name = 'Rendering';
			?>
			@include ('layouts/provider_hover')
		</td>
		<td>
			<a id="someelem{{hash('sha256','p_'.@$claims_list->billing_provider_id.$count)}}" class="someelem" data-id="{{hash('sha256','p_'.@$claims_list->billing_provider_id.$count)}}" href="javascript:void(0);"> {{str_limit(@$claims_list->billing_short_name,15,'..')}}</a>
			<?php 
				@$provider->id = @$provider->billing_provider_id = 'p_'.@$claims_list->billing_provider_id.$count; 
				@$provider->provider_name = @$claims_list->billing_short_name;
				@$provider->provider_dob = @$claims_list->billing_dob;
				@$provider->gender = @$claims_list->billing_gender;
				@$provider->etin_type = @$claims_list->billing_etin_type;
				@$provider->etin_type_number = @$claims_list->billing_etin_no;
				@$provider->npi = @$claims_list->billing_npi;
				$provider->type_name = 'Billing';
			?> 
			@include ('layouts/provider_hover')
		</td>
		<td>
			<a id="someelem{{hash('sha256','f_'.@$claims_list->facility_id.$count)}}" class="someelem" data-id="{{hash('sha256','f_'.@$claims_list->facility_id.$count)}}" href="javascript:void(0);"> {{ str_limit(@$claims_list->facility_short_name,15,' ...') }}</a> 
			<?php @$facility->id = 'f_'.@$claims_list->facility_id.$count; ?>		
			@include ('layouts/facility_hover')
		</td> 
		@if(empty($claims_list->insurance_id))
		<td>Self</td>
		@else
		<td>{!!App\Http\Helpers\Helpers::getInsuranceName(@$claims_list->insurance_id)!!}</td>
		@endif  
		<td class="text-right"> {!!App\Http\Helpers\Helpers::priceFormat(@$claims_list->total_charge)!!}</td>
		<td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claims_list->total_paid)!!}</td>
		<td class="text-right"> {!! App\Http\Helpers\Helpers::priceFormat(@$claims_list->totalAdjustment) !!}</td>
		<td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claims_list->patient_due)!!}</td>
		<td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claims_list->insurance_due)!!}</td>
		<td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claims_list->balance_amt)!!}</td>
		<td><span class="@if(@$claims_list->status == 'Ready') ready-to-submit @elseif(@$claims_list->status == 'Partial Paid') c-ppaid @else {{ @$claims_list->status}} @endif">{{ @$claims_list->status}}</span></td>
		<td> <a class = "js-not-click" onClick="window.open('{{ url('getcmsform/'.$claim_id) }}', '_blank')"> <i class="fa {{Config::get('cssconfigs.patient.cms_icon')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="CMS 1500"></i> </a></td>
	</tr>
	<?php $count++; ?> 
@endforeach  
@else
	<tr><td colspan="14" class="text-center"><span class="med-gray-dark">No Claims Available</span> </td></tr>
@endif