<?php $count = 1;   ?>  
@foreach($claims_lists as $claims_list)                       
<?php
	  //dd($claims_list);
	$facility = $claims_list;
	$provider = $claims_list;                         
	$insurance_payment_count = App\Models\Payments\PMTInfoV1::checkpaymentDone($claims_list->id, 'any');    
	$edit_link = App\Http\Helpers\Helpers::getChareEditLink(@$claims_list->id, @$insurance_payment_count, "Billing");
	$claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claims_list->id, 'encode');
?>    
@if($claims_list->charge_add_type == 'esuperbill' && $claims_list->status == "E-bill"|| $claims_list->charge_add_type == 'ehr')
<?php $url = url('patients/'.$id.'/billing/create/'.$claim_id) ?>
@elseif($claims_list->status == 'Submitted' || $claims_list->status == 'Ready' || $claims_list->status == 'Denied' && $insurance_payment_count > 0 || $claims_list->status == 'Patient' && $insurance_payment_count > 0 || $claims_list->status == 'Paid' && $insurance_payment_count > 0|| $claims_list->status == 'Pending' && $insurance_payment_count > 0 || $claims_list->status == 'Hold' && $insurance_payment_count > 0 )  
	<?php $url = url('patients/'.$id.'/billing/edit/'.$claim_id) ?>                          
@else
	<?php $url = url('patients/'.$id.'/billing/create/'.$claim_id) ?>  
<!-- <tr data-toggle="modal" data-target="#payment_details" data-url="{{$url}}">     -->                    
@endif                                             
@if(!$checkpermission->check_url_permission('patients/{id}/billing/create/{claim_id?}')) 
	<?php $url = "" ?>
@endif
  <?php $paymentUrl = url('patients/payment/popuppayment/'.$claim_id); ?>   
<tr data-url="{{$edit_link}}" class="js-table-click-billing">
	<td><a href="#" class="js-prevent-redirect"  claim_number = "{{$claims_list->claim_number}}"data-toggle="modal" data-target="#js-model-popup-payment" data-url="{{$paymentUrl}}">{{ App\Http\Helpers\Helpers::dateFormat($claims_list->date_of_service,'claimdate') }}</a></td>   
	<td><a href="{{$edit_link}}">{{@$claims_list->claim_number}}</a> </td>
	<td>
		<a id="someelem{{hash('sha256','p_'.@$claims_list->rendering_provider_id.$count)}}" class="someelem" data-id="{{hash('sha256','p_'.@$claims_list->rendering_provider_id.$count)}}" href="javascript:void(0);"> {{str_limit(@$claims_list->rendering_short_name,15,' ...')}}</a> 
		<?php
			//@$claims_list->rendering_provider_id = 'p_'.@$claims_list->rendering_provider_id.$count; 
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
	<?php $provider = $claims_list;?>
	<td>
		<a id="someelem{{hash('sha256','p_'.@$claims_list->billing_provider_id.$count)}}" class="someelem" data-id="{{hash('sha256','p_'.@$claims_list->billing_provider_id.$count)}}" href="javascript:void(0);"> {{str_limit(@$claims_list->billing_short_name,15,'..')}}</a>
		<?php 
			//@$provider->billing_provider_id = 'p_'.@$claims_list->billing_provider_id.$count; 
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
	<?php 
		// When billed amount comes unbilled amount should not come
		$charge_amt = App\Http\Helpers\Helpers::BilledUnbilled($claims_list);
		$billed = isset($charge_amt['billed'])?$charge_amt['billed']:0.00;
		$unbilled = isset($charge_amt['unbilled'])?$charge_amt['unbilled']:0.00;
	?>                       
	<td class="text-right"> {!!App\Http\Helpers\Helpers::priceFormat(@$unbilled)!!}</td>
	<td class="text-right"> {!!App\Http\Helpers\Helpers::priceFormat(@$billed)!!}</td>
	<td class="text-right"> {!!App\Http\Helpers\Helpers::priceFormat(@$claims_list->total_paid)!!}</td>
	<td class="text-right"> {!!App\Http\Helpers\Helpers::priceFormat(@$claims_list->balance_amt)!!}</td>
	<td><span class="@if(@$claims_list->status == 'Ready') ready-to-submit @elseif(@$claims_list->status == 'Partial Paid') c-ppaid @else {{ @$claims_list->status}} @endif">{{ @$claims_list->status}}</span></td>
	<td>
		@if(isset($claims_list->sub_status_desc))
			{{ $claims_list->sub_status_desc }}
		@else 
			-Nil-
		@endif
	</td>
	<td class = "js-not-click hidden-print"> 
	@if($claims_list->status != 'E-bill')
	<a href= "#" onClick="window.open('{{url('/getcmsform/'.$claim_id)}}', '_blank')" class="js-prevent-redirect"> <i class="fa {{Config::get('cssconfigs.patient.cms_icon')}} js-prevent-redirect" data-placement="bottom"  data-toggle="tooltip" data-original-title="CMS 1500"></i></a>
	@else
	<a href= "#"> <i class="fa {{Config::get('cssconfigs.patient.cms_icon')}} js-prevent-redirect" data-placement="bottom"  data-toggle="tooltip" data-original-title="CMS 1500"></i></a>
	@endif</td>   
</tr>
<?php $count++;   ?> 
@endforeach