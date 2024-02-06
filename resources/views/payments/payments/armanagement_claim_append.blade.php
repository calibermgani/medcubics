@if(!empty($claim))                                                                       
	<tr>                                      		
		<?php 
			$claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claim->id,'encode');
			$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claim->patient_id,'encode');
			$disabled = $insur_data = $claim_insurance_id = '';
			$claim_multi_insurance = 0;
			if(empty($claim->insurance_details)) {
				$insurance_data = "Patient";
				//$disabled  = "disabled = disabled" ;
				$disabled  = "" ;
				$insur_data = "Patient";
				$claim_insurance_id = "patient";
			} else {
				$insurance_data = App\Http\Helpers\Helpers::getInsuranceName(@$claim->insurance_details->id);                
				$insur_data = @$claim->insurance_details->id;
				$claim_insurance_id = (!empty($claim->insurance_details))?$claim->insurance_details->id:"patient"; 
				$claim_multi_insurance = App\Http\Helpers\Helpers::checkIsMultiInsurance(@$claim->insurance_details->id, @$claim->patient_id);
			}         
			$claimFinDetails = App\Models\Payments\ClaimInfoV1::getClaimFinDetails($claim->id, @$claim->total_charge);
			$patient_other_ins = App\Http\Helpers\Helpers::getPatientOtherIns(@$claim->patient_id);
		?> 
		{!! Form::hidden('patient_id', $patient_id) !!}      
		{!! Form::hidden('patient_other_ins',@$patient_other_ins,['id' => 'patient_other_ins']) !!}
		{!! Form::hidden('pmt_post_ins_cat',"",['id' => 'pmt_post_ins_cat']) !!}

		<td><a href="javascript:void(0)"><input id = "{{$claim_id}}" data-insid = "{{$claim_insurance_id}}" data-ismultiins = "{{ $claim_multi_insurance }}" data-insurance = "{{$insur_data}}" type="checkbox" class="js-sel-claim" checked name = "insurance_checkbox" data-claim = "js-bal-{{$claim->claim_number}}" {{$disabled}}><label for="{{$claim_id}}" class="no-bottom">&nbsp;</label></a></td>
		<?php $url = url('patients/popuppayment/'.$claim->id) ?>
		<td><a>{{App\Http\Helpers\Helpers::dateFormat(@$claim->date_of_service, 'dob')}}</a></td>     
		<td>{{@$claim->claim_number}}</td>               
		<td>{!!$insurance_data!!}</td>                               
		<td class="text-right">{{@$claim->total_charge}}</td>                  
		<td class="text-right">{{@$claimFinDetails['total_paid']}}</td> 
		<td class="text-right">{{@$claimFinDetails['totalAdjustment']}}</td> 
		<td class="text-right" id = "js-bal-{{$claim->claim_number}}">{{@$claimFinDetails['balance_amt']}}</td>
		<td><span class="@if(@$claim->status == 'Ready') ready-to-submit @elseif(@$claim->status == 'Partial Paid') c-ppaid @else {{ @$claim->status }} @endif">{{@$claim->status}}</span></td>
	</tr>
@else
	<tr><td colspan="9" class="text-center"><span class="med-gray-dark">No Claims Available</span> </td></tr>
@endif      