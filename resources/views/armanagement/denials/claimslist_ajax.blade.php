<?php $count = 1;  ?>
@foreach($denial_cpt_list as  $result)
<?php 
	if(isset($result->claim_number) && $result->claim_number != ''){
?>
<tr style="cursor:default;">
	@if($count == 1)
		<input type="hidden" name="encodeClaim" value="{{ implode(',',$claim_nos) }}" />
	@endif
	<td>{!! Form::checkbox('claim_ids[]', @$claim->claim_number, null, ['class'=>"chk js_claim_ids js-select-all-sub-checkbox",'data-id'=>"",'id'=>$key]) !!}<label for="{{$key}}" class="no-bottom"> &nbsp;</label></td>
	<td>{{ @$result->claim_number }}</td>
	<td>{{ App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$result->dos_from,'','Nil') }}</td>
	<td>{{ @$result->account_no }}</td>
	<td>{{ @$result->patient_name }}</td>
	<td>{{ @$result->responsibility }}</td>
	<td>{{ @$result->ins_category }}</td>
	<td>{{ @$result->claim->rend_providers->provider_short }}</td>
	<td>{{ @$result->claim->facility->facility_short }}</td>
	<td>{{ @$result->cpt_code }}</td>
	<td>{{ @$result->denial_date }}</td>
	<td>
		@if(isset($result->claim->claim_sub_status->sub_status_desc) && $result->claim->claim_sub_status->sub_status_desc != '')
			{{ @$result->claim->claim_sub_status->sub_status_desc }}
		@else
			-Nil- 
		@endif
	</td>
	<?php /*
	<td>
		@if(@$result->denial_code != '')
			{{ rtrim(@$result->denial_code,',') }}
		@else
			Nil
		@endif
	</td>
	*/ ?>
	<td>{{ @$result->claim_age_days }}</td>
	@if(isset($workbench_status) && $workbench_status == 'Include')
	<td>
		@if(isset($result->last_workbench_status))
			{{ $result->last_workbench_status }}
		@else
			N/A
		@endif
	</td>
	@endif
	<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->charge) !!}</td>
	<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->total_ar_due) !!}</td>
</tr>
<?php
	} else {
?>
<?php
	$last_name = @$result->claim->patient->last_name;
	$first_name = @$result->claim->patient->first_name;
	$middle_name = @$result->claim->patient->middle_name;
	$patient_name = App\Http\Helpers\Helpers::getNameformat($last_name, $first_name, $middle_name);

	$ar_due = @$result->total_ar_due;
	$tot_charge = isset($result->total_charge) ? $result->total_charge : @$result->claimcpt->charge;
	if(isset($result->lastcptdenialdesc->pmtinfo)) {
		if($result->lastcptdenialdesc->pmtinfo->pmt_mode == 'EFT')
			$denial_date = @$result->lastcptdenialdesc->pmtinfo->eft_details->eft_date;
		elseif($result->lastcptdenialdesc->pmtinfo->pmt_mode == 'Credit')
			$denial_date = @$result->lastcptdenialdesc->pmtinfo->credit_card_details->expiry_date ;
		else 
			$denial_date = @$result->lastcptdenialdesc->pmtinfo->check_details->check_date ;
	}else{
		$denial_date = @$result->denied_date;
	}
	$denial_date = App\Http\Helpers\Helpers::dateFormat(@$denial_date);

	$responsibility = 'Patient';
	$ins_category = 'Patient';
	
	if(isset($result->lastcptdenialdesc->claimcpt_txn->claimtxdetails->payer_insurance_id)) {
        $responsibility = App\Http\Helpers\Helpers::getInsuranceName($result->lastcptdenialdesc->claimcpt_txn->claimtxdetails->payer_insurance_id);
		$ins_category = @$result->lastcptdenialdesc->claimcpt_txn->claimtxdetails->ins_category;
    }    else{
        $responsibility = App\Http\Helpers\Helpers::getInsuranceName(@$result->rec_claim_txn->payer_insurance_id);
		$ins_category = @$result->rec_claim_txn->ins_category;
    }
	//$last_txn_id = $result->last_txn_id;
	$cpt_info_id = $result->claim_cpt_info_id;
	if(isset($result->cpt_codes)){		
		$cpt_arr = array_unique(explode(",", $result->cpt_codes));
		$cpt_codes = implode(",", $cpt_arr);
	} else {
		$cpt_codes = @$result->claimcpt->cpt_code;
	}
?>
<tr style="cursor:default;">
	@if($count == 1)
		<input type="hidden" name="encodeClaim" value="{{ implode(',',$claim_nos) }}" />
	@endif
	<td>{!! Form::checkbox('claim_ids[]', @$result->claim->claim_number, null, ['class'=>"chk js_claim_ids js-select-all-sub-checkbox",'data-id'=>"",'id'=>$result->claim->claim_number]) !!}<label for="{{$result->claim->claim_number}}" class="no-bottom"> &nbsp;</label></td>
	<td>{{ @$result->claim->claim_number }}</td>
	<td>{{ App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$result->claimcpt->dos_from,'','Nil') }}</td>
	<td>{{ @$result->claim->patient->account_no }}</td>
	<td>{{ $patient_name }}</td>
	<td>{{ $responsibility }}</td>
	<td>{{ $ins_category }}</td>
	<td>{{ @$result->claim->rend_providers->provider_short }}</td>
	<td>{{ @$result->claim->facility->facility_short }}</td>
	<td>
		{{ @$cpt_codes }}
	</td>
	<td>{{ $denial_date }}</td>
	<td>
		@if(isset($result->claim->claim_sub_status->sub_status_desc) && $result->claim->claim_sub_status->sub_status_desc != '')
			{{ @$result->claim->claim_sub_status->sub_status_desc }}
		@else
			-Nil-	
		@endif	
	</td>
	<td>{{ @$result->claim->claim_age_days }}</td>
	@if(isset($workbench_status) && $workbench_status == 'Include')
	<td class="fnWB">
		@if(isset($result->last_workbench))
			{{ $result->last_workbench->status }}
		@else
			N/A
		@endif
	</td>
	@endif
	<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$tot_charge) !!}</td>
	<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$ar_due) !!}</td>
</tr>
<?php } ?>
@endforeach