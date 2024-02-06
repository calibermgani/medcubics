<?php 
	$count = 1;   
	$insurances = App\Http\Helpers\Helpers::getInsuranceNameLists();
	$patient_insurances = App\Models\Patients\PatientInsurance::getAllPatientInsuranceList();     
	$payment_claimed_det = App\Models\Payments\PMTInfoV1::getAllpaymentClaimDetails('patient');                    
?>
@foreach($claims_lists as $key => $claim)                     
<?php
	$facility = $claim->facility_detail;
	$provider = $claim->rendering_provider;
	$status_display_class = ($claim->status == "Paid") ? "js-listclaim ar-hide-class" : "";
	$claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claim->id, 'encode');
	$url = url('patients/payment/popuppayment/' . $claim_id);

	$insurance_name = "";
	if (empty($claim->insurance_details) || $claim->insurance_details->id == '' || $claim->insurance_details->id == '0') {
		$insurance_name = "Self";
	} else {
		$insurance_name = !empty($insurances[$claim->insurance_details->id]) ? $insurances[$claim->insurance_details->id] : App\Http\Helpers\Helpers::getInsuranceName(@$claim->insurance_details->id);
	}

	$patient_insurance_name = '';
	if (isset($patient_insurances['primary'][@$claim->patient->id]))
		$patient_insurance_name = $patient_insurances['primary'][@$claim->patient->id];
	elseif (isset($patient_insurances['secondary'][@$claim->patient->id]))
		$patient_insurance_name = $patient_insurances['secondary'][@$claim->patient->id];
	elseif (isset($patient_insurances['others'][@$claim->patient->id]))
		$patient_insurance_name = $patient_insurances['others'][@$claim->patient->id];
	// $patient_insurance_name = App\Models\Patients\PatientInsurance::CheckAndReturnInsuranceName(@$claim->patient->id); 
	$patient_name = App\Http\Helpers\Helpers::getNameformat(@$claim->patient->last_name, @$claim->patient->first_name, @$claim->patient->middle_name);
	$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claim->patient->id);
	// Charge edit link
	$insurance_payment_count = (!empty($payment_claimed_det[$claim->id])) ? $payment_claimed_det[$claim->id] : 0;
	$detArr = ['patient_id' => @$claim->patient->id, 'status' => @$claim->status, 'charge_add_type' => @$claim->charge_add_type, 'claim_submit_count' => @$claim->claim_submit_count];
	$charge_link = App\Http\Helpers\Helpers::getChargeEditLinkByDetails(@$claim->id, @$insurance_payment_count, "Charge", $detArr);
?>
<tr class = "">
@if($count == 1)
	<input type="hidden" name="encodeClaim" value="{{ implode(',',$encodeClaimIds) }}" />
@endif
    <td class='table-check'>        
        <!--div class="checkbox checkbox-primary no-margin" aria-checked="false" aria-disabled="false"-->
        {!! Form::checkbox('claim_ids[]', @$claim->claim_number, null, ['class'=>"chk js_claim_ids js-select-all-sub-checkbox",'data-id'=>"",'id'=>$key]) !!}<label for="{{$key}}" class="no-bottom"> &nbsp;</label>
            <!--label for="text" style="">
            </label-->
        <!--/div-->
    </td>
    <td> 
        <a href="#js-model-popup-payment" target="_blank" claim_number = "{{$claim->claim_number}}"data-toggle="modal" data-target="#js-model-popup-payment" data-url="{{$url}}" class="claimbilling">{{ App\Http\Helpers\Helpers::checkAndDisplayDateInInput
(@$claim->date_of_service)}}</a>
    </td> 
    <td>
        <a href="{{ $charge_link }}" target="_blank"> {{@$claim->claim_number}}</a>
    </td>  
    
    <td>
        <a href="" target="_blank"> {{@$claim->patient->account_no}}</a>
    </td>
 
    <td>
        <a id="someelem{{@$claim->id.$count}}" target="_blank" class="someelem" data-id="{{@$claim->id.$count}}" href="{{ url('patients/'.$patient_id.'/ledger') }}"> {{ $patient_name }}</a>
        <span class="on-hover-content js-tooltip_{{@$claim->id.$count}}" style="display:none;">
            <span class="med-orange font600">{{ $patient_name }}</span> 
            <p class="no-bottom hover-color">
                <span class="font600">Acc No :</span> {{ @$claim->patient->account_no }}<br>
                @if(@$claim->patient->dob != "0000-00-00" && @$claim->patient->dob != "" && @$claim->patient->dob != "1901-01-01")
                <span class="font600">DOB :</span>{{ App\Http\Helpers\Helpers::dateFormat(@$claim->patient->dob,'claimdate') }}
                <span class="font600">Age :</span> {{ App\Http\Helpers\Helpers::dob_age(@$claim->patient->dob) }} 
                @endif
                <span class="font600">Gender :</span> {{ @$claim->patient->gender }}<br>
                <span class="font600">Ins :</span> {{ $patient_insurance_name }}<br>
                <span class="font600">Address :</span> {{ @$claim->patient->address1 }}<br>
                {{ @$claim->patient->city}}, {{ @$claim->patient->state}}, {{ @$claim->patient->zip5}}-{{ @$claim->patient->zip4}}<br>
                @if(@$claim->patient->phone)<span class="font600">Home Phone :</span>{{@$claim->patient->phone}} <br>@endif
                @if(@$claim->patient->work_phone)
                <span class="font600">Work Phone :</span> {{@$claim->patient->work_phone}}
                @endif
            </p>
        </span>                                 
    </td>
    <td>
        <a id="someelem{{hash('sha256','p_'.@$claim->rendering_provider->id.$count)}}" class="someelem" data-id="{{hash('sha256','p_'.@$claim->rendering_provider->id.$count)}}" href="javascript:void(0);"> {{str_limit(@$claim->rendering_provider->short_name,15,' ...')}}</a> 
        <?php @$provider->id = 'p_'.@$claim->rendering_provider->id.$count; ?> 
        @include ('layouts/provider_hover')
    </td>
    <td> 
        <a id="someelem{{hash('sha256','f_'.@$claim->facility_detail->id.$count)}}" class="someelem" data-id="{{hash('sha256','f_'.@$claim->facility_detail->id.$count)}}" href="javascript:void(0);"> {{str_limit(@$claim->facility_detail->short_name,15,' ...')}}</a>
        <?php @$facility->id = 'f_'.@$claim->facility_detail->id.$count; ?> 
        @include ('layouts/facility_hover')
    </td> 
    <td>
        {{ $insurance_name }}
    </td>
    <td class="text-right">{{@$claim->total_charge}}</td>
    <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim->total_paid)!!}</td>                        
    <td><span class="pull-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim->pmt_claim_fin_data->patient_due)!!}</span></td>
    <td><span class="pull-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim->pmt_claim_fin_data->insurance_due)!!}</span></td>
    <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim->balance_amt)!!}</td>
    <td>
        <span class="@if(@$claim->status == 'Ready') ready-to-submit @elseif(@$claim->status == 'Partial Paid') c-ppaid @else {{ @$claim->status}} @endif">{{ @$claim->status}}</span>
    </td>
	<td>
		@if(isset($claim->claim_sub_status->sub_status_desc))
			{{ $claim->claim_sub_status->sub_status_desc }}
		@else 
			--Nil--
		@endif		
    </td>
	<td>
		@if(isset($claim->followup_details))
			<a data-url="{{ url('patients/armanagement/followup/history') }}/{{$claim->claim_number}}" data-claimno="{{$claim->claim_number}}" class="js_arfullnotes_link cur-pointer font600 js_showing_history"><i class="fa fa-file-text-o" data-placement="bottom" data-toggle="tooltip" data-original-title="History"></i></a>
		@else 
			
		@endif
	</td>			
</tr>
<?php $count++;   ?> 
@endforeach   