<?php $last_addin_problemlist = isset($last_addin_problemlist->problem_list_data)?$last_addin_problemlist->problem_list_data:$last_addin_problemlist; ?>
@if(!empty($last_addin_problemlist))
	<?php
   		$patId = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient_id, 'decode');
        $payment_claimed_det = App\Models\Payments\PMTInfoV1::getAllpaymentClaimDetailsByPatient('payment', $patId);
	?>
	@foreach(@$last_addin_problemlist as $keys=>$last_addin_problemlist)  
		<?php
			@$patient_id = isset($patient_id)?$patient_id:App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$last_addin_problemlist->patient->id,'encode');	
			$unencrypt_patid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
			$claimId = @$last_addin_problemlist->claim->id;
			$claimDet = @$last_addin_problemlist->claim;
			$insurance_payment_count = (!empty($payment_claimed_det[$claimId])) ? $payment_claimed_det[$claimId] : 0;
			$detArr = ['patient_id'=> @$unencrypt_patid, 'status' => @$claimDet->status, 'charge_add_type' => @$claimDet->charge_add_type, 'claim_submit_count' => @$claimDet->claim_submit_count];
			if(isset($listFor) && $listFor == 'patients') {
	        	$edit_link = App\Http\Helpers\Helpers::getChargeEditLinkByDetails(@$claimId, @$insurance_payment_count, "Billing", $detArr);
	    	} else {
	    		$edit_link = App\Http\Helpers\Helpers::getChargeEditLinkByDetails(@$claimId, @$insurance_payment_count, "Charge", $detArr);
	    	}
		?>
		<tr>		
			<td>
				@if(Request::segment(1) == 'armanagement')
					{!! Form::checkbox('claim_ids[]', @$last_addin_problemlist->claim->claim_number, null, ['class'=>"chk js_claim_ids js-select-all-sub-checkbox",'data-id'=>"",'id'=>$keys]) !!}<label for="{{$keys}}" class="no-bottom">&nbsp;</label>
				@endif
                <label for="{{$keys}}" class="no-bottom">&nbsp;</label>
				<i @if($checkpermission->check_url_permission('patients/{patient_id}/problem/{claimid}/show') == 1) data-toggle="modal" data-target="#show_problem_list" class="fa fa-edit med-green cur-pointer js_show_problem_list" data-url="{{url('patients/'.@$patient_id.'/problem/'.@$last_addin_problemlist->claim->id.'/show')}}" @endif></i>
			</td>
		
			<td>
				{{ App\Http\Helpers\Helpers::dateFormat(@$last_addin_problemlist->claim->date_of_service,'claimdate') }}
			</td>			
                    
			<td>
				<a href="{{ $edit_link }}" class="js-claimlink">{{@$last_addin_problemlist->claim->claim_number}}</a>
				<!-- Workbench: Description should be in Export and show the notes when hover the mouse -->
				<!-- Rev. 1, Ref: MR-2873 - Ravi - 16-09-19 -->
				<a id="someelem{{hash('sha256',@$last_addin_problemlist->claim->id)}}" class="someelem" data-id="{{hash('sha256',@$last_addin_problemlist->claim->id)}}" href="javascript:void(0);"><i class="fa fa-sticky-note-o med-orange fa-5x cursor-pointer bill-lblue" style="font-size: 12px; margin-top: 1px;"></i></a>
				<div class="on-hover-content js-tooltip_{{hash('sha256',@$last_addin_problemlist->claim->id)}}" style="display:none;">
					{{ @$last_addin_problemlist->description }}
				</div>
			</td>  
			<td>
				<?php 
					$patient = $last_addin_problemlist->patient;
					$getReachEndday = '';
					$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient->id);
					$plan_end_date = ''; //App\Http\Helpers\Helpers::getPatientPlanEndDate(@$patient->id,'','','Primary'); 
					if ($plan_end_date == '0000-00-00' || $plan_end_date == '') {
						$getReachEndday = 0;
					} else {
						$now = strtotime(date('Y-m-d')); // or your date as well
						$your_date = strtotime($plan_end_date);
						$datediff = $now - $your_date;
						$getReachEndday = floor($datediff / (60 * 60 * 24));
					}
					$insurance_name = "";
					if ($patient->is_self_pay == 'Yes') {
						$insurance_name = "Self Pay";
					} else {
						//$insurance_name = App\Models\Patients\PatientInsurance::CheckAndReturnInsuranceName(@$patient->id)	
						$insurance_name = (isset($insurances['all'][$patient->id])) ? ($insurances['all'][$patient->id]) : '';		
					}
					$patient_ins_name = $insurance_name;
					$open_new_window = 0; // open patient view in same page. 		

					$fin_details = @$patient->patient_claim_fin[0];
					//$patient_due = (!empty($fin_details->total_pat_due)) ? App\Http\Helpers\Helpers::priceFormat(@$fin_details->total_pat_due): '0.00';  
					$patient_due = (!empty($patient->total_pat_due)) ? App\Http\Helpers\Helpers::priceFormat(@$patient->total_pat_due) : '0.00';
					//$ins_due  = (!empty($fin_details->total_ins_due)) ? App\Http\Helpers\Helpers::priceFormat(@$fin_details->total_ins_due): '0.00';  
					$ins_due = (!empty($patient->total_ins_due)) ? App\Http\Helpers\Helpers::priceFormat(@$patient->total_ins_due) : '0.00';
					//$ar_due = (!empty($fin_details->total_ar)) ? App\Http\Helpers\Helpers::priceFormat(@$fin_details->total_ar): '0.00';  
					$ar_due = (!empty($patient->total_ar)) ? App\Http\Helpers\Helpers::priceFormat(@$patient->total_ar) : '0.00';
					$patient_name = App\Http\Helpers\Helpers::getNameformat($patient->last_name, $patient->first_name, $patient->middle_name);
				?>
				<div class="p-b-0 p-l-0" style="width:10%;float:left;">
		            <span class="js_insgray{{ @$patient->id }} hidden-print" @if(@$patient->eligibility_verification == 'None' || @$patient->eligibility_verification == 'Error') @else style="display:none;" @endif >  
		                <a title="Check Eligibility" data-unid="{{ @$patient->id }}"  data-patientid="{{ @$patient_id }}" data-category="Primary" class="js-patient-eligibility_check js-claimlink" href="javascript:void(0);"><i class="fa {{Config::get('cssconfigs.common.user')}} text-gray font10"></i></a> 
		            </span>                             
		            <i class="fa fa-spinner fa-spin font11 patientinsloadingimg{{ @$patient->id }}" style="display:none;"></i>
		            <span class="js_insgreen{{ @$patient->id }} hidden-print" @if(@$patient->eligibility_verification == 'Active' && $getReachEndday <= 0) @else style="display:none;" @endif >    
		                <a title="Eligibility Details" data-unid="{{ @$patient->id }}" class="js_get_eligiblity_details js-claimlink" data-patientid="{{ @$patient_id }}" data-category="Primary" data-toggle="modal" href="#eligibility_content_popup"><i class="fa {{Config::get('cssconfigs.common.user')}} text-green font10"></i></a> 
		            </span>
		            <span class="js_insred{{ @$patient->id }} hidden-print" @if(@$patient->eligibility_verification == 'Inactive' || $getReachEndday>0) @else style="display:none;" @endif >    
		                <a title="Eligibility Details" data-unid="{{ @$patient->id }}" class="js_get_eligiblity_details js-claimlink" data-patientid="{{ @$patient_id }}" data-category="Primary" data-toggle="modal" href="#eligibility_content_popup"><i class="fa {{Config::get('cssconfigs.common.user')}} text-red font10"></i></a> 
		            </span>                                         
	       		</div>
		        <!-- Patient name block click not redirect issue fixed -->
		        <div class="p-b-0 p-l-0 js-table-click">                
		            @include ('layouts/patient_hover')                         
		        </div>                    
			</td>
			<td>
				<div class="col-lg-12" style="padding-bottom: 0px; padding-left: 0px;">
					<a id="someelem{{hash('sha256',@$last_addin_problemlist->claim->rendering_provider->id)}}" class="someelem" data-id="{{hash('sha256',@$last_addin_problemlist->claim->rendering_provider->id)}}" href="javascript:void(0);">@if(isset($last_addin_problemlist->claim->rendering_provider->id) && $last_addin_problemlist->claim->rendering_provider->id !=''){{ str_limit(@$last_addin_problemlist->claim->rendering_provider->short_name,25,'...') }} @else -Nil- @endif</a>
					<?php @$provider = @$last_addin_problemlist->claim->rendering_provider; ?>  
					@include ('layouts/provider_hover')
				</div>
			</td>
			<td>
				<div class="col-lg-12" style="padding-bottom: 0px; padding-left: 0px;">
					<a id="someelem{{hash('sha256',@$last_addin_problemlist->claim->facility_detail->id)}}" class="someelem" data-id="{{hash('sha256',@$last_addin_problemlist->claim->facility_detail->id)}}" href="javascript:void(0);">@if(isset($last_addin_problemlist->claim->facility_detail->id) && $last_addin_problemlist->claim->facility_detail->id !=''){{ str_limit(@$last_addin_problemlist->claim->facility_detail->short_name,25,'...') }} @else -Nil- @endif</a>
					<?php $facility = $last_addin_problemlist->claim->facility_detail; ?>  
					@include ('layouts/facility_hover')
				</div>
			</td>
			<td>
				@if(@$last_addin_problemlist->claim->insurance_details)
					{!! App\Http\Helpers\Helpers::getInsuranceName(@$last_addin_problemlist->claim->insurance_details->id) !!}
				@else
					Self
				@endif	
			</td>
			<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$last_addin_problemlist->claim->total_charge) !!}</td>
			<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$last_addin_problemlist->claim->total_paid) !!}</td>
			<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$last_addin_problemlist->claim->balance_amt) !!}</td>   
			<td>
				<span class="font600 {{@$last_addin_problemlist->status}}">{{ @$last_addin_problemlist->status }}</span>
			</td>
			<td>
				@if(isset($last_addin_problemlist->claim->claim_sub_status->sub_status_desc))
					{{ @$last_addin_problemlist->claim->claim_sub_status->sub_status_desc }}
				@else 
					-Nil-
				@endif
			</td>
			<td>
				<?php $fllowup_date = date("m/d/y", strtotime($last_addin_problemlist->fllowup_date)); ?>
				@if(date("m/d/y") == $fllowup_date)
					<span class="med-orange">{{$fllowup_date}}</span>
				@elseif(date("m/d/y") >= $fllowup_date)
					<span class="med-red">{{$fllowup_date}}</span>
				@else
					<span class="med-gray">{{$fllowup_date}}</span>
				@endif
			</td>
			<td>{{ App\Http\Helpers\Helpers::shortname($last_addin_problemlist->assign_user_id) }}</td>
			<td style="width:4%;">
				<span class="{{@$last_addin_problemlist->priority}}">
				@if($last_addin_problemlist->priority == 'High')
					<span class="hide">{{$last_addin_problemlist->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
				@elseif($last_addin_problemlist->priority == 'Low')
					<span class="hide">{{$last_addin_problemlist->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
				@else
					<span class="hide">{{$last_addin_problemlist->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
				@endif							
				</span>
			</td>
		</tr>
	@endforeach 

@endif