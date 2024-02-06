<?php
	if (@$patient_id == true) {
		$patient_id =$patient_id;
	} else {
		$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient->id,'encode');
	}
?>
@if($patient_id != '')
<span class="p-b-0 p-l-0">
    <?php 
    	$patienthover = (isset($maincharge) && $maincharge = 1) ? "js-prevent-redirect" : "";
    	$open_new_window = (isset($open_new_window)) ? $open_new_window : 1; // open patient page separate window or same page settings. default option set as open in separate window.
		
		if (@$patient_data_id == true) {
			$patient_data_id =$patient_data_id;
		}else if(!isset($patient->patient_id)){
			$patient_data_id = @$patient->id;
		} else { 
			$patient_data_id = @$patient->patient_id;
		}
		if (@$patient_ins_name == true) {
			$patient_ins_name = $patient_ins_name;
		} else {
			$patient_ins_name = App\Models\Patients\PatientInsurance::CheckAndReturnInsuranceName(@$patient->id);
		}
		if($patient_ins_name == '' || $patient_ins_name == null)
			$patient_ins_name = "- Nil -";		
    ?>		
    <a href="{{ url('patients/'.$patient_id.'/ledger') }}" @if($open_new_window) target="_blank" @endif ><span class="someelem {{$patienthover}}" data-id="{{ @$patient_data_id }}" id="someelem{{ @$patient_data_id }}">{{ str_limit($patient_name,25,'...') }}</span></a> 
    <div class="on-hover-content js-tooltip_{{@$patient_data_id}}" style="display:none;">
        <span class="med-orange font600"> @if($patient->title){{ @$patient->title }}. @endif{{ @$patient_name }}</span> 
        <p class="no-bottom hover-color">
        	<span style="border-bottom:1px dashed #e0d775; display:block; padding-bottom: 2px; margin-bottom: 5px;"><span class="font600">Acc No : {{ @$patient->account_no }}</span> 
            </span>				
            @if($patient->dob !='' && ($patient->dob !='1901-01-01') && ($patient->dob !='0000-00-00'))
            <span style="display:block; padding: 2px 0px;"><span class="font600"><i class="fa fa-birthday-cake" style="color:#98924d;"></i> </span> 
            {{ App\Http\Helpers\Helpers::dateFormat(@$patient->dob,'claimdate') }} | {{ App\Http\Helpers\Helpers::dob_age(@$patient->dob) }} |                    
            @endif  {{ $patient->gender }} </span>
            <span style="display:block; padding: 2px 0px;"><span class="font600"><i class="fa fa-map-marker" style="color: #98924d;"></i></span> {{ $patient->address1 }}<br>
            {{ $patient->city}} - {{ $patient->state}}, {{ $patient->zip5}}@if($patient->zip4 !='')-{{ $patient->zip4}}@endif</span>
            @if(@$patient->phone !='' || @$patient->mobile !='')
				<span style="display:block;padding: 2px 0px;"><span class="font600"><i class="fa fa-phone" style="color:#98924d"></i> </span> @endif  @if(@$patient->mobile =='' && @$patient->phone !='') {{$patient->phone}}  @elseif(@$patient->mobile !='' && @$patient->phone !='') {{@$patient->phone}} @elseif(@$patient->mobile !='' && @$patient->phone =='') {{@$patient->mobile}} @else {{@$patient->phone}} @endif </span>     
            <span class="font600"><i class="fa fa-bank" style="color:#98924d"></i></span>
            @if($patient->is_self_pay == 'Yes')
            	Self Pay
            @else
            	{{ @$patient_ins_name }}
            @endif
            <br>        
        </p>
    </div>
</span>
@endif